<?php
 require "conn.php";

$PostId = $_GET["PostId"];
$StoryStatus = $_GET["StoryStatus"];
$ShopID = $_GET["ShopID"]; 





   $sql = "UPDATE ShopStory SET StoryStatus = '$StoryStatus' WHERE StotyID=$PostId";

   if(mysqli_query($con,$sql)){


		if($StoryStatus!="ACTIVE"){
			
			$res = mysqli_query($con,"SELECT * FROM ShopStory WHERE ShopID='$ShopID' AND StoryStatus = 'ACTIVE'");

                $result = array();

                while($row = mysqli_fetch_assoc($res)){

                    $x++;
                
                }
                
                 $sql2="UPDATE Shops SET StoryCount=StoryCount-1 WHERE ShopID=$ShopID";
	 
                	if(mysqli_query($con,$sql2))
                   {}
                
                if($x==0){
                     $sql2="UPDATE Shops SET HasStory='No' WHERE ShopID=$ShopID";
	 
                	 if(mysqli_query($con,$sql2))
						{}
                }
			
			
			
		}else{
			
			
			 $sql2="UPDATE Shops SET HasStory='YES',StoryCount=StoryCount+1 WHERE ShopID=$ShopID";
	 
				 if(mysqli_query($con,$sql2))
			   {}
		   
		   
		   $res = mysqli_query($con,"SELECT ShopFirebaseToken,LANG FROM Shops WHERE ShopID='$ShopID'");

                $result = array();

                while($row = mysqli_fetch_assoc($res)){

                  $ShopFirebaseToken =  $row["ShopFirebaseToken"];
				  $ShopLang = $row["LANG"];
				  
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
		   
		   
			
		//	$sql2="UPDATE Shops SET StoryCount=StoryCount+1 WHERE ShopID=$ShopID";
	 
          //      	if(mysqli_query($con,$sql2))
            //       {}
			
			
		}

  /*
		
	  $url = 'apps.php';
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;
		
		*/
		
	echo " Done ";
    
   }
   else
   {
 //  echo "UserCode used before";
 
 /*
      $url = 'apps.php';
      echo '<script>alert(" Error ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;

	echo json_encode($key);
	
	*/
	
	echo " Error ";
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

