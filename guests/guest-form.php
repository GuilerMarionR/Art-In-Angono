<?php
session_start();

$closedDates = [];
$closedTimes = [];
$guestData = null;

$db = new mysqli('localhost', 'root', '', 'art_in_angono_db');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$museumName = isset($_GET['museum']) ? htmlspecialchars($_GET['museum']) : 'Unknown Museum';

$query = "SELECT closed_date FROM closed_dates WHERE museumName = '$museumName'";
$result = $db->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $closedDates[] = $row['closed_date'];
    }
}

$appointmentDate = $_GET['appointmentDate'] ?? date('Y-m-d');

$closedTimeQuery = "
    SELECT startTime, endTime 
    FROM closed_times 
    WHERE museumName = '$museumName' 
    AND date = '$appointmentDate'";
$closedTimeResult = $db->query($closedTimeQuery);

if ($closedTimeResult) {
    while ($row = $closedTimeResult->fetch_assoc()) {
        $closedTimes[] = [
            'startTime' => date("h:i A", strtotime($row['startTime'])),
            'endTime' => date("h:i A", strtotime($row['endTime']))
        ];        
    }
}

if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $guestQuery = "SELECT * FROM guests WHERE username = '$username'";
    $guestResult = $db->query($guestQuery);

    if ($guestResult && $guestResult->num_rows > 0) {
        $guestData = $guestResult->fetch_assoc();

        // Calculate the age from the birthdate
        $birthdate = $guestData['birthDate'];
        $age = '';
        if ($birthdate) {
            $birthDate = new DateTime($birthdate);
            $currentDate = new DateTime();
            $ageInterval = $currentDate->diff($birthDate);
            $age = $ageInterval->y; // Get the year difference, which represents the age
            $guestData['age'] = $age; // Set the calculated age to the guest data
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['formData'] = $_POST;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

$formData = $_SESSION['formData'] ?? [];
unset($_SESSION['formData']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO - Book A Tour</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <style>
        /* Styling */
    </style>
</head>
<body onload="initializeForm();">

<?php 
    if (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
        include '../includes/navigation-loggedin.php';
    } else {
        include '../includes/navigation-guest.php';
    }
?>
    <div class="form-wrapper">
        <div class="container">
            <div class="title text-center">CLIENT AND APPOINTMENT INFORMATION</div>
            
            <div class="selected-museum text-center">
                <p id="display-museum-name" style="font-size: 25px;"><?php echo $museumName; ?></p>
            </div>

            <form id="appointment-form" method="POST" action="../guests/guest-review.php" onsubmit="handleFormReview(event);">
                <input type="hidden" id="museumName" name="museumName" value="<?php echo $museumName; ?>" required>
                
                <div class="name-row">
                    <?php
                        $fields = [
                            'lastName' => 'Last Name',
                            'firstName' => 'First Name',
                            'middleName' => 'Middle Name',
                            'address' => 'Address',
                            'email' => 'Email',
                            'age' => 'Age',
                            'contactNumber' => 'Contact Number',
                            'numberOfGuests' => 'Number of Guests'
                        ];

                        foreach ($fields as $name => $label) {
                            $value = $formData[$name] ?? ($guestData[$name] ?? '');
                            $required = in_array($name, ['lastName', 'firstName', 'address', 'email', 'age', 'contactNumber', 'numberOfGuests']) ? 'required' : '';
                            $type = ($name === 'email') ? 'email' : (($name === 'age' || $name === 'numberOfGuests') ? 'number' : 'text');
                            echo "
                                <div class=\"input-box\">
                                    <span class=\"details\">$label</span>
                                    <input type=\"$type\" name=\"$name\" placeholder=\"Enter $label\" value=\"$value\" $required>
                                </div>
                            ";
                        }
                    ?>
                </div>

                <div class="date-time-section">
                    <div class="date-box">
                        <label for="date">Date</label>
                        <input type="text" placeholder="Enter the date here" id="date" name="appointmentDate" value="<?= $formData['appointmentDate'] ?? '' ?>" required>
                    </div>
                    
                    <div class="time-box">
    <p>Time</p>
    <div class="time-options">
    <label for="startTime">Start Time</label>
<select id="startTime" name="startTime" required>
    <option value="">Select Start Time</option>
    <option data-value="08:00">8:00 AM</option>
    <option data-value="09:00">9:00 AM</option>
    <option data-value="10:00">10:00 AM</option>
    <option data-value="11:00">11:00 AM</option>
    <option data-value="12:00">12:00 PM</option>
    <option data-value="13:00">1:00 PM</option>
    <option data-value="14:00">2:00 PM</option>
    <option data-value="15:00">3:00 PM</option>
    <option data-value="16:00">4:00 PM</option>
</select>


        <label for="endTime">End Time</label>
        <input type="text" id="endTime" name="endTime" readonly required>
    </div>
</div>
</div>

                <div class="button-container" style="margin-top:150px">
                    <button type="submit" class="btn btn-danger">Review Appointment</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Appointment Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="confirmationDetails"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="confirmSubmit()">Confirm and Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
    const closedDates = <?php echo json_encode($closedDates); ?>;
    const museumName = "<?php echo $museumName; ?>";

    function initializeForm() {
        $("#date").datepicker({
            dateFormat: 'yy-mm-dd',
            minDate: 0,
            beforeShowDay: function(date) {
                const dateString = $.datepicker.formatDate('yy-mm-dd', date);
                return closedDates.indexOf(dateString) === -1 ? [true] : [false, 'disabled-date', 'Closed'];
            },
            onSelect: function(selectedDate) {
                document.querySelector("input[name='appointmentDate']").value = selectedDate;
                fetchClosedTimes(selectedDate);
            }
        });

        // Set previously selected date if available
        const storedDate = "<?php echo $formData['appointmentDate'] ?? ''; ?>";
        if (storedDate) {
            $("#date").datepicker("setDate", storedDate);
        }
    }

    // Fetch closed times for the selected date
    function fetchClosedTimes(date) {
        $.ajax({
            url: 'fetch_closed_times.php',
            type: 'GET',
            data: { date: date, museum: museumName },
            success: function(response) {
                const closedTimes = JSON.parse(response);
                updateAvailableTimes(closedTimes);
            }
        });
    }

    function updateAvailableTimes(closedTimes) {
    $("#startTime option").each(function() {
        const optionTime = $(this).val();
        const optionTimeMinutes = convertToMinutes(optionTime);

        // Disable options that fall within any closed time range
        const closedTimeRange = closedTimes.find(timeRange => {
            const startMinutes = convertToMinutes(timeRange.startTime);
            const endMinutes = convertToMinutes(timeRange.endTime);
            
            // Include the entire last hour by checking if the time is less than or equal to the closed end time
            return optionTimeMinutes >= startMinutes && optionTimeMinutes < (endMinutes + 60);
        });

        if (closedTimeRange) {
            $(this).prop("disabled", true);
            $(this).data("end-time", closedTimeRange.endTime);  // Store end time for reference
        } else {
            $(this).prop("disabled", false);
            $(this).data("end-time", "");  // Clear any previous end time data
        }
    });
}


    // Helper function to convert time (e.g., "13:00") to minutes
    function convertToMinutes(timeStr) {
        const [hours, minutes] = timeStr.split(":").map(Number);
        return hours * 60 + minutes;
    }

    // Handle start time changes and set end time
    $("#startTime").on("change", function() {
    const selectedOption = $(this).find(":selected");
    const startTime24 = selectedOption.data("value");
    const [hour, minute] = startTime24.split(":").map(Number);

    let endHour = hour + 1;
    if (endHour >= 17) {
        $("#endTime").val("");
    } else {
        const formattedEndTime = formatTo12Hour(endHour);
        $("#endTime").val(formattedEndTime);
    }
});

// Format 24-hour time to 12-hour format
function formatTo12Hour(hour) {
    const isPM = hour >= 12;
    const hour12 = hour % 12 || 12;
    return `${hour12}:00 ${isPM ? 'PM' : 'AM'}`;
}

document.getElementById("appointment-form").addEventListener("submit", function () {
    const startTimeOption = document.querySelector("#startTime :checked");
    if (startTimeOption) {
        startTimeOption.value = startTimeOption.dataset.value;
    }
});


        function handleFormReview(event) {
            event.preventDefault(); 
            const form = event.target;
            const formData = new FormData(form);
            let detailsHtml = "<h5>Appointment Review:</h5><div>";
            formData.forEach((value, key) => {
                const formattedKey = key.replace(/([A-Z])/g, ' $1');
                detailsHtml += `<p><strong>${formattedKey.charAt(0).toUpperCase() + formattedKey.slice(1)}</strong>: ${value}</p>`;
            });
            detailsHtml += "</div>";
            document.getElementById('confirmationDetails').innerHTML = detailsHtml;
            $('#confirmationModal').modal('show');
        }

        function confirmSubmit() {
            document.getElementById('appointment-form').submit();
        }
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

