<?php
// Database connection
include 'util/dbconn.php';

// Fetch temperature data from the database
$sql = "SELECT time, val FROM tricksumo_nodemcu WHERE serialNumber = ? ORDER BY id DESC LIMIT 10";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $serialNumber);
$stmt->execute();
$result = $stmt->get_result();

// Convert the result into an associative array
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

// Output the data as JSON
echo json_encode($data);