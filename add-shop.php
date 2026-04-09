<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title> Add Shop | Jibler Dashboard </title>
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
                 <ul>
                     <li > <a href="index.php"> <img src="images/nav-icon1.png" height="22px"> <span> Dashboard </span> </a> </li>
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

        <form class="form-horizontal" enctype="multipart/form-data" method="POST" action="AddShopAPI.php">

               <div class="container">
                  <div class="row m-b-20 m-t-30">
                     <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                        <div class="title-text1">
                           <h4 class="col-black"> Add New Shop </h4>
                        </div>
                     </div>
                  </div>

                  <div class="row center-row1">
                     <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                        <div class="custom-block1 block-element2 m-b-30">
                            
                           <div class="block-element">
                              <div class="form-2">
                                 <div class="row">
                                 <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                                  <div class="image-uploader1 image-uploader2">
                                          <div class="avatar-upload">
                                             <div class="avatar-edit">
                                                <input type='file' id="imageUpload2" type="file" name="Photo2" accept=".png, .jpg, .jpeg" />
                                                <label for="imageUpload2"></label>
                                             </div>
                                             <div class="avatar-preview">
                                                <div id="imagePreview2" style="background-image: url(jibler.png);" name=''>
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                 </div>   
                                 </div>

                                 <div class="row center-row1 shop-avatar-row">
                                    <div class="col-md-8 col-lg-8 col-sm-12 col-12 order-lg-2 order-md-2 order-sm-2 order-2">
                                       <div class="form-field1 m-t-20">
                                          <input type="text" placeholder="NAME" name="ShopName"  class="field-style1" >
                                       </div>
                                      
                                    </div>
                                    <div class="col-md-4 col-lg-4 col-sm-12 col-12 order-lg-1 order-md-1 order-1 order-sm-1">
                                       <div class="image-uploader1 image-uploader3">
                                          <div class="avatar-upload">
                                             <div class="avatar-edit">
                                                <input type='file' id="imageUpload" name="Photo" accept=".png, .jpg, .jpeg" />
                                                <label for="imageUpload"></label>
                                             </div>
                                             <div class="avatar-preview" style="border:none">
                                                <div id="imagePreview" style="background-image: url(jibler.png);">
                                                </div>
                                             </div>
                                          </div>
                                       </div>
                                    </div>
                                 </div>

                                  <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                                       <div class="form-field1">
                                           <select name="CategoryID"  class="field-style1">
                                              <?php
                                                require "conn.php";
                                                $pass="a";
                                                $res = mysqli_query($con,"SELECT * FROM Categories");
                                
                                                $result = array();
                                
                                                while($row = mysqli_fetch_assoc($res)){
                                
                                                    ?>
                                                <option value="<?php echo $row["CategoryId"] ?>"><?php echo $row["EnglishCategory"] ?></option>
                                              <?php } ?>
                                            
                                        </select>
                                       </div>
                                    </div>
                                 </div>
                                 
                                 <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                                       <div class="form-field1">
                                           <select name="Type"  class="field-style1">
										   <option value="Ourplus"> Store Premium Plus</option>
                                             <option value="Our"> Store Premium</option>
                                            <option value="Other">Not Partner</option>
                                            <option value="Other"> Start Plus</option>
                                            
                                        </select>
                                       </div>
                                    </div>
                                 </div>
                                 
                                

                                 <!--  <div class="row">-->
                                 <!--   <div class="col-md-12 col-lg-12 col-sm-12 col-12 m-b-20">-->
                                 <!--    <div class="checkbox-parent2 shop-selection">-->
                                 <!--   <label> <input type="checkbox" name=""> Not Partner </label>-->
                                 <!--   <label> <input type="checkbox" name=""> Start Plus </label>-->
                                 <!--   <label> <input type="checkbox" name=""> Store Premium </label>-->
                                 <!--    </div>-->
                                 <!--   </div>-->
                                 <!--</div>-->
								 <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                                       <div class="form-field1">
                                           <select name="CityID"  class="field-style1">
                                              <?php
                                                $pass="a";
                                                $res = mysqli_query($con,"SELECT * FROM DeliveryZone");
                                
                                                $result = array();
                                
                                                while($row = mysqli_fetch_assoc($res)){
                                
                                                    ?>
                                                <option value="<?php echo $row["DeliveryZoneID"] ?>"><?php echo $row["CityName"] ?></option>
                                              <?php } ?>
                                            
                                        </select>
                                       </div>
                                    </div>
                                 </div>
                                 
                                 <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                                       <div class="form-field1">
                                          <input type="text"   name="ShopPhone" placeholder="Shop Phone" class="field-style1" name="">
                                       </div>
                                    </div>
                                 </div>
                                 <div class="row" style="display:none;">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                                       <div class="form-field1">
                                          <input type="text"  name="ShopLatPosition" placeholder=" like 32.3333,7.2323" step="0.000000001" class="field-style1" >
                                       </div>
                                    </div>
                                 </div>
                                 
                                 <div class="row">
                                    <div class="col-md-6 col-lg-6 col-sm-12 col-12">
                                       <div class="form-field1">
                                          <input type="text"    placeholder="Login" class="field-style1" name="ShopLoginName">
                                       </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-sm-12 col-12">
                                       <div class="form-field1">
                                          <input type="password"   placeholder="Password" class="field-style1" name="ShopLoginPassword">
                                       </div>
                                    </div>
                                 </div>

                                 <div class="row">
                                    <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                                       <div class="block-element m-t-20 m-b-30">
                                          <button class="submit-btn1"> Add </button>   
                                       </div>
                                    </div>
                                 </div>
                                  
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                       <div class="custom-image1">
                     <img src="images/shop-graphics.png">
                       </div>
                     </div>
                  </div>
                  </form>
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