<?php
require_once('../db-config.php');
// Retrieve the table data
$sql = 'SELECT * FROM tbl_logs ORDER BY created_at DESC';
$result = $conn->query($sql);
// Create a CSV file and open it for writing
$fp = fopen('file.csv', 'w');

// Write the table headers to the CSV file
$header = array('No', 'Email', 'Name', 'Passport', 'Log', 'Created at');
fputcsv($fp, $header);

// Loop through the table data and write each row to the CSV file
$index = 1;
while ($row = $result->fetch_assoc()) {
    // Convert datetime format with milliseconds
    $timestamp = round((int)$row['created_at'] / 1000);
    $dateTimeString = date('Y-m-d H:i:s', $timestamp) . '.' . sprintf('%03d', (int)$row['created_at'] % 1000);
    
    $data = array(
        $index ++,
        $row['email'],
        $row['name'],
        $row['passport'],
        $row['log'],
        $dateTimeString
    );
    fputcsv($fp, $data);
}

// Close the CSV file
fclose($fp);

// Set the filename
$filename = 'bot_activity_log.csv';

// Set the Content-Disposition header
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Output the contents of the CSV file
readfile('file.csv');

// Delete the CSV file
unlink('file.csv');
?>