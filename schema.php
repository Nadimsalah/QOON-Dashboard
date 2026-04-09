<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');
foreach(['Users','Drivers','Shops','Orders'] as $t) {
    echo "\n$t: ";
    $res = $c->query("SHOW COLUMNS FROM $t");
    while($r = $res->fetch_assoc()) echo $r['Field'].', ';
}
?>
