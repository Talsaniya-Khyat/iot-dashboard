<?php
header('Content-Type: application/json; charset=utf-8');
include "util/dbconn.php";

$sql = "SELECT val, time, power FROM tricksumo_nodemcu";
$result = $conn->query($sql);

$res = array();
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $res[] = array("val" => (int)$row["val"], "time" => (string)$row["time"]);
    }
} else {
    echo "0 results";
}
$conn->close();

echo json_encode($res);

?>
