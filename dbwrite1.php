<?php
include 'dbconn.php';
// Get date and time variables
date_default_timezone_set('Asia/Kolkata');  // for other timezones, refer:- https://www.php.net/manual/en/timezones.asia.php
$d = date("Y-m-d");
$t = date("H:i:s");

// If values send by NodeMCU are not empty then insert into MySQL database table
if (!empty($_POST['sendval']) || !empty($_POST['sendval2']) || !empty($_POST['sendIrms']) || !empty($_POST['sendPower']) || !empty($_POST['sendEnergy'])) {
    $val = validateValue($_POST['sendval']);
    $val2 = validateValue($_POST['sendval2']);
    $Irms = validateValue($_POST['sendIrms']);
    $power = validateValue($_POST['sendPower']);
    $energy = validateValue($_POST['sendEnergy']);

    // Update your tablename here
    $sql = "INSERT INTO tricksumo_nodemcu (val, val2, Irms, power, energy, Date, Time) VALUES ('$val', '$val2','$Irms' ,'$power','$energy', '$d', '$t')";

    if ($conn->query($sql) === TRUE) {
        echo "Values inserted in MySQL database table.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close MySQL connection
$conn->close();

// Function to validate value
function validateValue($value)
{
    // Check if the value is 'nan', if so, replace it with 0
    return ($value == 'nan') ? 0 : $value;
}

