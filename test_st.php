<?php
require "conn.php";
$res=$con->query("SELECT SS.StotyID as PostId, SS.StoryStatus as PostStatus, SS.AiChecked, SS.ShopID FROM ShopStory SS WHERE SS.StotyType = 'Photos' ORDER BY SS.StotyID DESC LIMIT 3");
while($r=$res->fetch_assoc()) {
    print_r($r);
} 
?>
