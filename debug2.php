<?php
$lines = file('content.php');
$output = '';
foreach ($lines as $i => $line) {
    if (strpos($line, 'view-reels') !== false || strpos($line, 'reel-card') !== false || strpos($line, 'view-posts') !== false) {
        $output .= ($i + 1) . ': ' . trim($line) . "\n";
    }
}
file_put_contents('debug.txt', $output);
?>
