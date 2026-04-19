<?php
$con = new mysqli("145.223.33.118", "qoon_Qoon", ";)xo6b(RE}K%", "qoon_Qoon");
$tables = ['Orders', 'Drivers', 'Shops', 'Categories', 'Foods'];
$output = [];
foreach ($tables as $t) {
    $res = $con->query("DESCRIBE $t");
    $cols = [];
    while($row = $res->fetch_assoc()) {
        $cols[] = $row['Field'] . ' (' . $row['Type'] . ')';
    }
    $output[$t] = $cols;
}
echo json_encode($output, JSON_PRETTY_PRINT);
?>
