<?php
$ctx = stream_context_create(['http'=>['method'=>'HEAD']]);
print_r(get_headers('https://vz-92ee1d34-d8a.b-cdn.net/0a4fb720-973a-4a09-be5e-32cfbe975c06/play_360p.mp4', 1, $ctx));
?>
