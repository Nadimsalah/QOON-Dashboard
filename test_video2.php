<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');
$c->set_charset('utf8mb4');
$r = $c->query("SELECT PostId, Video, BunnyV FROM Posts WHERE BunnyV IS NOT NULL AND BunnyV NOT IN ('none','NONE','0','-','null','') LIMIT 1");
$row = $r->fetch_assoc();
print_r($row);
?>
