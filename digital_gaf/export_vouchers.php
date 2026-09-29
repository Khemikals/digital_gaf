<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    exit("Unauthorized");
}

// Headers for download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=GAF_Active_Vouchers_' . date('Y-m-d') . '.csv');

$output = fopen('php://output', 'w');
fputcsv($output, array('Serial Number', 'PIN Code', 'Status', 'Date Generated'));

// Fetch only ACTIVE vouchers
$sql = "SELECT serial_number, pin_code, status, created_at FROM vouchers WHERE status='Active'";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}
fclose($output);
?>