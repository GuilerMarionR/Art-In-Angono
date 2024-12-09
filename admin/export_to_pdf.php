<?php
// Include Composer's autoloader to load TCPDF
require_once(__DIR__ . '/../vendor/autoload.php'); // Adjust path based on your project structure

// Fetch your database records (Modify according to your actual query and database connection)
include('../includes/db_connections.php'); // Assuming you have a file to connect to the database

// Assume that the admin username is stored in the session
session_start();
$admin_username = $_SESSION['username']; // Get admin's username from session

// Query to check if museumName matches the admin username
$sql_check_museum = "SELECT museumName FROM clientbookings WHERE museumName = '$admin_username'";
$result_check_museum = $conn->query($sql_check_museum);

// If no matching museum name is found, stop script execution
if ($result_check_museum->num_rows == 0) {
    die("You do not have access to this data.");
}

// Get today's date
$today = date('Y-m-d');

// Get yesterday's date
$yesterday = date('Y-m-d', strtotime('-1 day'));

// Query to get guest data for different categories (adjust for your actual database schema)
$sql_guest_list = "SELECT * FROM clientbookings WHERE appointmentDate = '$today' AND museumName = '$admin_username'"; // Guest list: today
$sql_pending_list = "SELECT * FROM clientbookings WHERE appointmentDate > '$today' AND museumName = '$admin_username'"; // Pending list: tomorrow onward
$sql_history = "SELECT * FROM clientbookings WHERE appointmentDate <= '$yesterday' AND museumName = '$admin_username'"; // History: past appointments

// Execute queries for each category
$result_guest_list = $conn->query($sql_guest_list);
$result_pending_list = $conn->query($sql_pending_list);
$result_history = $conn->query($sql_history);

// Create new PDF document using TCPDF
$pdf = new TCPDF('L', 'mm', array(420, 300)); // 'L' for landscape orientation, 'A4' size

// Set document information (optional)
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Art In Angono');
$pdf->SetTitle('Guest List');
$pdf->SetSubject('List of Guests');

// Set default font size (adjust font size to fit)
$pdf->SetFont('helvetica', '', 8); // Reduced font size to fit more data

// Add a page
$pdf->AddPage();

// Set title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Guest List', 0, 1, 'C');

// Add a line break
$pdf->Ln(10);

// Function to output table data
function output_table($pdf, $result, $section_title) {
    $pdf->SetFont('helvetica', 'B', 8); // Table header font size
    
    // Table Header
    $pdf->Cell(30, 10, 'First Name', 1);
    $pdf->Cell(30, 10, 'Middle Name', 1);
    $pdf->Cell(30, 10, 'Last Name', 1);
    $pdf->Cell(100, 10, 'Address', 1);
    $pdf->Cell(50, 10, 'Email', 1);
    $pdf->Cell(20, 10, 'Age', 1);
    $pdf->Cell(40, 10, 'Contact Number', 1);
    $pdf->Cell(30, 10, 'Number of Guests', 1);
    $pdf->Cell(30, 10, 'Date', 1);
    $pdf->Cell(30, 10, 'Time', 1);
    $pdf->Cell(30, 10, 'Status', 1);
    $pdf->Ln();

    // Set font for the table content
    $pdf->SetFont('helvetica', '', 8); // Adjusted for smaller content font

    // Fetch and display data from the database
    while ($row = $result->fetch_assoc()) {
        // Format time (if applicable)
        $startTimeFormatted = date("H:i", strtotime($row['startTime']));
        $endTimeFormatted = date("H:i", strtotime($row['endTime']));
        $fullTime = $startTimeFormatted . ' - ' . $endTimeFormatted;

        // Output row data
        $pdf->Cell(30, 10, htmlspecialchars($row['firstName'] ?? 'N/A'), 1);
        $pdf->Cell(30, 10, htmlspecialchars($row['middleName'] ?? 'N/A'), 1);
        $pdf->Cell(30, 10, htmlspecialchars($row['lastName'] ?? 'N/A'), 1);
        $pdf->Cell(100, 10, htmlspecialchars($row['address'] ?? 'N/A'), 1);
        $pdf->Cell(50, 10, htmlspecialchars($row['email'] ?? 'N/A'), 1);
        $pdf->Cell(20, 10, htmlspecialchars($row['age'] ?? 'N/A'), 1);
        $pdf->Cell(40, 10, htmlspecialchars($row['contactNumber'] ?? 'N/A'), 1);
        $pdf->Cell(30, 10, htmlspecialchars($row['numberOfGuests'] ?? 'N/A'), 1);
        $pdf->Cell(30, 10, htmlspecialchars($row['appointmentDate'] ?? 'N/A'), 1);
        $pdf->Cell(30, 10, $fullTime, 1);
        $pdf->Cell(30, 10, htmlspecialchars($row['Status'] ?? 'N/A'), 1);
        $pdf->Ln();
    }
}

// Output Guest List
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Guest List - Today\'s Appointments', 0, 1, 'L');
$pdf->Ln(5); // Space between title and table
output_table($pdf, $result_guest_list, 'Guest List');

// Add a page break between sections
$pdf->AddPage();

// Output Pending List
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Pending List - Tomorrow and Beyond', 0, 1, 'L');
$pdf->Ln(5); // Space between title and table
output_table($pdf, $result_pending_list, 'Pending List');

// Add a page break between sections
$pdf->AddPage();

// Output History
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'History - Past Appointments', 0, 1, 'L');
$pdf->Ln(5); // Space between title and table
output_table($pdf, $result_history, 'History');

// Close and output the PDF
$pdf->Output('guest_list.pdf', 'I'); // 'I' for inline display in the browser
?>
