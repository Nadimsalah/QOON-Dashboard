<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

$ch = curl_init('https://api.deepseek.com/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer sk-d25ba3eadc464644a051ea2fe7d83f7a'
    ],
    CURLOPT_POSTFIELDS     => json_encode([
        'model' => 'deepseek-chat',
        'messages' => [['role' => 'user', 'content' => 'hello']],
        'max_tokens' => 10
    ]),
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_SSL_VERIFYHOST => 0,
    CURLOPT_SSL_VERIFYPEER => 0
]);

$start = time();
$response = curl_exec($ch);
$err = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Time taken: " . (time() - $start) . " seconds\n";
echo "HTTP Code: $httpCode\n";
echo "cURL Error: $err\n";
echo "Response: $response\n";
