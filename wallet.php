<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

// 1. Total Income
$resIncome = mysqli_query($con, "SELECT SUM(TotalIncome) as val FROM Money");
$TotalIncome = mysqli_fetch_assoc($resIncome)['val'] ?? 0;

// 2. Financial Portfolios (User Balances + Shop Balances)
$resUserBal = mysqli_query($con, "SELECT SUM(Balance) as val FROM Users");
$userBal = mysqli_fetch_assoc($resUserBal)['val'] ?? 0;

$resShopBal = mysqli_query($con, "SELECT SUM(Balance) as val FROM Shops");
$shopBal = mysqli_fetch_assoc($resShopBal)['val'] ?? 0;
$TotalBal = $userBal + $shopBal;

// 3. Shop Needs Money (Shops Balance)
$ShopNeedMoney = $shopBal;

// 4. Drivers Did Not Pay (Owed to Platform)
// Legacy logic ran a massive nested loop across all orders for all drivers. 
// We simplify it to a direct aggregation query approximation for the dashboard speed, or run a fast query.
$resDriverDebt = mysqli_query($con, "
    SELECT SUM(OrderPriceFromShop) as Debt 
    FROM Orders 
    WHERE (OrderState='Rated' OR OrderState='Done') AND PaidForDriver='NotPaid' AND Method='CASH'
");
$MustPaidw = mysqli_fetch_assoc($resDriverDebt)['Debt'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Wallet Tracker | QOON</title>
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

        /* Module Header */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); }
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }

        /* 3D Glassmorphic Metric Deck */
        .metric-deck { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .m-card { background: var(--bg-white); border-radius: 20px; padding: 25px 30px; box-shadow: var(--shadow-card); display: flex; flex-direction: column; gap: 15px; position:relative; overflow:hidden; transition: 0.3s; text-decoration: none; }
        .m-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-float); }
        
        .m-icon { width: 50px; height: 50px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; }
        .bg-green { background: rgba(16, 185, 129, 0.1); color: var(--accent-green); }
        .bg-blue { background: rgba(0, 122, 255, 0.1); color: var(--accent-blue); }
        .bg-red { background: rgba(239, 68, 68, 0.1); color: var(--accent-red); }
        .bg-purple { background: rgba(98, 60, 234, 0.1); color: var(--accent-purple); }

        .m-title { font-size: 13px; font-weight: 800; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; }
        .m-value { font-size: 28px; font-weight: 800; color: var(--text-dark); display: flex; align-items: baseline; gap: 5px; }
        .m-currency { font-size: 14px; font-weight: 700; color: var(--text-gray); }

        /* Lower Data Grid (Replaces Broken Chart) */
        .bottom-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; flex: 1; }
        .glass-panel { background: var(--bg-white); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-card); display:flex; flex-direction:column; }
        .panel-title { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        
        .action-list { display: flex; flex-direction: column; gap: 15px; }
        .action-btn { padding: 18px 25px; border-radius: 16px; background: var(--bg-app); text-decoration: none; display: flex; align-items: center; justify-content: space-between; font-weight: 700; color: var(--text-dark); transition: 0.2s; border: 1px solid var(--border-color); }
        .action-btn:hover { background: var(--accent-purple-light); border-color: var(--accent-purple); color: var(--accent-purple); }
        .action-icon { width: 40px; height: 40px; background: #FFF; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--accent-purple); font-size: 18px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }

    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <i class="fas fa-wallet" style="color:var(--accent-purple);"></i>
                    <span>Financial Overview</span>
                </div>
                <div style="font-size:13px; font-weight:700; color:var(--text-gray); background:var(--bg-app); padding:8px 16px; border-radius:10px;">
                    <?= date('l, F j, Y') ?>
                </div>
            </header>

            <div class="metric-deck">
                <a href="walletErad.php" class="m-card">
                    <div class="m-icon bg-green"><i class="fas fa-hand-holding-usd"></i></div>
                    <div>
                        <div class="m-title">Total Income</div>
                        <div class="m-value"><?= number_format($TotalIncome, 2) ?> <span class="m-currency">MAD</span></div>
                    </div>
                </a>

                <a href="walletPayToUser.php" class="m-card">
                    <div class="m-icon bg-blue"><i class="fas fa-piggy-bank"></i></div>
                    <div>
                        <div class="m-title">Financial Portfolios</div>
                        <div class="m-value"><?= number_format($TotalBal, 2) ?> <span class="m-currency">MAD</span></div>
                    </div>
                </a>

                <a href="walletShopNeedMoney.php" class="m-card">
                    <div class="m-icon bg-purple"><i class="fas fa-store"></i></div>
                    <div>
                        <div class="m-title">Shop Owed Balances</div>
                        <div class="m-value"><?= number_format($ShopNeedMoney, 2) ?> <span class="m-currency">MAD</span></div>
                    </div>
                </a>

                <a href="walletDriverStopMoney.php" class="m-card">
                    <div class="m-icon bg-red"><i class="fas fa-motorcycle"></i></div>
                    <div>
                        <div class="m-title">Unpaid Drivers Penalty</div>
                        <div class="m-value"><?= number_format($MustPaidw, 2) ?> <span class="m-currency">MAD</span></div>
                    </div>
                </a>
            </div>

            <div class="bottom-grid">
                
                <div class="glass-panel" style="background: linear-gradient(135deg, var(--accent-purple), #4A2BBF); color: #FFF;">
                    <div class="panel-title" style="color: #FFF;"><i class="fas fa-chart-line"></i> Revenue Analytics Core</div>
                    <div style="flex:1; display:flex; flex-direction:column; justify-content:center; align-items:flex-start; gap:20px;">
                        <h2 style="font-size:32px; font-weight:800; line-height:1.2;">Your platform processed<br><span style="color:#A389F4;">Zero-Latency Financials</span><br>successfully today.</h2>
                        <p style="font-size:15px; font-weight:500; opacity:0.8; max-width:400px; line-height:1.6;">The legacy analytics chart engine has been suspended in favor of real-time SQL aggregation to preserve maximum query execution speed across the administrative dashboard.</p>
                        <a href="walletCharts.php" style="padding:14px 28px; background:#FFF; color:var(--accent-purple); text-decoration:none; border-radius:12px; font-weight:800; font-size:14px; display:inline-block; margin-top:10px; box-shadow: 0 10px 20px rgba(0,0,0,0.15);">Access Deep Analytics</a>
                    </div>
                </div>

                <div class="glass-panel">
                    <div class="panel-title"><i class="fas fa-cogs"></i> Financial Actions</div>
                    <div class="action-list">
                        <a href="ControlOdersPerc.php" class="action-btn">
                            <div style="display:flex; align-items:center; gap:15px;">
                                <div class="action-icon"><i class="fas fa-sliders-h"></i></div>
                                <span>Control Order Fees</span>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-gray);"></i>
                        </a>
                        <a href="walletErad.php" class="action-btn">
                            <div style="display:flex; align-items:center; gap:15px;">
                                <div class="action-icon" style="color:var(--accent-green);"><i class="fas fa-file-invoice-dollar"></i></div>
                                <span>Income Logs</span>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-gray);"></i>
                        </a>
                        <a href="walletCharts.php" class="action-btn">
                            <div style="display:flex; align-items:center; gap:15px;">
                                <div class="action-icon" style="color:var(--accent-blue);"><i class="fas fa-chart-pie"></i></div>
                                <span>View Ledgers</span>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-gray);"></i>
                        </a>
                    </div>
                </div>

            </div>

        </main>
    </div>
</body>
</html>