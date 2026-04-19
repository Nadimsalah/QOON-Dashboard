<?php
$lines = file('content.php');
$output = '';
foreach ($lines as $i => $line) {
    if (strpos($line, 'reel-card') !== false) {
        $output .= ($i + 1) . ': ' . trim($line) . "\n";
    }
}
file_put_contents('debug3.txt', $output);
?>
