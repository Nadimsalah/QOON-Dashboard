<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title> Wallet | Jibler Dashboard </title>
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
	  <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
      <style type="text/css">
         .custom-box1-head {
         flex-flow: column;
         }
         .custom-box1-head h4 {
         width: 100%;
         text-align: center;
         margin-bottom: 10px;
         }
      </style>
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
                     <li > <a href="shop.php"> <img src="images/nav-icon4.png" height="25px"> <span> Shop </span> </a> </li>
                     <?php } ?>
                     <?php if($OrdersPage==1){ ?>
                     <li> <a href="orders.php"> <img src="images/nav-icon5.png" height="24px"> <span> orders </span> </a> </li>
                     <?php } ?>
                     <?php if($WalletPage==1){ ?>
                     <li class="active"> <a href="wallet.php"> <img src="images/nav-icon6.png" height="23px"> <span> wallet </span> </a> </li>
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
                              <input type="text" placeholder="Search anything..." name="">
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
                                    <a class="dropdown-item" href="orders.php"> All Cities</a>

                                    <?php                 require "conn.php";

                                    
                                     $res = mysqli_query($con,"SELECT * FROM DeliveryZone");

                                        $result = array();
                        
                                        while($row = mysqli_fetch_assoc($res)){
                                    
                                    ?> 
                                     
                                     
                                    <a class="dropdown-item" href="wallet.php?CityName=<?php echo $row["CityName"]; ?>&cityID=<?php echo $row["DeliveryZoneID"]; ?>"><?php echo $row["CityName"]; ?></a>
                                    
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
 
				  
				  
				   <?php
                                    require "conn.php";
                                        $res = mysqli_query($con,"select sum(OrderPriceFromShop) from Orders");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$sum = $row["sum(OrderPriceFromShop)"];
								
							}
							
							
					?>		
					
					
					 <?php
                              require "conn.php";
                            $res = mysqli_query($con,"select sum(OrderPrice) from Orders");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$OrderPrice = $row["sum(OrderPrice)"];
								
							}
							
							
					?>	


					<?php
                              require "conn.php";
                            $res = mysqli_query($con,"select sum(Money) from DriverTransactions");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$Money = $row["sum(Money)"];
								
							}
							
							
					?>		
					
				  
				  
                  <div class="row">
                     
                     
                    
                    
                  </div>
                  <div class="row" >
                     <div class="col-md-6 col-lg-7 col-sm-12 col-12" style="width:100%;">
                        <div class="custom-block3">
                           <div class="block-element">
						      <div class="form-4 m-b-20">
                                 <form action="BoostPricesApi.php" method="POST">
                                    <div class="row category-adding">
                                       <div class="col-md-9 col-lg-5 col-sm-9 col-12">
                                          <input type="number" class="field-style2" placeholder="Days" name="DDay">
                                       </div>
									   <div class="col-md-9 col-lg-4 col-sm-9 col-12">
                                          <input type="number" class="field-style2" placeholder="Prices" name="Price">
                                       </div>
                                       <div class="col-md-3 col-lg-3 col-sm-3 col-12">
                                          <input type="submit" class="submit-btn2" value="ADD" name="">
                                       </div>
                                    </div>
                                 </form>
                              </div>
						   
                              <div class="sec-head3">
							  
                                 <h4> Prices </h4>
                                 <div class="search-form2">
                                    <form style="display:none;">
                                       <input type="text" placeholder="Search by Name Phone  Email Id" name="">   
                                       <i class="fa fa-search"> </i>
                                    </form>
                                 </div>
                                 <button style="display:none;" class="custom-btn1"> See all </button>
                              </div>
                           </div>
						   
                           <div class="table-wrapper">
						   
                              <table class="table-1" id="tbl_exporttable_to_xls">
                                 <thead>
                                    <tr>
										<th class="text-center"> Price </th>          
                                       <th class="text-center">  Day</th>
									   <th class="text-center">  Status</th>
                                       <th class="text-center"> Control </th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                     <?php
                                    require "conn.php";
                                        $res = mysqli_query($con,"SELECT * FROM BoostPrices order by BoostPricesID desc");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
                                
                                
                                    $Price = $row["Price"];
                                    $DDay   = $row["DDay"];
									$BoostPricesStatus   = $row["BoostPricesStatus"];
									$BoostPricesID   = $row["BoostPricesID"];
                                    
            
                                ?>
                                    <tr>
                                       <td class="status-col1 text-center"> <?php echo $Price; ?> </td> 
                                       <td class="col-grey1 text-center"> <?php echo $DDay; ?>  </td>
									   <td class="col-grey1 text-center"> <?php echo $BoostPricesStatus; ?>  </td>
                                       <td class="status-col1 text-center">
									   <center> 
											<?php if($BoostPricesStatus=="ACTIVE"){ ?>
												<button  class="submit-btn2"  ><a href="changeStatusBoostPrice.php?BoostPricesStatus=NOTACTIVE&BoostPricesID=<?php echo $BoostPricesID ?>" style="color:white;"> DeActive</a></button> 
											<?php }else{ ?>
												<button  class="submit-btn2" style="background:red;"><a href="changeStatusBoostPrice.php?BoostPricesStatus=ACTIVE&BoostPricesID=<?php echo $BoostPricesID ?>" style="color:white;">  Active </a></button>
											<?php } ?>	
									   </center> </td>
                                    </tr>
                                 <?php 
                                
                                
                            } 
                            
                            ?>        
                                 </tbody>
                              </table>
                           </div>
                        </div>
                        <div class="block-element m-b-40">
                           <div class="custom-pagination">
                              <h5> Showing 1 to 10 of 1,384,485 entries </h5>
                              <ul>
                                 <li> <a href=""> <i class="fa fa-angle-left"> </i> </a> </li>
                                 <li> <a href="" class="active"> 1 </a> </li>
                                 <li> <a href=""> <i class="fa fa-angle-right"> </i> </a> </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-6 col-lg-4 col-sm-12 col-12" style="display:none;">
                        <div class="custom-block1">
                           <div class="block-element">
                              <div class="sec-head1">
                                 <h4 class="col-green1"> Profiles </h4>
                                 <select>
                                    <option> Date </option>
                                    <option> Date </option>
                                    <option> Date </option>
                                 </select>
                              </div>
                           </div>
                           <div class="block-element">
                              <div class="profit-block profit-bg1">
                                 <img src="images/profit-icon1.png">
                                 <h6 class="col-green2"> Subscriptions </h6>
                                 <h4> 0 MAD </h4>
                              </div>
                              <div class="profit-block profit-bg2">
                                 <img src="images/profit-icon2.png">
                                 <h6 class="col-green3"> Commissions </h6>
                                 <h4> 0 MAD </h4>
                              </div>
                              <div class="profit-block profit-bg3">
                                 <img src="images/profit-icon3.png">
                                 <h6 class="col-purple1"> Jibler ADS </h6>
                                 <h4> 0 MAD </h4>
                              </div>
                              <div class="profit-block profit-bg4">
                                 <img src="images/profit-icon4.png">
                                 <h6 class="col-purple2"> Jibler Pay </h6>
                                 <h4> 0 MAD </h4>
                              </div>
                              <div class="profit-block profit-bg5">
                                 <img src="images/profit-icon5.png">
                                 <h6 class="col-red1"> Total </h6>
                                 <h4> 0 MAD </h4>
                              </div>
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
	  
	 <script>

        function ExportToExcel(type, fn, dl) {
            var elt = document.getElementById('tbl_exporttable_to_xls');
            var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
            return dl ?
                XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }) :
                XLSX.writeFile(wb, fn || ('ShopsRequestMoney.' + (type || 'xlsx')));
        }

    </script>

	  
	  
   </body>
</html>