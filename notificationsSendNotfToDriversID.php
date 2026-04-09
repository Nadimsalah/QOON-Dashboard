<?php
 require "conn.php";


$Message = $_POST["Message"];
$PostTitle = $_POST["PostTitle"];
$DriverID = $_POST["DriverID"];



 require "conn.php";

                
         $res = mysqli_query($con,"SELECT * FROM Drivers WHERE DriverID = $DriverID");
                        
         $result = array();
                        
              while($row = mysqli_fetch_assoc($res)){
                        
               $FirebaseDriverToken = $row["FirebaseDriverToken"];
										
										
					send_notification($FirebaseDriverToken,$Message,$PostTitle);	


			//	echo $ShopFirebaseToken;

					$url = 'driver-profile.php?id='.$DriverID;
      echo '<script>alert(" تم بنجاح ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;		
									
			} 
   
   
   
   function send_notification($tokens, $Message, $PostTitle)
	{
		$url = 'https://fcm.googleapis.com/fcm/send';
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
   
   
die;
mysqli_close($con);

?>

