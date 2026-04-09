<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title> Shops | Jibler Dashboard </title>
      <!-- Animate With CSS -->
      <link rel="stylesheet" type="text/css" href="css/animate.css">
      <!-- Font Awesome KIT -->
      <link href="fontawesome-kit-5/css/all.css" rel="stylesheet">
      <link href="fontawesome-kit-5/css/fontawesome.css" rel="stylesheet">
      <link href="fontawesome-kit-5/css/brands.css" rel="stylesheet">
      <link href="fontawesome-kit-5/css/solid.css" rel="stylesheet">
      <script defer src="fontawesome-kit-5/js/all.js"></script>
      <script defer src="fontawesome-kit-5/js/brands.js"></script>
      <script defer src="fontawesome-kit-5/js/solid.js"></script>
      <script defer src="fontawesome-kit-5/js/fontawesome.js"></script>
      <!-- Bootstrap Grids -->
      <link href="css/bootstrap.min.css" rel="stylesheet">
	     <script src="https://maps.google.com/maps/api/js?sensor=false"></script>
		    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1DPGIuuuJKZMXlK_ehSH07-5Ab2ab9-8&callback=initMap&v=weekly"
      async
    ></script>
      <!-- Custom Stylings -->
      <link href="css/custom.css" rel="stylesheet">
      <!-- Jquery Library -->
      <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
   </head>
   <body>
      <section class="all-content">
         <!-- Sidebar Section Starts Here -->
         <aside class="sidebar-1">
            <div class="sidebar-wrapper">
               <div class="sidebar-head">
                  <div class="sidebar-logo">
                     <img src="images/logo.png">   
                  </div>
                  <button class="navbar-handler"> <i class="fa fa-bars"> </i> </button>
               </div>
               
               
               
                <?php 
                   
                      $xx = $_COOKIE["Emailjibler"];
                      
                        $Userspage        = $_COOKIE["Userspage"];
                        $UserInformation  = $_COOKIE["UserInformation"];
                        $DownloadUsersData  = $_COOKIE["DownloadUsersData"];
                        $DriversPage  = $_COOKIE["DriversPage"];
                        $AddNewDriver  = $_COOKIE["AddNewDriver"];
                        $DriverProfile  = $_COOKIE["DriverProfile"];
                        $ShopsPage  = $_COOKIE["ShopsPage"];
                        $AddNewShop  = $_COOKIE["AddNewShop"];
                        $ShopProfile  = $_COOKIE["ShopProfile"];
                        $OrdersPage    = $_COOKIE["OrdersPage"];
                        $OrderDetails  = $_COOKIE["OrderDetails"];
                        $WalletPage  = $_COOKIE["WalletPage"];
                        $AddSlides = $_COOKIE["AddSlides"];
                        $ControleDistance = $_COOKIE["ControleDistance"];
                        $Categores = $_COOKIE["Categores"];
                        $Notification = $_COOKIE["Notification"];
                        $Profile = $_COOKIE["Profile"];
                        $Staffaccounts = $_COOKIE["Staffaccounts"];
                        $blacklistr = $_COOKIE["blacklistr"];
                        $Payments = $_COOKIE["Payments"];

                   
                   ?>
               
               
               <div class="custom-menu">
                  <ul>
                     <li > <a href="index.php"> <img src="images/nav-icon1.png" height="22px"> <span> Dashboard </span> </a> </li>
                     <?php if($Userspage==1){ ?>
                     <li > <a href="user.php"> <img src="images/nav-icon2.png" height="22px"> <span> Users </span>  </a> </li>
                     <?php } ?>
                     <?php if($DriversPage==1){ ?>
                     <li > <a href="driver.php"> <img src="images/nav-icon3.png" height="35px"> <span> Drivers </span>  </a> </li>
                     <?php } ?>
                     <?php if($ShopsPage==1){ ?>
                     <li class="active"> <a href="shop.php"> <img src="images/nav-icon4.png" height="25px"> <span> Shop </span> </a> </li>
                     <?php } ?>
                     <?php if($OrdersPage==1){ ?>
                     <li> <a href="orders.php"> <img src="images/nav-icon5.png" height="24px"> <span> orders </span> </a> </li>
                     <?php } ?>
                     <?php if($WalletPage==1){ ?>
                     <li> <a href="wallet.php"> <img src="images/nav-icon6.png" height="23px"> <span> wallet </span> </a> </li>
                     <?php } ?>
                     <li> <a href="apps.php"> <img src="images/nav-icon7.png" height="24px">  <span> Apps </span> </a> </li>
                     <?php if($Notification==1){ ?>
                     <li> <a href="notifications.php"> <img src="images/nav-icon8.png" height="25px">  <span> notifications </span> </a> </li>
                      <?php } ?>
                     <?php if($Profile==1){ ?>
                     <li> <a href="settings-profile.php"> <img src="images/nav-icon9.png" height="26px"> <span> Settings </span> </a> </li>
                     <?php } ?>
                     <li class="logout-list"> <a href="logout.php"> <img src="images/nav-icon10.png" height="26px"> <span> Logout </span> </a> </li>
                  </ul>
               </div>
            </div>
         </aside>
         <!-- Sidebar Section Starts Here -->
         <!-- Right Section Starts Here -->
         <main class="right-content">

            <!-- Top Bar Section Starts Here -->
            <section class="top-bar">
               <div class="top-logo">
                  <img src="images/logo.png">
               </div>
               <div class="top-right">
                  <div class="row center-row1">
                     <div class="col-md-5 col-lg-5 col-sm-12 col-12 order-lg-1 order-md-1 order-sm-2 order-2">
                        <div class="search-form1">
                           <form>
                              <input type="text" placeholder="Search shop name..." name="ShopName">
                              <button> <i class="fa fa-search"> </i> </button>
                           </form>
                        </div>
                     </div>
                     <div class="col-md-7 col-lg-7 col-sm-12 col-12 order-lg-2 order-md-2 order-sm-1 order-1">
                        <div class="widgets-holder1">
                           <div class="country-dropdown">
                              <div class="dropdown right-drop">
                                 <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 Morocco 
                                 <img src="images/flag-1.png">
                                 <i class="fa fa-angle-down"> </i>
                                 </button>
                                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                   
                                 </div>
                              </div>
                           </div>
                           <div class="country-dropdown">
                              <div class="dropdown right-drop">
                                 <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                     
                                <?php $CityName = $_GET["CityName"]; $cityID = $_GET["cityID"]; ?>
     
                                     
                                     
                                  <?php if($CityName == ''){ ?>    
                                 All Cities
                                 <?php }else{ ?>
                                 
                                 <?php echo $CityName; } ?>
                                 
                                 
                                 <i class="fa fa-angle-down"> </i>
                                 </button>
                                 
                                 
                                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="shop.php"> All Cities</a>

                                    <?php                 require "conn.php";

                                    
                                     $res = mysqli_query($con,"SELECT * FROM DeliveryZone");

                                        $result = array();
                        
                                        while($row = mysqli_fetch_assoc($res)){
                                    
                                    ?> 
                                     
                                     
                                    <a class="dropdown-item" href="shop.php?CityName=<?php echo $row["CityName"]; ?>&cityID=<?php echo $row["DeliveryZoneID"]; ?>"><?php echo $row["CityName"]; ?></a>
                                    
                                    <?php } ?>
                                 </div>
                                 
                                 
                                 
                              </div>
                           </div>
                           <div class="bell-dropdown">
                              <div class="dropdown right-drop">
                                 <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 <img src="images/bell-icon.png">
                                 <span class="counter-1"> 2 </span>
                                 </button>
                                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    
                                 </div>
                              </div>
                           </div>
                           <div class="user-dropdown">
                              <div class="dropdown right-drop">
                                 <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                 <img src="images/avatar-1.png">
                                 <i class="fa fa-angle-down"> </i>
                                 </button>
                                 <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                   <a class="dropdown-item" href="settings-profile.php">Setting</a>
                                    <a class="dropdown-item" href="logout.php">logout</a>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </section>
            <!-- Top Bar Section Starts Here -->
            
            <!-- Main Content Section Starts Here -->
            <section class="main-content">
               <div class="container">
                  

				<div id="map" style="width: 100%; height: 600px;"></div>
<script type="text/javascript">
    var locations = [
        
        
        <?php 
                              require "conn.php";
                              

								
                              
                              $res = mysqli_query($con,"SELECT * FROM Shops");

                                $result = array();
                
                                while($row = mysqli_fetch_assoc($res)){
                                    
                                    
                            $ShopLat	                 = $row["ShopLat"];
                            $ShopLongt                    = $row["ShopLongt"];
							$ShopName                    = $row["ShopName"];
							$ShopPhone                   = $row["ShopPhone"];
							$ShopOpen                     = $row["ShopOpen"];
							$ShopLogo                     = $row["ShopLogo"];


                            
                           
                                    
                            ?>
        
        
        
      ['<?php echo 'Shop Name '.$ShopName . ' - Phone ' . $ShopPhone . ' - OPEN ' . $ShopOpen; ?>', <?php echo $ShopLat; ?>, <?php echo $ShopLongt; ?>, 4],
      
      
      <?php 
      
                                }
      ?> 

    ];
    
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 3,
      center: new google.maps.LatLng(30.025488108089657, 31.49183914474898),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    
    var infowindow = new google.maps.InfoWindow();

    var marker, i;
    
    for (i = 0; i < locations.length; i++) {  
        if (locations[i][0].includes('FREE')){
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        icon: 'https://jibler.app/db/db/images/jiblerc.png',
        map: map
      });
        }else{
            
        marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        icon: 'https://jibler.app/db/db/images/jiblerc.png',
        map: map
      });
      
        }
      
      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
                         <!--</div>-->

                       
                    </div> <!-- /. card-body -->
           



               </div>
            </section>
            <!-- Main Content Section Ends Here -->
         </main>
         <!-- Right Section Ends Here -->
      </section>
      
      
      
      <?php 

     $res = mysqli_query($con,"SELECT * FROM Shops");
     
     
     if($cityID !=''){
                        
     $res = mysqli_query($con,"SELECT *, (6372.797 * acos(cos(radians($CityLat)) * cos(radians(ShopLat)) * cos(radians(ShopLongt) - radians($CityLongt)) + sin(radians($CityLat)) * sin(radians(ShopLat)))) AS distance FROM Shops WHERE  Shops.Status = 'ACTIVE' HAVING distance <= $Deliveryzone ORDER BY priority DESC , distance ASC ");
                
                                    
      }

    $d = new DateTime('2022-01-19');
    $d->modify('first day of this month');
    $jun =  $d->format('Y-m-d');
    
    
    $d = new DateTime('2022-02-19');
    $d->modify('first day of this month');
    $feb =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-03-19');
    $d->modify('first day of this month');
    $march =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-04-19');
    $d->modify('first day of this month');
    $April =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-05-19');
    $d->modify('first day of this month');
    $May =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-06-19');
    $d->modify('first day of this month');
    $Jun =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-07-19');
    $d->modify('first day of this month');
    $jul =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-08-19');
    $d->modify('first day of this month');
    $Aug =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-09-19');
    $d->modify('first day of this month');
    $Sep =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-10-19');
    $d->modify('first day of this month');
    $Oct =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-11-19');
    $d->modify('first day of this month');
    $NOv =  $d->format('Y-m-d');
    
    $d = new DateTime('2022-12-19');
    $d->modify('first day of this month');
    $Dec =  $d->format('Y-m-d');
    
    
    $junNum1 = 0;
    $febNum1 = 0;
    
    $MarchNum1 = 0;
    $AprilNum1 = 0;
    
    $MayNum1 = 0;
    $JunNum1 = 0;
    
    $JulyNum1 = 0;
    $AugNum1 = 0;
    
    $SepNum1 = 0;
    $OctNum1 = 0;
    
    $NovNum1 = 0;
    $DecNum1 = 0;
    
    
    $junNum2 = 0;
    $febNum2 = 0;
    
    $MarchNum2 = 0;
    $AprilNum2 = 0;
    
    $MayNum2 = 0;
    $JunNum2 = 0;
    
    $JulyNum2 = 0;
    $AugNum2 = 0;
    
    $SepNum2 = 0;
    $OctNum2 = 0;
    
    $NovNum2 = 0;
    $DecNum2 = 0;
    
    
    $junNum3 = 0;
    $febNum3 = 0;
    
    $MarchNum3 = 0;
    $AprilNum3 = 0;
    
    $MayNum3 = 0;
    $JunNum3 = 0;
    
    $JulyNum3 = 0;
    $AugNum3 = 0;
    
    $SepNum3 = 0;
    $OctNum3 = 0;
    
    $NovNum3 = 0;
    $DecNum3 = 0;
    
    $x = date("Y-m-d") ;
    $pieces = explode("-", $x);
    $list=array();
    $month = $pieces[1];
    $year = $pieces[0];
    
    for($d=1; $d<=31; $d++)
    {
    $time=mktime(12, 0, 0, $month, $d, $year);          
    if (date('m', $time)==$month)       
        $list[]=date('Y-m-d', $time);
        
    //    echo date('Y-m-d', $time). '-';
        
    }
    
    
     $Day1 = 0; $Day2 = 0; $Day3 = 0; $Day4 = 0; $Day5 = 0; $Day6 = 0; $Day7 = 0; $Day8 = 0; $Day9 = 0; $Day10 = 0;
    $Day11 = 0; $Day12 = 0; $Day13 = 0; $Day14 = 0; $Day15 = 0; $Day16 = 0; $Day17 = 0; $Day18 = 0; $Day19 = 0; $Day20 = 0;
    $Day21 = 0; $Day22 = 0; $Day23 = 0; $Day24 = 0; $Day25 = 0; $Day26 = 0; $Day27 = 0; $Day28 = 0; $Day29 = 0; $Day30 = 0;
    $Day31 = 0;
    
    
    /////////////////////////////////////////////////////////////////////////
    
    $Days1 = 0; $Days2 = 0; $Days3 = 0; $Days4 = 0; $Days5 = 0; $Days6 = 0; $Days7 = 0; $Days8 = 0; $Days9 = 0; $Days10 = 0;
    $Days11 = 0; $Days12 = 0; $Days13 = 0; $Days14 = 0; $Days15 = 0; $Days16 = 0; $Days17 = 0; $Days18 = 0; $Days19 = 0; $Days20 = 0;
    $Days21 = 0; $Days22 = 0; $Days23 = 0; $Days24 = 0; $Days25 = 0; $Days26 = 0; $Days27 = 0; $Days28 = 0; $Days29 = 0; $Days30 = 0;
    $Days31 = 0;
    
    /////////////////////////////////////////////////////////////////////////
    
    $Daysw1 = 0; $Daysw2 = 0; $Daysw3 = 0; $Daysw4 = 0; $Daysw5 = 0; $Daysw6 = 0; $Daysw7 = 0; $Daysw8 = 0; $Daysw9 = 0; $Daysw10 = 0;
    $Daysw11 = 0; $Daysw12 = 0; $Daysw13 = 0; $Daysw14 = 0; $Daysw15 = 0; $Daysw16 = 0; $Daysw17 = 0; $Daysw18 = 0; $Daysw19 = 0; $Daysw20 = 0;
    $Daysw21 = 0; $Daysw22 = 0; $Daysw23 = 0; $Daysw24 = 0; $Daysw25 = 0; $Daysw26 = 0; $Daysw27 = 0; $Daysw28 = 0; $Daysw29 = 0; $Daysw30 = 0;
    $Daysw31 = 0;
    


                $result = array();
                $res = mysqli_query($con,"SELECT * FROM Shops");
                while($row = mysqli_fetch_assoc($res)){
                    
                    
                    
                    
                    
                    if (strpos($row["CreatedAtShops"], $list[0]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days1++;

                            }else{
                                $Daysw1++;
                            }
                            
                        }
                        
                        
                    }
                    
                    else if (strpos($row["CreatedAtShops"], $list[1]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day2++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days2++;

                            }else{
                                $Daysw2++;
                            }
                            
                        }
                        
                        
                    }
                    
                    else if (strpos($row["CreatedAtShops"], $list[2]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day3++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days3++;

                            }else{
                                $Daysw3++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[3]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day4++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days4++;

                            }else{
                                $Daysw4++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[4]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day5++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days5++;

                            }else{
                                $Daysw5++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[5]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day6++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days6++;

                            }else{
                                $Daysw6++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[6]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day7++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days7++;

                            }else{
                                $Daysw7++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[7]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day8++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days8++;

                            }else{
                                $Daysw8++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[8]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day9++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days9++;

                            }else{
                                $Daysw9++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[9]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day10++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days10++;

                            }else{
                                $Daysw10++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[10]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day11++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days11++;

                            }else{
                                $Daysw11++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[11]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day12++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days12++;

                            }else{
                                $Daysw12++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[12]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day13++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days13++;

                            }else{
                                $Daysw13++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[13]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day14++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days14++;

                            }else{
                                $Daysw14++;
                            }
                            
                        }
                        
                        
                    }
                    else if (strpos($row["CreatedAtShops"], $list[14]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day15++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days15++;

                            }else{
                                $Daysw15++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[15]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day16++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days16++;

                            }else{
                                $Daysw16++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[16]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day17++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days17++;

                            }else{
                                $Daysw17++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[17]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day18++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days18++;

                            }else{
                                $Daysw18++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[18]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day19++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days19++;

                            }else{
                                $Daysw19++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[18]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day19++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days19++;

                            }else{
                                $Daysw19++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[19]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day20++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days20++;

                            }else{
                                $Daysw20++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[20]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day21++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days21++;

                            }else{
                                $Daysw21++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[21]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day22++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days22++;

                            }else{
                                $Daysw22++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[22]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day23++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days23++;

                            }else{
                                $Daysw23++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[23]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day24++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days24++;

                            }else{
                                $Daysw24++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[24]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day25++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days25++;

                            }else{
                                $Daysw25++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[25]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day26++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days26++;

                            }else{
                                $Daysw26++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[26]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day27++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days27++;

                            }else{
                                $Daysw27++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[27]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day28++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days28++;

                            }else{
                                $Daysw28++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[28]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day29++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days29++;

                            }else{
                                $Daysw29++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[29]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day30++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days30++;

                            }else{
                                $Daysw30++;
                            }
                            
                        }
                        
                        
                    }else if (strpos($row["CreatedAtShops"], $list[30]) !== false) {
                        
                        if($row["Type"]=="Our"){
                        $Day31++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $Days31++;

                            }else{
                                $Daysw31++;
                            }
                            
                        }
                        
                        
                    }






                    $test=4;
                    $DriverNumber++;
                    
                    if($jun<$row["CreatedAtShops"]&&$feb>$row["CreatedAtShops"]){
                        
                        
                        if($row["Type"]=="Our"){
                        $junNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $junNum2++;

                            }else{
                                $junNum3++;
                            }
                            
                        }
                    }
                    
                    else if($feb<$row["CreatedAtShops"]&&$march>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $febNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $febNum2++;

                            }else{
                                $febNum3++;
                            }
                            
                        }
                    }
                    
                    else if($march<$row["CreatedAtShops"]&&$April>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $MarchNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $MarchNum2++;

                            }else{
                                $MarchNum3++;
                            }
                            
                        }
                    }
                    
                    else if($April<$row["CreatedAtShops"]&&$May>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $AprilNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $AprilNum2++;

                            }else{
                                $AprilNum3++;
                            }
                            
                        }
                    }
                    
                    else if($May<$row["CreatedAtShops"]&&$Jun>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $MayNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $MayNum2++;

                            }else{
                                $MayNum3++;
                            }
                            
                        }
                    }
                    
                    else if($Jun<$row["CreatedAtShops"]&&$jul>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $JunNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $JunNum2++;

                            }else{
                                $JunNum3++;
                            }
                            
                        }
                    }
                    
                    
                    
                    else if($jul<$row["CreatedAtShops"]&&$Aug>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $JulyNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $JulyNum2++;

                            }else{
                                $JulyNum3++;
                            }
                            
                        }
                    }
                    
                    else if($Aug<$row["CreatedAtShops"]&&$Sep>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $AugNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $AugNum2++;

                            }else{
                                $AugNum3++;
                            }
                            
                        }
                    }
                    
                    
                    else if($Sep<$row["CreatedAtShops"]&&$Oct>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $SepNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $SepNum2++;

                            }else{
                                $SepNum3++;
                            }
                            
                        }
                    }
                    
                    else if($Oct<$row["CreatedAtShops"]&&$NOv>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $OctNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                                $OctNum2++;

                            }else{
                                $OctNum3++;
                            }
                            
                        }
                    }
                    
                    
                    
                    else if($NOv<$row["CreatedAtShops"]&&$Dec>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $NovNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                               $NovNum2++;

                            }else{
                                $NovNum3++;
                            }
                            
                        }
                    }
                    
                    else if($Dec<$row["CreatedAtShops"]&&'2023-01-01'>$row["CreatedAtShops"]){
                        
                        if($row["Type"]=="Our"){
                        $DecNum1++;
                        }else if($row["Type"]=="Other"){
                            if($row["Token"]!=""){
                               $DecNum2++;

                            }else{
                                $DecNum3++;
                            }
                            
                        }
                    }


                }
                


?>

      
      
      
      
      <!-- Bootstrap Javascript -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
      <script src="js/bootstrap.min.js"> </script>
      <!-- Chart JS -->
      <script src="https://cdn2.hubspot.net/hubfs/476360/Chart.js"></script>
      <script src="https://cdn2.hubspot.net/hubfs/476360/utils.js"></script>
      <script src="js/functions.js"> </script>
      <script type="text/javascript">
         $(document).ready(function(){
         var config = {
              type: 'line',
              data: {
            <?php if($TypeShop==""||$TypeShop=="YEAR"){ ?> 
         labels: ['Jan', 'Feb', 'March', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dev'],
         <?php }else if($TypeShop=="MONTH"){ ?>
         labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12','13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24','25', '26', '27', '28', '29', '30', '31'],
         <?php } ?>                 datasets: [{
                    label: 'Start plus',
                    backgroundColor: '#00D409',
                    borderColor: '#00D409',
                    fill: false,
                    data: [
                <?php if($TypeShop==""||$TypeShop=="YEAR"){ ?> 
        
               <?php echo $junNum2; ?>,
               <?php echo $febNum2; ?>,
               <?php echo $MarchNum2; ?>,
               <?php echo $AprilNum2; ?>,
               <?php echo $MayNum2; ?>,
               <?php echo $JunNum2;?>,
               <?php echo $JulyNum2 ?>,
               <?php echo $AugNum2; ?>,
              <?php  echo $SepNum2?>,
              <?php  echo $OctNum2;?>,
			                <?php  echo $NovNum2?>,
              <?php  echo $DecNum2;?>,
              
              <?php }else if($TypeShop=="MONTH"){ ?>
              
              
               <?php echo $Days1; ?>,
               <?php echo $Days2; ?>,
               <?php echo $Days3; ?>,
               <?php echo $Days4; ?>,
               <?php echo $Days5; ?>,
               <?php echo $Days6;?>,
               <?php echo $Days7 ?>,
               <?php echo $Days8; ?>,
               <?php echo $Days9?>,
               <?php echo $Days10;?>,
               <?php echo $Days11; ?>,
               <?php echo $Days12; ?>,
               <?php echo $Days13; ?>,
               <?php echo $Days14; ?>,
               <?php echo $Days15; ?>,
               <?php echo $Days16;?>,
               <?php echo $Days17 ?>,
               <?php echo $Days18; ?>,
               <?php echo $Days19?>,
               <?php echo $Days20;?>,
               <?php echo $Days21; ?>,
               <?php echo $Days22; ?>,
               <?php echo $Days23; ?>,
               <?php echo $Days24; ?>,
               <?php echo $Days25; ?>,
               <?php echo $Days26;?>,
               <?php echo $Days27 ?>,
               <?php echo $Days28; ?>,
               <?php echo $Days29?>,
               <?php echo $Days30;?>,
               <?php echo $Days31;?>,
                        
              
              
              
              <?php } ?> 
              
               <?php // echo $NovNum2;?>,
               <?php // echo $DecNum2; ?>
                       /*randomScalingFactor(),
                       randomScalingFactor(),
                       randomScalingFactor(),
                       randomScalingFactor(),
                       randomScalingFactor(),
                       randomScalingFactor(),
                       randomScalingFactor()*/
                    ],
                 }, {
                    label: 'Store Premium',
                    backgroundColor: '#5250F9',
                    borderColor: '#5250F9' ,
                    fill: false,
                    data: [
             <?php if($TypeShop==""||$TypeShop=="YEAR"){ ?>              
                <?php echo $junNum1; ?>,
               <?php echo $febNum1; ?>,
               <?php echo $MarchNum1; ?>,
               <?php echo $AprilNum1; ?>,
               <?php echo $MayNum1; ?>,
               <?php echo $JunNum1;?>,
               <?php echo $JulyNum1 ?>,
               <?php echo $AugNum1; ?>,
               <?php  echo $SepNum1?>,
               <?php  echo $OctNum1;?>,
               <?php  echo $NovNum1;?>,
               <?php  echo $DecNum1; ?>
               
                <?php }else if($TypeShop=="MONTH"){ ?>
              
              
              
              
              
               <?php echo $Day1; ?>,
               <?php echo $Day2; ?>,
               <?php echo $Day3; ?>,
               <?php echo $Day4; ?>,
               <?php echo $Day5; ?>,
               <?php echo $Day6;?>,
               <?php echo $Day7 ?>,
               <?php echo $Day8; ?>,
               <?php echo $Day9?>,
               <?php echo $Day10;?>,
               <?php echo $Day11; ?>,
               <?php echo $Day12; ?>,
               <?php echo $Day13; ?>,
               <?php echo $Day14; ?>,
               <?php echo $Day15; ?>,
               <?php echo $Day16;?>,
               <?php echo $Day17 ?>,
               <?php echo $Day18; ?>,
               <?php echo $Day19?>,
               <?php echo $Day20;?>,
               <?php echo $Day21; ?>,
               <?php echo $Day22; ?>,
               <?php echo $Day23; ?>,
               <?php echo $Day24; ?>,
               <?php echo $Day25; ?>,
               <?php echo $Day26;?>,
               <?php echo $Day27 ?>,
               <?php echo $Day28; ?>,
               <?php echo $Day29?>,
               <?php echo $Day30;?>,
               <?php echo $Day31;?>,
              
              
                        
              
              
              
              <?php } ?> 
               
               
               
               
                    ],
                 }, {


                    label: 'Not Partners',
                    backgroundColor: '#232360',
                    borderColor: '#232360' ,
                    fill: false,
                    data: [
             
             
                          <?php if($TypeShop==""||$TypeShop=="YEAR"){ ?>              

                        
               <?php echo $junNum3; ?>,
               <?php echo $febNum3; ?>,
               <?php echo $MarchNum3; ?>,
               <?php echo $AprilNum3; ?>,
               <?php echo $MayNum3; ?>,
               <?php echo $JunNum3;?>,
               <?php echo $JulyNum3 ?>,
               <?php echo $AugNum3; ?>,
               <?php  echo $SepNum3?>,
               <?php  echo $OctNum3;?>,
               <?php  echo $NovNum3;?>,
               <?php  echo $DecNum3; ?>
                    //   90,
                    //   120,
                    //   90,
                    //   40
                    
                <?php }else if($TypeShop=="MONTH"){ ?>
                
                
                <?php echo $Daysw1; ?>,
               <?php echo $Daysw2; ?>,
               <?php echo $Daysw3; ?>,
               <?php echo $Daysw4; ?>,
               <?php echo $Daysw5; ?>,
               <?php echo $Daysw6;?>,
               <?php echo $Daysw7 ?>,
               <?php echo $Daysw8; ?>,
               <?php echo $Daysw9?>,
               <?php echo $Daysw10;?>,
               <?php echo $Daysw11; ?>,
               <?php echo $Daysw12; ?>,
               <?php echo $Daysw13; ?>,
               <?php echo $Daysw14; ?>,
               <?php echo $Daysw15; ?>,
               <?php echo $Daysw16;?>,
               <?php echo $Daysw17 ?>,
               <?php echo $Daysw18; ?>,
               <?php echo $Daysw19?>,
               <?php echo $Daysw20;?>,
               <?php echo $Daysw21; ?>,
               <?php echo $Daysw22; ?>,
               <?php echo $Daysw23; ?>,
               <?php echo $Daysw24; ?>,
               <?php echo $Daysw25; ?>,
               <?php echo $Daysw26;?>,
               <?php echo $Daysw27 ?>,
               <?php echo $Daysw28; ?>,
               <?php echo $Daysw29?>,
               <?php echo $Daysw30;?>,
               <?php echo $Daysw31;?>,
                
                
                
                   <?php } ?> 
                 
                    
                    ],
              
                 }]
              },
              options: {
                  
                 title: {
                    display: true,
                    text: ''
                 },
                 scales: {
                    xAxes: [{
                       display: true,
                  scaleLabel: {
                    display: true,
                    labelString: ' '
                  },
                 
                    }],
                    yAxes: [{
                       display: true,
                       //type: 'logarithmic',
                  scaleLabel: {
                             display: true,
                             labelString: ''
                          },
                          ticks: {
                             min: 0,
                            <?php if($TypeShop==""||$TypeShop=="YEAR"){ ?>
 
                     max: 4000,
                     

                     // forces step size to be 5 units
                     stepSize: 400
                     
                    <?php }else if($TypeShop=="MONTH"){ ?> 
                    
                     max: 4000,
                     

                     // forces step size to be 5 units
                     stepSize: 400
                    
                    
                    <?php } ?>
                          }
                    }],

                   /* zAxes: [{
                       display: true,
                  scaleLabel: {
                             display: true,
                             labelString: ''
                          },
                          ticks: {
                             min: 0,
                             max: 500,
         
                             // forces step size to be 5 units
                             stepSize: 100
                          }
                 
                    }],*/
                 }
              }
           };
         
           window.onload = function() {
              var ctx = document.getElementById('driversChart').getContext('2d');
              window.myLine = new Chart(ctx, config);
           };
         
           document.getElementById('randomizeData').addEventListener('click', function() {
              config.data.datasets.forEach(function(dataset) {
                 dataset.data = dataset.data.map(function() {
                    return randomScalingFactor();
         
                 });
         
              });
         
              window.myLine.update();
           });
         });
         
         
         
         
      </script>
   </body>
</html>