<?php
// fetch_closed_times.php
if (isset($_GET['date']) && isset($_GET['museum'])) {
   include '../includes/db_connections.php';
    $appointmentDate = $conn->real_escape_string($_GET['date']);
    $museumName = $conn->real_escape_string($_GET['museum']);

    $closedTimes = [];
    $query = "
        SELECT startTime, endTime 
        FROM closed_times 
        WHERE museumName = '$museumName' 
        AND date = '$appointmentDate'";

    $result = $conn->query($query);

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
