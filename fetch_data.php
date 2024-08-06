<?php
// fetch_data.php

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "id21566548_iotproject";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT time, val FROM tricksumo_nodemcu ORDER BY time DESC LIMIT 10";
$result = $conn->query($sql);

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
?>
