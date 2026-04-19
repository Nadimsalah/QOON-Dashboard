<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');
$r = $c->query("SHOW COLUMNS FROM Posts");
while($row = $r->fetch_assoc()) echo $row['Field'] . " - " . $row['Default'] . "\n";
?>
