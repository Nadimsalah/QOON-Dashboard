<?php
/**
 * tick_ai_worker.php
 * A lightweight, non-blocking trigger designed to be called silently 
 * from the main QOON app APIs (feed, reels) to moderate unchecked posts continuously.
 */
set_time_limit(60);
require 'conn.php';
// We only check a maximum of 3 items per tick to prevent server lag
$limit = 3;
$checkedCount = 0;

// 1. Posts
$res = $con->query("SELECT PostId FROM Posts WHERE AiChecked = 0 ORDER BY PostId ASC LIMIT $limit");
while ($row = $res->fetch_assoc()) {
    $id = $row['PostId'];
    $output = shell_exec("c:\\xampp\\php\\php.exe " . __DIR__ . "\\moderate_post.php $id post");
    $checkedCount++;
}

// 2. Stories
$resS = $con->query("SELECT StotyID FROM ShopStory WHERE AiChecked = 0 ORDER BY StotyID ASC LIMIT $limit");
while ($row = $resS->fetch_assoc()) {
    $id = $row['StotyID'];
    $output = shell_exec("c:\\xampp\\php\\php.exe " . __DIR__ . "\\moderate_post.php $id story");
    $checkedCount++;
}

header('Content-Type: application/json');
echo json_encode(['success' => true, 'checkedCount' => $checkedCount]);
?>
