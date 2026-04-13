<?php
require "conn.php";

// Fetch Current Limits
$res = mysqli_query($con, "SELECT * FROM MoneyStop");
$MoneyStopNumber = 0;
$subscription = 0;
$DriverCommesion = 0;

if ($row = mysqli_fetch_assoc($res)) {
    $MoneyStopNumber = $row["MoneyStopNumber"];
    $subscription = $row["subscription"];
    $DriverCommesion = $row["DriverCommesion"];
}

// Determine Type
$Type = $_GET["Type"] ?? $_POST["Type"] ?? "limit";

$pageTitle = "";
$pageSubtitle = "";
$icon = "";
$accentColor = "";
$currentValue = "";
$inputName = "";

if ($Type == "limit") {
    $pageTitle = "Driver Debt Limit";
    $pageSubtitle = "Set the maximum unpaid cash debt a driver can hold before their account is suspended.";
    $icon = "fa-hand-paper";
    $accentColor = "#E11D48";
    $currentValue = $MoneyStopNumber;
    $inputName = "MoneyStopNumber";
} else if ($Type == "Subscription") {
    $pageTitle = "Driver Subscription Fee";
    $pageSubtitle = "Configure the monthly base subscription fee required for driver network access.";
    $icon = "fa-sync";
    $accentColor = "#10B981";
    $currentValue = $subscription;
    $inputName = "subscription";
} else {
    $pageTitle = "Driver Commission Rate";
    $pageSubtitle = "Adjust the percentage commission taken by the system per completed delivery.";
    $icon = "fa-percentage";
    $accentColor = "#623CEA";
    $currentValue = $DriverCommesion;
    $inputName = "DriverCommesion";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --border-color: #F0F2F6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); height: 100vh; display: flex; overflow: hidden; }

        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        /* Unified Sidebar CSS */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item:hover:not(.active) { color: var(--text-dark); background: #F8F9FB; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; justify-content: center; align-items: center;}

        .header { position: absolute; top: 35px; left: 300px; right: 40px; display: flex; align-items: center; justify-content: space-between; }
        .back-btn { display: inline-flex; align-items: center; gap: 10px; padding: 12px 20px; border-radius: 14px; background: var(--bg-white); color: var(--text-dark); text-decoration: none; font-weight: 700; font-size: 14px; box-shadow: var(--shadow-card); transition: 0.2s; border: 1px solid var(--border-color); }
        .back-btn:hover { background: #F8F9FB; transform: translateY(-2px); box-shadow: 0 12px 25px rgba(0,0,0,0.05); color: var(--accent-purple); }

        .profile { display: flex; align-items: center; gap: 10px; cursor: pointer; padding-left: 10px; }
        .profile img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }

        /* Configuration Card */
        .config-card { background: var(--bg-white); border-radius: 24px; padding: 50px; box-shadow: var(--shadow-card); border: 1px solid rgba(255,255,255,0.8); width: 100%; max-width: 500px; text-align: center; position: relative;}
        
        .config-icon { width: 80px; height: 80px; border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 32px; color: <?= $accentColor ?>; background: <?= $accentColor ?>20; margin: 0 auto 25px; box-shadow: 0 10px 25px <?= $accentColor ?>20; }

        .config-title { font-size: 24px; font-weight: 800; color: var(--text-dark); margin-bottom: 10px; letter-spacing: -0.5px;}
        .config-sub { color: var(--text-gray); font-size: 14px; font-weight: 500; line-height: 1.5; margin-bottom: 35px; }

        .input-group { display: flex; flex-direction: column; text-align: left; margin-bottom: 25px; position: relative;}
        .input-group label { font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.5px;}
        
        .input-wrapper { position: relative; }
        .input-wrapper i { position: absolute; left: 20px; top: 50%; transform: translateY(-50%); color: var(--text-gray); font-size: 18px; }
        .input-wrapper span { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); color: var(--text-dark); font-weight: 800; font-size: 14px; }
        .input-wrapper input { width: 100%; padding: 18px 50px; border-radius: 16px; border: 2px solid var(--border-color); background: #F8F9FA; color: <?= $accentColor ?>; font-size: 20px; font-weight: 800; outline: none; transition: 0.2s; font-family: 'Inter', sans-serif; }
        .input-wrapper input:focus { background: #FFF; border-color: <?= $accentColor ?>; box-shadow: 0 0 0 4px <?= $accentColor ?>15; }

        .btn-submit { background: <?= $accentColor ?>; color: #FFF; border: none; padding: 18px 24px; border-radius: 16px; font-weight: 800; font-size: 16px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; box-shadow: 0 10px 25px <?= $accentColor ?>40; }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 35px <?= $accentColor ?>60; }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }

            /* Hide desktop sidebar */
            .sidebar { display: none !important; }

            /* Header: drop absolute positioning — it breaks on mobile */
            .header {
                position: static;
                padding: 16px 16px 0;
                display: flex;
                align-items: center;
                justify-content: flex-start;
            }

            .back-btn { font-size: 13px; padding: 10px 16px; }

            /* Main panel: scrolls naturally, centered card */
            .main-panel {
                padding: 24px 16px 80px;
                justify-content: flex-start;
                align-items: center;
                overflow-y: visible;
            }

            /* Config card: narrower padding on tablet */
            .config-card { padding: 32px 24px; }
            .config-icon { width: 64px; height: 64px; font-size: 26px; border-radius: 16px; margin-bottom: 18px; }
            .config-title { font-size: 20px; }
            .config-sub  { font-size: 13px; margin-bottom: 24px; }

            .input-wrapper input { font-size: 18px; padding: 16px 50px; }
            .btn-submit { padding: 16px 20px; font-size: 15px; border-radius: 14px; }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .config-card { padding: 24px 18px; border-radius: 20px; }
            .config-icon { width: 56px; height: 56px; font-size: 22px; }
            .config-title { font-size: 18px; }
            .input-wrapper input { font-size: 16px; padding: 14px 46px; }
            .input-wrapper i { font-size: 16px; left: 16px; }
            .input-wrapper span { font-size: 13px; right: 16px; }
        }

    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>
        
        <header class="header">
            <a href="driver.php" class="back-btn"><i class="fas fa-arrow-left"></i> Drivers Dashboard</a>

        </header>

        <main class="main-panel">
            <div class="config-card">
                <div class="config-icon">
                    <i class="fas <?= $icon ?>"></i>
                </div>
                
                <h1 class="config-title"><?= $pageTitle ?></h1>
                <p class="config-sub"><?= $pageSubtitle ?></p>

                <form action="UpdateLimitApi.php" method="POST">
                    <input type="hidden" name="Type" value="<?= $Type ?>">

                    <div class="input-group">
                        <label>Active Configuration Value</label>
                        <div class="input-wrapper">
                            <i class="fas <?= $icon ?>"></i>
                            <input type="number" step="any" name="<?= $inputName ?>" value="<?= $currentValue ?>" required>
                            <span>MAD</span>
                        </div>
                    </div>

                    <!-- Hidden fields to prevent submission schema errors if the API rigidly requires them all to exist -->
                    <?php if($Type != 'limit'): ?><input type="hidden" name="MoneyStopNumber" value="<?= $MoneyStopNumber ?>"><?php endif; ?>
                    <?php if($Type != 'Subscription'): ?><input type="hidden" name="subscription" value="<?= $subscription ?>"><?php endif; ?>
                    <?php if($Type != 'DriverCommesion'): ?><input type="hidden" name="DriverCommesion" value="<?= $DriverCommesion ?>"><?php endif; ?>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check-circle"></i> Update Configuration
                    </button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>