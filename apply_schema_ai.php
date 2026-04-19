<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');

$c->query("ALTER TABLE Posts ADD COLUMN AiChecked TINYINT(1) DEFAULT 0;");
if($c->error) echo "Posts err: " . $c->error . "\n";
else echo "Posts updated\n";

$c->query("ALTER TABLE ShopStory ADD COLUMN AiChecked TINYINT(1) DEFAULT 0;");
if($c->error) echo "ShopStory err: " . $c->error . "\n";
else echo "ShopStory updated\n";
?>
