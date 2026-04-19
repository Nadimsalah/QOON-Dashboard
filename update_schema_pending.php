<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');

$q1 = "ALTER TABLE Posts MODIFY COLUMN PostStatus VARCHAR(255) DEFAULT 'PENDING'";
if($c->query($q1)) echo "Posts default changed to PENDING.\n";
else echo "Error Post: " . $c->error . "\n";

$q2 = "ALTER TABLE ShopStory MODIFY COLUMN StoryStatus VARCHAR(255) DEFAULT 'PENDING'";
if($c->query($q2)) echo "ShopStory default changed to PENDING.\n";
else echo "Error Story: " . $c->error . "\n";
?>
