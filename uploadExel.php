<?php
 require "conn.php";


 
 
//echo $t[0];
 if(isset($_POST["submit_file"]))
{
	 $file = $_FILES["file"]["tmp_name"];
	 $file_open = fopen($file,"r");
	 $x = 0;
	while(($csv = fgetcsv($file_open, 1000, ",")) !== false){
		
		if($x>1){



	$CityName = $csv[0];
	$CityLat = $csv[1];
	$CityLongt = $csv[2];
	
	
  
  $sql="INSERT INTO DeliveryZone (CountryID,CityName,CityLat,CityLongt,Deliveryzone) 
								VALUES ('1','$CityName','$CityLat','$CityLongt','30');";
			   if(mysqli_query($con,$sql))
			   {}

	
  
  
		
	}

$x++;
		
		


   
  
	}
	
}
	
 $url = 'settings-delivery-zone.php';
                echo '<script>alert(" Done ")</script>';
                echo '<script type="text/javascript">';
                echo 'window.location.href="'.$url.'";';
                echo '</script>';
                echo '<noscript>';
                echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
                echo '</noscript>'; exit;
 








die;
mysqli_close($con);

?>

