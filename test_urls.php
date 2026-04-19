<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');
$c->set_charset('utf8mb4');

function resolve($post) {
    $bunny = trim($post['BunnyV'] ?? '');
    if ($bunny && !in_array(strtolower($bunny), ['','none','0','-','null'])) {
        return $bunny;
    }
    return trim($post['Video'] ?? '');
}

$r = $c->query("SELECT PostId, Video, BunnyV FROM Posts WHERE BunnyV!='-' AND BunnyV!='' LIMIT 5");
while($row=$r->fetch_assoc()) echo "POST: " . resolve($row) . "\n";

$r = $c->query("SELECT StotyID, BunnyV FROM ShopStory WHERE BunnyV!='-' AND BunnyV!='' AND StotyType='Video' LIMIT 5");
while($row=$r->fetch_assoc()) echo "STORY: " . $row['BunnyV'] . "\n";
?>
