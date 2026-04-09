<!--
=========================================================
* Material Dashboard 2 - v3.1.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2023 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <title>
    Material Dashboard 2 by Creative Tim
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="../assets/css/material-dashboard.css?v=3.1.0" rel="stylesheet" />
  <!-- Nepcha Analytics (nepcha.com) -->
  <!-- Nepcha is a easy-to-use web analytics. No cookies and fully compliant with GDPR, CCPA and PECR. -->
  <script defer data-site="YOUR_DOMAIN_HERE" src="https://api.nepcha.com/js/nepcha-analytics.js"></script>
  	  <script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>
</head>

<body class="g-sidenav-show  bg-gray-200">
  <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main" style="background-color:#353dce">
   <!-- <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href=" https://demos.creative-tim.com/material-dashboard/pages/dashboard " target="_blank">
        <img src="../assets/img/logo-ct.png" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold text-white">Material Dashboard 2</span>
      </a>
    </div> -->
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main" >
      <ul class="navbar-nav">
		<li class="nav-item">
          <a class="nav-link text-white active bg-gradient-primary" style="background:#353dce" href="https://jibler.ma/db/db/index.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white active bg-gradient-primary" style="background:#353dce" href="dashboard.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Main Dashboard</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="User.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Users</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="Drivers.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Drivers</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="ShopsWhoPaid.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Shops</span>
          </a>
        </li>
       <!-- <li class="nav-item">
          <a class="nav-link text-white " href="../pages/rtl.html">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">format_textdirection_r_to_l</i>
            </div>
            <span class="nav-link-text ms-1">RTL</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="../pages/notifications.html">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">notifications</i>
            </div>
            <span class="nav-link-text ms-1">Notifications</span>
          </a>
        </li>
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Account pages</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="../pages/profile.html">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">person</i>
            </div>
            <span class="nav-link-text ms-1">Profile</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="../pages/sign-in.html">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">login</i>
            </div>
            <span class="nav-link-text ms-1">Sign In</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-white " href="../pages/sign-up.html">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">assignment</i>
            </div>
            <span class="nav-link-text ms-1">Sign Up</span>
          </a>
        </li> -->
      </ul>
    </div>
   <!-- <div class="sidenav-footer position-absolute w-100 bottom-0 ">
      <div class="mx-3">
        <a class="btn btn-outline-primary mt-4 w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard?ref=sidebarfree" type="button">Documentation</a>
        <a class="btn bg-gradient-primary w-100" href="https://www.creative-tim.com/product/material-dashboard-pro?ref=sidebarfree" type="button">Upgrade to pro</a>
      </div>
    </div> -->
  </aside>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
          </ol>
		  
		  <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-3 px-0 me-sm-10 me-7">
		  
			<?php $Type = $_GET["Type"]; ?>
		  
			<?php if($Type=="Today"){ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:blue;color:white;"  href="dashboard.php?Type=Today">Today</a></li>
			<?php }else{ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:white"  href="dashboard.php?Type=Today">Today</a></li>
			<?php } ?>
			<?php if($Type=="Yesterday"){ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:blue;color:white;"  href="dashboard.php?Type=Yesterday">Yesterday</a></li>
			<?php }else{ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:white"  href="dashboard.php?Type=Yesterday">Yesterday</a></li>
			<?php } ?>
			<?php if($Type=="week"){ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:blue;color:white;"  href="dashboard.php?Type=week">This week</a></li>
			<?php }else{ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:white"  href="dashboard.php?Type=week">This week</a></li>
			<?php } ?>
			<?php if($Type=="month"){ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:blue;color:white;"  href="dashboard.php?Type=month">This month</a></li>
			<?php }else{ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:white"  href="dashboard.php?Type=month">This month</a></li>
			<?php } ?>
			<?php if($Type=="Year"){ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:blue;color:white;"  href="dashboard.php?Type=Year">This Year</a></li>
			<?php }else{ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:white"  href="dashboard.php?Type=Year">This Year</a></li>
			<?php } ?>
			<?php if($Type=="All"||$Type==""){ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:blue;color:white;"  href="dashboard.php?Type=All">All</a></li>
			<?php }else{ ?>
				<li class="breadcrumb-item text-sm" aria-current="page"><a style="font-size: 15px;border-radius: 10px;padding: 10px;background:white"  href="dashboard.php?Type=All">All</a></li>
			<?php } ?>
          </ol>
		  
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <!--<div class="ms-md-auto pe-md-3 d-flex align-items-center">
            <div class="input-group input-group-outline">
              <label class="form-label">Type here...</label>
              <input type="text" class="form-control">
            </div>
          </div> -->
          <ul class="navbar-nav  justify-content-end">
            <!--<li class="nav-item d-flex align-items-center">
              <a class="btn btn-outline-primary btn-sm mb-0 me-3" target="_blank" href="https://www.creative-tim.com/builder?ref=navbar-material-dashboard">Online Builder</a>
            </li>-->
            <!--<li class="mt-2">
              <a class="github-button" href="https://github.com/creativetimofficial/material-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/material-dashboard on GitHub">Star</a>
            </li>-->
            <!--<li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>-->
            <!--<li class="nav-item px-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0">
                <i class="fa fa-cog fixed-plugin-button-nav cursor-pointer"></i>
              </a>
            </li>-->
            <!--<li class="nav-item dropdown pe-2 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-bell cursor-pointer"></i>
              </a>
              <ul class="dropdown-menu  dropdown-menu-end  px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="../assets/img/team-2.jpg" class="avatar avatar-sm  me-3 ">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">New message</span> from Laur
                        </h6>
                        <p class="text-xs text-secondary mb-0">
                          <i class="fa fa-clock me-1"></i>
                          13 minutes ago
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li class="mb-2">
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="my-auto">
                        <img src="../assets/img/small-logos/logo-spotify.svg" class="avatar avatar-sm bg-gradient-dark  me-3 ">
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          <span class="font-weight-bold">New album</span> by Travis Scott
                        </h6>
                        <p class="text-xs text-secondary mb-0">
                          <i class="fa fa-clock me-1"></i>
                          1 day
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item border-radius-md" href="javascript:;">
                    <div class="d-flex py-1">
                      <div class="avatar avatar-sm bg-gradient-secondary  me-3  my-auto">
                        <svg width="12px" height="12px" viewBox="0 0 43 36" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                          <title>credit-card</title>
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <g transform="translate(-2169.000000, -745.000000)" fill="#FFFFFF" fill-rule="nonzero">
                              <g transform="translate(1716.000000, 291.000000)">
                                <g transform="translate(453.000000, 454.000000)">
                                  <path class="color-background" d="M43,10.7482083 L43,3.58333333 C43,1.60354167 41.3964583,0 39.4166667,0 L3.58333333,0 C1.60354167,0 0,1.60354167 0,3.58333333 L0,10.7482083 L43,10.7482083 Z" opacity="0.593633743"></path>
                                  <path class="color-background" d="M0,16.125 L0,32.25 C0,34.2297917 1.60354167,35.8333333 3.58333333,35.8333333 L39.4166667,35.8333333 C41.3964583,35.8333333 43,34.2297917 43,32.25 L43,16.125 L0,16.125 Z M19.7083333,26.875 L7.16666667,26.875 L7.16666667,23.2916667 L19.7083333,23.2916667 L19.7083333,26.875 Z M35.8333333,26.875 L28.6666667,26.875 L28.6666667,23.2916667 L35.8333333,23.2916667 L35.8333333,26.875 Z"></path>
                                </g>
                              </g>
                            </g>
                          </g>
                        </svg>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="text-sm font-weight-normal mb-1">
                          Payment successfully completed
                        </h6>
                        <p class="text-xs text-secondary mb-0">
                          <i class="fa fa-clock me-1"></i>
                          2 days
                        </p>
                      </div>
                    </div>
                  </a>
                </li>
              </ul>
            </li> -->
           <!-- <li class="nav-item d-flex align-items-center">
              <a href="../pages/sign-in.html" class="nav-link text-body font-weight-bold px-0">
                <i class="fa fa-user me-sm-1"></i>
                <span class="d-sm-inline d-none">Sign In</span>
              </a>
            </li> -->
          </ul>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">weekend</i>
              </div>
			  
			  <?php
                                    require "conn.php";
									
									if($Type=="Today"){
									$todaydate = date("Y-m-d");
                                        $res = mysqli_query($con,"SELECT count(*) FROM `Orders` WHERE CreatedAtOrders like '$todaydate%' AND (OrderState='Done' OR OrderState='Rated')");
									}else if($Type=="Yesterday"){
										$Yesterday = date("Y-m-d",strtotime("-1 days"));
                                        $res = mysqli_query($con,"SELECT count(*) FROM `Orders` WHERE CreatedAtOrders like '$Yesterday%' AND (OrderState='Done' OR OrderState='Rated')");
									
									}else if($Type==""||$Type=="All"){
                                       $res = mysqli_query($con,"SELECT count(*) FROM `Orders` WHERE  (OrderState='Done' OR OrderState='Rated')");
									}
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$TodayCommestion = $row["count(*)"] * 1;
								
								
							}
							
							if($Type=="Today"){
							$todaydate = date("Y-m-d");
								$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM `Orders` WHERE CreatedAtOrders like '$todaydate%' AND (OrderState='Done' OR OrderState='Rated')");
							}else if($Type=="Yesterday"){
										$Yesterday = date("Y-m-d",strtotime("-1 days"));
										$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM `Orders` WHERE CreatedAtOrders like '$Yesterday%' AND (OrderState='Done' OR OrderState='Rated')");

							}else if($Type==""||$Type=="All"){
								$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM `Orders` WHERE  (OrderState='Done' OR OrderState='Rated')");
							}
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$TodayCommestionPers = $row["sum(OrderPriceFromShop)"] * 1;
								
								
							}
							
							$res = mysqli_query($con,"SELECT * FROM OrdersJiblerpercentage");
                        
                                        $result = array();
                        
                                        while($row = mysqli_fetch_assoc($res)){
                        
                                        $percent = $row["percent"];
                                                               
                                        }
							
							$TodayCommestionPers = $TodayCommestionPers * $percent / 100;
							
							$TodayCommestion = $TodayCommestion + $TodayCommestionPers;
							
							?>		
			  
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Sales commission</p>
                <h4 class="mb-0"><?php echo $TodayCommestion ;  ?> MAD</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
           <!-- <div class="card-footer p-3">
              <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p>
            </div> -->
          </div>
        </div>
		
		<?php
                                    require "conn.php";
									
									$SevenDayDate = date('Y-m-d',strtotime("-7 days"));
									
                                        $res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM Orders JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE (OrderState='Rated' OR OrderState='Done') AND ShopRecive = 'NO' AND Shops.Type = 'Our' AND CreatedAtOrders < '$SevenDayDate'");

            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$fsum = $row["sum(OrderPriceFromShop)"];
								
								
							}?>
							
		
		
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute" style="background:#353dce">
                <i class="material-icons opacity-10">person</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Partners' dues</p>
                <h4 class="mb-0"><?php echo $fsum ?> MAD</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <!-- <div class="card-footer p-3">
              <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p>
            </div> -->
          </div>
        </div>
		
		 <?php
                                    require "conn.php";
									
									
                                        $res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM Orders JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE (OrderState='Rated' OR OrderState='Done') AND PaidForDriver = 'Paid' AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$ssum = $row["sum(OrderPriceFromShop)"];
								
								
							}?>
		
		
		
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">person</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Cash collect done</p>
                <h4 class="mb-0"><?php echo $ssum; ?> MAD</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <!-- <div class="card-footer p-3">
              <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p>
            </div> -->
          </div>
        </div>
		
		<?php
                                    require "conn.php";
									
									
                                        $res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM Orders JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE (OrderState='Rated' OR OrderState='Done') AND PaidForDriver != 'Paid' AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$sssum = $row["sum(OrderPriceFromShop)"];
								
								
							}?>		
		
        <div class="col-xl-3 col-sm-6">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">weekend</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Cash collect pending</p>
                <h4 class="mb-0"><?php echo $sssum ?> MAD</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <!-- <div class="card-footer p-3">
              <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p>
            </div> -->
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent" >
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1" style="background:#353dce">
                <div class="chart" >
                  <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 ">Total Profit</h6>
              <p class="text-sm ">Our Profit of orders this week</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> From shops, Jibler pay,subscription, drivers, boost </p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6 mt-4 mb-4">
          <div class="card z-index-2  ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 "> Orders this Week </h6>
              <p class="text-sm "> Orders this Week from partener shops </p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> orders </p>
              </div>
            </div>
          </div>
        </div>
		
		
		
		
		
        <div class="col-lg-4 mt-4 mb-3">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 ">Boosts this week</h6>
              <p class="text-sm ">Paid boosts this week</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm">boosts</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <div class="row">
                <div class="col-lg-6 col-7">
                  <h6>Must Paid To Shops</h6>
				  
				  
				  
                  <p class="text-sm mb-0">
                    <i class="fa fa-check text-info" aria-hidden="true"></i>
                    <span class="font-weight-bold ms-1">Shops deserve</span> Money
                  </p>
                </div>
                <div class="col-lg-6 col-5 my-auto text-end">
                  <div class="dropdown float-lg-end pe-4">
                    
                   
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Money</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ShopID</th>
					  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date</th>
					  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Show Transactions</th>
                    </tr>
                  </thead>
                  <tbody>
				  					<?php 
					
					$SevenDayDate = date('Y-m-d',strtotime("-7 days"));
									
                                        $res = mysqli_query($con,"SELECT * FROM Orders JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE (OrderState='Rated' OR OrderState='Done') AND ShopRecive = 'NO' AND Shops.Type = 'Our' AND CreatedAtOrders < '$SevenDayDate' GROUP BY Shops.ShopID order by CreatedAtOrders desc");
                                    
            
                            $result = array();
					
					                            while($row = mysqli_fetch_assoc($res)){
                                
                                
                                    $ShopName = $row["ShopName"];
                                    $ShopID   = $row["ShopID"];
                                    $ShopLogo = $row["ShopLogo"];
									$GetPaidMoney = $row["Money"];
									$CreatedAtShopTransaction = $row["CreatedAtOrders"];
									$ShopID =$row["ShopID"];
            
                                ?>
								
								<?php 
								
								
								$res2 = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM Orders WHERE ShopID = '$ShopID' AND (OrderState='Rated' OR OrderState='Done') AND ShopRecive = 'NO' AND CreatedAtOrders < '$SevenDayDate'");
									while($row22 = mysqli_fetch_assoc($res2)){
										
										$CanGet = $row22["sum(OrderPriceFromShop)"];
										
									}
																
								
								?>
				  
				  
				  
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            <img src="<?php echo $ShopLogo; ?>" class="avatar avatar-sm me-3" alt="xd">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm"><?php echo $ShopName; ?></h6>
                          </div>
                        </div>
                      </td>
                    
                      <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold"> <?php echo $CanGet ?> MAD </span>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold"> <?php echo $ShopID; ?> </span>
                      </td>
					  <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold"> <?php echo $CreatedAtShopTransaction; ?> </span>
                      </td>
					  <td class="align-middle text-center text-sm">
                        <span class="text-xs font-weight-bold"><a href="ShopTrans.php?ShopID=<?php echo $ShopID; ?>">show </a> </span>
                      </td>
                    </tr>
					
					
					   <?php 
                                
                                
                            } 
                            
                            ?>        
                    
                    

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-6">
          <div class="card h-100">
            <div class="card-header pb-0">
              <h6>Export and import</h6>
              
            </div>
            <div class="card-body p-3">
			<button class="field-style3" style="background-color:#353dce;color:white;border-radius: 2%;height:50px;" onclick="ExportToExcel('xlsx')">Export table to excel</button>
			
			<br>
			<br>
			<form action="uploadExel.php" method="post" enctype="multipart/form-data">
				<input class="field-style3" value="load file" type="file" name="file"  />
				<button class="field-style3" name="submit_file" type="" style="background-color:#353dce;color:white;border-radius: 2%;" >upload</button>

			</form>
              <div class="timeline timeline-one-side"style="display:none;">
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-success text-gradient">shopping_cart</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Most selling store </h6>
					<?php
					$todaydate = date("Y-m-d");
                            $res = mysqli_query($con,"SELECT MAX(ShopID),SUM(OrderPriceFromShop),DestinationName FROM Orders  Where CreatedAtOrders like '$todaydate%' AND (OrderState='Done' OR OrderState='Rated') GROUP BY ShopID");
                           
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$DestinationName = $row["DestinationName"];
								$OrderPriceFromShop = $row["SUM(OrderPriceFromShop)"];
								
								
							}
							
					?>		
					
					
					
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0"><?php echo $DestinationName . ' Money '.$OrderPriceFromShop . 'MAD'; ?></p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-danger text-gradient">payments</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Most Driver make orders</h6>
					
					<?php
					$todaydate = date("Y-m-d");
                            $res = mysqli_query($con,"SELECT MAX(DelvryId),FName,LName FROM Orders JOIN Drivers ON Orders.DelvryId = Drivers.DriverID Where CreatedAtOrders like '$todaydate%' AND (OrderState='Done' OR OrderState='Rated') GROUP BY DelvryId");
                           
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$FName = $row["FName"];
								$LName = $row["LName"];
								
								
							}
							
					?>		
					
					
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0"><?php echo $FName . ' ' . $LName ?></p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-info text-gradient">credit_card</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Most User buy orders</h6>
					
					<?php
					$todaydate = date("Y-m-d");
                            $res = mysqli_query($con,"SELECT MAX(Orders.UserID),name FROM Orders JOIN Users ON Orders.UserID = Users.UserID Where CreatedAtOrders like '$todaydate%' AND (OrderState='Done' OR OrderState='Rated') GROUP BY Orders.UserID");
                           
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$name = $row["name"];
								
								
								
							}
							
					?>		
					
					
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0"><?php echo $name; ?></p>
                  </div>
                </div>
                <!--<div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-warning text-gradient">credit_card</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">New card added for order #4395133</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">20 DEC 2:20 AM</p>
                  </div>
                </div>
                <div class="timeline-block mb-3">
                  <span class="timeline-step">
                    <i class="material-icons text-primary text-gradient">key</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">Unlock packages for development</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">18 DEC 4:54 AM</p>
                  </div>
                </div>
                <div class="timeline-block">
                  <span class="timeline-step">
                    <i class="material-icons text-dark text-gradient">payments</i>
                  </span>
                  <div class="timeline-content">
                    <h6 class="text-dark text-sm font-weight-bold mb-0">New order #9583120</h6>
                    <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">17 DEC</p>
                  </div>
                </div> -->
              </div>
            </div>
          </div>
        </div>
      </div>
      <footer class="footer py-4  ">
        <div class="container-fluid">
          <div class="row align-items-center justify-content-lg-between">
            <div class="col-lg-6 mb-lg-0 mb-4">
              <div class="copyright text-center text-sm text-muted text-lg-start" style="display:none;">
                © <script>
                  document.write(new Date().getFullYear())
                </script>,
                made with <i class="fa fa-heart"></i> by
                <a href="https://www.creative-tim.com" class="font-weight-bold" target="_blank">Creative Tim</a>
                for a better web.
              </div>
            </div>
            <div class="col-lg-6" style="display:none;">
              <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                <li class="nav-item">
                  <a href="https://www.creative-tim.com" class="nav-link text-muted" target="_blank">Creative Tim</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/presentation" class="nav-link text-muted" target="_blank">About Us</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/blog" class="nav-link text-muted" target="_blank">Blog</a>
                </li>
                <li class="nav-item">
                  <a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-muted" target="_blank">License</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </main>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="material-icons py-2">settings</i>
    </a>
    <div class="card shadow-lg">
      <div class="card-header pb-0 pt-3">
        <div class="float-start">
          <h5 class="mt-3 mb-0">Material UI Configurator</h5>
          <p>See our dashboard options.</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="material-icons">clear</i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-gradient-primary active" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between 2 different sidenav types.</p>
        </div>
        <div class="d-flex">
          <button class="btn bg-gradient-dark px-3 mb-2 active" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
        <!-- Navbar Fixed -->
        <div class="mt-3 d-flex">
          <h6 class="mb-0">Navbar Fixed</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
          </div>
        </div>
        <hr class="horizontal dark my-3">
        <div class="mt-2 d-flex">
          <h6 class="mb-0">Light / Dark</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
          </div>
        </div>
        <hr class="horizontal dark my-sm-4">
        <a class="btn bg-gradient-info w-100" href="https://www.creative-tim.com/product/material-dashboard-pro">Free Download</a>
        <a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard">View documentation</a>
        <div class="w-100 text-center">
          <a class="github-button" href="https://github.com/creativetimofficial/material-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/material-dashboard on GitHub">Star</a>
          <h6 class="mt-3">Thank you for sharing!</h6>
          <a href="https://twitter.com/intent/tweet?text=Check%20Material%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
          </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/material-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
          </a>
        </div>
      </div>
    </div>
  </div>
  
  <div class="table-wrapper">
						   
                              <table class="table-1" id="tbl_exporttable_to_xlsww" style="display:none;">
                                 <thead>
                                    <tr>
									<th class="text-center"> phone </th>
                                       <th class="text-center"> amount </th>
                                       <th class="text-center">  motif_transfer</th>
                                       <th class="text-center"> first_name </th>
									   <th class="text-center"> last_name </th>
									   <th class="text-center"> Shop_id </th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                     <?php
                                    require "conn.php";
									
									$SevenDayDate = date('Y-m-d',strtotime("-7 days"));
									
                                        $res = mysqli_query($con,"SELECT * FROM Orders JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE (OrderState='Rated' OR OrderState='Done') AND ShopRecive = 'NO' AND Shops.Type = 'Our' AND CreatedAtOrders < '$SevenDayDate' GROUP BY Shops.ShopID");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
                                
                                
                                    $ShopPhone = $row["ShopPhone"];
                                    $ShopID   = $row["ShopID"];
                                    $ShopLogo = $row["ShopLogo"];
									$GetPaidMoney = $row["Money"];
									$CreatedAtShopTransaction = $row["CreatedAtOrders"];
									$ShopID =$row["ShopID"];
									
									$FullName = $row["FullName"];
									$ShopID =$row["ShopID"];
            
                                ?>
								
								<?php 
								
								
								$res2 = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM Orders WHERE ShopID = '$ShopID' AND (OrderState='Rated' OR OrderState='Done') AND ShopRecive = 'NO' AND CreatedAtOrders < '$SevenDayDate'");
									while($row22 = mysqli_fetch_assoc($res2)){
										
										$CanGet = $row22["sum(OrderPriceFromShop)"];
										
									}
																
								
								?>
								
								
                                    <tr>
                                       <td class="col-grey1 text-center" ><?php echo $ShopPhone ?> </td>
									   <td class="col-grey1 text-center" ><?php echo $CanGet ?> </td> 
                                       <td class="col-grey1 text-center"> <?php echo 'Cash collect'; ?>  </td>
                                       <td class="status-col1 text-center">  <?php echo $FullName; ?> </td>
									   <td class="status-col1 text-center">  <?php echo $FullName; ?> </td>
									   <td class="status-col1 text-center">  <?php echo $ShopID; ?> </td>
                                    </tr>
                                 <?php 
                                
                                
                            } 
                            
                            ?>        
                                 </tbody>
                              </table>
                           </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../assets/js/plugins/chartjs.min.js"></script>
  <script>
  
		<?php 
		
		$Monday = date('Y-m-d', strtotime('this week Monday', time()));
		$Tuesday = date('Y-m-d', strtotime('this week Tuesday', time()));
		$Wednesday = date('Y-m-d', strtotime('this week Wednesday', time()));
		$Thursday = date('Y-m-d', strtotime('this week Thursday', time()));
		$Friday = date('Y-m-d', strtotime('this week Friday', time()));
		$Saturday = date('Y-m-d', strtotime('this week Saturday', time()));
		$Sunday = date('Y-m-d', strtotime('this Sunday', time()));
		
		
$MonOrder = 0;
$TuesOrder = 0 ;
$WednesOrder = 0;
$ThursOrder = 0;
$FriOrder = 0;
$SaturOrder = 0;
$SunOrder = 0;

$res = mysqli_query($con,"SELECT count(*) FROM Orders WHERE CreatedAtOrders LIKE '$Monday%' AND (OrderState='Done' OR OrderState='Rated')");
                while($row = mysqli_fetch_assoc($res)){
					
					$MonOrder = $row["count(*)"];
					
				}

$res = mysqli_query($con,"SELECT count(*) FROM Orders WHERE CreatedAtOrders LIKE '$Tuesday%' AND (OrderState='Done' OR OrderState='Rated')");
                while($row = mysqli_fetch_assoc($res)){
					
					$TuesOrder = $row["count(*)"];
					
				}
$res = mysqli_query($con,"SELECT count(*) FROM Orders WHERE CreatedAtOrders LIKE '$Wednesday%' AND (OrderState='Done' OR OrderState='Rated')");
                while($row = mysqli_fetch_assoc($res)){
					
					$WednesOrder = $row["count(*)"];
					
				}				
					
$res = mysqli_query($con,"SELECT count(*) FROM Orders WHERE CreatedAtOrders LIKE '$Thursday%' AND (OrderState='Done' OR OrderState='Rated')");
                while($row = mysqli_fetch_assoc($res)){
					
					$ThursOrder = $row["count(*)"];
					
				}						

$res = mysqli_query($con,"SELECT count(*) FROM Orders WHERE CreatedAtOrders LIKE '$Friday%' AND (OrderState='Done' OR OrderState='Rated')");
                while($row = mysqli_fetch_assoc($res)){
					
					$FriOrder = $row["count(*)"];
					
				}

$res = mysqli_query($con,"SELECT count(*) FROM Orders WHERE CreatedAtOrders LIKE '$Saturday%' AND (OrderState='Done' OR OrderState='Rated')");
                while($row = mysqli_fetch_assoc($res)){
					
					$SaturOrder = $row["count(*)"];
					
				}
				
$res = mysqli_query($con,"SELECT count(*) FROM Orders WHERE CreatedAtOrders LIKE '$Sunday%' AND (OrderState='Done' OR OrderState='Rated')");
                while($row = mysqli_fetch_assoc($res)){
					
					$SundayOrder = $row["count(*)"];
					
				}
				
				
$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM Orders JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE CreatedAtOrders like '$Monday%' AND (OrderState='Done' OR OrderState='Rated') AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$MondaySum = $row["sum(OrderPriceFromShop)"] * 1;
								
								
							}	
$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM `Orders` JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE CreatedAtOrders like '$Tuesday%' AND (OrderState='Done' OR OrderState='Rated') AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$TuesdaySum = $row["sum(OrderPriceFromShop)"] * 1;
								
								
							}
$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM `Orders` JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE CreatedAtOrders like '$Wednesday%' AND (OrderState='Done' OR OrderState='Rated') AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$WednesdaySum = $row["sum(OrderPriceFromShop)"] * 1;
								
								
							}
$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM `Orders` JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE CreatedAtOrders like '$Thursday%' AND (OrderState='Done' OR OrderState='Rated') AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$ThursdaySum = $row["sum(OrderPriceFromShop)"] * 1;
								
								
							}	
$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM `Orders` JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE CreatedAtOrders like '$Friday%' AND (OrderState='Done' OR OrderState='Rated') AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$FridaySum = $row["sum(OrderPriceFromShop)"] * 1;
								
								
							}
$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM `Orders` JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE CreatedAtOrders like '$Saturday%' AND (OrderState='Done' OR OrderState='Rated') AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$SaturdaySum = $row["sum(OrderPriceFromShop)"] * 1;
								
								
							}
$res = mysqli_query($con,"SELECT sum(OrderPriceFromShop) FROM `Orders` JOIN Shops ON Orders.ShopID = Shops.ShopID WHERE CreatedAtOrders like '$Sunday%' AND (OrderState='Done' OR OrderState='Rated') AND Shops.Type = 'Our'");
                                    
            
                            $result = array();
            
                            while($row = mysqli_fetch_assoc($res)){
								
								$SundaySum = $row["sum(OrderPriceFromShop)"] * 1;
								
								
							}							
		
	$MonOrderGain = $MondaySum * $percent / 100 + $MonOrder;
	$TuesOrderGain = $TuesdaySum * $percent / 100 + $TuesOrder;
	$WednesOrderGain = $WednesdaySum * $percent / 100 + $WednesOrder;
	$ThursOrderGain = $ThursdaySum * $percent / 100+ $ThursOrder;
	$FriOrderGain = $FridaySum  * $percent / 100+ $FriOrder;
	$SaturOrderGain = $SaturdaySum * $percent / 100 + $SaturOrder;
	$SundayOrderGain = $SundaySum * $percent / 100 + $SundayOrder;
	
	
	
	
	$res = mysqli_query($con,"SELECT count(*) FROM BoostsByShop WHERE CreatedAtBoostsByShop LIKE '$Monday%' AND BoostStatus = 'Active'");
                while($row = mysqli_fetch_assoc($res)){
					
					$MonBoost = $row["count(*)"];
					
				}
	$res = mysqli_query($con,"SELECT count(*) FROM BoostsByShop WHERE CreatedAtBoostsByShop LIKE '$Tuesday%' AND BoostStatus = 'Active'");
                while($row = mysqli_fetch_assoc($res)){
					
					$TuesBoost = $row["count(*)"];
					
				}
    $res = mysqli_query($con,"SELECT count(*) FROM BoostsByShop WHERE CreatedAtBoostsByShop LIKE '$Wednesday%' AND BoostStatus = 'Active'");
                while($row = mysqli_fetch_assoc($res)){
					
					$WedBoost = $row["count(*)"];
					
				}
	$res = mysqli_query($con,"SELECT count(*) FROM BoostsByShop WHERE CreatedAtBoostsByShop LIKE '$Thursday%' AND BoostStatus = 'Active'");
                while($row = mysqli_fetch_assoc($res)){
					
					$ThursBoost = $row["count(*)"];
					
				}
	$res = mysqli_query($con,"SELECT count(*) FROM BoostsByShop WHERE CreatedAtBoostsByShop LIKE '$FriOrder%' AND BoostStatus = 'Active'");
                while($row = mysqli_fetch_assoc($res)){
					
					$FriBoost = $row["count(*)"];
					
				}
    $res = mysqli_query($con,"SELECT count(*) FROM BoostsByShop WHERE CreatedAtBoostsByShop LIKE '$SaturOrder%' AND BoostStatus = 'Active'");
                while($row = mysqli_fetch_assoc($res)){
					
					$SaturBoost = $row["count(*)"];
					
				}
	$res = mysqli_query($con,"SELECT count(*) FROM BoostsByShop WHERE CreatedAtBoostsByShop LIKE '$Sunday%' AND BoostStatus = 'Active'");
                while($row = mysqli_fetch_assoc($res)){
					
					$SunBoost = $row["count(*)"];
					
				}					
	
	
		
		?>
 
  
    var ctx = document.getElementById("chart-bars").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["M", "T", "W", "T", "F", "S", "S"],
        datasets: [{
          label: "MAD",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "rgba(255, 255, 255, .8)",
          data: [<?php echo $MonOrderGain; ?>, <?php echo $TuesOrderGain; ?>, <?php echo $WednesOrderGain; ?>, <?php echo $ThursOrderGain; ?>, <?php echo $FriOrderGain; ?>, <?php echo $SaturOrderGain; ?>, <?php echo $SundayOrderGain; ?>],
          maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: 500,
              beginAtZero: true,
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
              color: "#fff"
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });


    var ctx2 = document.getElementById("chart-line").getContext("2d");

    new Chart(ctx2, {
      type: "line",
      data: {
        labels: ["M", "T", "W", "T", "F", "S", "S"],
        datasets: [{
          label: "Orders",
          tension: 0,
          borderWidth: 0,
          pointRadius: 5,
          pointBackgroundColor: "rgba(255, 255, 255, .8)",
          pointBorderColor: "transparent",
          borderColor: "rgba(255, 255, 255, .8)",
          borderColor: "rgba(255, 255, 255, .8)",
          borderWidth: 4,
          backgroundColor: "transparent",
          fill: true,
          data: [<?php echo $MonOrder; ?>, <?php echo $TuesOrder; ?>, <?php echo $WednesOrder; ?>, <?php echo $ThursOrder; ?>, <?php echo $FriOrder; ?>, <?php echo $SaturOrder; ?>, <?php echo $SundayOrder; ?>],
          maxBarThickness: 12

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });

    var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

    new Chart(ctx3, {
      type: "line",
      data: {
        labels: ["M", "T", "W", "T", "F", "S", "S"],
        datasets: [{
          label: "Mobile apps",
          tension: 0,
          borderWidth: 0,
          pointRadius: 5,
          pointBackgroundColor: "rgba(255, 255, 255, .8)",
          pointBorderColor: "transparent",
          borderColor: "rgba(255, 255, 255, .8)",
          borderWidth: 4,
          backgroundColor: "transparent",
          fill: true,
          data: [<?php echo $MonBoost; ?>, <?php echo $TuesBoost; ?>, <?php echo $WedBoost; ?>, <?php echo $ThursBoost; ?>, <?php echo $FriBoost; ?>, <?php echo $SaturBoost; ?>, <?php echo $SunBoost; ?>],

          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              padding: 10,
              color: '#f8f9fa',
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: false,
              drawTicks: false,
              borderDash: [5, 5]
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
  </script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  
  <script>

        function ExportToExcel(type, fn, dl) {
            var elt = document.getElementById('tbl_exporttable_to_xlsww');
            var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
            return dl ?
                XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }) :
                XLSX.writeFile(wb, fn || ('ShopsRequestMoney.' + (type || 'xlsx')));
        }

    </script>
  
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.min.js?v=3.1.0"></script>
</body>

</html>