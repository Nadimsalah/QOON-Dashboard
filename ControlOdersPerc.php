<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

// 1. Load Subscription Packages
$baka1 = 0; $baka2 = 0;
$res = mysqli_query($con,"SELECT Price FROM Bakat");
$i = 1;
while($row = mysqli_fetch_assoc($res)){
    if($i == 1) $baka1 = $row["Price"];
    if($i == 2) $baka2 = $row["Price"];
    $i++;
}

// 2. Global Operational Thresholds
$DriverCommesion = 0; $MoneyStopNumber = 0; $subscription = 0;
$resStop = mysqli_query($con,"SELECT * FROM MoneyStop");
if($row = mysqli_fetch_assoc($resStop)){ 
    $DriverCommesion = $row["DriverCommesion"];
    $MoneyStopNumber = $row["MoneyStopNumber"];
    $subscription = $row["subscription"];
}

// 3. Platform Commissions & Percentages
$SendMoneyPerc = 0; $getMoneyPerc = 0; $disUser = 0;
$resPerc = mysqli_query($con,"SELECT SendMoneyPerc, getMoneyPerc, disUser, percent FROM OrdersJiblerpercentage LIMIT 1");
if($row = mysqli_fetch_assoc($resPerc)){
    $SendMoneyPerc = $row["SendMoneyPerc"];
    $getMoneyPerc  = $row["getMoneyPerc"];
    $disUser       = $row["disUser"];
}

// 4. Per-Category Commission Models
$categoriesList = [];
$resCats = mysqli_query($con,"SELECT CategoryId, EnglishCategory, Photo, PercForOrder FROM Categories WHERE (CategoryId != 56 AND CategoryId != 55) ORDER BY priority DESC");
if($resCats) {
    while($row = mysqli_fetch_assoc($resCats)){
        $categoriesList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Core Logic & Pricing Controls | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="js/jquery-3.2.1.min.js"></script>

    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-green: #10B981; --accent-blue: #007AFF; --accent-orange: #F59E0B; --accent-red: #EF4444;
            --border-color: #F0F2F6;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --shadow-float: 0 12px 35px rgba(0, 0, 0, 0.05);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink:0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        .super-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; }

        .panel-card { background: var(--bg-white); border-radius: 20px; padding: 35px; box-shadow: var(--shadow-card); border: 1px solid var(--border-color); }
        .panel-header { display:flex; align-items:center; gap:12px; margin-bottom: 25px; border-bottom: 1px solid var(--border-color); padding-bottom: 20px; }
        .panel-header i { font-size: 24px; color: var(--accent-purple); }
        .panel-header h2 { font-size: 18px; font-weight: 800; color: var(--text-dark); }
        .panel-header p { font-size: 13px; font-weight: 600; color: var(--text-gray); }

        /* Modernized Category Grid Assembly */
        .cats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .cat-bubble { border: 1px solid var(--border-color); background: var(--bg-app); border-radius: 16px; padding: 20px; text-align: center; transition: 0.2s; box-shadow: inset 0 2px 10px rgba(0,0,0,0.01); }
        .cat-bubble:hover { border-color: var(--accent-purple); background: var(--bg-white); box-shadow: var(--shadow-float); transform: translateY(-3px); }
        .cat-bubble img { height: 60px; width: auto; object-fit: contain; margin-bottom: 15px; border-radius:8px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); }
        .cat-bubble h5 { font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 15px; word-break: break-word; }
        .cat-bubble .inp-wrap { position: relative; background: var(--bg-white); border-radius: 8px; }
        .cat-bubble input { width: 100%; border: 1px solid #E2E8F0; border-radius: 8px; padding: 10px 30px 10px 15px; font-size: 14px; font-weight: 800; color: var(--accent-purple); text-align: center; transition: 0.2s; outline:none; background:transparent;}
        .cat-bubble input:focus { border-color: var(--accent-purple); box-shadow: 0 0 0 3px var(--accent-purple-light); }
        .cat-bubble .inp-wrap span { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 12px; font-weight: 800; color: var(--text-gray); pointer-events: none; }

        .btn-update { width: 100%; border: none; background: var(--accent-purple); color: #FFF; padding: 16px; border-radius: 12px; font-size: 15px; font-weight: 700; cursor: pointer; transition: 0.2s; display:flex; justify-content:center; align-items:center; gap:8px;}
        .btn-update:hover { background: #4A2BBF; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(98, 60, 234, 0.3); }

        .global-form { display: flex; flex-direction: column; gap: 30px; }
        .form-section { background: var(--bg-app); border-radius: 16px; padding: 25px; border: 1px solid var(--border-color); }
        .form-section h4 { font-size: 14px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display: flex; align-items:center; gap:8px; text-transform:uppercase; letter-spacing:0.5px;}
        
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
        .input-row { display: flex; gap: 20px; }
        .input-wrap-f { flex: 1; position: relative; display:flex; align-items:center;}
        .input-wrap-f .icon-pre { position:absolute; left:15px; color:var(--text-gray); font-size:14px; }
        .input-wrap-f input { width: 100%; background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 10px; padding: 14px 15px 14px 40px; font-size: 14px; font-weight: 700; color: var(--text-dark); transition: 0.2s; outline:none; box-shadow: 0 2px 5px rgba(0,0,0,0.02);}
        .input-wrap-f input:focus { border-color: var(--accent-purple); box-shadow: 0 0 0 3px var(--accent-purple-light); }
        .input-wrap-f span.unit { position: absolute; right: 15px; font-size: 13px; font-weight: 800; color: var(--text-gray); background: var(--bg-app); padding: 4px 8px; border-radius: 6px; pointer-events:none;}
        .loader-overlay { display: none; position: fixed; inset:0; background:rgba(255,255,255,0.9); z-index:9999; justify-content:center; align-items:center; flex-direction:column; gap:15px; backdrop-filter: blur(5px);}
    </style>
</head>
<body>
    <div class="loader-overlay" id="globalLoader">
        <i class="fas fa-circle-notch fa-spin fa-4x" style="color:var(--accent-purple);"></i>
        <h2 style="font-weight:800; color:var(--text-dark);">Synchronizing Logic Nodes...</h2>
    </div>

    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="index.php"><i class="fas fa-home"></i> Core Dashboard</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">Platform Financial Logic & Controls</span>
                </div>
            </header>

            <div class="super-grid">
                
                <!-- LEFT PANEL: Dynamic Category Percentages -->
                <div class="panel-card" style="align-self: start;">
                    <div class="panel-header">
                        <div>
                            <h2><i class="fas fa-layer-group"></i> Dynamic Store Commissions</h2>
                            <p>Independently adjust fractional sales percentages levied by business sector category.</p>
                        </div>
                    </div>
                    
                    <div class="cats-grid">
                        <script> var categoryIds = []; </script>
                        <?php foreach($categoriesList as $cat): ?>
                        <div class="cat-bubble">
                            <img src="<?= htmlspecialchars($cat['Photo']) ?>" onerror="this.src='images/placeholder.png'">
                            <h5 title="<?= htmlspecialchars($cat['EnglishCategory']) ?>"><?= htmlspecialchars($cat['EnglishCategory']) ?></h5>
                            <div class="inp-wrap">
                                <input type="number" id="<?= $cat['CategoryId'] ?>" value="<?= $cat['PercForOrder'] ?>" name="PercForOrder" step="0.1">
                                <span>%</span>
                            </div>
                            <script> categoryIds.push(<?= $cat['CategoryId'] ?>); </script>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button class="btn-update" onclick="saveForCat();">
                        <i class="fas fa-cloud-upload-alt"></i> Apply Category Modifications
                    </button>
                    
                </div>


                <!-- RIGHT PANEL: Master Global Form Variables -->
                <div class="panel-card">
                    <form class="global-form" method="POST" action="ControlOrderPercUpdateApi.php">
                        
                        <div class="panel-header" style="border:none; padding-bottom:0; margin-bottom:10px;">
                            <div>
                                <h2><i class="fas fa-sliders-h" style="color:var(--accent-orange)"></i> Global Logic Parameters</h2>
                                <p>Strict modification of root variable thresholds impacting financial logic bounds.</p>
                            </div>
                        </div>

                        <!-- Subscriptions Block -->
                        <div class="form-section">
                            <h4><i class="fas fa-gem" style="color:var(--accent-blue)"></i> Store Subscription Tiers</h4>
                            <div class="input-row">
                                <div class="input-group" style="flex:1;">
                                    <label>Premium Package Rate</label>
                                    <div class="input-wrap-f">
                                        <i class="fas fa-money-bill icon-pre"></i>
                                        <input type="number" name="Premium" value="<?= $baka1 ?>" step="0.1">
                                        <span class="unit">MAD</span>
                                    </div>
                                </div>
                                <div class="input-group" style="flex:1;">
                                    <label>Premium Plus Strategy</label>
                                    <div class="input-wrap-f">
                                        <i class="fas fa-money-bill-wave icon-pre"></i>
                                        <input type="number" name="PremiumPlus" value="<?= $baka2 ?>" step="0.1">
                                        <span class="unit">MAD</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Drivers Logic Block -->
                        <div class="form-section">
                            <h4><i class="fas fa-motorcycle" style="color:var(--accent-green)"></i> Delivery Constants</h4>
                            
                            <div class="input-group">
                                <label>Driver Added Value Commission</label>
                                <div class="input-wrap-f">
                                    <i class="fas fa-plus-circle icon-pre"></i>
                                    <input type="number" name="DriverCommesion" value="<?= $DriverCommesion ?>" step="0.1">
                                    <span class="unit">MAD</span>
                                </div>
                            </div>
                            
                            <div class="input-row">
                                <div class="input-group" style="flex:1;">
                                    <label>Penalty System [Stop Money]</label>
                                    <div class="input-wrap-f">
                                        <i class="fas fa-hand-paper icon-pre"></i>
                                        <input type="number" name="MoneyStopNumber" value="<?= $MoneyStopNumber ?>" step="0.1">
                                        <span class="unit">MAD</span>
                                    </div>
                                </div>
                                <div class="input-group" style="flex:1;">
                                    <label>Driver Subscription</label>
                                    <div class="input-wrap-f">
                                        <i class="fas fa-id-card icon-pre"></i>
                                        <input type="number" name="subscription" value="<?= $subscription ?>" step="0.1">
                                        <span class="unit">MAD</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ledger Cuts Block -->
                        <div class="form-section">
                            <h4><i class="fas fa-exchange-alt" style="color:var(--accent-purple)"></i> Ledger Flow Rates</h4>
                            <div class="input-row">
                                <div class="input-group" style="flex:1;">
                                    <label>Balance Transfer Overhead</label>
                                    <div class="input-wrap-f">
                                        <i class="fas fa-percentage icon-pre"></i>
                                        <input type="number" name="SendMoneyPerc" value="<?= $SendMoneyPerc ?>" step="0.1">
                                        <span class="unit">%</span>
                                    </div>
                                </div>
                                <div class="input-group" style="flex:1;">
                                    <label>Balance Withdrawal Cut</label>
                                    <div class="input-wrap-f">
                                        <i class="fas fa-percentage icon-pre"></i>
                                        <input type="number" name="getMoneyPerc" value="<?= $getMoneyPerc ?>" step="0.1">
                                        <span class="unit">%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group" style="margin-bottom:0;">
                                <label>System Service Retainer Fee</label>
                                <div class="input-wrap-f">
                                    <i class="fas fa-percentage icon-pre"></i>
                                    <input type="number" name="disUser" value="<?= $disUser ?>" step="0.1">
                                    <span class="unit">%</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-update" style="background:var(--text-dark);">
                            <i class="fas fa-lock"></i> Commit Global Constants Database
                        </button>

                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Legacy API Preservation -->
    <script>
        function saveForCat() {
            document.getElementById('globalLoader').style.display = 'flex';
            let promises = [];

            categoryIds.forEach(function (id) {
                var inputElement = document.getElementById(id);
                if (inputElement) {
                    var inputValue = inputElement.value;
                    var formData = new FormData();
                    formData.append("CategoryId", id);
                    formData.append("PercForOrder", inputValue);
                    
                    let p = $.ajax({
                        url: "UpdateCatsPercValuesApi.php",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        cache: false
                    });
                    promises.push(p);
                }
            });

            // Wait for all AJAX modifications to succeed before hard-refresh
            $.when.apply($, promises).then(function() {
                location.reload();
            });
        }
    </script>
</body>
</html>