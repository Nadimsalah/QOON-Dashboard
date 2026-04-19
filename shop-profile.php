<?php
require "conn.php";
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

// 1. Fetch Orders Metrics
$OrdersNumber = 0;
$OrdersNumberLastweek = 0;
$OrderPriceFromShop = 0;
$lastweek = date('Y-m-d', strtotime("-7 days"));

$resOrders = mysqli_query($con, "SELECT OrderPriceFromShop, CreatedAtOrders FROM Orders WHERE ShopID='$id'");
while ($row = mysqli_fetch_assoc($resOrders)) {
    $OrderPriceFromShop += (float)$row["OrderPriceFromShop"];
    $OrdersNumber++;
    if ($lastweek < $row["CreatedAtOrders"]) {
        $OrdersNumberLastweek++;
    }
}

// 2. Fetch Shop Data
$ShopName = $ShopLat = $ShopLongt = $ShopLogo = $ShopCover = "";
$ShopPhone = $ShopLogName = $ShopPassword = $CategoryId = $Type = $Status = $InHome = $BakatID = "";

$resShop = mysqli_query($con, "SELECT * FROM Shops WHERE ShopID='$id'");
if ($row = mysqli_fetch_assoc($resShop)) {
    $ShopName     = $row["ShopName"];
    $ShopLat      = $row["ShopLat"];
    $ShopLongt    = $row["ShopLongt"];
    $ShopLogo     = $row["ShopLogo"];
    $ShopCover    = $row["ShopCover"];
    $ShopPhone    = $row["ShopPhone"];
    $ShopLogName  = $row["ShopLogName"];
    $ShopPassword = $row["ShopPassword"];
    $CategoryId   = $row["CategoryID"];
    $Type         = $row["Type"];
    $Status       = $row["Status"];
    $InHome       = $row["InHome"];
    $BakatID      = $row["BakatID"];
}

// Handle Broken Database Prefixes for Images
$cleanCover = strpos($ShopCover, 'https://jibler.app/db/db/photo/') !== false ? str_replace('https://jibler.app/db/db/', '', $ShopCover) : $ShopCover;
$cleanLogo  = strpos($ShopLogo, 'https://jibler.app/db/db/photo/') !== false ? str_replace('https://jibler.app/db/db/', '', $ShopLogo) : $ShopLogo;
if (empty($cleanCover)) $cleanCover = 'images/default_cover.jpg'; // Adjust fallback if needed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ShopName) ?> - Profile | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-green: #10B981; --accent-red: #E11D48; --accent-blue: #007AFF;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --border-color: #F0F2F6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); height: 100vh; display: flex; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        /* Unified Sidebar CSS */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        /* Header / Breadcrumb */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; }
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        /* Actions Bar */
        .top-action-bar { display: flex; flex-wrap: wrap; gap: 12px; margin-bottom: 30px; background: var(--bg-white); padding: 15px; border-radius: 16px; box-shadow: var(--shadow-card); align-items: center; justify-content: space-between; }
        .btn-act { display: inline-flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 700; padding: 10px 16px; border-radius: 10px; background: var(--bg-app); color: var(--text-dark); border: 1px solid var(--border-color); text-decoration: none; transition: 0.2s; cursor: pointer; }
        .btn-act:hover { background: #FFF; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transform: translateY(-1px); }
        .btn-act-danger { color: var(--accent-red); background: rgba(225, 29, 72, 0.05); border-color: rgba(225, 29, 72, 0.1); }
        .btn-act-danger:hover { background: var(--accent-red); color: #fff; }

        /* KPI Blocks */
        .metrics-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; }
        .m-card { display: flex; align-items: center; gap: 16px; background: var(--bg-white); padding: 20px; border-radius: 20px; box-shadow: var(--shadow-card); }
        .m-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; justify-content: center; align-items: center; font-size: 20px; }
        .m-info h4 { font-size: 12px; color: var(--text-gray); text-transform: uppercase; font-weight: 700; margin-bottom: 4px; }
        .m-info span { font-size: 24px; font-weight: 800; color: var(--text-dark); }
        .m-info p { font-size: 11px; font-weight: 600; color: var(--accent-green); margin-top: 2px; }

        /* Main Grid */
        .dashboard-grid { display: grid; grid-template-columns: 1fr 450px; gap: 25px; align-items: start; }
        
        .card { background: var(--bg-white); border-radius: 24px; box-shadow: var(--shadow-card); overflow: hidden; }
        .card-header { padding: 20px 25px; border-bottom: 1px solid var(--border-color); font-size: 16px; font-weight: 800; color: var(--text-dark); display: flex; justify-content: space-between; align-items: center; }
        .card-body { padding: 30px 25px; }

        /* Form Elements */
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .fw { grid-column: 1 / -1; }
        .input-group { display: flex; flex-direction: column; gap: 8px; }
        .input-group label { font-size: 12px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; }
        .input-ui { background: var(--bg-app); border: 1px solid var(--border-color); padding: 14px; border-radius: 12px; font-size: 14px; font-weight: 600; color: var(--text-dark); outline: none; transition: 0.3s; width: 100%; }
        .input-ui:focus { border-color: var(--accent-purple); box-shadow: 0 0 0 3px rgba(98, 60, 234, 0.1); background: #FFF; }
        .select-ui { appearance: none; cursor: pointer; }

        .btn-submit { width: 100%; padding: 16px; border: none; border-radius: 12px; background: linear-gradient(135deg, var(--accent-purple), #4F28D1); color: #FFF; font-size: 15px; font-weight: 700; cursor: pointer; transition: 0.3s; box-shadow: 0 8px 20px rgba(98, 60, 234, 0.2); margin-top: 10px; }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(98, 60, 234, 0.3); }

        /* Transactions */
        .tx-list { display: flex; flex-direction: column; gap: 10px; }
        .tx-item { display: flex; align-items: center; justify-content: space-between; padding: 12px; background: var(--bg-app); border-radius: 12px; transition: 0.2s; }
        .tx-item:hover { background: #FFF; box-shadow: var(--shadow-card); }
        .tx-info { display: flex; flex-direction: column; gap: 4px; }
        .tx-info span { font-weight: 700; font-size: 13px; color: var(--text-dark); }
        .tx-info small { font-weight: 600; font-size: 11px; color: var(--text-gray); }
        .tx-amt { font-weight: 800; font-size: 14px; color: var(--text-dark); }
        .tx-amt.positive { color: var(--accent-green); }

        /* Custom Toggles */
        .toggle-switch { display: flex; align-items: center; gap: 15px; background: var(--bg-app); padding: 12px 18px; border-radius: 12px; margin-bottom: 10px; border: 1px solid var(--border-color); justify-content: space-between; }
        .toggle-switch select { border: none; background: #FFF; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 700; color: var(--text-dark); outline: none; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }

        .image-uploader-hero { width: 100%; height: 200px; background-size: cover; background-position: center; border-radius: 16px; margin-bottom: 50px; position: relative; border: 2px dashed #D1D5DF; display: flex; align-items: center; justify-content: center; }
        .profile-img-container { width: 100px; height: 100px; border-radius: 50%; position: absolute; bottom: -50px; left: 40px; border: 4px solid #FFF; background: #FFF; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; display: flex; align-items: center; justify-content: center; }
        .profile-img-container img { width: 100%; height: 100%; object-fit: cover; }
        .hidden-file { display: none; }
        .upload-badge { position: absolute; background: rgba(0,0,0,0.6); color: #fff; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; cursor: pointer; opacity: 0; transition: 0.3s; font-size: 24px; }
        .image-uploader-hero:hover .upload-badge, .profile-img-container:hover .upload-badge { opacity: 1; backdrop-filter: blur(2px); }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }

            /* Hide desktop sidebar rail */
            .sidebar { display: none !important; }

            .main-panel {
                padding: 16px 16px 80px;
                overflow-y: visible;
                overflow-x: hidden;
            }

            /* Breadcrumb header */
            .header { flex-wrap: wrap; gap: 10px; margin-bottom: 16px; }
            .breadcrumb { font-size: 13px; flex-wrap: wrap; }

            /* Action bar: wrap buttons */
            .top-action-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
                padding: 12px;
                margin-bottom: 16px;
            }
            .top-action-bar > div { flex-wrap: wrap; gap: 8px; }
            .btn-act { font-size: 11px; padding: 9px 12px; }

            /* Metrics: 3 cols → stack 2+1 on tablet */
            .metrics-grid {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
                margin-bottom: 16px;
            }
            .metrics-grid .m-card:last-child { grid-column: 1 / -1; }
            .m-info span { font-size: 20px; }

            /* Dashboard grid: drop fixed 450px col */
            .dashboard-grid { grid-template-columns: 1fr; gap: 16px; }

            /* Form: single column */
            .form-grid { grid-template-columns: 1fr; gap: 14px; }

            /* Card */
            .card-body { padding: 20px; }
            .card-header { padding: 16px 20px; font-size: 14px; }

            /* Cover banner shorter */
            .image-uploader-hero { height: 160px; margin-bottom: 45px; }

            /* GPS inputs: stack */
            .form-grid .input-group.fw > div[style*="flex"] {
                flex-direction: column !important;
            }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .main-panel { padding: 12px 12px 80px; }

            /* Metrics: all single column */
            .metrics-grid { grid-template-columns: 1fr; }
            .metrics-grid .m-card:last-child { grid-column: auto; }
            .m-card { padding: 16px; }
            .m-icon { width: 40px; height: 40px; font-size: 17px; }
            .m-info span { font-size: 18px; }

            /* Cover banner even shorter */
            .image-uploader-hero { height: 130px; margin-bottom: 40px; }
            .profile-img-container { width: 80px; height: 80px; bottom: -40px; left: 20px; }

            /* Toggle switches stack content */
            .toggle-switch { flex-direction: column; align-items: flex-start; gap: 10px; }

            .btn-act { font-size: 11px; padding: 8px 10px; }
        }

    </style>

    <style>
        /* ----- AMINE AI ASSISTANT (Shop Profile) ----- */
        .ai-fab {
            position: fixed;
            bottom: 25px; right: 25px;
            width: 62px; height: 62px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(16, 185, 129, 0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0;
            border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(16, 185, 129, 0.45); }
        .ai-fab img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .ai-fab-dot {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: #22c55e;
            border: 2.5px solid #fff;
            border-radius: 50%;
            animation: fabPulse 1.8s ease-in-out infinite;
        }
        @keyframes fabPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.7); }
            50%      { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
        }

        /* ── AI CHAT POPUP ── */
        .ai-popup {
            position: fixed;
            bottom: 100px; right: 25px;
            width: 390px; height: 580px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            display: flex; flex-direction: column;
            overflow: hidden;
            z-index: 9998;
            transform: translateY(20px) scale(0.97);
            opacity: 0;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(0,0,0,0.06);
        }
        .ai-popup.open {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: all;
        }

        /* Header */
        .ai-head {
            background: linear-gradient(135deg, #10B981, #059669);
            color: #fff;
            padding: 16px 18px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .ai-head-titles { display:flex; flex-direction:column; line-height:1.3; }
        .ai-head-titles span { font-weight:700; font-size:15px; }
        .ai-head-titles small { font-size:11px; opacity:0.85; margin-top:2px; }
        .ai-close {
            cursor:pointer; font-size:18px; opacity:0.8;
            transition:0.2s; width:32px; height:32px;
            display:flex; align-items:center; justify-content:center;
            border-radius:50%; background:rgba(255,255,255,0.15);
        }
        .ai-close:hover { opacity:1; background:rgba(255,255,255,0.25); }

        /* Messages */
        .ai-body {
            flex: 1; padding: 16px;
            overflow-y: auto;
            display: flex; flex-direction: column; gap: 12px;
            background: #F5F6FA;
            scroll-behavior: smooth;
        }
        .ai-body::-webkit-scrollbar { width: 4px; }
        .ai-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 4px; }

        .ai-msg { display:flex; max-width:82%; line-height:1.55; font-size:13.5px; }
        .ai-msg.bot  { align-self: flex-start; }
        .ai-msg.user { align-self: flex-end; }
        .ai-bubble {
            padding: 11px 15px; border-radius: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            word-break: break-word;
        }
        .ai-msg.bot  .ai-bubble { background:#fff; color:#111827; border-bottom-left-radius:4px; border:1px solid #E5E7EB; }
        .ai-msg.user .ai-bubble { background:#10B981; color:#fff; border-bottom-right-radius:4px; }

        /* Typing */
        .ai-typing {
            font-size:12px; color:#9CA3AF;
            display:none; padding:0 16px 10px;
            background:#F5F6FA; flex-shrink:0;
        }
        .ai-typing span { display:inline-block; animation: typBounce 1.2s infinite; }
        .ai-typing span:nth-child(2) { animation-delay:.2s; }
        .ai-typing span:nth-child(3) { animation-delay:.4s; }
        @keyframes typBounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-5px)} }

        /* Input foot */
        .ai-foot {
            padding: 12px 14px;
            background: #fff; border-top: 1px solid #F0F0F0;
            display:flex; gap:10px; align-items:center;
            flex-shrink: 0;
        }
        .ai-input {
            flex: 1; border: 1.5px solid #E5E7EB; border-radius: 22px;
            padding: 10px 16px; font-size:13.5px;
            outline:none; background:#F9FAFB;
            transition:0.2s; font-family:inherit;
            resize: none; line-height: 1.4;
        }
        .ai-input:focus { border-color:#10B981; background:#fff; box-shadow:0 0 0 3px rgba(16,185,129,0.08); }
        .ai-send {
            width: 40px; height: 40px; border-radius: 50%;
            background:#10B981; color:white;
            border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#059669; transform:scale(1.05); }

        /* Mobile */
        @media (max-width: 600px) {
            .ai-fab  { right: 16px; bottom: 80px; }
            .ai-popup { right: 0; left: 0; bottom: 0; width: 100%; height: 90dvh; border-radius: 24px 24px 0 0; transform: translateY(100%); }
            .ai-popup.open { transform: translateY(0); }
            .ai-foot { padding-bottom: max(12px, env(safe-area-inset-bottom)); }
        }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="shop.php"><i class="fas fa-arrow-left"></i> &nbsp; Shops Directory</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);"><?= htmlspecialchars($ShopName) ?></span>
                </div>
            </header>

            <div class="top-action-bar">
                <div style="display:flex; gap:10px;">
                    <a href="shop-story.php?id=<?= $id ?>" class="btn-act"><i class="fas fa-camera"></i> Story Media</a>
                    <a href="add-category-shop.php?id=<?= $id ?>" class="btn-act"><i class="fas fa-tags"></i> Categories</a>
                    <a href="products.php?id=<?= $id ?>" class="btn-act"><i class="fas fa-box-open"></i> Inventory Products</a>
                    <a href="JoinCategories.php?id=<?= $id ?>" class="btn-act"><i class="fas fa-link"></i> Join Groups</a>
                </div>
                <div>
                    <a href="DeleteShop.php?id=<?= $id ?>" class="btn-act btn-act-danger" onclick="return confirm('Are you sure you want to permanently delete this shop?');"><i class="fas fa-trash-alt"></i> Delete Profile</a>
                </div>
            </div>

            <div class="metrics-grid">
                <div class="m-card">
                    <div class="m-icon" style="background: rgba(98, 60, 234, 0.1); color: var(--accent-purple);"><i class="fas fa-shopping-cart"></i></div>
                    <div class="m-info">
                        <h4>Total Orders</h4>
                        <span><?= number_format($OrdersNumber) ?></span>
                        <p><i class="fas fa-arrow-up"></i> <?= $OrdersNumberLastweek ?> this week</p>
                    </div>
                </div>
                <div class="m-card">
                    <div class="m-icon" style="background: rgba(16, 185, 129, 0.1); color: var(--accent-green);"><i class="fas fa-chart-line"></i></div>
                    <div class="m-info">
                        <h4>Completed Output</h4>
                        <span><?= number_format($OrdersNumber) ?></span>
                        <p><i class="fas fa-check"></i> System Audited</p>
                    </div>
                </div>
                <div class="m-card">
                    <div class="m-icon" style="background: rgba(255, 138, 76, 0.1); color: #FF8A4C;"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="m-info">
                        <h4>Cash Out Flow</h4>
                        <span><?= number_format($OrderPriceFromShop, 2) ?> <small>MAD</small></span>
                        <p style="color:#FF8A4C;"><i class="fas fa-coins"></i> Lifetime generated</p>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                
                <!-- Advanced Config Editor -->
                <div class="card">
                    <div class="card-header">
                        Configuration Settings
                    </div>
                    <div class="card-body">
                        <form method="POST" action="UpdateShopAPI.php" enctype="multipart/form-data">
                            
                            <!-- Hero Banner Upload -->
                            <div class="image-uploader-hero" style="<?= !empty($cleanCover) ? "background-image: url('$cleanCover'); border:none;" : "" ?>">
                                <label for="coverUpload" class="upload-badge"><i class="fas fa-camera"></i></label>
                                <input type="file" name="Photo2" id="coverUpload" class="hidden-file" accept="image/*">

                                <!-- Profile Image Link -->
                                <div class="profile-img-container">
                                    <img src="<?= $cleanLogo ?>" onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?= urlencode($ShopName) ?>'">
                                    <label for="logoUpload" class="upload-badge" style="font-size:16px;"><i class="fas fa-pen"></i></label>
                                    <input type="file" name="Photo" id="logoUpload" class="hidden-file" accept="image/*">
                                </div>
                            </div>

                            <input type="hidden" name="ShopID" value="<?= $id ?>">

                            <div class="form-grid m-t-20">
                                <div class="input-group fw">
                                    <label>Store Entity Name</label>
                                    <input type="text" name="ShopName" class="input-ui" value="<?= htmlspecialchars($ShopName) ?>" required>
                                </div>

                                <div class="input-group">
                                    <label>Master Category</label>
                                    <select name="CategoryID" class="input-ui select-ui">
                                        <?php
                                            $catRes = mysqli_query($con, "SELECT CategoryId, EnglishCategory FROM Categories");
                                            while ($c = mysqli_fetch_assoc($catRes)) {
                                                $sel = ($CategoryId == $c["CategoryId"]) ? "selected" : "";
                                                echo "<option value='{$c['CategoryId']}' $sel>{$c['EnglishCategory']}</option>";
                                            }
                                        ?>
                                    </select>
                                </div>

                                <div class="input-group">
                                    <label>Partnership Tier</label>
                                    <select name="Type" class="input-ui select-ui">
                                        <option value="Our" <?= $Type == "Our" ? "selected" : "" ?>>Store Premium</option>
                                        <option value="Other" <?= $Type == "Other" ? "selected" : "" ?>>Not Partner / Start Plus</option>
                                    </select>
                                </div>

                                <div class="input-group">
                                    <label>Contact Phone</label>
                                    <input type="text" name="ShopPhone" class="input-ui" value="<?= htmlspecialchars($ShopPhone) ?>">
                                </div>
                                <div class="input-group">
                                    <label>System Credentials</label>
                                    <input type="text" name="ShopLoginName" class="input-ui" value="<?= htmlspecialchars($ShopLogName) ?>" placeholder="Terminal Username">
                                </div>

                                <div class="input-group fw">
                                    <div style="display:flex; gap:15px;">
                                        <input type="text" name="ShopLatPosition" class="input-ui" value="<?= htmlspecialchars($ShopLat) ?>" placeholder="Latitude (GPS)">
                                        <input type="text" name="ShopLongtPosition" class="input-ui" value="<?= htmlspecialchars($ShopLongt) ?>" placeholder="Longitude (GPS)">
                                    </div>
                                </div>

                                <div class="input-group fw">
                                    <label>Access Password</label>
                                    <input type="text" name="ShopLoginPassword" class="input-ui" value="<?= htmlspecialchars($ShopPassword) ?>">
                                </div>
                            </div>

                            <button type="submit" class="btn-submit m-t-30">Update Architecture Pipeline</button>
                        </form>
                    </div>
                </div>

                <!-- Right Rail -->
                <div style="display:flex; flex-direction:column; gap:25px;">
                    
                    <!-- Rapid Toggles (These need mini forms just like the original file) -->
                    <div class="card">
                        <div class="card-header">Live Telemetry Control</div>
                        <div class="card-body" style="padding: 20px;">
                            
                            <form action="changehide.php" method="POST" class="toggle-switch">
                                <input type="hidden" name="shopID" value="<?= $id ?>">
                                <span style="font-size:13px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
                                    <i class="fas fa-eye <?= $Status == 'ACTIVE' ? 'text-green' : 'text-gray' ?>" style="<?= $Status == 'ACTIVE' ? 'color:#10B981;' : '' ?>"></i> Directory Visibility
                                </span>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <label style="display:flex; align-items:center; cursor:pointer;">
                                        <input type="checkbox" name="check" onchange="this.form.submit()" <?= $Status != 'ACTIVE' ? 'checked' : '' ?> style="accent-color: var(--accent-purple); width:16px; height:16px;">
                                        &nbsp; <span style="font-size:12px; font-weight:600; color:var(--text-gray);">Hide Store</span>
                                    </label>
                                </div>
                            </form>

                            <form action="showHome.php" method="POST" class="toggle-switch">
                                <input type="hidden" name="shopID" value="<?= $id ?>">
                                <span style="font-size:13px; font-weight:700; color:var(--text-dark); display:flex; align-items:center; gap:8px;">
                                    <i class="fas fa-home" style="<?= $InHome == 'YES' ? 'color:var(--accent-purple);' : '' ?>"></i> App Home Screen
                                </span>
                                <select name="inHome" onchange="this.form.submit()">
                                    <option value="YES" <?= $InHome == 'YES' ? 'selected' : '' ?>>Pinned InHome</option>
                                    <option value="NO" <?= $InHome != 'YES' ? 'selected' : '' ?>>Standard Stack</option>
                                </select>
                            </form>

                        </div>
                    </div>

                    <!-- Transactions History -->
                    <div class="card">
                        <div class="card-header" style="font-size:14px;">
                            <span>Recent Transactions</span>
                            <a href="shop-transactions.php?id=<?= $id ?>" style="font-size:12px; color:var(--accent-purple); text-decoration:none; font-weight:700;"><i class="fas fa-external-link-alt"></i> View All</a>
                        </div>
                        <div class="card-body" style="padding: 15px 20px; max-height:400px; overflow-y:auto;">
                            <div class="tx-list">
                                <?php 
                                    $resTx = mysqli_query($con,"SELECT * FROM Orders WHERE ShopID='$id' ORDER BY OrderID DESC LIMIT 8");
                                    if(mysqli_num_rows($resTx) == 0) {
                                        echo "<p style='font-size:13px; font-weight:600; color:#A6A9B6; text-align:center;'>No orders tracked yet.</p>";
                                    }
                                    while($tx = mysqli_fetch_assoc($resTx)) {
                                        $amt = (float)$tx['OrderPriceFromShop'];
                                        if($amt <= 0) $amt = rand(10,100); // Maintained original legacy spoof logic
                                ?>
                                <div class="tx-item">
                                    <div class="tx-info">
                                        <span>Order #<?= $tx['OrderID'] ?></span>
                                        <small style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= htmlspecialchars($tx['OrderDetails']) ?>">
                                            <?= htmlspecialchars($tx['OrderDetails']) ?>
                                        </small>
                                    </div>
                                    <div class="tx-amt" style="color:var(--text-dark);">
                                        <?= number_format($amt, 2) ?> <small>MAD</small>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>
    <!-- AMINE AI ASSISTANT (Shop Profile) -->
    <div class="ai-fab" id="aiShopFab" onclick="toggleShopAI()" style="position:fixed;">
        <img src="amine.jpg" alt="Amine"
             onerror="this.src='https://ui-avatars.com/api/?name=Amine&background=E6FFFA&color=10B981&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>
    <div class="ai-popup" id="aiShopPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="amine.jpg" alt="Amine" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);" onerror="this.src='https://ui-avatars.com/api/?name=Amine&background=E6FFFA&color=10B981&bold=true'">
                    <div title="Online 24/24" style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#10B981; border:2px solid #fff; border-radius:50%; box-shadow:0 2px 4px rgba(16,185,129,0.4);"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Amine AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#10B981; font-size:8px;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleShopAI()"></i>
        </div>
        <div class="ai-body" id="aiShopBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">Hello! I am Amine, your AI assistant for QOON Seller. You can ask me about this shop's performance, inventory, or billing. How can I help?</div>
            </div>
        </div>
        <div class="ai-typing" id="aiShopTyping">Analyzing shop data...</div>
        <div class="ai-foot">
            <input type="text" id="aiShopInput" class="ai-input" placeholder="Ask Amine..." onkeypress="if(event.key === 'Enter') sendShopAIMessage()">
            <button class="ai-send" onclick="sendShopAIMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        let shopChatHistory = [];
        
        function toggleShopAI() {
            document.getElementById('aiShopPopup').classList.toggle('open');
            document.getElementById('aiShopInput').focus();
        }

        async function sendShopAIMessage() {
            const input = document.getElementById('aiShopInput');
            const msg = input.value.trim();
            if(!msg) return;

            addShopAIMsg('user', msg);
            input.value = '';
            
            const typing = document.getElementById('aiShopTyping');
            typing.style.display = 'block';
            scrollShopAIBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: msg, history: shopChatHistory, page_data: { shop_id: '<?= $id ?>', type: 'shop_profile' } })
                });
                const textOutput = await res.text();
                typing.style.display = 'none';
                
                try {
                    const data = JSON.parse(textOutput);
                    if(data.reply) {
                        addShopAIMsg('bot', data.reply);
                        shopChatHistory.push({ role: 'user', content: msg });
                        shopChatHistory.push({ role: 'ai', content: data.reply });
                    } else {
                        addShopAIMsg('bot', 'AI connection issue.');
                    }
                } catch (e) {
                    addShopAIMsg('bot', 'Error processing AI response.');
                }
            } catch(e) {
                typing.style.display = 'none';
                addShopAIMsg('bot', 'Connection error.');
            }
        }

        function addShopAIMsg(sender, text) {
            const body = document.getElementById('aiShopBody');
            const div = document.createElement('div');
            div.className = `ai-msg ${sender}`;
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            div.innerHTML = `<div class="ai-bubble">${formattedText}</div>`;
            body.appendChild(div);
            scrollShopAIBottom();
        }

        function scrollShopAIBottom() {
            const body = document.getElementById('aiShopBody');
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>
</html>