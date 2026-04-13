<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

// Core Income Channels
$resIncome = mysqli_query($con, "SELECT * FROM Money");
$incomeData = mysqli_fetch_assoc($resIncome);
$TotalIncome     = $incomeData["TotalIncome"]     ?? 0;
$SubscriptionR   = $incomeData["SubscriptionR"]   ?? 0;
$SalesR          = $incomeData["SalesR"]           ?? 0;
$DeliveryR       = $incomeData["DeliveryR"]        ?? 0;
$BalanceTraComm  = $incomeData["BalanceTraComm"]   ?? 0;
$BalanceWithComm = $incomeData["BalanceWithComm"]  ?? 0;
$ServComm        = $incomeData["ServComm"]         ?? 0;

// Transfer commission percentage + count
$resCfg = mysqli_query($con, "SELECT SendMoneyPerc FROM OrdersJiblerpercentage");
$cfgRow = mysqli_fetch_assoc($resCfg);
$SendMoneyPerc = $cfgRow["SendMoneyPerc"] ?? 0;

$resCnt = mysqli_query($con, "SELECT COUNT(*) AS cnt FROM SendMoneyTransactions");
$cntRow = mysqli_fetch_assoc($resCnt);
$totalCount = $cntRow["cnt"] ?? 0;

// Pagination
$page   = isset($_GET["Page"]) ? (int)$_GET["Page"] : 0;
if ($page < 0) $page = 0;
$limit  = 10;
$offset = $page * $limit;

$SearchQuery = isset($_GET["UserName"]) ? mysqli_real_escape_string($con, $_GET["UserName"]) : '';
$where = "1=1";
if ($SearchQuery != '') {
    $where .= " AND Users.name LIKE '%$SearchQuery%'";
}

$resTx = mysqli_query($con, "
    SELECT SendMoneyTransactions.SendMoneyTransactionsID,
           SendMoneyTransactions.TotalMoney,
           SendMoneyTransactions.CuttenMoney,
           SendMoneyTransactions.CreatedAtSendMoneyTransactions,
           Users.UserID, Users.name, Users.UserPhoto
    FROM SendMoneyTransactions
    JOIN Users ON SendMoneyTransactions.UserID = Users.UserID
    WHERE $where
    ORDER BY SendMoneyTransactionsID DESC
    LIMIT $limit OFFSET $offset
");
$txList = [];
if ($resTx) {
    while ($row = mysqli_fetch_assoc($resTx)) { $txList[] = $row; }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balance Transfer Commission | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-green: #10B981; --accent-blue: #007AFF;
            --accent-orange: #F59E0B; --accent-red: #EF4444;
            --border-color: #F0F2F6;
            --shadow-card: 0 8px 30px rgba(0,0,0,0.03);
            --shadow-float: 0 12px 35px rgba(0,0,0,0.05);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink: 0; }
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); flex-wrap: wrap; }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        .kpi-master { display: flex; align-items: center; justify-content: space-between; background: linear-gradient(135deg, #1D4ED8, #2563EB); border-radius: 20px; padding: 30px 40px; color: #FFF; margin-bottom: 30px; box-shadow: var(--shadow-float); flex-shrink: 0; }
        .kpi-master h4 { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-bottom: 5px; }
        .kpi-master h1 { font-size: 42px; font-weight: 800; display: flex; align-items: baseline; gap: 10px; }
        .kpi-master h1 span { font-size: 20px; font-weight: 700; opacity: 0.7; }
        .kpi-badge { background: rgba(255,255,255,0.15); border-radius: 12px; padding: 12px 20px; text-align: center; }
        .kpi-badge small { display: block; font-size: 11px; opacity: 0.75; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
        .kpi-badge strong { font-size: 20px; font-weight: 800; }

        .rev-channels { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px; flex-shrink: 0; }
        .channel-card { background: var(--bg-white); border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 15px; box-shadow: var(--shadow-card); text-decoration: none; transition: 0.3s; border: 2px solid transparent; }
        .channel-card:hover { transform: translateY(-3px); }
        .channel-card.active { border-color: var(--accent-purple); background: var(--accent-purple-light); }
        .ch-icon { width: 45px; height: 45px; border-radius: 12px; background: var(--accent-purple-light); color: var(--accent-purple); display: flex; align-items: center; justify-content: center; font-size: 18px; }
        .channel-card.active .ch-icon { background: var(--accent-purple); color: #FFF; }
        .ch-data h5 { font-size: 12px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; margin-bottom: 3px; }
        .ch-data h3 { font-size: 18px; font-weight: 800; color: var(--text-dark); }
        .channel-card.active .ch-data h5 { color: var(--accent-purple); }

        .content-grid { display: block; margin-bottom: 20px; }
        .table-container { background: var(--bg-white); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-card); overflow: hidden; }
        .table-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .search-box { background: var(--bg-app); border-radius: 12px; padding: 10px 18px; display: flex; align-items: center; gap: 10px; width: 280px; }
        .search-box input { border: none; background: transparent; outline: none; width: 100%; font-size: 13px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 1px; padding: 15px; border-bottom: 2px solid var(--border-color); text-align: left; }
        td { font-size: 14px; font-weight: 600; color: var(--text-dark); padding: 18px 15px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        .part-node { display: flex; align-items: center; gap: 12px; text-decoration: none; color: var(--text-dark); transition: 0.2s; }
        .part-node:hover { color: var(--accent-purple); }
        .part-node img { width: 35px; height: 35px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border-color); }
        .badge-fee { font-size: 12px; font-weight: 800; background: rgba(245,158,11,0.1); color: var(--accent-orange); padding: 4px 10px; border-radius: 8px; }
        .rev-amt { font-size: 15px; font-weight: 800; color: var(--accent-blue); }

        .pagination { display: flex; align-items: center; justify-content: space-between; margin-top: 25px; font-size: 13px; font-weight: 600; color: var(--text-gray); }
        .page-ctrls { display: flex; gap: 8px; }
        .page-btn { padding: 8px 16px; border-radius: 10px; background: var(--bg-app); color: var(--text-dark); text-decoration: none; transition: 0.2s; font-weight: 700; border: 1px solid var(--border-color); }
        .page-btn:hover { background: var(--accent-purple); color: #FFF; border-color: var(--accent-purple); }
        .page-btn.disabled { opacity: 0.4; pointer-events: none; }

        .stats-row { display: flex; gap: 16px; flex-wrap: wrap; margin-top: 0px; }
        .stat-item { display: flex; align-items: center; justify-content: space-between; background: var(--bg-app); border-radius: 10px; padding: 10px 14px; flex: 1; min-width: 160px; }
        .stat-item span { font-size: 12px; font-weight: 600; color: var(--text-gray); }
        .stat-item strong { font-size: 14px; font-weight: 800; color: var(--text-dark); }

        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }
            .sidebar { display: none !important; }
            .main-panel { padding: 16px 16px 80px; overflow-y: visible; overflow-x: hidden; }
            .header { flex-wrap: wrap; gap: 8px; margin-bottom: 16px; padding: 12px 16px; }
            .kpi-master { flex-direction: column; align-items: flex-start; gap: 12px; padding: 22px; margin-bottom: 16px; border-radius: 16px; }
            .kpi-master h1 { font-size: 28px; }
            .kpi-badge { width: 100%; }
            .rev-channels { grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
            .channel-card { padding: 14px; }
            .content-grid { grid-template-columns: 1fr; gap: 16px; }
            .table-container { padding: 16px; border-radius: 14px; }
            .table-head { flex-wrap: wrap; gap: 10px; }
            .search-box { width: 100%; }
            table { display: block; overflow-x: auto; -webkit-overflow-scrolling: touch; }
            td, th { padding: 12px 10px; }
            .pagination { flex-direction: column; gap: 10px; align-items: flex-start; }
            .chart-panel { padding: 16px; }
        }
        @media (max-width: 600px) {
            .rev-channels { grid-template-columns: 1fr; }
            table thead tr th:nth-child(3),
            table tbody tr td:nth-child(3) { display: none; }
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
                <a href="walletErad.php">Income Ledgers</a>
                <span>/</span>
                <span style="color: var(--accent-purple);">Balance Transfer Commission</span>
            </div>
        </header>

        <div class="kpi-master">
            <div>
                <h4>Balance Transfer Total Commissions</h4>
                <h1><?= number_format($BalanceTraComm, 2) ?> <span>MAD</span></h1>
            </div>
            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <div class="kpi-badge">
                    <small>Commission Rate</small>
                    <strong><?= $SendMoneyPerc ?>%</strong>
                </div>
                <div class="kpi-badge">
                    <small>Total Transfers</small>
                    <strong><?= number_format($totalCount) ?></strong>
                </div>
            </div>
        </div>

        <div class="rev-channels">
            <a href="walletErad.php" class="channel-card">
                <div class="ch-icon"><i class="fas fa-crown"></i></div>
                <div class="ch-data"><h5>Subscription Rev.</h5><h3><?= number_format($SubscriptionR, 2) ?> MAD</h3></div>
            </a>
            <a href="walletEradSalesR.php" class="channel-card">
                <div class="ch-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="ch-data"><h5>Sales Revenues</h5><h3><?= number_format($SalesR, 2) ?> MAD</h3></div>
            </a>
            <a href="walletEradDelv.php" class="channel-card">
                <div class="ch-icon"><i class="fas fa-motorcycle"></i></div>
                <div class="ch-data"><h5>Delivery Revenues</h5><h3><?= number_format($DeliveryR, 2) ?> MAD</h3></div>
            </a>
            <a href="walletEradRased1.php" class="channel-card active">
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

        <div class="content-grid">
            <div class="table-container">
                <div class="table-head">
                    <h2 style="font-size:18px; font-weight:800;">
                        <i class="fas fa-exchange-alt" style="color:var(--accent-purple);"></i> Transfer Commission Log
                    </h2>
                    <form class="search-box" method="GET">
                        <i class="fas fa-search" style="color:var(--text-gray);"></i>
                        <input type="text" name="UserName" placeholder="Search User Name..." value="<?= htmlspecialchars($SearchQuery) ?>">
                    </form>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Total Transferred</th>
                            <th>Commission Cut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($txList) === 0): ?>
                        <tr><td colspan="4" style="text-align:center; padding:30px; color:var(--text-gray);">No transfer commission logs found.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($txList as $t): ?>
                        <tr>
                            <td>
                                <a href="user.php?id=<?= $t['UserID'] ?>" class="part-node">
                                    <img src="<?= htmlspecialchars($t['UserPhoto']) ?>" onerror="this.src='images/placeholder.png'">
                                    <span><?= htmlspecialchars($t['name']) ?></span>
                                </a>
                            </td>
                            <td><span class="rev-amt"><?= number_format($t['TotalMoney'], 2) ?> MAD</span></td>
                            <td><span class="badge-fee">+<?= number_format($t['CuttenMoney'], 2) ?> MAD</span></td>
                            <td style="color:var(--text-gray); font-size:13px;"><?= $t['CreatedAtSendMoneyTransactions'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="pagination">
                    <span>Balance Transfer Commissions</span>
                    <div class="page-ctrls">
                        <a href="?UserName=<?= urlencode($SearchQuery) ?>&Page=<?= max(0, $page - 1) ?>" class="page-btn <?= $page <= 0 ? 'disabled' : '' ?>">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                        <a href="?UserName=<?= urlencode($SearchQuery) ?>&Page=<?= $page + 1 ?>" class="page-btn <?= count($txList) < $limit ? 'disabled' : '' ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Summary Bar -->
        <div class="stats-row" style="margin-bottom:20px;">
            <div class="stat-item">
                <span>Total Transfer Commission</span>
                <strong><?= number_format($BalanceTraComm, 2) ?> MAD</strong>
            </div>
            <div class="stat-item">
                <span>Commission Rate</span>
                <strong><?= $SendMoneyPerc ?>% per transfer</strong>
            </div>
            <div class="stat-item">
                <span>Total Transactions</span>
                <strong><?= number_format($totalCount) ?></strong>
            </div>
        </div>
    </main>
</div>

</body>
</html>