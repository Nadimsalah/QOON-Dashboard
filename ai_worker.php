<?php
/**
 * ai_worker.php
 * QOON Seamless AI Background Worker
 * This script runs infinitely in the background. It continuously monitors the database 
 * for any newly uploaded posts or stories (from ANY source like the mobile app or dashboard).
 * The literal second an untouched post appears in the database, this worker instantly flags it 
 * and routes it natively through the `moderate_post.php` engine.
 */

set_time_limit(0);
require 'conn.php';

echo "\n────────────────────────────────────────────────\n";
echo " 🤖 QOON Seamless AI Worker Initialized         \n";
echo "────────────────────────────────────────────────\n";
echo "[INFO] Monitoring all network endpoints natively in real-time.\n";
echo "[INFO] Waiting for stores to upload new images/videos...\n\n";

$con->autocommit(TRUE);

while (true) {
    // 1. Check Standard Posts
    $res = $con->query("SELECT PostId FROM Posts WHERE AiChecked = 0 ORDER BY PostId ASC LIMIT 1");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $id = $row['PostId'];
        echo "[" . date('H:i:s') . "] 📸 New Post Uploaded (ID: $id) -> Running AI Checks...\n";
        
        $output = shell_exec("c:\\xampp\\php\\php.exe moderate_post.php $id post");
        
        // Try parsing JSON to print a clean verdict for the admin looking at the terminal
        $decoded = @json_decode(trim($output), true);
        if (isset($decoded['decision'])) {
            $verdict = $decoded['decision'];
            if ($verdict === 'APPROVED') echo "   └─ ✅ RESULT: CLEAN (APPROVED & SET PUBLIC natively!)\n";
            elseif ($verdict === 'PENDING') echo "   └─ ⚠️ RESULT: FLAGGED FOR REVIEW (Moved to Pending Queue!)\n";
            else echo "   └─ 🚫 RESULT: REJECTED (Hidden from platform!)\n";
        } else {
            echo "   └─ Error/Raw Output: " . trim($output) . "\n";
        }
        
        sleep(1);
        continue; 
    }
    
    // 2. Check Shop Stories
    $resS = $con->query("SELECT StotyID FROM ShopStory WHERE AiChecked = 0 ORDER BY StotyID ASC LIMIT 1");
    if ($resS && $resS->num_rows > 0) {
        $row = $resS->fetch_assoc();
        $id = $row['StotyID'];
        echo "[" . date('H:i:s') . "] 📖 New Reel/Story Uploaded (ID: $id) -> Running AI Checks...\n";
        
        $output = shell_exec("c:\\xampp\\php\\php.exe moderate_post.php $id story");
        
        $decoded = @json_decode(trim($output), true);
        if (isset($decoded['decision'])) {
            $verdict = $decoded['decision'];
            if ($verdict === 'APPROVED') echo "   └─ ✅ RESULT: CLEAN (APPROVED!)\n";
            elseif ($verdict === 'PENDING') echo "   └─ ⚠️ RESULT: FLAGGED FOR REVIEW!\n";
            else echo "   └─ 🚫 RESULT: REJECTED!\n";
        } else {
            echo "   └─ Error/Raw Output: " . trim($output) . "\n";
        }
        
        sleep(1);
        continue;
    }
    
    // Nothing found, idle wait for 3 seconds before next poll
    sleep(3);
}
?>
