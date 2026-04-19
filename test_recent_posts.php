<?php
$c = new mysqli('145.223.33.118', 'qoon_Qoon', ';)xo6b(RE}K%', 'qoon_Qoon');
$res = $c->query("SELECT PostId, PostStatus, AiChecked FROM Posts ORDER BY PostId DESC LIMIT 5");
while($row = $res->fetch_assoc()) echo json_encode($row)."\n";
?>
