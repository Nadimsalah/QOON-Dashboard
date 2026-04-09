WALLET<?php
 require "conn.php";

$BoostStatus = $_GET["BoostStatus"];
$BoostsByShopID = $_GET["BoostsByShopID"];

   
   
   $res = mysqli_query($con,"SELECT * FROM BoostsByShop JOIN Shops ON BoostsByShop.ShopID = Shops.ShopID WHERE BoostsByShopID='$BoostsByShopID'");

          $result = array();
                        
          while($row = mysqli_fetch_assoc($res)){
			  
			  $ShopID = $row["ShopID"];
			  $ShopFirebaseToken = $row["ShopFirebaseToken"];
			  $BoostPrice = $row["BoostPrice"];
			  $ShopLang = $row["LANG"];
			  
			  $BoostPhoto = $row["BoostPhoto"];
			  
			  if($BoostStatus=="Active"){
				  
				  
				  $sql="INSERT INTO ShopNotification (ShopID,OrderID,NotificationText,ShopNotificationType) VALUES ('$ShopID','0','your ads is active now','BOOST');";
				   if(mysqli_query($con,$sql))
				   {

						
						
					
				  
				  if($ShopLang=="AR"||$ShopLang=="ar"){
		   
				   $ShopTitle = "تم الموافقة على المحتوى للنشر ✅";
				   $ShopBody  = "تمت الموافقة على محتواك للنشر من قبل فريق جيبلر";
				   
			   }else if($ShopLang=="EN"||$ShopLang=="en"){
				   
				   $ShopTitle = "Content Approved for Publication ✅";
				   $ShopBody  = "Your content has been approved for publication by Jibler Team.";
				   
			   }else{
				   
				   $ShopTitle = "Contenu approuvé pour la publication ✅";
				   $ShopBody  = "Votre contenu a été approuvé pour la publication par l'équipe Jibler.";
				   
			   }
				  	


					   ResturantNotification($ShopFirebaseToken,$ShopTitle,$ShopBody);
  
				   }
				   
				   
// 			$sql="INSERT INTO ShopLastTransaction (ShopID,Money,Method,TransactionName,TransactionPhoto,DriverPhoto,DriverName,TransactionStatus,OrderID) VALUES ('$ShopID','-$BoostPrice','WALLET','Cash plus','jibler boost','','','Done','');";
// 			   if(mysqli_query($con,$sql))
// 			   {}
			   
			$sql="INSERT INTO ShopLastTransaction (ShopID,Money,Method,TransactionName,TransactionPhoto,DriverPhoto,DriverName,TransactionStatus,OrderID) VALUES ('$ShopID','-$BoostPrice','WALLET','BOOST','$BoostPhoto','$BoostPhoto','$BoostPhoto','Done','0');";
			   if(mysqli_query($con,$sql))
			   {}   
				  
			  }
			  
			  
		  }


   $sql = "UPDATE BoostsByShop SET BoostStatus = '$BoostStatus' WHERE BoostsByShopID='$BoostsByShopID'";

   if(mysqli_query($con,$sql)){


  
		
	  $url = 'apps.php';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
		
		
    
   }
   else
   {
 //  echo "UserCode used before";
     $url = 'apps.php';
      echo '<script>alert(" Error ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;

	echo json_encode($key);
   }
   
   
   function ResturantNotification($tokens,$ShopTitlew,$ShopBodyw)
	{
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields =array(
			 'to' => $tokens,
			 'notification'=>array(
			 'title' => $ShopTitlew,
			 'body' => $ShopBodyw)
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
   
   
   
die;
mysqli_close($con);

?>

