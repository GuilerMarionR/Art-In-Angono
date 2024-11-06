<?php
session_start();

// Ensure there is a receipt file to download
if (isset($_SESSION['receiptFileName'])) {
    $filePath = $_SESSION['receiptFileName'];
    
    // Check if file exists
    if (file_exists($filePath)) {
        // Set headers to prompt for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Clear output buffer
        ob_clean();
        flush();
        
        // Read the file and send it to the output buffer
        readfile($filePath);
        exit();
    } else {
        echo "Error: File not found.";
    }
} else {
    echo "Error: No file to download.";
}
?>
