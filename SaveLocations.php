<?php

require "conn.php";
// استقبال البيانات من الجهة العميل
$cityBounds = json_decode($_POST['cityBounds'], true);


$latitude  = "";
$longitude = "";
$idw       = $_POST["idw"];

// يمكنك تحليل واستخدام $cityBounds كما تحتاج
// في هذا المثال، سيتم طباعة اللاتيتود واللونجيتود لكل نقطة في السجل (log)
foreach ($cityBounds as $point) {
    $latitude = $point[0];
    $longitude = $point[1];
	
  $sql="INSERT INTO CityBoders (DeliveryZoneID,CityLat,CityLongt) VALUES
  ('$idw','$latitude',$longitude)";   
   if(mysqli_query($con,$sql))
   {}
	
}

// يمكنك القيام بمعالجة إضافية هنا

// يمكنك إرسال استجابة JSON إلى الجهة العميل إذا كنت بحاجة إلى ذلك
$response = ['status' => 'success', 'message' => $latitude];
echo json_encode($response);
?>