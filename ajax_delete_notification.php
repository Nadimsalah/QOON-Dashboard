<?php
require "conn.php";

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id > 0) {
    $stmt = $con->prepare("DELETE FROM NotificationsSentByAdmin WHERE NotificationsSentByAdminID = ?");
    $stmt->bind_param("i", $id);
    $ok = $stmt->execute();
    echo json_encode(['ok' => $ok]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Invalid ID']);
}
