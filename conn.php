<?php

// $con=mysqli_connect("localhost","qoon_Qoon",";)xo6b(RE}K%","qoon_Qoon");

//   if (mysqli_connect_errno($con))
//   {
//   echo "Failed to connect to MySQL: " . mysqli_connect_error();
//   }


$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
try {
  $con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
  if ($con->connect_error) {
    echo "Failed to connect to MySQL: " . $con->connect_error;
    exit();
  }
} catch (Exception $e) {
  echo "Connection failed: " . $e->getMessage();
}

session_start();

$xx = $_SESSION["Emailjibler"];

$xx = $_COOKIE["Emailjibler"];

if ($xx == '') {
  //  header("location: login.html"); 


  $url = 'login.html';
  echo '<script>alert("You must login")</script>';
  echo '<script type="text/javascript">';
  echo 'window.location.href="' . $url . '";';
  echo '</script>';
  echo '<noscript>';
  echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
  echo '</noscript>';
  exit;



} else {
  //       header("location: index.php");
}





$DomainNamee = "https://qoon.app/dash/";

?>