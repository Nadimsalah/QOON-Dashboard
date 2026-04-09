<?php
 require "conn.php";

$ShopName = $_POST["ShopName"];
$ShopPhone = $_POST["ShopPhone"];
$ShopLoginName = $_POST["ShopLoginName"];
$ShopLoginPassword = $_POST["ShopLoginPassword"];
$ShopLatPosition = $_POST["ShopLatPosition"];
$ShopLongtPosition = $_POST["ShopLongtPosition"];
$priority = $_POST["priority"];
$Type = $_POST["Type"];
$CategoryID = $_POST["CategoryID"];


$ID = $_POST["ShopID"];

  $Carphoto =  $_FILES["Photo"]["tmp_name"];

  $photo1name="w-".rand();

  $path = "db/db/photo/$photo1name.png";

 
  $actualpath = "https://jibler.ma/$path";
 
  $path = "photo/$photo1name.png";
 

  $sql="";
  
  
  $photofound = "no";
 
  if($_FILES["Photo"]["tmp_name"]==""){
	  
  }else{
	  $photofound = "yes";
  }
  
  
      if($photofound == "yes"){
           if (move_uploaded_file($_FILES["Photo"]["tmp_name"], $path)) {
                echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
               
                $sql = "UPDATE Shops SET ShopLogo='$actualpath' WHERE ShopID=$ID";
                   
                   if(mysqli_query($con,$sql)){}
               
           }
    }

   



    $sql = "UPDATE Shops SET ShopName='$ShopName',ShopPhone='$ShopPhone',CategoryID='$CategoryID',ShopLogName='$ShopLoginName',ShopPassword='$ShopLoginPassword',ShopLat='$ShopLatPosition',ShopLongt='$ShopLongtPosition',Type='$Type'
    WHERE ShopID=$ID";
    
    
   if(mysqli_query($con,$sql)){
       
       $photofound2 = "NO";
       
       if($_FILES["Photo2"]["tmp_name"]==""){
	  
          }else{
        	  $photofound2 = "yes";
          }
          
          

          
       
       
       if($photofound2 == "yes"){
           
           
           $Carphoto =  $_FILES["Photo2"]["tmp_name"];

              $photo2name="w-".rand();
            
              $path2 = "db/db/photo/$photo2name.png";
            
             
              $actualpath2 = "https://jibler.ma/$path2";
             
              $path2 = "photo/$photo2name.png";
 
           
           
           
           $sql = "UPDATE Shops SET ShopCover='$actualpath2' WHERE ShopID=$ID";
           
           if(mysqli_query($con,$sql)){}
           
           
           if (move_uploaded_file($_FILES["Photo2"]["tmp_name"], $path2)) {
                echo "The file ". basename( $_FILES["Photo"]["name"]). " has been uploaded.";
           }
        }
        
        
    
   $key['Result'] = "success";

  
       $url = 'shop-profile.php?id='.$ID;
      echo '<script>alert(" Done ")</script>';
      echo '<script type="text/javascript">';
      echo 'window.location.href="'.$url.'";';
      echo '</script>';
      echo '<noscript>';
      echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
      echo '</noscript>'; exit;

    
	
	echo json_encode($key);
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

