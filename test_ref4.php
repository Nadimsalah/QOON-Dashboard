<?php
$refs = [
    'https://qoon.app',
    'https://qoon.app/',
    'https://www.qoon.app',
    'com.qoon.app',        // Mobile app bundle ID?
    'https://dashboard.jibler.ma/',
    'https://jibler.app/'
];

foreach ($refs as $ref) {
    $ctx = stream_context_create(['http'=>[
        'method'=>'HEAD',
        'header'=>"Referer: $ref\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
    ]]);
    $res = get_headers('https://vz-92ee1d34-d8a.b-cdn.net/0a4fb720-973a-4a09-be5e-32cfbe975c06/play_360p.mp4', 1, $ctx);
    echo "Ref $ref: " . $res[0] . "\n";
}
?>
