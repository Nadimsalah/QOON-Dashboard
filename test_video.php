<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');
$c->set_charset('utf8mb4');
$r = $c->query("SELECT PostId, Video, BunnyV FROM Posts WHERE BunnyV!='-' AND BunnyV!='' LIMIT 1");
$row = $r->fetch_assoc();
print_r($row);

$ctx = stream_context_create(['http'=>['method'=>'HEAD']]);
if ($row['Video']) {
    print_r(get_headers($row['Video'], 1, $ctx));
}
?>
