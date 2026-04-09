<?php
require "conn.php";


$Message          		   = $_POST["Message"];
$DriverID          		   = $_POST["DriverID"];
$PostTitle        			= $_POST["PostTitle"];





    if(true){
        

       
    $res = mysqli_query($con,"SELECT * FROM Drivers WHERE DriverID=$DriverID");
	while($row = mysqli_fetch_assoc($res)){
		
		
		$FirebaseDriverToken=$row["FirebaseDriverToken"];
	//	echo $FirebaseDriverToken;
		//die;
		send_notification($FirebaseDriverToken,$Message,$PostTitle);
	
		$url = 'notificationsDriver.php';
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;		
			

	}
	
	
	}
	
	
	



function send_notification($UserFirebaseToken,$Message2,$PostTitle2)
	{
		
	//	echo $UserFirebaseToken;
	//	die;
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields =array(
			 'to' => $UserFirebaseToken,
			 'notification'=>array(
			 'title' => $PostTitle2,
			 'body' => $Message2)
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