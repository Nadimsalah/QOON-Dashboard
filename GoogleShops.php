<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title> Products | Jibler Dashboard </title>
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
                     <li> <a href="index.php"> <img src="images/nav-icon1.png" height="22px"> <span> Dashboard </span> </a> </li>
                     <li> <a href="user.php"> <img src="images/nav-icon2.png" height="22px"> <span> Users </span>  </a> </li>
                     <li> <a href="driver.php"> <img src="images/nav-icon3.png" height="35px"> <span> Drivers </span>  </a> </li>
                     <li  class="active"> <a href="shop.php"> <img src="images/nav-icon4.png" height="25px"> <span> Shop </span> </a> </li>
                     <li> <a href="orders.php"> <img src="images/nav-icon5.png" height="24px"> <span> Orders </span> </a> </li>
                     <li> <a href="wallet.html"> <img src="images/nav-icon6.png" height="23px"> <span> Wallet </span> </a> </li>
                     <li> <a href="apps.html"> <img src="images/nav-icon7.png" height="24px">  <span> Apps </span> </a> </li>
                     <li> <a href="notifications.html"> <img src="images/nav-icon8.png" height="25px">  <span> Notifications </span> </a> </li>
                     <li> <a href="settings-profile.html"> <img src="images/nav-icon9.png" height="26px"> <span> Settings </span> </a> </li>
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
                           <h4 class="col-black"> GoogleShops </h4>
                        </div>
                     </div>
                  </div>

                  <div class="row">

                  <div class="col-md-12 col-lg-12 col-sm-12 col-12">

                  <div class="custom-block1 block-element2">   

                  <div class="block-element m-t-10 m-b-20">
                  <div class="search-form4">
                  <form action="add-product.php?id=<?php echo $id ?>" method="GET">
                     <input class="field-style2" type="text" placeholder="Search" name="">
                     <?php $id = $_GET["id"]; ?>
                     <input type="hidden" name="id" value="<?php echo $id ?>" >
                     <button  class="submit-btn2" name=""><a href="add-GoogleShops.php?id=<?php echo $id ?>" style="color:white;"> Add Shops </a></button>
                  </form>   
                  </div>  
                  </div> 


                  <div class="block-element">
                  <div class="table-wrapper">
                  <table class="table-4">
                  
                  <thead>
                     <tr>
                        <th> Shop Name </th>
                        <th class="text-center"> ID  </th>
                        <th class="text-center"> Creation Date </th>
                        <th class="text-center"> Actions </th>
                     </tr>
                  </thead>

                  <tbody>

                    <?php
                    require "conn.php";
                              $OrdersNumber = 0;
                              $id = $_GET["id"];
                              $OrdersNumberLastweek = 0;

                              
                             
                              
                            $res = mysqli_query($con,"SELECT * FROM `Shops` WHERE googleshophkey !='NO' order by ShopID desc limit 60");
                            
                            $result = array();

                            $OrderPriceFromShop = 0;
                            $Type  = "";
                            while($row = mysqli_fetch_assoc($res)){
                                
                        ?>        
                                


                     <tr>
                        <td class="image-col2"> <img src="<?php echo $row["ShopLogo"]; ?>" style="width=150px;height:100px;border-radius: 20%;"> <span>  <?php echo $row["ShopName"]; ?> </span> </td>
                        <td class="text-center"> <?php echo $row["ShopID"]; ?>   </td>
                        <td class="text-center"> <?php echo $row["CreatedAtShops"]; ?> </td>
                        <td class="text-center action-col">  
                           <a href="shop-profile.php?id=<?php echo $row["ShopID"] ?>&shopid=<?php echo $id?>" class="bg-blue1"> <i class="fa fa-eye"> </i> </a>
                           
                         </td>
                     </tr>
                     
                     
                     <?php 
                     
                            }
                     ?>

                     <!--<tr>-->
                     <!--   <td class="image-col2"> <img src="images/dish-2.png"> <span>  Pizza supper </span> </td>-->
                     <!--   <td class="text-center"> 66.87 MAD  </td>-->
                     <!--   <td class="text-center"> 2022-05-11 22:38:11 </td>-->
                     <!--   <td class="text-center action-col">  -->
                     <!--      <a href="" class="bg-blue1"> <i class="fa fa-eye"> </i> </a>-->
                     <!--      <a href="" class="bg-green1"> <i class="fa fa-pencil-alt"> </i> </a>-->
                     <!--      <a href="" class="bg-red1"> <i class="fa fa-trash "> </i> </a>-->
                     <!--    </td>-->
                     <!--</tr>-->


                     <!--<tr>-->
                     <!--   <td class="image-col2"> <img src="images/dish-3.png"> <span>  Pizza supper </span> </td>-->
                     <!--   <td class="text-center"> 66.87 MAD  </td>-->
                     <!--   <td class="text-center"> 2022-05-11 22:38:11 </td>-->
                     <!--   <td class="text-center action-col">  -->
                     <!--      <a href="" class="bg-blue1"> <i class="fa fa-eye"> </i> </a>-->
                     <!--      <a href="" class="bg-green1"> <i class="fa fa-pencil-alt"> </i> </a>-->
                     <!--      <a href="" class="bg-red1"> <i class="fa fa-trash "> </i> </a>-->
                     <!--    </td>-->
                     <!--</tr>-->


                     <!--<tr>-->
                     <!--   <td class="image-col2"> <img src="images/dish-4.png"> <span>  Pizza supper </span> </td>-->
                     <!--   <td class="text-center"> 66.87 MAD  </td>-->
                     <!--   <td class="text-center"> 2022-05-11 22:38:11 </td>-->
                     <!--   <td class="text-center action-col">  -->
                     <!--      <a href="" class="bg-blue1"> <i class="fa fa-eye"> </i> </a>-->
                     <!--      <a href="" class="bg-green1"> <i class="fa fa-pencil-alt"> </i> </a>-->
                     <!--      <a href="" class="bg-red1"> <i class="fa fa-trash "> </i> </a>-->
                     <!--    </td>-->
                     <!--</tr>-->

                  </tbody>

                  <tfoot>
                     <tr>
                        <td colspan="4" class="text-right"> 
                           <a href="" class="previous-btn"> Previous  </a>
                            <a href="" class="next-btn"> Next  </a>
                            </td>
                     </tr>
                  </tfoot>

                  </table>   
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
   </body>
</html>