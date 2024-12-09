<?php
session_start();
include '../includes/db_connections.php';

$username = $_SESSION['username'];

$sql = "SELECT * FROM closed_dates WHERE museumName = '$username'";
$result = mysqli_query($conn, $sql);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($data);
?>
