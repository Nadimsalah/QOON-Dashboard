<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');
$c->set_charset('utf8mb4');
$r = $c->query("SELECT StoryPhoto, BunnyV FROM ShopStory WHERE StotyType='Video' AND StoryPhoto!='' AND StoryPhoto!='none' LIMIT 5");
while($row=$r->fetch_assoc()) {
    echo "Photo: " . $row['StoryPhoto'] . "\n";
    $ctx = stream_context_create(['http'=>['method'=>'HEAD']]);
    print_r(get_headers($row['StoryPhoto'], 1, $ctx));
}
?>
