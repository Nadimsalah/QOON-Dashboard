<?php
session_start();
$_SESSION["Emailjibler"] = "admin@admin.com";
$_GET['table_ajax'] = 1;
require 'shop.php';
