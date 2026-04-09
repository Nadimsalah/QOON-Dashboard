<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title> Users | Jibler Dashboard </title>
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
               <div class="custom-menu">
                   
                   
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
                   
                   
                   
                   
                  <ul>
                     <li > <a href="index.php"> <img src="images/nav-icon1.png" height="22px"> <span> Dashboard </span> </a> </li>
                     <?php if($Userspage==1){ ?>
                     <li class="active"> <a href="user.php"> <img src="images/nav-icon2.png" height="22px"> <span> Users </span>  </a> </li>
                     <?php } ?>
                     <?php if($DriversPage==1){ ?>
                     <li> <a href="driver.php"> <img src="images/nav-icon3.png" height="35px"> <span> Drivers </span>  </a> </li>
                     <?php } ?>
                     <?php if($ShopsPage==1){ ?>
                     <li> <a href="shop.php"> <img src="images/nav-icon4.png" height="25px"> <span> Shop </span> </a> </li>
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
                              <input type="text" placeholder="Search User name..." name="UserName">
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
                                 All Cities
                                 <i class="fa fa-angle-down"> </i>
                                 </button>
                                  <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                     
                                     <a class="dropdown-item" href="index.php"> All Cities</a>

                                    <?php                 require "conn.php";

                                    
                                     $res = mysqli_query($con,"SELECT * FROM DeliveryZone");

                                        $result = array();
                        
                                        while($row = mysqli_fetch_assoc($res)){
                                    
                                    ?> 
                                     
                                     
                                    <a class="dropdown-item" href="users.php?CityName=<?php echo $row["CityName"]; ?>&cityID=<?php echo $row["DeliveryZoneID"]; ?>"><?php echo $row["CityName"]; ?></a>
                                    
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
            
            
              <?php 
                                       
                                       require "conn.php";

                $pass="a";
                $lastweek = date('Y-m-d',strtotime("-7 days"));
                
                $UserLastWeeks = 0;
                $DriverLastWeeks = 0;
                $ShopsLastWeeks = 0;


						$first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
						$last_day_this_month  = date('Y-m-t');


				$res = mysqli_query($con,"SELECT COUNT(*) FROM Users WHERE lastUpdatedUsers > '$first_day_this_month' AND lastUpdatedUsers < '$last_day_this_month'");

				$ActiveUsers = '';
                $result = array();

                while($row = mysqli_fetch_assoc($res)){
					
					
					$ActiveUsers = $row["COUNT(*)"];
				}

                $test=0;
                $UserNumber = 0;
                $res = mysqli_query($con,"SELECT * FROM Users");

                $result = array();
                
                
                $les18  = 0;
                $les25  = 0;
                $les40  = 0;
                $more40 = 0;

                while($row = mysqli_fetch_assoc($res)){


                    $test=4;
                    $UserNumber++;
                    
                    if($lastweek<$row["CreatedAtUser"]){
                        
                        $UserLastWeeks++;
                    }
                    
                    
                    
                      $dateOfBirth = $row["BirthDate"];
                      $today = date("Y-m-d");
                      $diff = date_diff(date_create($dateOfBirth), date_create($today));
                       $x = $diff->format('%y');
                       
                       
                       if($x<18){
                           
                           $les18++;
                       }else if($x<25){
                           $les25++;
                       }else if($x<40){
                           $les40++;
                       }else{
                           $more40++;
                       }
                   
                                        

                }
                
                
                $OrdersNumber = 0;
                $res = mysqli_query($con,"SELECT * FROM Orders");

                $result = array();

                while($row = mysqli_fetch_assoc($res)){


                    $test=4;
                    $OrdersNumber++;
                    
                    if($lastweek<$row["CreatedAtOrders"]){
                        
                        $OrdersLastWeeks++;
                    }

                }
                
                                       
                                       
                                       ?>
                                       
            
            
            <!-- Top Bar Section Starts Here -->
            <!-- Main Content Section Starts Here -->
            <section class="main-content">
               <div class="container">
                  <div class="row" style="width:100%">
                     <div  style="width:100%">
                        <div class="custom-block3" >
                           <div class="block-element">
                              <div class="sec-head3">
                                 <h4> Users </h4>
                                 <div class="search-form2" style="display:none;">
                                    <form>
                                       <input type="text" placeholder="Search by Name Phone  Email Id" name="">   
                                       <i class="fa fa-search"> </i>
                                    </form>
                                 </div>
                                 <button class="custom-btn1"  style="display:none;"> See all </button>
                              </div>
                           </div>
                           <div class="table-wrapper" >
                              <table class="table-1">
                                 <thead>
                                    <tr>
                                       <th> Name </th>
                                       <th> Jibler ID </th>
									   <th> PhoneNumber </th>
									   <th> Email </th>
									   <th> UserOrdersNum </th>
									   <th> AccountType</th>
                                       <!--<th> Rating </th>-->
                                    </tr>
                                 </thead>
                                 <tbody>
                                     
                                    <?php 
                                    
                                    $UserName = $_GET["UserName"];
                                    $Page = $_GET["Page"];
                                    if($Page==""){
                                        $Page = 0;
                                    }
                                    $rr = 10 * $Page;
                                    if($UserName==''){
                                        
                                        
                                        
                                        $res = mysqli_query($con,"SELECT * FROM Users WHERE name !='' order by UserOrdersNum desc limit $rr, 10");
										
										
						$first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
						$last_day_this_month  = date('Y-m-t');


				$res = mysqli_query($con,"SELECT * FROM Users WHERE lastUpdatedUsers > '$first_day_this_month' AND lastUpdatedUsers < '$last_day_this_month' order by UserOrdersNum desc limit $rr, 10");
										
										
                                    }else{
                                        
                                        
                                        
                                        $res = mysqli_query($con,"SELECT * FROM Users WHERE name LIKE '%$UserName%' order by UserOrdersNum desc limit $rr, 10");
										
										
										$first_day_this_month = date('Y-m-01'); // hard-coded '01' for first day
						$last_day_this_month  = date('Y-m-t');


				$res = mysqli_query($con,"SELECT * FROM Users WHERE lastUpdatedUsers > '$first_day_this_month' AND lastUpdatedUsers < '$last_day_this_month' AND name LIKE '%$UserName%' order by UserOrdersNum desc limit $rr, 10");
										
                                    }

                                    $result = array();
                    
                                    while($row = mysqli_fetch_assoc($res)){

                                    
                                    $UserPhoto = $row["UserPhoto"];
                                    $name   = $row["name"];
                                    $UserID = $row["UserID"];
									$PhoneNumber = $row["PhoneNumber"];
									$Email  = $row["Email"]; 
									$UserOrdersNum = $row["UserOrdersNum"];
									$AccountType    = $row["AccountType"];
                                    
                                    ?>
                                     
                                    <tr>
                                       <td class="image-col col-blue1"> <img src="<?php if($UserPhoto!=''){ echo $UserPhoto;}else { echo 'images/ensan.jpg';}  ?>" style = 'border-radius: 50%;width:30px;height:30px;'> <a href="user-profile.php?id=<?php echo $UserID ?>"> <?php echo $name; ?> </a> </td>
                                       <td class="col-grey1"> <?php echo $UserID ?> </td>
									   <td class="col-grey1"> <?php echo $PhoneNumber ?> </td>
									   <td class="col-grey1"> <?php echo $Email ?> </td>
									   <td class="col-grey1"> <?php echo $UserOrdersNum ?> </td>
									   <td class="col-grey1"> <?php echo $AccountType ?> </td>
                                       <!--<td>-->
                                       <!--   <div class="progress progress-1">-->
                                       <!--      <div class="progress-bar" style="width:90%"></div>-->
                                       <!--   </div>-->
                                       <!--</td>-->
                                    </tr>
                                    
                                    <?php } ?>
                                    
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="block-element m-b-40">
                           <div class="custom-pagination">
                              <h5> Showing <?php echo $Page; ?> to 10 of <?php echo $UserNumber ?> entries </h5>
                              <ul>
                                 <li> <a href="user.php?UserName=<?php echo $UserName ?>&Page=<?php echo $Page-1; ?>"> <i class="fa fa-angle-left"> </i> </a> </li>
                                 <li> <a href="#" class="active"> <?php echo $Page+1 ?> </a> </li>
                                 <li> <a href="user.php?UserName=<?php echo $UserName ?>&Page=<?php echo $Page+1; ?>"> <i class="fa fa-angle-right"> </i> </a> </li>
                              </ul>
                           </div>
                        </div>
                     </div>

                  </div>
				  
				  
				  
				  
				  
				  
				  
				  
				  
				  
               </div>
            </section>
			
			

			
			
			
			
            <!-- Main Content Section Ends Here -->
         </main>
         <!-- Right Section Ends Here -->
      </section>
      <!-- Bootstrap Javascript -->
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
      <script src="js/bootstrap.min.js"> </script>
      <!-- Chart JS -->
      <script src="https://cdn2.hubspot.net/hubfs/476360/Chart.js"></script>
      <script src="https://cdn2.hubspot.net/hubfs/476360/utils.js"></script>
      <script src="js/functions.js"> </script>
      <script type="text/javascript">
         $(document).ready(function(){
         var oilCanvas = document.getElementById("ageChart");
         
         Chart.defaults.global.defaultFontFamily = "DM Sans";
         Chart.defaults.global.defaultFontSize = 10;
         
         var dataset11 = '100%';
         var oilData = {
            labels: [
                "< 18 Years",
                " 18 - 25 Years",
                " 25 - 40 Years ",
                " > 40 Years "
                    ],
            datasets: [
                {
                    data: [<?php echo $les18; ?>, <?php echo $les25; ?>, <?php echo $les40; ?>, <?php echo $more40; ?>],
                    backgroundColor: [
                        "#43B5F4",
                        "#89CFF5",
                        "#B4E2FB",
                        "#D4EDFB"
                    ]
                }]
         };
         
         var pieChart = new Chart(oilCanvas, {
          type: 'pie',
          data: oilData
         });
         
         })
              
      </script>
	  
	  <script type="text/javascript">
         $(document).ready(function(){
         var oilCanvas = document.getElementById("ageChartg");
         
         Chart.defaults.global.defaultFontFamily = "DM Sans";
         Chart.defaults.global.defaultFontSize = 10;
         
         var dataset11 = '100%';
         var oilData = {
            labels: [
                "Man",
                "Women"
                    ],
            datasets: [
                {
                    data: [<?php echo $les18; ?>, <?php echo $les25; ?>],
                    backgroundColor: [
                        "#43B5F4",
                        "#89CFF5"
                    ]
                }]
         };
         
         var pieChart = new Chart(oilCanvas, {
          type: 'pie',
          data: oilData
         });
         
         })
              
      </script>
   </body>
</html>