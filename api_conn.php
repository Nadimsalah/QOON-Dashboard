<?php
/**
 * api_conn.php
 * Lightweight DB connection for public-facing APIs.
 * Does NOT start sessions or check admin login — safe for mobile API calls.
 */

$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";

try {
    $con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($con->connect_error) {
        http_response_code(503);
        echo json_encode(['success' => false, 'error' => 'DB connection failed']);
        exit;
    }
    $con->set_charset("utf8mb4");
    $con->query("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
} catch (Exception $e) {
    http_response_code(503);
    echo json_encode(['success' => false, 'error' => 'DB exception']);
    exit;
}

$DomainNamee = "https://qoon.app/dash/";
