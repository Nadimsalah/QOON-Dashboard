<?php
require "conn.php";







$CategoryID = $_POST["CategoryID"];
$Key = "";
$lat = $_POST["lat"];
$longt = $_POST["longt"];
$actualpath = "";

		$ShopLat =  $lat;
		$ShopLongt = $longt;

$res = mysqli_query($con,"SELECT * FROM Categories WHERE CategoryID = $CategoryID");
                                
                                                $result = array();
                                
                                                while($row = mysqli_fetch_assoc($res)){
													
													$EnglishCategory = $row["EnglishCategory"];
												}


$res = mysqli_query($con,"SELECT * FROM GoogleKey");
                                
                                                $result = array();
                                
                                                while($row = mysqli_fetch_assoc($res)){
													
													$Key = $row["GKey"];
													
												}

$loc = $lat . ',' . $longt;
$x = getShops($EnglishCategory,$Key,$loc);

$arr = json_decode($x, true);


$arrLength = count($arr["results"]);
$ss = $arr["results"];
//echo $arrLength;

foreach($arr["results"] as $result) {
    $ShopName = $result['name'];
//	echo 'Name :' .$ShopName;
	
	foreach($result['geometry'] as $geometry) {
		
		
		$locations = $geometry['viewport'];
		$ShopLat =  $geometry['lat'];
		$ShopLongt = $geometry['lng'];

		 
		echo 'location : '. $ShopLat . ' , ' .  $ShopLongt;
		break;
		
	}
	

	
	//echo ' Rate : '. $result['rating']; 
	$actualpath = '';
	foreach($result['photos'] as $photo) {
		$photow = 'https://maps.googleapis.com/maps/api/place/photo?photoreference='.$photo["photo_reference"].'&sensor=false&maxheight=400&maxwidth=400&key=AIzaSyC2E7CGzA-t0WDY6VXvnvErSyI-emyMgok';
		$actualpath = $photow;
//		$photo["photo_reference"];
	//	echo ' Photo ' .$actualpath; 
	}
	
 //	echo ' CategoryID ' . $CategoryID;
	echo  '<br>';
	
	
	//$actualpath = 'https://maps.googleapis.com/maps/api/place/photo?photoreference=AW30NDw8XqjBCrnwmel3PqAQpnk7s1unc3vkSDr8WyyHm0JPik1chBMpyV5eDARARZeMEjpuSmP1iXBj5rRlUNzQsaHquCXC_11Ve0wUXQq7nPHrgJCbZoWdHf9nka2FmcLCJAR2jVw1zsEKfSerabdRQpO1ocJZdNq2WP6CeCoIzudK1WtN&sensor=false&maxheight=400&maxwidth=400&key=AIzaSyC2E7CGzA-t0WDY6VXvnvErSyI-emyMgok';
	
	//$ShopName = 'Shilvanti Patel';
	//echo $ShopName;
//	$ShopLat = floatval($ShopLat);
//	$ShopLongt = floatval($ShopLongt);
	
	//echo ' loc ';
	//echo  $ShopLat . ' ';
	//echo  $ShopLongt;
	
	$ShopName2 = str_replace(" ","",$ShopName);
	
	$sql="INSERT INTO Shops (ShopName,ShopPhone,ShopLogName,ShopPassword,ShopLat,ShopLongt,ShopLogo,ShopCover,CategoryID,Type,AdminID,googleshophkey) VALUES
  ('$ShopName','','$ShopName2','admin',$ShopLat,$ShopLongt,'$actualpath','$actualpath','$CategoryID','Other','1','YES')";
   if(mysqli_query($con,$sql))
   {
	   
	   echo 'added';
	   
   }else{
	   
	   echo $con -> error;
   }
	
}

header("location: GoogleShops.php"); 




//echo $arr["results"];

//$split = explode("results", $x);

//echo $split[1];

function getShops($Category,$Key,$loca)
	{
		$url = 'https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$loca.'&type='.$Category.'&key='.$Key.'&pagetoken&language=en&rankby=distance';
		$fields =array(
			 'to' => $tokens,
			 'notification'=>array(
			 'title' => $PostTitle,
			 'body' => $Message)
			);

			$headers = array(
			'Authorization:key=AAAAEDOF67k:APA91bFMPNwvWHetPtqc1i--ztKxrPdSd7ZbTXvrm0LWFV6KHlkw5I-9yOdt6ZtBq1PXo3uVEDcJnFmbAKpNH7tTS9wiKLjAaeLzB0J0KMI6xvsZ5z0C-4Kn98VzSLp_fJs-ibpmOJY2',
			'Content-Type:application/json'
			);

	   $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
       return $result;
	}



?> 