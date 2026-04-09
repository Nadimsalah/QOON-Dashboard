<?php require "conn.php"; 
$AdminID = $_COOKIE["AdminID"] ?? '';
$AdminName = $_COOKIE["AdminName"] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings | QOON Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="fontawesome-kit-5/css/all.css" rel="stylesheet">
    <style>
        :root {
            --bg-app: #F4F7FE;
            --bg-white: #FFFFFF;
            --text-dark: #2B3674;
            --text-gray: #A3AED0;
            --accent-purple: #4318FF;
            --accent-purple-light: #F4F7FE;
            --border-color: #E2E8F0;
            --shadow-card: 0px 18px 40px rgba(112, 144, 176, 0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

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

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
        .form-control { width: 100%; background: var(--bg-app); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 18px; font-size: 14px; font-weight: 600; color: var(--text-dark); outline:none; transition:0.3s; }
        .form-control:focus { border-color: var(--accent-purple); box-shadow: 0 0 0 4px var(--accent-purple-light); background: #FFF; }

        .btn-submit { display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; padding: 16px; background: var(--accent-purple); color: #FFF; font-weight: 800; font-size: 15px; border-radius: 14px; border: none; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(67, 24, 255, 0.2); margin-top:30px;}
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(67, 24, 255, 0.3); background: #3210c4; }

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
                    <span>Configuration & Defaults / Profile Options</span>
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
                        <a href="settings-profile.php" class="active"><i class="fas fa-user-shield"></i> Master Profile</a>
                        <a href="settings-staff-accounts.php"><i class="fas fa-users-cog"></i> Staff Accounts</a>
                        <a href="settings-delivery-zone.php"><i class="fas fa-map-marked-alt"></i> Delivery Zones</a>
                        <a href="bakat.php"><i class="fas fa-box-open"></i> App Packages</a>
                    </div>
                    <?php } else { ?>
                    <div class="settings-nav">
                        <a href="settings-profile.php" class="active"><i class="fas fa-user-shield"></i> Agent Profile</a>
                    </div>
                    <?php } ?>
                </div>

                <!-- Right: Settings Form -->
                <div class="glass-card">
                    <div class="card-header">
                        <div><i class="fas fa-unlock-alt" style="color:var(--accent-purple); margin-right:8px;"></i> Security & Identity</div>
                    </div>

                    <form action="UpdateAdminAPI.php" method="POST">
                        <div class="form-group">
                            <label>Administrator Auth Scope</label>
                            <input type="text" class="form-control" name="AdminName" value="<?php echo htmlspecialchars($AdminName); ?>" style="opacity:0.8;">
                        </div>

                        <div class="form-group" style="margin-top:25px;">
                            <label>Update Password Registry</label>
                            <input type="text" class="form-control" name="AdminPassword" placeholder="Enter new secured password" required>
                            <input type="hidden" name="AdminID" value="<?php echo htmlspecialchars($AdminID); ?>">
                        </div>

                        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Save Security Changes</button>
                    </form>
                </div>

            </div>
        </main>
    </div>
</body>
</html>