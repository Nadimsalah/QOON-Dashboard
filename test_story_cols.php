<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');
$res = $c->query("SHOW COLUMNS FROM ShopStory");
while($row = $res->fetch_assoc()) echo $row['Field'] . " - " . $row['Type'] . "\n";
?>
