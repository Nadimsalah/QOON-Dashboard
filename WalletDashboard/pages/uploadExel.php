<?php
 require "conn.php";


 
 
//echo $t[0];

 	$res2 = mysqli_query($con,"SELECT * FROM OrdersJiblerpercentage");
									while($row22 = mysqli_fetch_assoc($res2)){
										
										$percent = $row22["percent"];
										
									}
									
									
 $SevenDayDate = date('Y-m-d',strtotime("-7 days"));
 
 if(isset($_POST["submit_file"]))
{
	 $file = $_FILES["file"]["tmp_name"];
	 $file_open = fopen($file,"r");
	 $x = 0;
	while(($csv = fgetcsv($file_open, 1000, ",")) !== false){
		
		if($x>1){



	$id = $csv[5];
	$Money = $csv[1];
	
	
	 	$res2 = mysqli_query($con,"SELECT ShopFirebaseToken,ShopPhone FROM Shops WHERE ShopID = $id");
									while($row22 = mysqli_fetch_assoc($res2)){
										
										$ShopFirebaseToken = $row22["ShopFirebaseToken"];
										$ShopPhone         = $row22["ShopPhone"];
										
									}
	

	
	
	$Money = $Money . '-' . (($percent/100)*$Money) ;
	
	$Moneyy = $Money . 'MAD';
	
	$realMoney = $Money - (($percent/100)*$Money);
	
	ResturantNotification($ShopFirebaseToken,$realMoney);
	
	
	
	$Message = $realMoney . ' MAD ' .'Envoyé à votre compte'  ;
	
	send($ShopPhone,$Message );
	
  
  $sql="INSERT INTO ShopLastTransaction (ShopID,Money,Method,TransactionName,TransactionPhoto,DriverPhoto,DriverName,TransactionStatus,OrderID) 
								VALUES ('$id','$Money','BankAccount','Cash plus','CASHPLUS','-','-','BankAccount','');";
			   if(mysqli_query($con,$sql))
			   {}

	$sql = "UPDATE Orders SET ShopRecive = 'YES' WHERE ShopID='$id' AND CreatedAtOrders < '$SevenDayDate'";
	if(mysqli_query($con,$sql))
	  {}

  
  
		
}

$x++;
		
		


   
  
	}
	
 $url = 'dashboard.php';
                echo '<script>alert(" Done ")</script>';
                echo '<script type="text/javascript">';
                echo 'window.location.href="'.$url.'";';
                echo '</script>';
                echo '<noscript>';
                echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
                echo '</noscript>'; exit;
 
}



function ResturantNotification($tokens,$Money)
	{
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields =array(
			 'to' => $tokens,
			 'notification'=>array(
			 'title' => "Jibler Pay",
			 'body' => "$Money Envoyé à votre compte ")
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
	
	
	function send($send_to, $message){
    
    $data = array('to_number' => $send_to, 'type' => 'text', 'message'=> $message);

    $url = "https://api.maytapi.com/api/7a8bc06b-a25a-4d60-84b5-eec2903d92ce/28858/sendMessage";
	
	$headers = array(
			'x-maytapi-key:366d2ac5-9387-4b5d-9dd0-15e6e6127b30',
			'Content-Type:application/json'
			);
	
	
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec( $ch );
    return $response;
  }



die;
mysqli_close($con);

?>

