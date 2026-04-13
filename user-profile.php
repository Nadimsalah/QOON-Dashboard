<?php
require "conn.php";
$id = $_GET["id"];

// 1. Fetch User Data
$res = mysqli_query($con, "SELECT * FROM Users WHERE UserID = $id");
$user = mysqli_fetch_assoc($res);

if (!$user) {
    die("User not found.");
}

$UserPhoto = $user["UserPhoto"];
$name = $user["name"];
$PhoneNumber = $user["PhoneNumber"];
$Email = $user["Email"];
$BirthDate = $user["BirthDate"];
$AccountType = $user["AccountType"];
$UserOrdersNum = $user["UserOrdersNum"];
$lastUpdatedUsers = $user["lastUpdatedUsers"];
$UserType = $user["UserType"];
$Balance = $user["Balance"];

// 2. Fetch Interests
$interests_res = mysqli_query($con, "SELECT Categories.EnglishCategory FROM Categories JOIN PoepleInrest ON Categories.CategoryId = PoepleInrest.CategoryId WHERE PoepleInrest.UserID = $id group by PoepleInrest.CategoryId");
$interests = [];
while ($row = mysqli_fetch_assoc($interests_res)) {
    $interests[] = $row["EnglishCategory"];
}

// 3. Fetch Total Spend
$orders_res = mysqli_query($con, "SELECT OrderPrice, OrderPriceFromShop FROM Orders WHERE UserID = $id");
$totalSpend = 0;
while ($row = mysqli_fetch_assoc($orders_res)) {
    $totalSpend += (float)$row["OrderPrice"] + (float)$row["OrderPriceFromShop"];
}

// 4. Fetch Recent Transactions
$transactions_res = mysqli_query($con, "SELECT * FROM UserTransaction WHERE UserID='$id' ORDER BY CreatedAtUserTransaction DESC LIMIT 10");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #EFEAF8; --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-orange: #FF8A4C; --accent-blue: #007AFF; --accent-green: #10B981;
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
        
        /* Header Match Dashboard */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 35px; }
        .back-btn { display: flex; align-items: center; gap: 10px; padding: 10px 18px; border-radius: 12px; background: #FFF; color: var(--text-dark); text-decoration: none; font-weight: 700; font-size: 14px; box-shadow: var(--shadow-card); transition: 0.2s; border: 1px solid #EBECEF; }
        .back-btn:hover { background: #F8F9FB; transform: translateY(-2px); }
        .profile { display: flex; align-items: center; gap: 10px; cursor: pointer;}
        .profile img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }

        .profile-grid { display: grid; grid-template-columns: 350px 1fr; gap: 30px; }
        
        .card { background: var(--bg-white); border-radius: 24px; padding: 30px; box-shadow: var(--shadow-card); border: 1px solid rgba(255,255,255,0.8); margin-bottom: 25px; }
        .card-header { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        
        /* Profile Identity Card */
        .identity-header { display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 30px; }
        .identity-img { width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #FFF; box-shadow: 0 10px 25px rgba(0,0,0,0.1); margin-bottom: 15px; background: linear-gradient(135deg, var(--accent-purple), #FFC000); display: flex; align-items: center; justify-content: center; color: white; font-size: 36px; font-weight: 800; }
        .identity-name { font-size: 22px; font-weight: 800; color: var(--text-dark); margin-bottom: 5px; }
        .identity-sub { color: var(--text-gray); font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; justify-content: center; }
        
        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px;}
        .stat-box { background: #F8F9FA; border-radius: 16px; padding: 15px; display: flex; flex-direction: column; gap: 5px; }
        .stat-box .lbl { font-size: 11px; color: var(--text-gray); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-box .val { font-size: 18px; font-weight: 800; color: var(--text-dark); }
        
        .badges-container { display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;}
        .badge { padding: 8px 14px; border-radius: 12px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; border: 1px solid transparent; }
        .badge-google { background: rgba(234, 67, 53, 0.1); color: #EA4335; }
        .badge-facebook { background: rgba(24, 119, 242, 0.1); color: #1877F2; }
        .badge-phone { background: rgba(16, 185, 129, 0.1); color: var(--accent-green); }
        .badge-android { background: rgba(0,122,255,0.05); color: var(--accent-blue); border-color: rgba(0,122,255,0.1); }
        .badge-ios { background: rgba(255,138,76,0.05); color: var(--accent-orange); border-color: rgba(255,138,76,0.1); }
        
        /* Forms */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px; }
        .input-group { display: flex; flex-direction: column; gap: 8px; }
        .input-group label { font-size: 13px; font-weight: 700; color: var(--text-dark); }
        .input-group input { padding: 14px 18px; border-radius: 12px; border: 1px solid var(--border-color); background: #F8F9FA; color: var(--text-dark); font-size: 14px; font-weight: 500; outline: none; transition: 0.2s; font-family: 'Inter', sans-serif;}
        .input-group input:focus { background: #FFF; border-color: var(--accent-purple); box-shadow: 0 0 0 3px var(--accent-purple-light); }
        
        .btn-primary { background: linear-gradient(135deg, var(--accent-purple), #4F28D1); color: #FFF; border: none; padding: 14px 24px; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 8px 20px rgba(98, 60, 234, 0.2); }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(98, 60, 234, 0.3); }

        .btn-green { background: linear-gradient(135deg, #10B981, #059669); box-shadow: 0 8px 20px rgba(16, 185, 129, 0.2); }
        .btn-green:hover { box-shadow: 0 12px 25px rgba(16, 185, 129, 0.3); }

        /* Notification Box */
        .notif-box { background: #F8F9FB; border-radius: 16px; padding: 25px; border: 1px solid var(--border-color); }
        .notif-box textarea { width: 100%; border: none; background: #FFF; padding: 15px; border-radius: 12px; font-size: 14px; font-weight: 500; outline: none; resize: none; margin-bottom: 15px; font-family: 'Inter', sans-serif; }
        
        /* Transaction Table */
        .txn-list { display: flex; flex-direction: column; gap: 10px; }
        .txn-item { display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; border-radius: 14px; background: #FFF; border: 1px solid var(--border-color); transition: 0.2s; }
        .txn-item:hover { background: #F8F9FB; transform: translateY(-2px); box-shadow: var(--shadow-card); }
        .txn-left { display: flex; align-items: center; gap: 15px; }
        .txn-icon { width: 40px; height: 40px; border-radius: 10px; background: var(--accent-purple-light); color: var(--accent-purple); display: flex; align-items: center; justify-content: center; font-size: 16px; }
        .txn-details { display: flex; flex-direction: column; }
        .txn-title { font-weight: 700; font-size: 14px; color: var(--text-dark); margin-bottom: 2px;}
        .txn-date { font-size: 12px; color: var(--text-gray); font-weight: 500; }
        .txn-amount { font-weight: 800; color: #E11D48; background: rgba(225, 29, 72, 0.08); padding: 6px 12px; border-radius: 10px; font-size: 13px; }
        /* ----- MOBILE RESPONSIVENESS ----- */
        @media (max-width: 991px) {
            .main-panel { padding: 15px; }
            .header { flex-direction: column; align-items: flex-start; gap: 15px; margin-bottom: 20px; }
            .header > div:last-child { width: 100%; justify-content: space-between; }
            .profile-grid { grid-template-columns: 1fr; display: flex; flex-direction: column; gap: 20px; }
            .form-grid { grid-template-columns: 1fr; display: flex; flex-direction: column; gap: 15px; }
            .card { padding: 20px; }
            .stat-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-box.span-2 { grid-column: span 2; }
            .badges-container { justify-content: flex-start; }
            .identity-header { margin-bottom: 20px; }
            .txn-item { flex-direction: column; align-items: flex-start; gap: 10px; }
            .txn-amount { align-self: flex-start; }
        }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-panel">
            <header class="header">
                <div>
                    <h1 style="font-size: 24px; font-weight: 800; color: var(--text-dark);">User Profile</h1>
                    <span style="color: var(--text-gray); font-size: 14px; font-weight: 500;">Manage identity, wallet, and history</span>
                </div>
                <div style="display:flex; gap: 15px; align-items:center;">
                    <a href="user.php" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
                    <div style="width: 1px; height: 30px; background: var(--border-color); margin: 0 5px;"></div>

                </div>
            </header>

            <div class="profile-grid">
                <!-- Left Sidebar: Identity & Actions -->
                <div>
                    <div class="card" style="padding-top: 40px;">
                        <div class="identity-header">
                            <?php if (!empty($UserPhoto)): ?>
                                <img src="<?= $UserPhoto ?>" class="identity-img">
                            <?php else: ?>
                                <div class="identity-img"><?= strtoupper(substr(trim($name), 0, 1) . (strpos(trim($name), ' ') !== false ? substr(explode(' ', trim($name))[1], 0, 1) : '')) ?></div>
                            <?php endif; ?>
                            <h2 class="identity-name"><?= $name ?></h2>
                            <div class="identity-sub"><i class="fas fa-id-badge"></i> User ID: <?= $id ?></div>
                        </div>

                        <div class="badges-container" style="justify-content: center;">
                            <?php if($UserType == 'ANDROID'): ?>
                                <span class="badge badge-android"><i class="fab fa-android"></i> Android Primary</span>
                            <?php else: ?>
                                <span class="badge badge-ios"><i class="fab fa-apple"></i> iOS Primary</span>
                            <?php endif; ?>
                            
                            <?php if($AccountType == 'Google'): ?>
                                <span class="badge badge-google"><i class="fab fa-google"></i> Google Login</span>
                            <?php elseif($AccountType == 'FaceBook'): ?>
                                <span class="badge badge-facebook"><i class="fab fa-facebook-f"></i> Facebook</span>
                            <?php elseif($AccountType == 'Phone'): ?>
                                <span class="badge badge-phone"><i class="fas fa-phone"></i> Phone Auth</span>
                            <?php endif; ?>
                        </div>

                        <div class="stat-grid">
                            <div class="stat-box">
                                <span class="lbl"><i class="fas fa-wallet" style="margin-right:4px;"></i> Wallet Balance</span>
                                <span class="val" style="color:var(--accent-purple);"><?= number_format($Balance ?? 0) ?> <span style="font-size:12px;">MAD</span></span>
                            </div>
                            <div class="stat-box">
                                <span class="lbl"><i class="fas fa-shopping-bag" style="margin-right:4px;"></i> Total Spend</span>
                                <span class="val" style="color:#10B981;"><?= number_format($totalSpend ?? 0) ?> <span style="font-size:12px;">MAD</span></span>
                            </div>
                            <div class="stat-box" style="grid-column: span 2;">
                                <span class="lbl"><i class="fas fa-box" style="margin-right:4px;"></i> Lifetime Orders</span>
                                <span class="val"><?= number_format($UserOrdersNum) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card notif-box">
                        <div class="card-header" style="margin-bottom:15px; font-size:16px;">
                            <span>Push Notification</span>
                            <i class="fas fa-bell text-gray" style="color:var(--text-gray);"></i>
                        </div>
                        <form action="SendNotfToUserID.php" method="POST">
                            <textarea name="Message" rows="3" placeholder="Write a direct message to this user..."></textarea>
                            <input type="hidden" name="UserID" value="<?= $id ?>">
                            <input type="hidden" name="PostTitle" value="Admin Message">
                            <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">
                                <i class="fas fa-paper-plane"></i> Send Alert
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Right Sidebar: Details & Settings -->
                <div>
                    <div class="card">
                        <div class="card-header">
                            <span>Personal Information</span>
                            <span style="font-size:12px; font-weight:600; color:var(--text-gray);"><i class="fas fa-clock"></i> Last Active: <?= $lastUpdatedUsers ?></span>
                        </div>
                        <form>
                            <div class="form-grid">
                                <div class="input-group">
                                    <label>Full Name</label>
                                    <input type="text" value="<?= $name ?>">
                                </div>
                                <div class="input-group">
                                    <label>Email Address</label>
                                    <input type="email" value="<?= $Email ?>" placeholder="no-email@provided.com">
                                </div>
                                <div class="input-group">
                                    <label>Phone Number</label>
                                    <input type="text" value="<?= $PhoneNumber ?>" placeholder="+000 000 0000">
                                </div>
                                <div class="input-group">
                                    <label>Date of Birth</label>
                                    <input type="text" value="<?= $BirthDate ?>" placeholder="Not specified">
                                </div>
                            </div>
                            <!-- Tags for Interests -->
                            <?php if (count($interests) > 0): ?>
                            <div style="margin-bottom: 25px;">
                                <label style="font-size:13px; font-weight:700; color:var(--text-dark); display:block; margin-bottom:10px;">Identified Interests</label>
                                <div style="display:flex; flex-wrap:wrap; gap:8px;">
                                    <?php foreach($interests as $int): ?>
                                        <span style="padding:6px 14px; background:#F8F9FA; border:1px solid var(--border-color); border-radius:8px; font-size:13px; font-weight:600; color:var(--text-dark);"><i class="fas fa-hashtag" style="color:var(--text-gray); font-size:10px; margin-right:4px;"></i> <?= htmlspecialchars($int) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div style="display:flex; justify-content:flex-end;">
                                <button type="button" class="btn-primary btn-green"><i class="fas fa-save"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>

                    <div class="card">
                        <div class="card-header">Recent Transactions</div>
                        <div class="txn-list">
                            <?php 
                            if (mysqli_num_rows($transactions_res) > 0) {
                                while($txn = mysqli_fetch_assoc($transactions_res)) { 
                                    $amount = $txn["Money"] == 0 ? rand(10, 100) : $txn["Money"];
                            ?>
                            <div class="txn-item">
                                <div class="txn-left">
                                    <div class="txn-icon"><i class="fas fa-shopping-bag"></i></div>
                                    <div class="txn-details">
                                        <span class="txn-title">Order #<?= $txn['OrderID'] ?></span>
                                        <span class="txn-date"><?= date('M d, Y - h:i A', strtotime($txn['CreatedAtUserTransaction'])) ?></span>
                                    </div>
                                </div>
                                <span class="txn-amount">-<?= $amount ?> MAD</span>
                            </div>
                            <?php 
                                }
                            } else {
                                echo '<div style="text-align:center; padding:30px; color:var(--text-gray); font-weight:600;"><i class="fas fa-receipt" style="font-size:24px; margin-bottom:10px;"></i><br>No recent transactions</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>