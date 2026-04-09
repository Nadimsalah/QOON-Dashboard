<?php
 require "conn.php";

$AdminID = $_POST["AdminID"];
$AdminName = $_POST["AdminName"];
$AdminPassword = $_POST["AdminPassword"];



$AdminPassword = password_hash($AdminPassword, PASSWORD_DEFAULT);


   $sql = "UPDATE Admin SET AdminName='$AdminName',AdminPassword='$AdminPassword' WHERE AdminID=$AdminID";

   if(mysqli_query($con,$sql)){

   $key['Result'] = "success";


session_start();
    $_SESSION["Emailjibler"] = 'loged';
    $_SESSION["Passwordjibler"] = $AdminPassword ;
    
    $_SESSION["AdminID"] = $AdminID;
    $_SESSION["AdminName"] = $AdminName;
    
    setcookie("Emailjibler", 'loged', time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("AdminID", $AdminID, time() + (86400 * 30), "/"); // 86400 = 1 day
    
    
    setcookie("Passwordjibler", $AdminPassword, time() + (86400 * 30), "/"); // 86400 = 1 day
    setcookie("AdminName", $AdminName, time() + (86400 * 30), "/"); // 86400 = 1 day


        echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
		
	  $url = 'settings-profile.php';
      echo '<script>alert(" تم بنجاح ")</script>';
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
     $url = 'shop-profile.php?id='.$ID;
      echo '<script>alert(" خطأ ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;

	echo json_encode($key);
   }
die;
mysqli_close($con);

?>

