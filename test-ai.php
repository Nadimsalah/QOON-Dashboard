<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Force executing the api logic and capturing its output:
ob_start();

$_SERVER['REQUEST_METHOD'] = 'POST';
$json = json_encode(["message" => "test connection"]);

// Mock php input by putting it into a stream
// We can't mock php://input easily, so we just require the file but mock the input by replacing file_get_contents inside the file, but we can't do that.
// Let's just create a raw curl locally
$ch = curl_init('http://localhost:8000/ai-chat-api.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$resp = curl_exec($ch);
curl_close($ch);

file_put_contents('debug.txt', $resp);
echo "Done";
