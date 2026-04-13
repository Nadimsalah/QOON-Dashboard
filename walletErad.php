<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

// 1. Core Income Channels
$resIncome = mysqli_query($con, "SELECT * FROM Money");
$incomeData = mysqli_fetch_assoc($resIncome);
$TotalIncome = $incomeData["TotalIncome"] ?? 0;
$SubscriptionR = $incomeData["SubscriptionR"] ?? 0;
$SalesR = $incomeData["SalesR"] ?? 0;
$DeliveryR = $incomeData["DeliveryR"] ?? 0;
$BalanceTraComm = $incomeData["BalanceTraComm"] ?? 0;
$BalanceWithComm = $incomeData["BalanceWithComm"] ?? 0;
$ServComm = $incomeData["ServComm"] ?? 0;

// Pagination for Erad Transactions (Platform Income Log)
$page = isset($_GET["Page"]) ? (int)$_GET["Page"] : 0;
if($page < 0) $page = 0;
$limit = 10;
$offset = $page * $limit;

$resErad = mysqli_query($con, "
    SELECT * FROM EradTrans 
    ORDER BY EradTransID DESC 
    LIMIT $limit OFFSET $offset
");
$eradTrans = [];
if($resErad) {
    while($row = mysqli_fetch_assoc($resErad)){
        // Fetch participant data dynamically based on Type
        $PayOwnerType = $row["PayOwnerType"];
        $PayOwnerID = $row["PayOwnerID"];
        
        $row['ParticipantName'] = "Unknown Entity";
        $row['ParticipantPhoto'] = "images/ensan.jpg";
        $row['ProfileLink'] = "#";
        
        if($PayOwnerType == "SHOP") {
            $q = mysqli_query($con, "SELECT ShopName, ShopLogo FROM Shops WHERE ShopID='$PayOwnerID'");
            if($s = mysqli_fetch_assoc($q)) {
                $row['ParticipantName'] = $s['ShopName'];
                $row['ParticipantPhoto'] = $s['ShopLogo'];
                $row['ProfileLink'] = "shop-profile.php?id=$PayOwnerID";
            }
        } else {
            $q = mysqli_query($con, "SELECT FName, LName, PersonalPhoto FROM Drivers WHERE DriverID='$PayOwnerID'");
            if($d = mysqli_fetch_assoc($q)) {
                $row['ParticipantName'] = $d['FName'] . ' ' . $d['LName'];
                $row['ParticipantPhoto'] = $d['PersonalPhoto'];
                $row['ProfileLink'] = "driver-profile.php?id=$PayOwnerID";
            }
        }
        $eradTrans[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Income Ledgers | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        /* Sidebar Architecture */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        /* Breadcrumbs */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink: 0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        /* KPI Block */
        .kpi-master { display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, var(--accent-purple), #4A2BBF); border-radius: 20px; padding: 30px 40px; color: #FFF; margin-bottom: 30px; box-shadow: var(--shadow-float); flex-shrink:0; }
        .kpi-master h4 { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-bottom: 5px; }
        .kpi-master h1 { font-size: 42px; font-weight: 800; display: flex; align-items: baseline; gap: 10px; }
        .kpi-master h1 span { font-size: 20px; font-weight: 700; opacity: 0.7; }

        /* Grid of Revenue Channels */
        .rev-channels { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; flex-shrink:0; }
        .channel-card { background: var(--bg-white); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 15px; box-shadow: var(--shadow-card); text-decoration:none; transition:0.3s; border:1px solid transparent; }
        .channel-card:hover { transform: translateY(-3px); border-color: var(--accent-purple); }
        .ch-icon { width: 45px; height: 45px; border-radius: 12px; background: var(--accent-purple-light); color: var(--accent-purple); display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .ch-data h5 { font-size: 12px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; margin-bottom: 3px; }
        .ch-data h3 { font-size: 18px; font-weight: 800; color: var(--text-dark); }

        /* Data Ledger Table Container */
        .table-container { background: var(--bg-white); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-card); overflow: hidden; margin-bottom:20px; flex-shrink:0; }
        .table-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .table-head h2 { font-size: 18px; font-weight: 800; color: var(--text-dark); display: flex; align-items: center; gap: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 1px; padding: 15px; border-bottom: 2px solid var(--border-color); text-align: left; }
        td { font-size: 14px; font-weight: 600; color: var(--text-dark); padding: 18px 15px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        
        .part-node { display: flex; align-items: center; gap: 12px; text-decoration:none; color:var(--text-dark); transition:0.2s;}
        .part-node:hover { color:var(--accent-purple); }
        .part-node img { width: 35px; height: 35px; border-radius: 8px; object-fit: cover; }
        .rev-amt { font-size: 16px; font-weight: 800; color: var(--accent-green); }
        
        /* Pagination */
        .pagination { display: flex; align-items: center; justify-content: space-between; margin-top: 30px; font-size: 13px; font-weight: 600; color: var(--text-gray); }
        .page-ctrls { display: flex; gap: 8px; }
        .page-btn { padding: 8px 16px; border-radius: 10px; background: var(--bg-app); color: var(--text-dark); text-decoration: none; transition: 0.2s; font-weight: 700; border: 1px solid var(--border-color); }
        .page-btn:hover { background: var(--accent-purple); color: #FFF; border-color: var(--accent-purple); }
        .page-btn.disabled { opacity: 0.5; pointer-events: none; }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }
            .sidebar { display: none !important; }
            .main-panel { padding: 16px 16px 80px; overflow-y: visible; overflow-x: hidden; }

            /* Header */
            .header { flex-wrap: wrap; gap: 8px; margin-bottom: 16px; padding: 12px 16px; }
            .breadcrumb { font-size: 13px; flex-wrap: wrap; }

            /* KPI hero: stack on tablet */
            .kpi-master {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
                padding: 24px;
                margin-bottom: 16px;
                border-radius: 16px;
            }
            .kpi-master h1 { font-size: 32px; }

            /* Revenue channels: 2 cols */
            .rev-channels { grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
            .channel-card { padding: 16px; gap: 12px; border-radius: 12px; }
            .ch-icon { width: 38px; height: 38px; font-size: 15px; }
            .ch-data h3 { font-size: 15px; }

            /* Table: horizontal scroll on tablet */
            .table-container { padding: 16px; border-radius: 16px; }
            table { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
            td, th { padding: 12px 10px; }

            .pagination { flex-direction: column; gap: 10px; align-items: flex-start; }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .kpi-master h1 { font-size: 26px; }
            .kpi-master h4 { font-size: 12px; }

            /* Revenue channels: single col */
            .rev-channels { grid-template-columns: 1fr; gap: 10px; }

            /* Table: hide Timestamp + Entity Type cols — keep ID, Entity, Revenue */
            table thead tr th:nth-child(2),
            table thead tr th:nth-child(4),
            table tbody tr td:nth-child(2),
            table tbody tr td:nth-child(4) { display: none; }

            td { font-size: 13px; padding: 10px 8px; }
            .rev-amt { font-size: 14px; }
            .part-node img { width: 28px; height: 28px; }
        }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="wallet.php"><i class="fas fa-wallet"></i> Financial Overview</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">Income Ledgers (Erad Options)</span>
                </div>
            </header>

            <div class="kpi-master">
                <div>
                    <h4>Total Aggregated Platform Income</h4>
                    <h1><?= number_format($TotalIncome, 2) ?> <span>MAD</span></h1>
                </div>
                <div>
                    <i class="fas fa-chart-line fa-3x" style="opacity:0.2;"></i>
                </div>
            </div>

            <div class="rev-channels">
                <a href="#" class="channel-card">
                    <div class="ch-icon"><i class="fas fa-crown"></i></div>
                    <div class="ch-data"><h5>Subscription Revenues</h5><h3><?= number_format($SubscriptionR, 2) ?> MAD</h3></div>
                </a>
                <a href="walletEradSalesR.php" class="channel-card">
                    <div class="ch-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="ch-data"><h5>Sales Revenues</h5><h3><?= number_format($SalesR, 2) ?> MAD</h3></div>
                </a>
                <a href="walletEradDelv.php" class="channel-card">
                    <div class="ch-icon"><i class="fas fa-motorcycle"></i></div>
                    <div class="ch-data"><h5>Delivery Revenues</h5><h3><?= number_format($DeliveryR, 2) ?> MAD</h3></div>
                </a>
                
                <a href="walletEradRased1.php" class="channel-card">
                    <div class="ch-icon"><i class="fas fa-exchange-alt"></i></div>
                    <div class="ch-data"><h5>Balance Transfer</h5><h3><?= number_format($BalanceTraComm, 2) ?> MAD</h3></div>
                </a>
                <a href="walletEradRased2.php" class="channel-card">
                    <div class="ch-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="ch-data"><h5>Withdrawal Comm.</h5><h3><?= number_format($BalanceWithComm, 2) ?> MAD</h3></div>
                </a>
                <a href="walletEradRased3.php" class="channel-card">
                    <div class="ch-icon"><i class="fas fa-percentage"></i></div>
                    <div class="ch-data"><h5>Service Commission</h5><h3><?= number_format($ServComm, 2) ?> MAD</h3></div>
                </a>
            </div>

            <div class="table-container">
                <div class="table-head">
                    <h2><i class="fas fa-list-ul" style="color:var(--accent-purple);"></i> Income Log (Transactions Matrix)</h2>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Transaction ID</th>
                            <th>Date / Timestamp</th>
                            <th>Paying Entity</th>
                            <th>Entity Type</th>
                            <th>Cleared Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($eradTrans) === 0): ?>
                        <tr><td colspan="5" style="text-align:center; padding:30px; color:var(--text-gray);">No verified transactions available in the ledger.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach($eradTrans as $t): ?>
                        <tr>
                            <td>
                                <span style="font-weight: 800; color: var(--accent-blue); background: rgba(0, 122, 255, 0.1); padding: 5px 12px; border-radius: 8px; font-size: 12px;">#<?= $t['EradTransID'] ?></span>
                            </td>
                            <td style="color:var(--text-gray); font-size:13px;"><?= $t['CreatedAtEradTrans'] ?></td>
                            <td>
                                <a href="<?= $t['ProfileLink'] ?>" class="part-node">
                                    <img src="<?= htmlspecialchars($t['ParticipantPhoto']) ?>" onerror="this.src='images/ensan.jpg'">
                                    <span><?= htmlspecialchars($t['ParticipantName']) ?></span>
                                </a>
                            </td>
                            <td>
                                <?php if($t['PayOwnerType'] == 'SHOP'): ?>
                                <span style="background:var(--accent-purple-light); color:var(--accent-purple); padding:4px 10px; border-radius:6px; font-size:11px; font-weight:800;"><i class="fas fa-store"></i> SHOP</span>
                                <?php else: ?>
                                <span style="background:var(--bg-app); color:var(--text-dark); padding:4px 10px; border-radius:6px; font-size:11px; font-weight:800;"><i class="fas fa-motorcycle"></i> DRIVER</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="rev-amt">+<?= number_format($t['Money'], 2) ?> MAD</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <span>Listing Global Cleared Incomes</span>
                    <div class="page-ctrls">
                        <a href="?Page=<?= max(0, $page - 1) ?>" class="page-btn <?= $page <= 0 ? 'disabled' : '' ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                        <a href="?Page=<?= $page + 1 ?>" class="page-btn <?= count($eradTrans) < $limit ? 'disabled' : '' ?>">Next <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>