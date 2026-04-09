<?php require "conn.php"; 
$AdminID = $_COOKIE["AdminID"] ?? '';
$AdminName = $_COOKIE["AdminName"] ?? '';

$features = [
    'DigitalStoreCreation' => 'Digital Store Creation',
    'FullControlOfStore' => 'Full Control Of Store',
    'AddMoreThanFiveProduct' => 'Add > 5 Products',
    'ReceiveOrder' => 'Receive Orders',
    'TrackAndManageOrder' => 'Track & Manage Orders',
    'DeliveryServiceRequest' => 'Delivery Service Request',
    'JiblerPay' => 'QOON Pay Integration',
    'JiblerCard' => 'QOON Card Access',
    'WithdrawProfits' => 'Withdraw Profits',
    'JiblerBoost' => 'QOON Ad Boost',
    'BoostNowPayLater' => 'Boost Now Pay Later',
    'OrganicCEO' => 'Organic CEO Dashboard',
    '5StoriesPerMonth' => '5 Stories / Month',
    '5PublicationMonth' => '5 Posts / Month',
    'InteractionWithCustomers' => 'Customer Interactions',
    'Hosting' => 'Cloud Hosting Server'
];

$packages = [
    1 => 'Free Tier', 
    2 => 'Premium Pro', 
    3 => 'Premium Plus'
];

$bakatData = [];
$res = mysqli_query($con, "SELECT * FROM Bakat WHERE BakatID IN (1,2,3)");
while($row = mysqli_fetch_assoc($res)) {
    $bakatData[$row['BakatID']] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Packages | QOON Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="fontawesome-kit-5/css/all.css" rel="stylesheet">
    <!-- Bootstrap Modals Support -->
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

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink: 0; }
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
        .premium-table th { padding: 15px; text-align: left; font-size: 13px; font-weight: 800; color: var(--text-gray); text-transform: uppercase; border-bottom: 2px solid var(--border-color); vertical-align:middle; }
        .premium-table td { padding: 15px; font-size: 14px; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .premium-table tr:hover td { background: var(--bg-app); }
        
        .feature-name { font-weight: 800; display:flex; align-items:center; gap:10px; color:var(--text-dark); }
        .feature-name i { color:var(--accent-purple); font-size:16px; width:20px; text-align:center; }
        
        .status-badge { display:inline-flex; align-items:center; justify-content:center; padding:6px 12px; border-radius:20px; font-size:12px; font-weight:800; text-transform:uppercase; }
        .status-yes { background:rgba(5, 205, 153, 0.1); color:var(--accent-green); }
        .status-no { background:rgba(238, 93, 80, 0.1); color:var(--accent-red); }

        .btn-update { background: var(--bg-app); color: var(--accent-purple); border: 1px solid var(--border-color); padding: 8px 16px; border-radius: 12px; font-size: 13px; font-weight: 800; cursor: pointer; transition: 0.3s; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-update:hover { background: var(--accent-purple); color: #FFF; border-color: var(--accent-purple); box-shadow: 0 5px 15px rgba(67, 24, 255, 0.2); }

        /* Modal Overrides */
        .modal-content { border-radius: 20px; border: none; box-shadow: var(--shadow-card); background: var(--bg-white); }
        .modal-header { border-bottom: 1px solid var(--border-color); padding: 25px; }
        .modal-title { font-weight: 800; font-size: 20px; color: var(--text-dark); }
        .modal-body { padding: 25px; }

        .perm-group { margin-bottom: 12px; background: var(--bg-app); padding: 12px 18px; border-radius: 12px; display:flex; align-items:center; justify-content:space-between; border: 1px solid var(--border-color); }
        .perm-group .perm-label { font-size: 14px; font-weight: 700; color: var(--text-dark); display:flex; align-items:center; gap:10px; }
        .btn-save-modal { width: 100%; padding: 16px; background: var(--accent-purple); color: #FFF; font-weight: 800; font-size: 15px; border-radius: 14px; border: none; cursor: pointer; transition: 0.3s; margin-top:20px;}
        .btn-save-modal:hover { background: #3210c4; box-shadow: 0 10px 20px rgba(67, 24, 255, 0.2); }

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
                    <span>Configuration & Defaults / Service Packages</span>
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
                        <a href="settings-staff-accounts.php"><i class="fas fa-users-cog"></i> Staff Accounts</a>
                        <a href="settings-delivery-zone.php"><i class="fas fa-map-marked-alt"></i> Delivery Zones</a>
                        <a href="bakat.php" class="active"><i class="fas fa-box-open"></i> App Packages</a>
                    </div>
                    <?php } else { ?>
                    <div class="settings-nav">
                        <a href="settings-profile.php"><i class="fas fa-user-shield"></i> Agent Profile</a>
                    </div>
                    <?php } ?>
                </div>

                <!-- Right: Package Configurator -->
                <div class="glass-card" style="padding:0; overflow:hidden;">
                    <div class="card-header" style="padding:25px 25px 0 25px; border-bottom:none;">
                        <div><i class="fas fa-cubes" style="color:var(--accent-purple); margin-right:8px;"></i> Packages Capabilities Configurator</div>
                    </div>

                    <div style="overflow-x:auto;">
                        <table class="premium-table">
                            <thead>
                                <tr style="background:var(--bg-app);">
                                    <th style="padding:20px 25px;">Feature Definition</th>
                                    <?php foreach($packages as $pid => $pname): ?>
                                    <th style="text-align:center; min-width:160px; padding:20px 15px;">
                                        <div style="font-size:15px; color:var(--text-dark); margin-bottom:12px;"><i class="fas fa-cube" style="color:var(--text-gray); margin-right:8px;"></i> <?php echo $pname; ?></div>
                                        <button class="btn-update" data-toggle="modal" data-target="#modal-pkg-<?php echo $pid; ?>"><i class="fas fa-sliders-h"></i> Configure</button>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($features as $key => $display): ?>
                                <tr>
                                    <td style="padding-left:25px;">
                                        <div class="feature-name">
                                            <i class="fas fa-check-circle"></i> <?php echo $display; ?>
                                        </div>
                                    </td>
                                    <?php foreach($packages as $pid => $pname): 
                                        $val = $bakatData[$pid][$key] ?? 'NO';
                                    ?>
                                    <td style="text-align:center;">
                                        <?php if($val == 'YES'): ?>
                                            <span class="status-badge status-yes"><i class="fas fa-check" style="margin-right:5px;"></i> Active</span>
                                        <?php else: ?>
                                            <span class="status-badge status-no"><i class="fas fa-times" style="margin-right:5px;"></i> Disabled</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Generate Modals Dynamically -->
    <?php foreach($packages as $pid => $pname): ?>
    <div class="modal fade" id="modal-pkg-<?php echo $pid; ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-box-open" style="color:var(--accent-purple); margin-right:10px;"></i> Tune <?php echo $pname; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" style="border:none; background:transparent; font-size:24px; color:var(--text-gray); cursor:pointer;">&times;</button>
                </div>
                <div class="modal-body">
                    <form action="UpdateBakaApi.php" method="POST">
                        <input type="hidden" name="BakatID" value="<?php echo $pid; ?>">
                        
                        <div style="max-height:60vh; overflow-y:auto; padding-right:10px;">
                            <?php foreach($features as $key => $display): 
                                $isChecked = ($bakatData[$pid][$key] ?? 'NO') == 'YES' ? 'checked' : '';
                            ?>
                            <div class="perm-group">
                                <span class="perm-label"><i class="fas fa-check-circle" style="color:var(--text-gray);"></i> <?php echo $display; ?></span>
                                <div class="custom-control custom-switch" style="transform:scale(1.2); margin-right:5px;">
                                    <input type="checkbox" class="custom-control-input" id="switch-<?php echo $pid; ?>-<?php echo $key; ?>" name="<?php echo $key; ?>" <?php echo $isChecked; ?>>
                                    <label class="custom-control-label" for="switch-<?php echo $pid; ?>-<?php echo $key; ?>"></label>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <button type="submit" class="btn-save-modal"><i class="fas fa-cloud-upload-alt"></i> Save Capability Matrix</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>