<?php
$data = ['message' => 'hello'];
$opts = ['http' => ['method' => 'POST', 'header' => "Content-Type: application/json\r\n", 'content' => json_encode($data)]];
$ctx = stream_context_create($opts);
$res = file_get_contents('http://localhost:8000/ai-user-agent-api.php', false, $ctx);
if ($res === false) {
    echo "FGC Failed\n";
    print_r($http_response_header);
} else {
    echo "RES: $res\n";
}
