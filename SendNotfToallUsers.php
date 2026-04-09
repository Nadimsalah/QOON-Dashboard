<?php
 require "conn.php";


$Message        = $_POST["Message"];
$PostTitle      = $_POST["PostTitle"];
$UserID         = $_POST["UserID"];
$UserTypes      = $_POST["UserTypes"];



$serviceAccountFile = 'jibler-37339-63535a118af8.json';

// Read the service account JSON file
$serviceAccount = json_decode(file_get_contents($serviceAccountFile), true);

// Google's OAuth 2.0 token endpoint
$googleOAuthUrl = 'https://oauth2.googleapis.com/token';

// Time constants
$now = time();
$tokenExpiry = $now + 3600; // Token valid for 1 hour

// Service account credentials
$privateKey = $serviceAccount['private_key'];
$clientEmail = $serviceAccount['client_email'];

// JWT header
$jwtHeader = [
    'alg' => 'RS256',
    'typ' => 'JWT'
];

// JWT claim set (payload)
$jwtClaimSet = [
    'iss' => $clientEmail,                      // Issuer: the service account email
    'scope' => 'https://www.googleapis.com/auth/cloud-platform',  // OAuth scope for FCM
    'aud' => $googleOAuthUrl,                   // Audience: OAuth token URL
    'exp' => $tokenExpiry,                      // Expiration time
    'iat' => $now                               // Issued at time
];

// Base64 URL encode a string
function base64UrlEncode($data) {
    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
}

// Create the JWT
$jwtHeaderEncoded = base64UrlEncode(json_encode($jwtHeader));
$jwtClaimSetEncoded = base64UrlEncode(json_encode($jwtClaimSet));
$jwtSignatureInput = $jwtHeaderEncoded . '.' . $jwtClaimSetEncoded;

// Sign the JWT using the private key (RS256)
$signature = '';
openssl_sign($jwtSignatureInput, $signature, $privateKey, 'sha256WithRSAEncryption');
$jwtSignatureEncoded = base64UrlEncode($signature);

// Final JWT token
$jwtToken = $jwtSignatureInput . '.' . $jwtSignatureEncoded;

// Prepare the POST fields to get an access token
$postFields = http_build_query([
    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
    'assertion' => $jwtToken
]);

// Send the request to get an access token
$ch = curl_init($googleOAuthUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);

// Execute the request and get the response
$response = curl_exec($ch);
curl_close($ch);

// Decode the JSON response
$tokenResponse = json_decode($response, true);

if (isset($tokenResponse['access_token'])) {
    // Access token received successfully
    $accessToken = $tokenResponse['access_token'];
    $ProgID = "jibler-37339";
  //  echo "Access Token: " . $accessToken . "\n";
} else {
    // Handle error
//    echo "Error: " . $response . "\n";
}





         if($UserTypes=="ALL"){       
            $res = mysqli_query($con,"SELECT * FROM Users");
         }else if($UserTypes=="NO_ORDERS"){
            $res = mysqli_query($con,"SELECT * FROM Users WHERE UserOrdersNum = 0;"); 
         }else{
            $rangeMin       = $_POST["rangeMin"];
            $rangeMax       = $_POST["rangeMax"]; 
            $res = mysqli_query($con,"SELECT *
FROM Users
WHERE UserOrdersNum >= $rangeMin
  AND UserOrdersNum <= $rangeMax;
");  
         }               
         $result = array();
                        
              while($row = mysqli_fetch_assoc($res)){
                        
               $UserFirebaseToken = $row["UserFirebaseToken"];
							
										
					//send_notification($UserFirebaseToken,$Message,$PostTitle);	
					newNotfi($UserFirebaseToken,$PostTitle,$Message,$accessToken,$ProgID);

					
										
			} 
			$AdminID = $_COOKIE["AdminID"];
			
		$sql="INSERT INTO NotificationsSentByAdmin (AdminID,Bodyy,Title,Type) VALUES ('$AdminID','$Message','$PostTitle','$UserTypes');";
           if(mysqli_query($con,$sql))
           {
               
           }	
			
			
	$url = 'notifications.php';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;				
   
   
   
  function send_notification($UserFirebaseToken,$Message,$PostTitle)
	{
		$url = 'https://fcm.googleapis.com/fcm/send';
		$fields =array(
			 'to' => $UserFirebaseToken,
			 'notification'=>array(
			 'title' => $PostTitle,
			 'body' => $Message,
			 "link"=> "http://sae-marketing.com/$Message",
			 "color"=>'$Message',
			 'data'=>'$Message')
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
  




function newNotfi($tokens,$TitleW,$MesBodyW,$accessTokenw,$Pid)
	{

		$url = 'https://fcm.googleapis.com/v1/projects/'.$Pid.'/messages:send';
		$fields =array(
			 'to' => $tokens,
			 'notification'=>array(
				 'title' => $TitleW, 
				 'body' => $MesBodyW)
			);

        $fields = array(
            'message' => array(
                'token' => $tokens,
                'notification' => array(
                    'title' => $TitleW,
                    'body' => $MesBodyW
                )
            )
        );

		$headers = array(         
			'Authorization:Bearer '.$accessTokenw,
			'Content-Type:application/json'
			);

	   $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //   curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: fcm.googleapis.com'));
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
       $result = curl_exec($ch);           
       if ($result === FALSE) {
           die('Curl failed: ' . curl_error($ch));
       }
       curl_close($ch);
	   //echo $result;
       return $result;
	}  
   
die;
mysqli_close($con);

?>

