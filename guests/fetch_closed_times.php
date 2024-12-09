<?php
// fetch_closed_times.php
if (isset($_GET['date']) && isset($_GET['museum'])) {
    $db = new mysqli('localhost', 'root', '', 'art_in_angono_db');
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    $appointmentDate = $db->real_escape_string($_GET['date']);
    $museumName = $db->real_escape_string($_GET['museum']);

    $closedTimes = [];
    $query = "
        SELECT startTime, endTime 
        FROM closed_times 
        WHERE museumName = '$museumName' 
        AND date = '$appointmentDate'";

    $result = $db->query($query);

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $closedTimes[] = [
                'startTime' => $row['startTime'],
                'endTime' => $row['endTime']
            ];
        }
    }

    echo json_encode($closedTimes);
}
?>
