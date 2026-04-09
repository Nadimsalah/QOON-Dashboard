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

// Pagination for Sales Revenue Logs
$page = isset($_GET["Page"]) ? (int)$_GET["Page"] : 0;
if($page < 0) $page = 0;
$limit = 10;
$offset = $page * $limit;

$SearchQuery = isset($_GET["ShopName"]) ? mysqli_real_escape_string($con, $_GET["ShopName"]) : '';
$where = "1=1";
if($SearchQuery != '') {
    $where .= " AND Shops.ShopName LIKE '%$SearchQuery%'";
}

$resSales = mysqli_query($con, "
    SELECT SlasesRevTransactionID, OrderID, TotalPrice, CutPers, CreatedAtSlasesRevTransaction, 
           Shops.ShopID, Shops.ShopName, Shops.ShopLogo 
    FROM SlasesRevTransaction 
    JOIN Shops ON SlasesRevTransaction.ShopID = Shops.ShopID 
    WHERE $where
    ORDER BY SlasesRevTransactionID DESC 
    LIMIT $limit OFFSET $offset
");

$salesList = [];
if($resSales) {
    while($row = mysqli_fetch_assoc($resSales)){
        $salesList[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Revenues Ledger | QOON</title>
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

        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink: 0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        .kpi-master { display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, var(--accent-purple), #4A2BBF); border-radius: 20px; padding: 30px 40px; color: #FFF; margin-bottom: 30px; box-shadow: var(--shadow-float); flex-shrink:0; }
        .kpi-master h4 { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-bottom: 5px; }
        .kpi-master h1 { font-size: 42px; font-weight: 800; display: flex; align-items: baseline; gap: 10px; }
        .kpi-master h1 span { font-size: 20px; font-weight: 700; opacity: 0.7; }

        .rev-channels { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; flex-shrink:0; }
        .channel-card { background: var(--bg-white); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 15px; box-shadow: var(--shadow-card); text-decoration:none; transition:0.3s; border:2px solid transparent; }
        .channel-card:hover { transform: translateY(-3px); }
        .channel-card.active { border-color: var(--accent-purple); background: var(--accent-purple-light); }
        .ch-icon { width: 45px; height: 45px; border-radius: 12px; background: var(--accent-purple-light); color: var(--accent-purple); display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .channel-card.active .ch-icon { background: var(--accent-purple); color: #FFF; }
        .ch-data h5 { font-size: 12px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; margin-bottom: 3px; }
        .ch-data h3 { font-size: 18px; font-weight: 800; color: var(--text-dark); }
        .channel-card.active .ch-data h5 { color: var(--accent-purple); }

        .table-container { background: var(--bg-white); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-card); overflow: hidden; margin-bottom:20px; flex-shrink:0; }
        .table-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .search-box { background: var(--bg-app); border-radius: 12px; padding: 10px 18px; display: flex; align-items: center; gap: 10px; width: 300px; }
        .search-box input { border: none; background: transparent; outline: none; width: 100%; font-size: 13px; font-weight: 600; }
        
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 1px; padding: 15px; border-bottom: 2px solid var(--border-color); text-align: left; }
        td { font-size: 14px; font-weight: 600; color: var(--text-dark); padding: 18px 15px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        
        .part-node { display: flex; align-items: center; gap: 12px; text-decoration:none; color:var(--text-dark); transition:0.2s;}
        .part-node:hover { color:var(--accent-purple); }
        .part-node img { width: 35px; height: 35px; border-radius: 8px; object-fit: cover; }
        .rev-amt { font-size: 16px; font-weight: 800; color: var(--accent-green); }
        
        .pagination { display: flex; align-items: center; justify-content: space-between; margin-top: 30px; font-size: 13px; font-weight: 600; color: var(--text-gray); }
        .page-ctrls { display: flex; gap: 8px; }
        .page-btn { padding: 8px 16px; border-radius: 10px; background: var(--bg-app); color: var(--text-dark); text-decoration: none; transition: 0.2s; font-weight: 700; border: 1px solid var(--border-color); }
        .page-btn:hover { background: var(--accent-purple); color: #FFF; border-color: var(--accent-purple); }
        .page-btn.disabled { opacity: 0.5; pointer-events: none; }

        .badge-cut { font-size:12px; font-weight:800; background:rgba(245, 158, 11, 0.1); color:var(--accent-orange); padding:4px 10px; border-radius:8px; display:inline-block; }
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
                    <a href="walletErad.php">Income Ledgers</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">Sales Revenues</span>
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
                <a href="walletErad.php" class="channel-card">
                    <div class="ch-icon"><i class="fas fa-crown"></i></div>
                    <div class="ch-data"><h5>Subscription Revenues</h5><h3><?= number_format($SubscriptionR, 2) ?> MAD</h3></div>
                </a>
                <a href="walletEradSalesR.php" class="channel-card active">
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
                    <h2 style="font-size:18px; font-weight:800;"><i class="fas fa-shopping-cart" style="color:var(--accent-purple);"></i> Sales Revenue Log</h2>
                    <form class="search-box" method="GET">
                        <i class="fas fa-search" style="color:var(--text-gray);"></i>
                        <input type="text" name="ShopName" placeholder="Search Shop Name..." value="<?= htmlspecialchars($SearchQuery) ?>">
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Originating Store</th>
                            <th>Order ID</th>
                            <th>Gross Total Sales</th>
                            <th>Platform Cut Retained</th>
                            <th>Cleared Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($salesList) === 0): ?>
                        <tr><td colspan="6" style="text-align:center; padding:30px; color:var(--text-gray);">No specific sales revenue logs matched.</td></tr>
                        <?php endif; ?>
                        
                        <?php foreach($salesList as $t): ?>
                        <tr>
                            <td>
                                <a href="shop-profile.php?id=<?= $t['ShopID'] ?>" class="part-node">
                                    <img src="<?= htmlspecialchars($t['ShopLogo']) ?>" onerror="this.src='images/placeholder.png'">
                                    <span><?= htmlspecialchars($t['ShopName']) ?></span>
                                </a>
                            </td>
                            <td>
                                <span style="font-weight: 800; color: var(--accent-blue); background: rgba(0, 122, 255, 0.1); padding: 5px 12px; border-radius: 8px; font-size: 12px;">#<?= $t['OrderID'] ?></span>
                            </td>
                            <td><span style="font-weight:700; color:var(--text-dark);"><?= number_format($t['TotalPrice'], 2) ?> MAD</span></td>
                            <td><span class="badge-cut">+<?= number_format($t['CutPers'], 2) ?> MAD</span></td>
                            <td style="color:var(--text-gray); font-size:13px;"><?= $t['CreatedAtSlasesRevTransaction'] ?></td>
                            <td><a href="order-detail.php?OrderID=<?= $t['OrderID'] ?>" style="color:var(--accent-purple); font-weight:700; text-decoration:none; font-size:13px;">View Analytics</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <span>Listing Platform Cleared Sales Cuts</span>
                    <div class="page-ctrls">
                        <a href="?ShopName=<?= urlencode($SearchQuery) ?>&Page=<?= max(0, $page - 1) ?>" class="page-btn <?= $page <= 0 ? 'disabled' : '' ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                        <a href="?ShopName=<?= urlencode($SearchQuery) ?>&Page=<?= $page + 1 ?>" class="page-btn <?= count($salesList) < $limit ? 'disabled' : '' ?>">Next <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>