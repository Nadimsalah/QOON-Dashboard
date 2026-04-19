<?php
session_start();
$_SESSION["Emailjibler"] = 'admin@admin.com';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://localhost:8000/shop.php?table_ajax=1");
curl_setopt($ch, CURLOPT_COOKIE, "PHPSESSID=".session_id());
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$server_output = curl_exec($ch);
curl_close ($ch);
echo "OUTPUT: ";
echo substr($server_output, 0, 500);
