<?php
header('Content-Type: application/json');
$con = new mysqli("145.223.33.118", "qoon_Qoon", ";)xo6b(RE}K%", "qoon_Qoon");
$res = $con->query("DESCRIBE Users");
$schema = [];
while($row = $res->fetch_assoc()) {
    $schema[] = $row;
}
echo json_encode($schema, JSON_PRETTY_PRINT);
?>
