<?php
require "conn.php";
header('Content-Type: application/json');

$ShopID = isset($_POST["ShopID"]) ? (int)$_POST["ShopID"] : 0;
$Type = isset($_POST["Type"]) ? $_POST["Type"] : "";

if($ShopID === 0 || !isset($_FILES["Photo"])) {
    echo json_encode(["status" => "error", "message" => "Missing data parameters"]);
    exit;
}

$photo1name = "w-" . rand();
$path = "";
$actualpath = "";

if($Type == 'Photos') {
    $path = "photo/$photo1name.png";
} else {
    $array = explode('.', $_FILES['Photo']['name']);
    $extension = end($array);
    $path = "photo/$photo1name.$extension";
}
// Using generic protocol relative path for compatibility with frontend replacements
$actualpath = "https://qoon.app/$path";

$sql = "INSERT INTO ShopStory (StoryPhoto, ShopID, StotyType) VALUES ('$actualpath', '$ShopID', '$Type')";

if(mysqli_query($con, $sql)) {
    // Increment shop story count
    $sql2 = "UPDATE Shops SET HasStory='YES', StoryCount=StoryCount+1 WHERE ShopID=$ShopID";
    mysqli_query($con, $sql2);

    if (move_uploaded_file($_FILES["Photo"]["tmp_name"], __DIR__ . '/' . $path)) {
        echo json_encode(["status" => "success", "message" => "Story published successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database updated, but file failed to upload locally."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Database insertion failed."]);
}
mysqli_close($con);
?>
