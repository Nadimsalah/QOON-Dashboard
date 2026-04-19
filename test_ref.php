<?php
$ctx1 = stream_context_create(['http'=>['header'=>"Referer: https://qoon.app/\r\n"]]);
$ctx2 = stream_context_create(['http'=>['header'=>"Referer: http://localhost:8000/\r\n"]]);

print_r(get_headers('https://vz-92ee1d34-d8a.b-cdn.net/0a4fb720-973a-4a09-be5e-32cfbe975c06/play_360p.mp4', 0, $ctx1));
print_r(get_headers('https://vz-92ee1d34-d8a.b-cdn.net/0a4fb720-973a-4a09-be5e-32cfbe975c06/play_360p.mp4', 0, $ctx2));
?>
