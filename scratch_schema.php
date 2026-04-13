<?php
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

echo "--- Orders Schema ---\n";
$res = $con->query("DESCRIBE Orders");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\n--- Users Schema ---\n";
$res = $con->query("DESCRIBE Users");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
