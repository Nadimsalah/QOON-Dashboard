<?php
require "conn.php";
$id = (int)$_GET["id"];

// 1. Fetch Driver Information
$res = mysqli_query($con, "SELECT * FROM Drivers WHERE DriverID='$id'");
$driver = mysqli_fetch_assoc($res);
if (!$driver) die("Driver Not Found");

// Fix Local Paths Flaw
$fieldsToFix = ['PersonalPhoto', 'CIN', 'CV', 'Contract', 'CartOwnership', 'Insurance'];
foreach ($fieldsToFix as $field) {
    if (strpos($driver[$field], 'https://jibler.app/db/db/photo/') !== false) {
        $driver[$field] = str_replace('https://jibler.app/db/db/', '', $driver[$field]);
    }
}

$FName = $driver["FName"];
$LName = $driver["LName"];
$FullName = $FName . ' ' . $LName;
$DriverEmail = $driver["DriverEmail"];
$Ckey = $driver["Ckey"];
$DriverPhoneRaw = $driver["DriverPhone"];
$DriverPhone = str_replace($Ckey, "", $DriverPhoneRaw);
$AGE = $driver["AGE"];
$NationalID = $driver["NationalID"];
$City = $driver["City"];
$DriverRate = $driver["DriverRate"];
$DriverPassword = $driver["DriverPassword"];
$PersonalPhoto = !empty($driver['PersonalPhoto']) ? $driver['PersonalPhoto'] : 'images/jiblers.jpg';
$AvatarFallback = "https://ui-avatars.com/api/?name=" . urlencode($FullName) . "&background=EFEAF8&color=623CEA&bold=true";

// 2. Compute Orders & Finance
$MustPaid = 0;
$OrdersNumber = 0;
$OrdersNumberLastweek = 0;
$lastweek = date('Y-m-d', strtotime("-7 days"));

$resOrders = mysqli_query($con, "SELECT * FROM Orders WHERE DelvryId='$id'");
while ($row = mysqli_fetch_assoc($resOrders)) {
    $OrdersNumber++;
    if ($lastweek < $row["CreatedAtOrders"]) {
        $OrdersNumberLastweek++;
    }
    if (($row['OrderState'] == 'Rated' || $row['OrderState'] == 'Done') && $row['PaidForDriver'] == 'NotPaid') {
        $MustPaid += $row["OrderPriceFromShop"];
    }
}

// 3. Transactions & Notes & Reviews
$transactions = mysqli_query($con, "SELECT * FROM DriverTransactions WHERE DriverID='$id' ORDER BY DriverTransactionsID DESC LIMIT 10");
$notes = mysqli_query($con, "SELECT * FROM DriverNotes WHERE DriverID='$id' ORDER BY CreatedAtDriverNotes DESC LIMIT 5");
$reviews = mysqli_query($con, "SELECT * FROM Orders JOIN Users ON Orders.UserID = Users.UserID WHERE DelvryId='$id' AND UserReview != '' ORDER BY Orders.CreatedAtOrders DESC LIMIT 5");

$countries_res = mysqli_query($con, "SELECT * FROM Countries");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($FullName) ?> | Driver Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-green: #10B981; --accent-blue: #007AFF; --accent-red: #E11D48;
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

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        /* Shared Components */
        .back-btn { display: inline-flex; align-items: center; gap: 10px; padding: 10px 18px; border-radius: 12px; background: var(--bg-white); color: var(--text-dark); text-decoration: none; font-weight: 700; font-size: 14px; box-shadow: var(--shadow-card); transition: 0.2s; border: 1px solid var(--border-color); align-self: flex-start; margin-bottom: 25px; }
        .back-btn:hover { background: #F8F9FB; transform: translateY(-2px); color: var(--accent-purple); box-shadow: 0 12px 25px rgba(0,0,0,0.05); }

        .card { background: var(--bg-white); border-radius: 24px; padding: 30px; box-shadow: var(--shadow-card); border: 1px solid rgba(255,255,255,0.8); }
        .card-header { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        
        .btn-primary { background: linear-gradient(135deg, var(--accent-purple), #4F28D1); color: #FFF; border: none; padding: 14px 20px; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; box-shadow: 0 8px 20px rgba(98, 60, 234, 0.2); width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;}
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(98, 60, 234, 0.3); }

        /* Top Identity Banner */
        .identity-banner { display: flex; gap: 30px; align-items: center; margin-bottom: 30px; }
        .identity-avatar { width: 110px; height: 110px; border-radius: 24px; object-fit: cover; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 4px solid #FFF;}
        .identity-text h1 { font-size: 28px; font-weight: 800; color: var(--text-dark); margin-bottom: 4px; letter-spacing: -0.5px;}
        .identity-text p { font-size: 14px; font-weight: 600; color: var(--text-gray); }
        .identity-rating { display: flex; gap: 5px; color: #F59E0B; font-size: 14px; margin-top: 10px; }

        /* Grid Layout */
        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }

        /* Form Inputs */
        .input-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 6px; }
        .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-group label { font-size: 12px; font-weight: 700; color: var(--text-dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .input-group input, .input-group select { width: 100%; padding: 14px 18px; border-radius: 12px; border: 1px solid var(--border-color); background: #F8F9FA; color: var(--text-dark); font-size: 14px; font-weight: 600; outline: none; transition: 0.2s; font-family: 'Inter', sans-serif; }
        .input-group input:focus, .input-group select:focus { background: #FFF; border-color: var(--accent-purple); box-shadow: 0 0 0 3px var(--accent-purple-light); }

        /* Finance Stats */
        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
        .stat-box { background: #F8F9FA; border-radius: 16px; padding: 20px; border: 1px solid var(--border-color); }
        .stat-box.debt { background: rgba(225, 29, 72, 0.05); border-color: rgba(225, 29, 72, 0.1); }
        .stat-box h5 { font-size: 12px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; margin-bottom: 5px;}
        .stat-box.debt h5 { color: var(--accent-red); margin-bottom: 5px;}
        .stat-box h3 { font-size: 24px; font-weight: 800; color: var(--text-dark); letter-spacing: -0.5px; }
        .stat-box.debt h3 { color: var(--accent-red); }

        /* Lists */
        .feed-list { display: flex; flex-direction: column; gap: 15px; }
        .feed-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #FFF; border: 1px solid var(--border-color); border-radius: 16px; transition: 0.2s;}
        .feed-item:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.03); border-color: #D1D5DF; }
        .feed-info { display: flex; gap: 12px; align-items: center; }
        .feed-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
        .feed-text h6 { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 3px; }
        .feed-text p { font-size: 12px; font-weight: 500; color: var(--text-gray); }

        .doc-btn { padding: 8px 14px; border-radius: 10px; background: var(--bg-white); border: 1px solid var(--border-color); color: var(--text-dark); font-size: 13px; font-weight: 700; text-decoration: none; transition: 0.2s; cursor: pointer;}
        .doc-btn:hover { background: var(--accent-purple-light); color: var(--accent-purple); border-color: var(--accent-purple); }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }

            /* Hide desktop sidebar rail */
            .sidebar { display: none !important; }

            .main-panel {
                padding: 16px;
                overflow-y: visible;
                overflow-x: hidden;
            }

            /* Back button: full width feel */
            .back-btn { font-size: 13px; padding: 9px 14px; margin-bottom: 16px; }

            /* Identity banner: stack avatar + text */
            .identity-banner {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
                margin-bottom: 20px;
            }
            .identity-avatar { width: 80px; height: 80px; border-radius: 18px; }
            .identity-text h1 { font-size: 22px; }
            .identity-text p  { font-size: 13px; }

            /* Main grid: single column */
            .dashboard-grid { grid-template-columns: 1fr; gap: 16px; }

            /* Cards */
            .card { padding: 20px; border-radius: 18px; }
            .card-header { font-size: 15px; margin-bottom: 18px; }

            /* Form input-row: single column */
            .input-row { grid-template-columns: 1fr; gap: 0; }

            /* Stat grid: 2 columns still fine on tablet */
            .stat-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
            .stat-box { padding: 16px; }
            .stat-box h3 { font-size: 20px; }

            /* Push notification form: stack inputs */
            .notif-form-row { flex-direction: column !important; gap: 10px !important; }
            .notif-form-row input[name="PostTitle"] { width: 100% !important; }

            /* Feed items */
            .feed-item { padding: 12px; }
            .feed-text h6 { font-size: 13px; }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .main-panel { padding: 12px; }

            .identity-avatar { width: 68px; height: 68px; }
            .identity-text h1 { font-size: 19px; }

            /* Stat grid: full single column on tiny phones */
            .stat-grid { grid-template-columns: 1fr; gap: 10px; }
            .stat-box h3 { font-size: 22px; }

            /* Doc buttons stack */
            .feed-item { flex-direction: column; align-items: flex-start; gap: 10px; }

            .card { padding: 16px; }
        }

        /* ----- DRIVER AI ASSISTANT (Tamo) ----- */
        .ai-fab {
            position: fixed;
            bottom: 25px; right: 25px;
            width: 62px; height: 62px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(217, 70, 168, 0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0;
            border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(217, 70, 168, 0.45); }
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
            background: linear-gradient(135deg, #D946A8, #8B5CF6);
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
        .ai-msg.user .ai-bubble { background:#D946A8; color:#fff; border-bottom-right-radius:4px; }

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
        .ai-input:focus { border-color:#D946A8; background:#fff; box-shadow:0 0 0 3px rgba(217,70,168,0.08); }
        .ai-send {
            width: 40px; height: 40px; border-radius: 50%;
            background:#D946A8; color:white;
            border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#C026D3; transform:scale(1.05); }

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
            <a href="driver.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Drivers Directory</a>

            <!-- Overview Header -->
            <div class="identity-banner">
                <img src="<?= $PersonalPhoto ?>" class="identity-avatar" onerror="this.onerror=null; this.src='<?= $AvatarFallback ?>'">
                <div class="identity-text">
                    <h1><?= htmlspecialchars($FullName) ?></h1>
                    <p>Driver ID: #<?= $id ?> &nbsp;•&nbsp; Active since <?= date('F Y') ?></p>
                    <div class="identity-rating">
                        <?php for($i = 0; $i < 5; $i++): ?>
                            <i class="fa<?= $i < round($DriverRate) ? 's' : 'r' ?> fa-star" <?= $i >= round($DriverRate) ? 'style="color:#EBECEF;"' : '' ?>></i>
                        <?php endfor; ?>
                        <span style="color:var(--text-dark); font-weight:800; margin-left:5px;"><?= number_format($DriverRate, 1) ?></span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                
                <!-- Left Column: Settings & Profile Details -->
                <div style="display:flex; flex-direction:column; gap:25px;">
                    <div class="card">
                        <div class="card-header"><i class="fas fa-user-edit" style="color:var(--accent-purple);"></i> Edit Profile Details</div>
                        <form action="UpdateDriverJiblerAPI.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="DriverID" value="<?= $id ?>">
                            
                            <div class="input-row">
                                <div class="input-group">
                                    <label>First Name</label>
                                    <input type="text" name="FName" value="<?= htmlspecialchars($FName) ?>">
                                </div>
                                <div class="input-group">
                                    <label>Last Name</label>
                                    <input type="text" name="LName" value="<?= htmlspecialchars($LName) ?>">
                                </div>
                            </div>

                            <div class="input-group">
                                <label>Email Address</label>
                                <input type="email" name="DriverEmail" value="<?= htmlspecialchars($DriverEmail) ?>">
                            </div>

                            <div class="input-row">
                                <div class="input-group">
                                    <label>Country</label>
                                    <select name="CountryKey">
                                        <?php while($row = mysqli_fetch_assoc($countries_res)): ?>
                                            <option value="<?= $row['country_code'] ?>" <?= $Ckey == $row['country_code'] ? 'selected' : '' ?>>
                                                <?= $row['EnglishName'] ?> (<?= $row['country_code'] ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <label>Phone Number</label>
                                    <input type="number" name="DriverPhone" value="<?= htmlspecialchars($DriverPhone) ?>">
                                </div>
                            </div>

                            <div class="input-row">
                                <div class="input-group">
                                    <label>City Hub</label>
                                    <input type="text" name="City" value="<?= htmlspecialchars($City) ?>">
                                </div>
                                <div class="input-group">
                                    <label>Age</label>
                                    <input type="number" name="AGE" value="<?= htmlspecialchars($AGE) ?>">
                                </div>
                            </div>

                            <div class="input-group" style="margin-top: 10px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                                <label>Reset Driver Password</label>
                                <input type="text" name="Password" value="<?= htmlspecialchars($DriverPassword) ?>">
                            </div>

                            <button type="submit" class="btn-primary" style="margin-top: 15px;">
                                <i class="fas fa-save"></i> Save Profile Changes
                            </button>
                        </form>
                    </div>

                    <!-- Verified Documents -->
                    <div class="card">
                        <div class="card-header" style="margin-bottom:15px;">
                            <span><i class="fas fa-file-contract" style="color:var(--accent-purple);"></i> Verified Documents</span>
                            <button onclick="downloadAllDocs()" class="doc-btn" style="background:var(--accent-purple-light); color:var(--accent-purple); border-color:var(--accent-purple);"><i class="fas fa-download"></i> Extract</button>
                        </div>
                        <div class="feed-list" style="gap:10px;">
                            <?php 
                                $docs = [
                                    "CIN" => ["icon" => "fa-id-card", "val" => $driver['CIN']],
                                    "CV" => ["icon" => "fa-file-alt", "val" => $driver['CV']],
                                    "Contract" => ["icon" => "fa-file-signature", "val" => $driver['Contract']],
                                    "Ownership" => ["icon" => "fa-car-side", "val" => $driver['CartOwnership']],
                                    "Insurance" => ["icon" => "fa-file-medical-alt", "val" => $driver['Insurance']]
                                ];
                                foreach($docs as $name => $data):
                                    $hasVal = !empty($data['val']);
                            ?>
                            <div class="feed-item" style="padding: 10px 15px;">
                                <div class="feed-info">
                                    <i class="fas <?= $data['icon'] ?> text-gray" style="font-size:18px;"></i>
                                    <span style="font-size:14px; font-weight:700; color:var(--text-dark);"><?= $name ?> Document</span>
                                </div>
                                <?php if($hasVal): ?>
                                    <a href="<?= $data['val'] ?>" target="_blank" class="doc-btn"><i class="fas fa-external-link-alt"></i> View</a>
                                <?php else: ?>
                                    <span style="font-size:11px; font-weight:700; color:var(--text-gray); background:#F0F2F6; padding:4px 8px; border-radius:6px;">Missing</span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Analytics & Transactions -->
                <div style="display:flex; flex-direction:column; gap:25px;">
                    <div class="card">
                        <div class="card-header"><i class="fas fa-chart-pie" style="color:var(--accent-purple);"></i> Telemetry & Performance</div>
                        <div class="stat-grid">
                            <div class="stat-box">
                                <h5>Total Orders</h5>
                                <h3><?= number_format($OrdersNumber) ?></h3>
                            </div>
                            <div class="stat-box debt">
                                <h5>Outstanding Cash Debt</h5>
                                <h3><?= number_format($MustPaid) ?> <span style="font-size:14px; font-weight:600;">MAD</span></h3>
                            </div>
                            <div class="stat-box">
                                <h5>Delivery Speed Avg</h5>
                                <h3>43 <span style="font-size:16px;">MIN</span></h3>
                            </div>
                            <div class="stat-box">
                                <h5>Network Status</h5>
                                <span style="display:inline-flex; align-items:center; gap:6px; background:rgba(16,185,129,0.1); color:var(--accent-green); padding:6px 12px; border-radius:8px; font-size:13px; font-weight:700;"><i class="fas fa-circle" style="font-size:8px;"></i> Cleared</span>
                            </div>
                        </div>

                        <!-- System Notify -->
                        <form method="POST" action="notificationsSendNotfToDriversID.php" style="background:#F8F9FA; padding:20px; border-radius:16px; border:1px solid var(--border-color);">
                            <h5 style="font-size:13px; font-weight:700; color:var(--text-dark); margin-bottom:12px; text-transform:uppercase;">Push Notification</h5>
                            <input type="hidden" name="DriverID" value="<?= $id ?>">
                            <div style="display:flex; gap:10px;" class="notif-form-row">
                                <input type="text" name="PostTitle" placeholder="Title" required style="width:30%; padding:10px; border-radius:8px; border:1px solid var(--border-color); outline:none;">
                                <input type="text" name="Message" placeholder="Message content..." required style="flex:1; padding:10px; border-radius:8px; border:1px solid var(--border-color); outline:none;">
                                <button type="submit" style="background:var(--accent-purple); color:#FFF; border:none; padding:10px 15px; border-radius:8px; font-weight:700; cursor:pointer;"><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </form>
                    </div>

                    <div class="card" style="flex:1;">
                        <div class="card-header"><i class="fas fa-list-ul" style="color:var(--accent-purple);"></i> Recent Activity Feed</div>
                        <div class="feed-list">
                            <?php if(mysqli_num_rows($transactions) == 0): ?>
                                <p style="font-size:13px; color:var(--text-gray); font-weight:600; text-align:center; padding:20px;">No payout transactions available.</p>
                            <?php endif; ?>
                            <?php while($t = mysqli_fetch_assoc($transactions)): ?>
                                <div class="feed-item">
                                    <div class="feed-info">
                                        <div class="feed-icon" style="background:rgba(225, 29, 72, 0.1); color:var(--accent-red);"><i class="fas fa-minus"></i></div>
                                        <div class="feed-text">
                                            <h6>System Payout Completed</h6>
                                            <p><?= date('M j, Y • g:i a', strtotime($t['CreatedAtDriverTransactions'])) ?></p>
                                        </div>
                                    </div>
                                    <span style="font-weight:800; color:var(--text-dark);">-<?= number_format($t["Money"]) ?> MAD</span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        function downloadAllDocs() {
            const urls = [
                <?php foreach($docs as $data) { if(!empty($data['val'])) echo '"'.$data['val'].'", '; } ?>
            ];
            if(urls.length === 0) return alert('No documents available to extract.');
            
            const zip = new JSZip();
            const folder = zip.folder("<?= htmlspecialchars(str_replace(' ', '_', $FullName)) ?>_Documents");
            
            const fetchPromises = urls.map(url => {
                return fetch(url).then(r => {
                    if (r.status === 200) return r.blob();
                    return Promise.reject(new Error(r.statusText));
                }).then(blob => {
                    const name = url.substring(url.lastIndexOf("/") + 1);
                    folder.file(name, blob);
                }).catch(e => console.warn('Could not load ' + url));
            });

            Promise.all(fetchPromises).then(() => {
                zip.generateAsync({ type: "blob" }).then((content) => {
                    saveAs(content, "<?= htmlspecialchars(str_replace(' ', '_', $FullName)) ?>_Documents.zip");
                });
            });
        }
    </script>
    <!-- DRIVER AI ASSISTANT (Tamo) -->
    <div class="ai-fab" id="aiDriverFab" onclick="toggleDriverAI()" style="position:fixed;">
        <img src="tamo.jpg" alt="Tamo"
             onerror="this.src='https://ui-avatars.com/api/?name=Tamo&background=FFF0F6&color=D946A8&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>
    <div class="ai-popup" id="aiDriverPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="tamo.jpg" alt="Tamo" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);" onerror="this.src='https://ui-avatars.com/api/?name=Tamo&background=FFF0F6&color=D946A8&bold=true'">
                    <div title="Online 24/24" style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#10B981; border:2px solid #fff; border-radius:50%; box-shadow:0 2px 4px rgba(16,185,129,0.4);"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Tamo AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#10B981; font-size:8px;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleDriverAI()"></i>
        </div>
        <div class="ai-body" id="aiDriverBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">Hello! I am Tamo, your AI assistant for QOON Express. You can ask me about this specific driver profile, their debt, or performance. How can I help?</div>
            </div>
        </div>
        <div class="ai-typing" id="aiDriverTyping">Analyzing database...</div>
        <div class="ai-foot">
            <input type="text" id="aiDriverInput" class="ai-input" placeholder="Ask Tamo..." onkeypress="if(event.key === 'Enter') sendDriverAIMessage()">
            <button class="ai-send" onclick="sendDriverAIMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        let driverChatHistory = [];
        
        function toggleDriverAI() {
            document.getElementById('aiDriverPopup').classList.toggle('open');
            document.getElementById('aiDriverInput').focus();
        }

        async function sendDriverAIMessage() {
            const input = document.getElementById('aiDriverInput');
            const msg = input.value.trim();
            if(!msg) return;

            addDriverAIMsg('user', msg);
            input.value = '';
            
            const typing = document.getElementById('aiDriverTyping');
            typing.style.display = 'block';
            scrollDriverAIBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: msg, history: driverChatHistory, page_data: { driver_id: '<?= $id ?>', type: 'driver_profile' } })
                });
                const textOutput = await res.text();
                typing.style.display = 'none';
                
                try {
                    const data = JSON.parse(textOutput);
                    if(data.reply) {
                        addDriverAIMsg('bot', data.reply);
                        driverChatHistory.push({ role: 'user', content: msg });
                        driverChatHistory.push({ role: 'ai', content: data.reply });
                    } else {
                        addDriverAIMsg('bot', 'AI connection issue.');
                    }
                } catch (e) {
                    addDriverAIMsg('bot', 'Error processing AI response.');
                }
            } catch(e) {
                typing.style.display = 'none';
                addDriverAIMsg('bot', 'Connection error.');
            }
        }

        function addDriverAIMsg(sender, text) {
            const body = document.getElementById('aiDriverBody');
            const div = document.createElement('div');
            div.className = `ai-msg ${sender}`;
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            div.innerHTML = `<div class="ai-bubble">${formattedText}</div>`;
            body.appendChild(div);
            scrollDriverAIBottom();
        }

        function scrollDriverAIBottom() {
            const body = document.getElementById('aiDriverBody');
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>
</html>