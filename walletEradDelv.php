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
	  
	 <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>

	  
   </head>
   <body>
      <section class="all-content">
         <!-- Sidebar Section Starts Here -->


           <div class="SecondDivID"></div> 
    <script src=" https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

        <script type="text/javascript">

          function loadhem(){

            $(".SecondDivID").load("leftNav.php?Page=wallet.php");

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
                            $res = mysqli_query($con,"select sum(OrderPrice) from Orders");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$OrderPrice = $row["sum(OrderPrice)"];
								
							}
							
							
					?>	


					<?php
                            $res = mysqli_query($con,"select sum(Money) from DriverTransactions");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$Money = $row["sum(Money)"];
								
							}
							
							
					?>		
					
					
					 <?php
									
									$SevenDayDate = date('Y-m-d',strtotime("-7 days"));
									
                                        $res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM Orders JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE (OrderState='Rated' OR OrderState='Done') AND ShopRecive = 'NO' AND Shops.Type = 'Our' AND CreatedAtOrders < '$SevenDayDate'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$fsum = $row["sum(OrderPriceFromShop)"];
								
								
							}?>
							
							
					 <?php
									
									
                                        $res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM Orders JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE (OrderState='Rated' OR OrderState='Done') AND PaidForDriver = 'Paid' AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$ssum = $row["sum(OrderPriceFromShop)"];
								
								
							}?>
							
							
							
					<?php
									
									
                                        $res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM Orders JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE (OrderState='Rated' OR OrderState='Done') AND PaidForDriver != 'Paid' AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$sssum = $row["sum(OrderPriceFromShop)"];
								
								
							}
							
							require "conn.php";
									
									
							$res = mysqli_query($con,"select * from Money");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$TotalIncome = $row["TotalIncome"];
								
								$SubscriptionR  = $row["SubscriptionR"];
								$SalesR = $row["SalesR"];
								$DeliveryR = $row["DeliveryR"];
								$BalanceTraComm = $row["BalanceTraComm"];
								$BalanceWithComm = $row["BalanceWithComm"];
								$ServComm = $row["ServComm"];

								
							}		
							
							?>		
							
							
							
				  		
				  
				  <h3 style="color:#5051f8"> Income : <span style="color:black"> <?php echo $TotalIncome; ?> MAD </span></h3>
                  <div class="row">
					
					
				  
					  <div class=" col-md-8 col-lg-8 col-sm-8 col-12">	
					     <div class="row"> 
						 <div class="col-md-4 col-lg-4 col-sm-12 col-12" >
							<a class="custom-box1" href="walletErad.php"> <div >
							   <div class="custom-box1-head">
								  <h4 style="font-size:15px;margin-bottom:10px;">  Subscription Revenues </h4>
								  
							   </div>
							   <div class="custom-box1-data" style="display: flex;justify-content: center;align-items: center;">
								  <h4 style="color:black"> <?php echo $SubscriptionR; ?> MAD </h4>
							   </div>
							</div></a>
						 </div>
						<div class="col-md-4 col-lg-4 col-sm-12 col-12" >
							<a class="custom-box1" href="walletEradSalesR.php"> <div >
							   <div class="custom-box1-head">
								  <h4 style="font-size:15px;margin-bottom:10px;"> Sales Revenues </h4>
								  
							   </div>
							   <div class="custom-box1-data" style="display: flex;justify-content: center;align-items: center;">
								  <h4 style="color:black"> <?php echo $SalesR ; ?> MAD </h4>
							   </div>
							</div></a>
						 </div>
						 <div class="col-md-4 col-lg-4 col-sm-12 col-12" >
							<a style="background-color:#5051f9;" class="custom-box1" href="walletEradDelv.php"> <div >
							   <div class="custom-box1-head">
								  <h4 style="font-size:15px;margin-bottom:10px;color:white">  Delivery Revenues </h4>
								  
							   </div>
							   <div class="custom-box1-data" style="display: flex;justify-content: center;align-items: center;">
								  <h4 style="color:white"> <?php echo $DeliveryR; ?> MAD </h4>
							   </div>
							</div></a>
						 </div>
						 <div class="col-md-4 col-lg-4 col-sm-12 col-12">
							<a class="custom-box1" href="walletEradRased1.php"> <div >
							   <div class="custom-box1-head">
								  <h4 style="font-size:15px;margin-bottom:10px;">  Balance Transfer Commission </h4>
								  
							   </div>
							   <div class="custom-box1-data" style="display: flex;justify-content: center;align-items: center;">
								  <h4 style="color:black"> <?php echo $BalanceTraComm; ?> MAD </h4>
							   </div>
							</div></a>
						 </div>
						 <div class="col-md-4 col-lg-4 col-sm-12 col-12"  >
							<a class="custom-box1" href="walletEradRased2.php"> <div >
							   <div class="custom-box1-head">  
								  <h4 style="font-size:15px;margin-bottom:10px;">  Balance Withdrawal Commission </h4>
								  
							   </div>
							   <div class="custom-box1-data" style="display: flex;justify-content: center;align-items: center;">
								  <h4 style="color:black"> <?php echo $BalanceWithComm; ?> MAD </h4>
							   </div>
							</div></a>
						 </div>
						 <div class="col-md-4 col-lg-4 col-sm-12 col-12" >
							<a  class="custom-box1" href="walletEradRased3.php"> <div >
							   <div class="custom-box1-head">
								  <h4 style="font-size:15px;margin-bottom:10px;">  Service Commission </h4>
								  
							   </div>
							   <div class="custom-box1-data" style="display: flex;justify-content: center;align-items: center;">
								  <h4 style="color:black"> <?php echo $ServComm; ?> MAD </h4>
							   </div>
							</div></a>
						 </div>
						 </div>
						 
						 
							<div class="row">
                     <div class="col-md-12 col-lg-12 col-sm-12 col-12 ">
                        <div class="custom-block3">
                           <div class="block-element">
                              <div class="sec-head3">
                                 <h4> Transactions </h4>
                                 <!--<div class="search-form2">-->
                                 <!--   <form>-->
                                 <!--      <input type="text" placeholder="Search by Name Phone  Email Id" name="">   -->
                                 <!--      <i class="fa fa-search"> </i>-->
                                 <!--   </form>-->
                                 <!--</div>-->
                                 <!--<button class="custom-btn1"> See all </button>-->
                              </div>
                           </div>
                           
                           
                          
                           
                           
                           
                           <div class="table-wrapper">
                              <table class="table-1">
                                 <thead>
                                    <tr >
                                       <th style="color:black;"> Driver name </th>
                                       <th style="color:black;"class="text-center"> Order ID </th>
									   <th  style="color:black;"class="text-center"> Money </th>
									   <th style="color:black;"class="text-center"> City </th>
									   <th style="color:black;"class="text-center"> Date </th>
									   <th style="color:black;" class="text-center"> Details </th>	
                                    </tr>
                                 </thead>
                                 <tbody>
                            <?php
                                    
                                    
                                    $Page = $_GET["Page"];
                                    if($Page==""){
                                        $Page = 0;
                                    }
                                    $rr = 10 * ($Page);
                                    
                                    $ShopNamew = $_GET["ShopName"];
                                    if($ShopNamew == ''){
                                        $res = mysqli_query($con,"SELECT * FROM DriverRevTransaction JOIN Drivers ON DriverRevTransaction.DriverID = Drivers.DriverID order by DriverRevTransactionID desc limit $rr,10");
                                    }else{
                                        $rr = 0;
                                        $res = mysqli_query($con,"SELECT * FROM Shops WHERE ShopName LIKE '%$ShopNamew%' order by ShopID desc limit $rr,10");

                                    }
                                    
                                    if($cityID !=''){
                        
                                     $res = mysqli_query($con,"SELECT *, (6372.797 * acos(cos(radians($CityLat)) * cos(radians(ShopLat)) * cos(radians(ShopLongt) - radians($CityLongt)) + sin(radians($CityLat)) * sin(radians(ShopLat)))) AS distance FROM Shops WHERE  Shops.Status = 'ACTIVE' HAVING distance <= $Deliveryzone ORDER BY priority DESC , distance ASC limit $rr,10");
                                                
                                                                    
                                      }
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
                                
                                
                                    $ShopName = $row["FName"] . ' ' .$row["LName"];
                                    $OrderID   = $row["OrderID"];
									$DeliveryZoneID   = $row["DeliveryZoneID"];
                                    $ShopLogo = $row["PersonalPhoto"];
									$Money = $row["Money"];
									$CreatedAtDriverRevTransaction = $row["CreatedAtDriverRevTransaction"];
            
                                    $res2 = mysqli_query($con,"SELECT * FROM DeliveryZone WHERE DeliveryZoneID =  $DeliveryZoneID");
									while($row2 = mysqli_fetch_assoc($res2)){ $CityNAme =  $row2["CityName"]; }
								
                                ?>
            
                           
                                     
                                   <tr>
                                       <td class="image-col col-blue1 "> <img width="30px" src="<?php echo $ShopLogo; ?>"> <a href="shop-profile.php?id=<?php echo $ShopID ?>"> <?php echo $ShopName; ?> </a>  </td>
                                       <td class="col-grey1 text-center"> <?php echo $OrderID; ?> </td>
									   <td class="col-grey1 text-center"> <?php echo $CityNAme; ?> </td>
									   <td class="col-grey1 text-center"> <?php echo $Money; ?> MAD </td>
									   <td class="col-grey1 text-center"> <?php echo $CreatedAtDriverRevTransaction; ?> </td>
									   <td class="col-grey1 text-center"> Show details </td>
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
                              <h5> Showing <?php echo $Page; ?> to 10  </h5>
                              <ul>
                                 <li> <a href="walletEradDelv.php?ShopName=<?php echo $ShopNamew ?>&Page=<?php echo $Page-1 ?>&CityName=<?php echo $CityName ?>&cityID=<?php echo $cityID ?>"> <i class="fa fa-angle-left"> </i> </a> </li>
                                 <li> <a href="#" class="active"> <?php echo $Page+1 ?> </a> </li>
                                 <li> <a href="walletEradDelv.php?ShopName=<?php echo $ShopNamew ?>&Page=<?php echo $Page+1 ?>&CityName=<?php echo $CityName ?>&cityID=<?php echo $cityID ?>"> <i class="fa fa-angle-right"> </i> </a> </li>
                              </ul>
                           </div>
                        </div>
                     </div>
					 </div>
  
					
				
						 
					 </div>
					 <div class="col-md-4 col-lg-4 col-sm-4 col-12">
					 
					 
					 
					 	<div >
								<div class="card" style="background: white; border-color:#3F6C8E;">
								<div class="card-header">
									<div class="card-title">Delivery revenues</div>
								</div>
								<div class="card-body">
								    
								     <?php
                  $currentYear = date('Y');
                  
                  $M1 = 0 ;$M2 = 0 ;$M3 = 0 ;$M4 = 0 ; $M5 = 0 ;$M6 = 0 ;$M7 = 0 ;$M8 = 0 ; $M9 = 0 ;$M10 = 0 ;$M11 = 0 ;$M12 = 0 ;
                                $res = mysqli_query($con,"SELECT MONTH(CreatedAtDriverRevTransaction) AS Month, COUNT(*) AS TotalOrders FROM DriverRevTransaction WHERE YEAR(CreatedAtDriverRevTransaction) = '$currentYear' GROUP BY MONTH(CreatedAtDriverRevTransaction);");
            
                            $result = array();
                            while($row = mysqli_fetch_assoc($res)){
                             
                             if($row["Month"]==1){$M1 = $row["TotalOrders"];} if($row["Month"]==2){$M2 = $row["TotalOrders"];}if($row["Month"]==3){$M3 = $row["TotalOrders"];}if($row["Month"]==4){$M4 = $row["TotalOrders"];}
                             if($row["Month"]==5){$M5 = $row["TotalOrders"];} if($row["Month"]==6){$M6 = $row["TotalOrders"];}if($row["Month"]==7){$M7 = $row["TotalOrders"];}if($row["Month"]==8){$M8 = $row["TotalOrders"];}
                             if($row["Month"]==9){$M9 = $row["TotalOrders"];} if($row["Month"]==10){$M10 = $row["TotalOrders"];}if($row["Month"]==11){$M11 = $row["TotalOrders"];}if($row["Month"]==12){$M12 = $row["TotalOrders"];}

                             
                              }
                              
                     
                    ?>            
								    
									
									<canvas id="myChart" style="width:100%;max-width:600px"></canvas>

                                        <script>
                                        const xValues = ["jun","Feb","March","Apr","May","Jun","July","Aug","Sep","Oct","Nov","Dec"];
                                        
                                        new Chart("myChart", {
                                          type: "line",
                                          data: {
                                            labels: xValues,
                                            datasets: [{ 
                                              data: [<?php echo $M1 ?>,<?php echo $M2 ?>,<?php echo $M3 ?>,<?php echo $M4 ?>,<?php echo $M5 ?>,<?php echo $M6 ?>,<?php echo $M7 ?>,<?php echo $M8 ?>,<?php echo $M9 ?>,<?php echo $M10 ?>,<?php echo $M11 ?>,<?php echo $M12 ?>],
                                              borderColor: '#5051f8',
                                              fill: false
                                            }]
                                          },
                                          options: {
                                            legend: {display: false}
                                          }
                                        });
                                        </script>
									
									
								</div>
							</div>
						</div>
						
						 <div >
                        <div class="custom-block1 height-box1">
                           <div class="block-element">
                              <div class="sec-head1">
                                 <h4 class="col-green1"> Delivery revenues </h4>
                                 <select name="forma" onchange="location = this.value;">
									<?php if($day=="Today"||$day==""){ ?>
                                    <option value="walletErad.php?day=Today" selected> Today </option>
									<?php }else{ ?>
									<option value="index.php?day=Today"> Today </option>
									<?php } ?>
									<?php if($day=="Month"){ ?>
                                    <option value="walletErad?day=Month" selected> Month </option>
									<?php }else{ ?>
									<option value="walletErad?day=Month" > Month </option>
									<?php } ?>
									<?php if($day=="Year"){ ?>
                                    <option value="walletErad?day=Year" selected> Year </option>
									<?php }else{ ?>
									<option value="walletErad?day=Year"> Year </option>
									<?php } ?>
                                 </select>
								 <select name="forma" onchange="location = this.value;">
											<?php                 require "conn.php";

                                    
                                     $res = mysqli_query($con,"SELECT * FROM DeliveryZone");

                                        $result = array();
                        
                                        while($row = mysqli_fetch_assoc($res)){
                                    
                                    ?> 
									
										<option value="#"> <?php echo $row["CityName"]; ?> </option>

										<?php } ?>
                                     
								 </select>
                              </div>
                           </div>
                           <div class="block-element">
                              <div class="profit-block profit-bg1">
                                 <img src="images/profit-icon1.png">
                                 <h6 class="col-green2"> Added value  </h6>
								 
								 <?php                 require "conn.php";

                                    
                                     $res = mysqli_query($con,"SELECT DriverCommesion FROM MoneyStop");

                                        $result = array();
                        
                                        while($row = mysqli_fetch_assoc($res)){ $DriverCommesion = $row["DriverCommesion"]; }
                                    
                                    ?> 
								   
								 
                                 <h4> <?php echo $DriverCommesion; ?> MAD</h4>
                              </div>
                              
                           </div>
                           <div class="block-element tags-holder" style="margin-top:10px;">
                              <a href="" class="bg-blue1 tag-block"> <?php echo $DeliveryR; ?> MAD <span> Income </span> </a>	
                           </div>
                        </div>
                     </div>
					 
					 
					 </div>
                  </div>
                  <div class="row">
                     <div class="col-md-6 col-lg-4 col-sm-12 col-12" style="display:none;">
                        <div class="custom-block3">
                           <div class="block-element">
                              <div class="sec-head3">
                                 <h4> Shops </h4>
                                 <div class="search-form2">
                                    <form>
                                       <input type="text" placeholder="Search by Name Phone  Email Id" name="">   
                                       <i class="fa fa-search"> </i>
                                    </form>
                                 </div>
                                 <button class="custom-btn1"> See all </button>
                              </div>
                           </div>
                           <div class="table-wrapper">
                              <table class="table-1">
                                 <thead>
                                    <tr>
                                       <th > Name </th>
                                       <th class="text-center"> Withdrawal request</th>
                                       <th class="text-center"> ShopID </th>
									   <th class="text-center"> Date </th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                     <?php
                                    require "conn.php";
                                        $res = mysqli_query($con,"SELECT * FROM ShopTransaction JOIN Shops ON ShopTransaction.ShopID = Shops.ShopID order by ShopTransaction.ShopID desc limit 0,10");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
                                
                                
                                    $ShopName = $row["ShopName"];
                                    $ShopID   = $row["ShopID"];
                                    $ShopLogo = $row["ShopLogo"];
									$GetPaidMoney = $row["GetPaidMoney"];
									$CreatedAtShopTransaction = $row["CreatedAtShopTransaction"];
            
                                ?>
                                    <tr>
                                       <td class="image-col col-blue1"> <img src="<?php echo $ShopLogo; ?>">  <?php echo $ShopName ?>  </td>
                                       <td class="col-grey1 text-center"> <?php echo $GetPaidMoney; ?> MAD </td>
                                       <td class="status-col1 text-center">  <?php echo $ShopID; ?> </td>
									   <td class="status-col1 text-center">  <?php echo $CreatedAtShopTransaction; ?> </td>
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
   </body>
</html>