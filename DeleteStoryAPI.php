<?php
require "conn.php";
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$ShopID = isset($data["ShopID"]) ? (int)$data["ShopID"] : 0;
$StoryPhoto = isset($data["StoryPhoto"]) ? mysqli_real_escape_string($con, $data["StoryPhoto"]) : "";

if($ShopID > 0 && !empty($StoryPhoto)){
    // Use LIKE to bypass domain prefix edge-cases in the URL
    $sql = "DELETE FROM ShopStory WHERE ShopID='$ShopID' AND StoryPhoto LIKE '%$StoryPhoto%'";
    
    if(mysqli_query($con, $sql)){
        echo json_encode(["status" => "success", "message" => "Story deleted successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to purge database entry."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Authorization Data"]);
}
?>
