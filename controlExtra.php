<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title> Add Category | Jibler Dashboard </title>
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
	  
	  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
		
		<?php 
		
		header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');
		
		?>
			  
	  
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
                 <ul>
                     <li> <a href="index.php"> <img src="images/nav-icon1.png" height="22px"> <span> Dashboard </span> </a> </li>
                     <li> <a href="user.php"> <img src="images/nav-icon2.png" height="22px"> <span> Users </span>  </a> </li>
                     <li> <a href="driver.php"> <img src="images/nav-icon3.png" height="35px"> <span> Drivers </span>  </a> </li>
                     <li  class="active"> <a href="shop.php"> <img src="images/nav-icon4.png" height="25px"> <span> Shop </span> </a> </li>
                     <li> <a href="orders.php"> <img src="images/nav-icon5.png" height="24px"> <span> Orders </span> </a> </li>
                     <li> <a href="wallet.php"> <img src="images/nav-icon6.png" height="23px"> <span> Wallet </span> </a> </li>
                     <li> <a href="apps.php"> <img src="images/nav-icon7.png" height="24px">  <span> Apps </span> </a> </li>
                     <li> <a href="notifications.php"> <img src="images/nav-icon8.png" height="25px">  <span> Notifications </span> </a> </li>
                     <li> <a href="settings-profile.php"> <img src="images/nav-icon9.png" height="26px"> <span> Settings </span> </a> </li>
                     <li class="logout-list"> <a href=""> <img src="images/nav-icon10.png" height="26px"> <span> Logout </span> </a> </li>
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
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
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
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
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
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
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
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
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
                  <div class="row m-b-20 m-t-30">
                     <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                        <div class="title-text1">
                           <h4 class="col-black"> Category </h4>
                        </div>
                     </div>
                  </div>
                  
                  <?php                           
                  require "conn.php";
                  $prodid = $_GET["prodid"]; ?>
                  
                  <div class="row">
                     <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                        <div class="custom-block1 block-element2 m-b-30">
                           <div class="block-element m-t-20 m-b-20">
                              <div class="form-4 m-b-20">
                                 <form action="AddCatExtraApi.php" method="POST">
                                    <div class="row category-adding">
                                       <div class="col-md-9 col-lg-9 col-sm-9 col-12">
                                          <input type="text" class="field-style2" placeholder="Add Category" name="ExtraCategoryName">
                                          <input type="hidden" class="field-style2" placeholder="Add Category" name="prodid" value="<?php echo $prodid ?>">
                                       </div>
                                       <div class="col-md-3 col-lg-3 col-sm-3 col-12">
                                          <input type="submit" class="submit-btn2" value="ADD" name="">
                                       </div>
                                    </div>
                                 </form>
                              </div>
                              <div class="block-element2">
                                 <div class="table-wrapper">
                                    <table class="table-3">
                                       <tbody>
                                           
                                           
                                           <?php 
                            require "conn.php";
                            $prodid = $_GET["prodid"];
			    $ShopsCategory = 'ShopsCategory';

				$var = rand(50,150);
                              $res = mysqli_query($con,"SELECT * FROM ExtraCategory WHERE ProductID='$prodid'");
                            
                            $result = array();

                            $OrderPriceFromShop = 0;
                                
                            while($row = mysqli_fetch_assoc($res)){
                                
                                
                                // $OrderPriceFromShop =  $row["OrderPriceFromShop"];
                                // $CreatedAtOrders    = $row["CreatedAtOrders"];
                                // $OrderID            = $row["OrderID"];
                                
                                
                                ?> 
                                           
                                          <tr>
                                             <td> <span class="category-name"> <?php echo $row["ExtraCategoryName"] ?> </span> </td>
                                             <td class="text-center"> <button class="category-name" style="backgound:white;"><a href="DeleteCatExtraShopApi.php?id=<?php echo $row["ExtraCategoryID"] ?>&prodid=<?php echo $prodid ?>"> Delete </a></button> </td>
											 <td class="text-center"> <button class="category-name" style="backgound:white;"><a href="ControlExtraValues.php?id=<?php echo $row["ExtraCategoryID"] ?>&prodid=<?php echo $prodid ?>"> Control Extra </a></button> </td>

                                          </tr>
                                <?php } ?>          
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                        <div class="custom-image1">
                           <img src="images/category-graphics.png">
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
   </body>
</html>