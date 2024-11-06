<?php
session_start(); // Start the session

// Assuming you have a database connection
$closedDates = [];

// Replace with your actual database connection and query to fetch closed dates
$db = new mysqli('localhost', 'root', '', 'art_in_angono_db'); // Update with your credentials
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Get museum name from URL parameter
$museumName = isset($_GET['museum']) ? htmlspecialchars($_GET['museum']) : 'Unknown Museum';

// Assuming $museumName is already set from the GET parameter
$query = "SELECT closed_date FROM closed_dates WHERE museumName = '$museumName'"; // Filter by museumName
$result = $db->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $closedDates[] = $row['closed_date']; // Store closed dates for the selected museum
    }
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['formData'] = $_POST;
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Retrieve form data from session, if available
$formData = $_SESSION['formData'] ?? [];
unset($_SESSION['formData']); // Clear the session data after retrieving
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ART IN ANGONO</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    
    <style>
        .form-wrapper {
            min-height: 100vh;
            background-color: #f5f5f5;
            padding-top: 50px;
        }
        .container {
            max-width: 600px;
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }
        .input-box input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            font-size: 1rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        .button-container button {
            width: 100%;
            padding: 10px;
            font-size: 1.1rem;
            color: #fff;
            background-color: #dc3545;
            border: none;
            border-radius: 4px;
        }
        .button-container button:hover {
            background-color: #b02a37;
        }
        .disabled-date .ui-state-default {
            background: #d3d3d3 !important;  /* Greys out disabled dates */
            pointer-events: none;
        }
    </style>
</head>
<body onload="initializeForm();">

    <?php include '../includes/navigation-guest.php'; ?>

    <div class="form-wrapper">
        <div class="container">
            <div class="title text-center">CLIENT AND APPOINTMENT INFORMATION</div>
            
            <div class="selected-museum text-center">
                <p id="display-museum-name" style="font-size: 25px;"><?php echo $museumName; ?></p>
            </div>

            <form id="appointment-form" method="POST" action="../guests/guest-review.php" onsubmit="handleFormReview(event);">
                <input type="hidden" id="museumName" name="museumName" value="<?php echo $museumName; ?>" required>
                
                <!-- Client Information Form -->
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
                            $value = $formData[$name] ?? '';
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
                            <?php
                                $timeSlots = [
                                    "8AM-9AM", "9AM-10AM", "10AM-11AM",
                                    "11AM-12PM", "12PM-1PM", "1PM-2PM",
                                    "2PM-3PM", "3PM-4PM"
                                ];
                                foreach ($timeSlots as $slot) {
                                    $checked = (isset($formData['appointmentTime']) && $formData['appointmentTime'] === $slot) ? 'checked' : '';
                                    echo "<label><input type=\"radio\" name=\"appointmentTime\" value=\"$slot\" $checked required> $slot</label>";
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="button-container">
                    <button type="submit" class="btn btn-primary">Review Appointment</button>
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
                <div class="modal-body" id="confirmationDetails">
                    <!-- Appointment details will be populated here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmSubmit()">Confirm and Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script>
        const closedDates = <?php echo json_encode($closedDates); ?>;

        function initializeForm() {
    const museumName = document.getElementById('museumName').value;
    document.getElementById('display-museum-name').textContent = museumName;

    const formData = <?php echo json_encode($formData); ?>;
    Object.keys(formData).forEach(key => {
        const input = document.querySelector(`[name="${key}"]`);
        if (input) input.value = formData[key];
    });

    $("#date").datepicker({
        dateFormat: "dd-mm-yy",
        beforeShowDay: function(date) {
            const today = new Date();
            const formattedToday = $.datepicker.formatDate('yy-mm-dd', today);
            const dateString = $.datepicker.formatDate('yy-mm-dd', date);

            // Disable previous dates and closed dates specific to the selected museum
            if (date < today || closedDates.includes(dateString)) {
                return [false, "disabled-date", "Unavailable date"];
            }
            return [true, ""];
        },
        onSelect: function(dateText) {
            const formattedDate = $.datepicker.formatDate('yy-mm-dd', $(this).datepicker('getDate'));
            if (closedDates.includes(formattedDate)) {
                alert("The selected date is unavailable. Please choose another date.");
                $(this).val(''); // Clear the selected date
            }
        }
    });
}

        function handleFormReview(event) {
            event.preventDefault();
            const form = document.forms['appointment-form'];
            const data = new FormData(form);

            let details = `<strong>Museum Name:</strong> ${document.getElementById('museumName').value}<br>`;
            for (let [key, value] of data.entries()) {
                details += `<strong>${key.replace(/([A-Z])/g, ' $1').toUpperCase()}:</strong> ${value}<br>`;
            }

            $('#confirmationDetails').html(details);
            $('#confirmationModal').modal('show');
        }

        function confirmSubmit() {
            document.getElementById('appointment-form').submit();
        }
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
