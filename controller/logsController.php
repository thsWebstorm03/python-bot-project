<?php
// Connect to MySQL
require_once('../db-config.php');
// Get all data from tbl_bots table
$sql = 'SELECT * FROM tbl_logs ORDER BY created_at DESC limit 20';
$result = $conn->query($sql);
// Create DOM using query result
$dom = '';
if ($result->num_rows > 0) {
    $index = 1;
    while ($row = $result->fetch_assoc()) {
        // $dateTime = new DateTime();
        // $dateTime->setTimestamp((int)$row['created_at'] / 1000);

        // format the date as "YYYY-mm-dd HH:mm:ss.u"
        // $dateTimeString = $dateTime->format('Y-m-d H:i:s.u');
        // $dateTimeString = substr($dateTimeString, 0, -3); // remove last 3 digits to get milliseconds only

        $totalMilliseconds = (int)$row['created_at'];
        $timestamp = round($totalMilliseconds / 1000);
        $dateTimeString = date('Y-m-d H:i:s', $timestamp) . '.' . sprintf('%03d', $totalMilliseconds % 1000);

        $dom .= '<tr class="border border-solid border-gray-300">' .
            '<td class="py-[12px] px-[10px]">' . $row['email'] . '</td>' .
            '<td class="py-[12px] px-[10px]">' . $row['name'] . '</td>' .
            '<td class="py-[12px] px-[10px]">' . $row['passport'] . '</td>' .
            '<td class="py-[12px] px-[10px] relative">' . $row['log'] . '</td>' .
            '<td class="py-[12px] px-[10px] text-right">' . $dateTimeString . '</td>' .
        '</tr>';
        $index ++;
    }
} else {
    $dom = '<tr class="border border-solid border-gray-300 text-center"><td colspan="5" class="py-[12px] px-[10px]">No data to display.</td></tr>';
}
echo $dom;
?>