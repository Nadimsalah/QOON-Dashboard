<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title> Add Product | Jibler Dashboard </title>
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
                 <div class="SecondDivID"></div> 
    <script src=" https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

        <script type="text/javascript">

          function loadhem(){

            $(".SecondDivID").load("leftNav.php?Page=shop.php");

          }
          
          loadhem();

        </script>
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
			
			
			<?php 
			                                                require "conn.php";

			$ProdId = $_GET["ProdId"];
			
			$res = mysqli_query($con,"SELECT * FROM Foods WHERE FoodID = $ProdId");
                                
                                                $result = array();
                                
                                                while($row = mysqli_fetch_assoc($res)){
													
													
													$FoodName = $row["FoodName"];
													$FoodDesc = $row["FoodDesc"];
													$FoodCatID = $row["FoodCatID"];
													$FoodPrice = $row["FoodPrice"];
													$FoodOfferPrice = $row["FoodOfferPrice"];
													$FoodPhoto = $row["FoodPhoto"];
													
												}
                                
                                
            ?>
			
			
			
			
            <section class="main-content">
               <form method="POST" action="UpdateProductAPI.php" enctype="multipart/form-data" class="container">
                   
                   
                                     <input type="hidden" placeholder="ProdId" value="<?php echo $ProdId; ?>" class="field-style1" name="ProdId">

                  <div class="row m-b-20 m-t-30">
                     <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                        <div class="title-text1">
						<h4 class="col-black"><a href="controlExtra.php?prodid=<?php echo $ProdId ?>"> Control Extra </a></h4>
                           <h4 class="col-black"> Update Product </h4>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                        <div class="custom-block1 block-element2 m-b-30">
                           <div class="block-element m-t-20 m-b-20">
                           
                           <form>

                              <div class="row">

                              <div class="col-md-4 col-lg-4 col-sm-12 col-12">
                              <div class="image-uploader4">
                   <div class="avatar-upload">
        <div class="avatar-edit">
            <input type="file" id="imageUpload"  name="Photo" accept=".png, .jpg, .jpeg">
            
            <label for="imageUpload"></label>
        </div>
        <div class="avatar-preview">
            <div id="imagePreview" style="background-image: url(<?php echo $FoodPhoto; ?>);">
            </div>
        </div>
    </div>
                  </div>   
                              </div>   

                              <div class="col-md-8 col-lg-8 col-sm-12 col-12">
                              <div class="form-field1">
                  <input type="text" placeholder="Product Name" value="<?php echo $FoodName; ?>" class="field-style1" name="ProdName">
                  </div>

                   <div class="form-field1">
                  <textarea style="height: 120px" placeholder="Description" value="" name="Description" class="field-style1"> <?php echo $FoodDesc; ?></textarea>
                  </div>
                              </div>


                              </div>
                              

                             <div class="row">
                  <div class="col-md-6 col-lg-6 col-sm-12 col-12">
                  <div class="form-field1">
                  <input type="text" placeholder="Price" class="field-style1" value="<?php echo $FoodPrice  ; ?>" name="Price">
                  </div>   
                  </div> 

                  <div class="col-md-6 col-lg-6 col-sm-12 col-12">
                  <div class="form-field1">
                  <input type="text" placeholder="Offer Price" class="field-style1" value="<?php echo $FoodOfferPrice; ?>" name="OfferPrice">
                  </div>   
                  </div> 


                  <div class="col-md-6 col-lg-6 col-sm-12 col-12">
                  <!--<div class="form-field1">-->
                  <!--<input type="email" placeholder="Extra" class="field-style1" name="">-->
                  <!--</div>   -->
                  </div> 

                  <!-- <div class="col-md-6 col-lg-6 col-sm-12 col-12">-->
                  <!--<div class="row custom-row1">-->
                  <!--<div class="col-md-9 col-lg-9 col-sm-9 col-8 custom-pad1">-->
                  <!--<div class="form-field1">-->
                  <!--<input type="email" placeholder="Price" class="field-style1" name="">-->
                  <!--</div> -->
                  <!--</div>-->
                  <!--<div class="col-md-3 col-lg-3 col-sm-3 col-4 custom-pad1">-->
                  <!--<input type="submit" class="submit-btn4" value="ADD" name="">-->
                  <!--</div>   -->
                  <!--</div>   -->
                    
                  <!--</div> -->

                  </div> 

                  <!--<div class="row">-->
                  <!--<div class="col-md-12 col-lg-12 col-sm-12 col-12">-->
                  <!--<div class="chosen-categories">-->
                  <!--<div> <span> Coca </span> <a href="" class="close-btn1"> <i class="fa fa-trash"> </i> </a> </div>   -->
                  <!--</div>-->
                  <!--</div>   -->
                  <!--</div>-->

                  <div class="row">
                  <div class="col-md-12 col-lg-12 col-sm-12 col-12">

                  <div class="block-element2">   
                  <div class="title-1">
                  <h4 class="col-black1"> <b> Category </b> </h4>   
                  </div>   
                  </div>

                  <div class="block-element2">
                  <select name="CategoryID"  class="field-style1">
                                              <?php
                                                $pass="a";
                                                $shopid = $_GET["shopid"]; 
                                                $res = mysqli_query($con,"SELECT * FROM ShopsCategory WHERE ShopID = $shopid");
                                
                                                $result = array();
                                
                                                while($row = mysqli_fetch_assoc($res)){
                                
													if($FoodCatID==$row["CategoryShopID"]){
                                                   ?>
                                              
                                              <option value="<?php echo $row["CategoryShopID"] ?>" selected><?php echo $row["CategoryName"] ?> </option>
                                              
                                              <?php  }else{ ?>

                                              <option value="<?php echo $row["CategoryShopID"] ?>"><?php echo $row["CategoryName"] ?> </option>

												<?php } } ?>
                                            
                                        </select>
                                        
                                                                             <input type="hidden" placeholder="shopid" value="<?php echo $shopid; ?>" class="field-style1" name="shopid">

                  </div>
                    <input type="hidden" id="imageUpload"  name="ShopID" value="<?php echo $id ?>" accept=".png, .jpg, .jpeg">
                  </div>
                  
                  
                                     <div class="col-md-12 col-lg-12 col-sm-12 col-12">
                                       <div class="block-element m-t-20 m-b-30">
                                          <button class="submit-btn1"> Add </button>   
                                       </div>
                                    </div>
                  
                  </div> 


                           </form>
                            
                           </div>
                        </div>
                     </div>
                     <div class="col-md-12 col-lg-6 col-sm-12 col-12">
                        <div class="graphic-image1">
                           <img src="images/product-graphics.png">
                        </div>
                     </div>
                  </div>
               </form>
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