<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');
$c->set_charset('utf8mb4');
$r = $c->query("SELECT PostId, Video, BunnyV, PostPhoto, PostPhoto2, PostPhoto3 FROM Posts WHERE PostStatus='ACTIVE' ORDER BY PostId DESC LIMIT 10");
while($row=$r->fetch_assoc()) {
    print_r($row);
}
?>
