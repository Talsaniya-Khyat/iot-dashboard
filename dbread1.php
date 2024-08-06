<?php
include 'dbconn.php';
// Select values from MySQL database table

$sql = "SELECT id, val, val2, Irms, power, energy, date, time FROM tricksumo_nodemcu";  // Update your tablename here

$result = $conn->query($sql);

echo "<center>";

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<strong> Temperature:</strong> " . $lastRow["val"] . " &nbsp <strong>Smoke:</strong> " . $lastRow["val2"] . " &nbsp <strong>Current:</strong> " . $lastRow["Irms"] . " &nbsp <strong>Power:</strong> " . $lastRow["power"] . " &nbsp <strong>Energy:</strong> " . $lastRow["energy"] . " &nbsp <strong>Date:</strong> " . $lastRow["date"] . " &nbsp <strong>Time:</strong>" . $lastRow["time"];
    }
} else {
    echo "0 results";
}

echo "</center>";

$conn->close();