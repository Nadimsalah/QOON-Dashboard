<?php require "conn.php"; 
$AdminID = $_COOKIE["AdminID"] ?? '';
$AdminName = $_COOKIE["AdminName"] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Accounts | QOON Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="fontawesome-kit-5/css/all.css" rel="stylesheet">
    <!-- Bootstrap Grids and Modals Support -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-app: #F4F7FE;
            --bg-white: #FFFFFF;
            --text-dark: #2B3674;
            --text-gray: #A3AED0;
            --accent-purple: #4318FF;
            --accent-purple-light: #F4F7FE;
            --accent-green: #05CD99;
            --accent-red: #EE5D50;
            --border-color: #E2E8F0;
            --shadow-card: 0px 18px 40px rgba(112, 144, 176, 0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }
        
        a { text-decoration: none !important; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink: 0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb i { color: var(--accent-purple); font-size: 18px; }

        .layout-grid { display: grid; grid-template-columns: 320px 1fr; gap: 30px; }

        .glass-card { background: var(--bg-white); border-radius: 20px; padding: 25px; box-shadow: var(--shadow-card); border: 1px solid var(--border-color); align-self: start; }
        .card-header { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display:flex; align-items:center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom:15px; }

        .settings-nav { display: flex; flex-direction: column; gap: 10px; }
        .settings-nav a { display: flex; align-items: center; gap: 15px; padding: 16px 20px; border-radius: 12px; font-size: 14px; font-weight: 700; color: var(--text-gray); text-decoration: none; transition: 0.3s; border: 2px solid transparent;}
        .settings-nav a i { font-size: 18px; width: 24px; text-align: center; }
        .settings-nav a:hover { background: var(--bg-app); color: var(--accent-purple); }
        .settings-nav a.active { background: var(--accent-purple); color: #FFF; box-shadow: 0 10px 20px rgba(67,24,255,0.2); pointer-events: none; }

        .premium-table { width: 100%; border-collapse: collapse; }
        .premium-table th { padding: 15px; text-align: left; font-size: 12px; font-weight: 800; color: var(--text-gray); text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
        .premium-table td { padding: 15px; font-size: 14px; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .premium-table tr:hover td { background: var(--bg-app); }

        .btn-new { background: var(--accent-purple); color: #FFF; font-weight: 700; font-size: 14px; padding: 10px 20px; border-radius: 10px; border: none; cursor: pointer; transition: 0.3s; }
        .btn-new:hover { background: #3210c4; box-shadow: 0 5px 15px rgba(67,24,255,0.3); }

        .action-icon { display:inline-flex; width:32px; height:32px; align-items:center; justify-content:center; border-radius:8px; cursor:pointer; transition:0.3s; margin:0 3px; font-size:14px; color:#FFF; }
        .action-edit { background: var(--accent-purple); box-shadow: 0 4px 10px rgba(67,24,255,0.2); }
        .action-del { background: var(--accent-red); box-shadow: 0 4px 10px rgba(238,93,80,0.2); }
        .action-edit:hover, .action-del:hover { transform: translateY(-2px); color:#FFF; opacity:0.9; }

        /* Modal Resets */
        .modal-content { border-radius: 20px; border: none; box-shadow: var(--shadow-card); background: var(--bg-white); }
        .modal-header { border-bottom: 1px solid var(--border-color); padding: 25px; }
        .modal-title { font-weight: 800; font-size: 20px; color: var(--text-dark); }
        .modal-body { padding: 25px; }

        .form-control { width: 100%; background: var(--bg-app); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 18px; font-size: 14px; font-weight: 600; color: var(--text-dark); outline:none; transition:0.3s; margin-bottom: 15px; }
        .form-control:focus { border-color: var(--accent-purple); box-shadow: 0 0 0 4px var(--accent-purple-light); background: #FFF; }

        .perm-group { margin-bottom: 15px; background: var(--bg-app); padding: 15px; border-radius: 12px; display:flex; align-items:center; justify-content:space-between; }
        .perm-group .perm-label { font-size: 14px; font-weight: 700; color: var(--text-dark); display:flex; align-items:center; gap:10px; }
        .perm-group .perm-label i { width: 20px; text-align:center; color: var(--accent-purple); }
        .perm-cat { font-weight: 800; font-size: 15px; color: var(--text-gray); margin-top:20px; margin-bottom:10px; border-bottom: 2px solid var(--border-color); padding-bottom:5px; text-transform:uppercase;}

        /* SIDEBAR SUPPORT */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i, .nav-item img { width: 22px; text-align: center; }
        .nav-item:hover, .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <i class="fas fa-cog"></i> 
                    <span>Configuration & Defaults / Staff Management</span>
                </div>
            </header>

            <div class="layout-grid">
                
                <!-- Left: Settings Menu -->
                <div class="glass-card">
                    <div class="card-header">
                        <div><i class="fas fa-sliders-h" style="color:var(--text-gray); margin-right:8px;"></i> System Settings</div>
                    </div>
                    
                    <?php if($AdminID == 1){ ?>
                    <div class="settings-nav">
                        <a href="settings-profile.php"><i class="fas fa-user-shield"></i> Master Profile</a>
                        <a href="settings-staff-accounts.php" class="active"><i class="fas fa-users-cog"></i> Staff Accounts</a>
                        <a href="settings-delivery-zone.php"><i class="fas fa-map-marked-alt"></i> Delivery Zones</a>
                        <a href="bakat.php"><i class="fas fa-box-open"></i> App Packages</a>
                    </div>
                    <?php } else { ?>
                    <div class="settings-nav">
                        <a href="settings-profile.php"><i class="fas fa-user-shield"></i> Agent Profile</a>
                    </div>
                    <?php } ?>
                </div>

                <!-- Right: Staff Accounts Manager -->
                <div class="glass-card">
                    <div class="card-header">
                        <div><i class="fas fa-users" style="color:var(--accent-purple); margin-right:8px;"></i> Authorized Staff</div>
                        <button class="btn-new" data-toggle="modal" data-target="#modal-staff"><i class="fas fa-plus"></i> New Staff</button>
                    </div>

                    <div style="overflow-x:auto;">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Password</th>
                                    <th>Function / Role</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = mysqli_query($con,"SELECT * FROM Admin");
                                while($row = mysqli_fetch_assoc($res)){ 
                                ?>
                                <tr>
                                    <td style="font-weight:700; color:var(--accent-purple);"><i class="fas fa-user-astronaut" style="color:var(--text-gray); margin-right:8px;"></i> <?php echo $row["AdminName"]; ?></td>
                                    <td><span style="letter-spacing:2px; font-weight:800; color:var(--text-gray);">••••••••</span></td>
                                    <td><span style="background:rgba(67, 24, 255, 0.1); color:var(--accent-purple); padding:4px 10px; border-radius:20px; font-size:11px; font-weight:800; text-transform:uppercase;"><?php echo $row["Functionn"]; ?></span></td>
                                    <td class="text-center">
                                        <a href="#" class="action-icon action-edit" title="Edit Staff"><i class="fas fa-pen"></i></a>
                                        <a href="deleteAdminAPI.php?id=<?php echo $row["AdminID"]; ?>" class="action-icon action-del" title="Revoke Access"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php } ?> 
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal Form -->
    <div class="modal fade" id="modal-staff" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus" style="color:var(--accent-purple); margin-right:10px;"></i> Provision New Staff Identity</h5>
                    <button type="button" class="close" data-dismiss="modal" style="border:none; background:transparent; font-size:24px; color:var(--text-gray); cursor:pointer;">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="AddAdminApi.php" method="POST">
                        
                        <div class="row">
                            <div class="col-md-6"><input type="text" placeholder="Full Name" class="form-control" name="AdminName" required></div>
                            <div class="col-md-6"><input type="email" placeholder="Email Address" class="form-control" name="Email" required></div>
                            <div class="col-md-4"><input type="text" placeholder="Department / Function" class="form-control" name="Function"></div>
                            <div class="col-md-4"><input type="text" placeholder="Phone Number" class="form-control" name="Phone"></div>
                            <div class="col-md-4"><input type="password" placeholder="Secure Password" class="form-control" name="AdminPassword" required></div>
                        </div>

                        <div style="background:var(--accent-purple-light); padding:15px; border-radius:12px; margin:20px 0; border:1px solid rgba(67,24,255,0.2); display:flex; justify-content:space-between; align-items:center;">
                            <div style="font-weight:800; color:var(--accent-purple);">Grand Master Rights (All Systems)</div>
                            <div class="custom-control custom-switch" style="transform:scale(1.2); margin-right:10px;">
                                <input type="checkbox" name="all" class="custom-control-input" id="checkAll">
                                <label class="custom-control-label" for="checkAll"></label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="perm-cat">Users & Drivers</div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-users"></i> Users page</span><input type="checkbox" name="Userspage"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-id-card"></i> User info</span><input type="checkbox" name="UserInformation"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-download"></i> Download data</span><input type="checkbox" name="DownloadUsersData"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-motorcycle"></i> Drivers page</span><input type="checkbox" name="DriversPage"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-plus"></i> Add driver</span><input type="checkbox" name="AddNewDriver"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-address-card"></i> Driver profile</span><input type="checkbox" name="DriverProfile"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="perm-cat">Stores & Commerce</div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-store"></i> Shops page</span><input type="checkbox" name="ShopsPage"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-store-alt"></i> Add Shop</span><input type="checkbox" name="AddNewShop"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-store-alt-slash"></i> Shop Profile</span><input type="checkbox" name="ShopProfile"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-box"></i> Orders page</span><input type="checkbox" name="OrdersPage"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-search-dollar"></i> Order details</span><input type="checkbox" name="OrderDetails"></div>
                                <div class="perm-group"><span class="perm-label"><i class="fas fa-wallet"></i> Wallet page</span><input type="checkbox" name="WalletPage"></div>
                            </div>
                            <div class="col-md-12">
                                <div class="perm-cat">Core System & Settings</div>
                                <div class="row">
                                    <div class="col-md-4"><div class="perm-group"><span class="perm-label"><i class="fas fa-sliders-h"></i> Add Slides</span><input type="checkbox" name="AddSlides"></div></div>
                                    <div class="col-md-4"><div class="perm-group"><span class="perm-label"><i class="fas fa-route"></i> Controls</span><input type="checkbox" name="ControleDistance"></div></div>
                                    <div class="col-md-4"><div class="perm-group"><span class="perm-label"><i class="fas fa-tags"></i> Categories</span><input type="checkbox" name="Categores"></div></div>
                                    <div class="col-md-4"><div class="perm-group"><span class="perm-label"><i class="fas fa-bell"></i> Notifications</span><input type="checkbox" name="Notification"></div></div>
                                    <div class="col-md-4"><div class="perm-group"><span class="perm-label"><i class="fas fa-ban"></i> Blacklist</span><input type="checkbox" name="blacklistr"></div></div>
                                    <div class="col-md-4"><div class="perm-group"><span class="perm-label"><i class="fas fa-money-check-alt"></i> Payments</span><input type="checkbox" name="Payments"></div></div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" style="width:100%;" class="btn-new" style="margin-top:20px; font-size:16px; padding:15px;"><i class="fas fa-shield-alt"></i> Provision Staff Administrator</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>