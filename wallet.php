<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$resIncome   = mysqli_query($con, "SELECT SUM(TotalIncome) as val FROM Money");
$TotalIncome = mysqli_fetch_assoc($resIncome)['val'] ?? 0;

$resUserBal = mysqli_query($con, "SELECT SUM(Balance) as val FROM Users");
$userBal    = mysqli_fetch_assoc($resUserBal)['val'] ?? 0;

$resShopBal = mysqli_query($con, "SELECT SUM(Balance) as val FROM Shops");
$shopBal    = mysqli_fetch_assoc($resShopBal)['val'] ?? 0;
$TotalBal   = $userBal + $shopBal;

$resDriverDebt = mysqli_query($con, "SELECT SUM(OrderPriceFromShop) as Debt FROM Orders WHERE (OrderState='Rated' OR OrderState='Done') AND PaidForDriver='NotPaid' AND Method='CASH'");
$MustPaidw     = mysqli_fetch_assoc($resDriverDebt)['Debt'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Core | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-master: #F3F4F6;
            --bg-surface: #FFFFFF;
            --border-subtle: #E5E7EB;
            --border-focus: #D1D5DB;

            --text-strong: #111827;
            --text-base: #374151;
            --text-muted: #6B7280;
            --text-on-dark: #FFFFFF;

            --green-bg: #ECFDF5; --green-text: #059669;
            --blue-bg: #EFF6FF;  --blue-text: #2563EB;
            --purple-bg: #F5F3FF; --purple-text: #7C3AED;
            --red-bg: #FEF2F2;   --red-text: #DC2626;

            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', -apple-system, sans-serif; }

        body {
            background: var(--bg-master);
            color: var(--text-base);
            display: flex;
            height: 100vh;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .layout-wrapper { display: flex; width: 100%; height: 100%; }

        main.content-area {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        main.content-area::-webkit-scrollbar { width: 6px; }
        main.content-area::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

        /* Header */
        .header-bar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-title h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-strong);
            letter-spacing: -0.5px;
        }
        .page-title p {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 4px;
        }
        .date-tag {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            padding: 8px 16px;
            border-radius: 8px;
            box-shadow: var(--shadow-sm);
        }

        /* Body */
        .page-body {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        /* Metrics */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }

        .metric-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 28px 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: var(--shadow-sm);
            transition: 0.2s;
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }
        .metric-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-3px);
            border-color: var(--border-focus);
        }
        .metric-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px;
        }
        .metric-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .metric-val {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-strong);
            letter-spacing: -1px;
            line-height: 1;
        }
        .metric-val span {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
            margin-left: 4px;
        }
        .metric-arrow {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: var(--border-focus);
            transition: 0.2s;
        }
        .metric-card:hover .metric-arrow { color: var(--text-strong); right: 16px; }

        .mi-green  { background: var(--green-bg);  color: var(--green-text); }
        .mi-blue   { background: var(--blue-bg);   color: var(--blue-text); }
        .mi-purple { background: var(--purple-bg); color: var(--purple-text); }
        .mi-red    { background: var(--red-bg);    color: var(--red-text); }

        /* Two-col layout */
        .two-col {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
        }

        /* Highlight Banner */
        .highlight-banner {
            background: var(--text-strong);
            color: var(--text-on-dark);
            border-radius: 20px;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            justify-content: center;
            box-shadow: var(--shadow-md);
        }
        .highlight-banner .tag {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: rgba(255,255,255,0.4);
        }
        .highlight-banner h2 {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            line-height: 1.3;
            max-width: 480px;
            color: #FFFFFF;
        }
        .highlight-banner p {
            font-size: 14px;
            color: rgba(255,255,255,0.55);
            line-height: 1.6;
            max-width: 440px;
            font-weight: 500;
        }
        .banner-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #FFFFFF;
            color: var(--text-strong);
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            align-self: flex-start;
            transition: 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .banner-cta:hover { opacity: 0.85; }

        /* Action Card */
        .action-panel {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .action-panel-head {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 15px;
            font-weight: 700;
            color: var(--text-strong);
            background: #F9FAFB;
        }
        .action-list { padding: 12px; display: flex; flex-direction: column; gap: 4px; }
        .action-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-radius: 10px;
            text-decoration: none;
            color: var(--text-strong);
            font-weight: 600;
            font-size: 14px;
            transition: 0.15s;
        }
        .action-item:hover {
            background: #F3F4F6;
        }
        .action-item-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .action-ico {
            width: 34px; height: 34px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-surface);
        }
        .action-item .arrow {
            font-size: 12px;
            color: var(--border-focus);
        }
        .action-item:hover .arrow { color: var(--text-strong); }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }
            .sb-container { display: none !important; }
            main.content-area { overflow-y: visible; }

            .header-bar { flex-wrap: wrap; gap: 10px; padding: 14px 16px; position: static; }
            .page-title h1 { font-size: 20px; }
            .date-tag { font-size: 12px; padding: 6px 12px; }
            .page-body { padding: 16px 16px 80px; gap: 20px; }

            /* 4-col metrics → 2-col */
            .metrics-grid { grid-template-columns: 1fr 1fr; gap: 14px; }
            .metric-card { padding: 20px 16px; }
            .metric-val { font-size: 20px; }

            /* two-col → single column */
            .two-col { grid-template-columns: 1fr; gap: 16px; }
            .highlight-banner { padding: 28px 24px; border-radius: 14px; }
            .highlight-banner h2 { font-size: 20px; }
            .highlight-banner p { font-size: 13px; }
            .action-item { padding: 14px 12px; }
        }
        @media (max-width: 600px) {
            /* 2-col → 1-col */
            .metrics-grid { grid-template-columns: 1fr; gap: 10px; }
            .metric-val { font-size: 18px; }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">

            <header class="header-bar">
                <div class="page-title">
                    <h1>Financial Core</h1>
                    <p>Manage QOON marketplace capital &amp; settlements.</p>
                </div>
                <div class="date-tag">
                    <i class="far fa-calendar" style="margin-right:8px; color:var(--text-muted);"></i>
                    <?= date('l, d M Y') ?>
                </div>
            </header>

            <div class="page-body">

                <!-- Metrics Row -->
                <div class="metrics-grid">
                    <a href="walletErad.php" class="metric-card">
                        <div class="metric-icon mi-green"><i class="fas fa-vault"></i></div>
                        <div>
                            <div class="metric-label">Managed Income</div>
                            <div class="metric-val"><?= number_format($TotalIncome, 2) ?><span>MAD</span></div>
                        </div>
                        <i class="fas fa-arrow-right metric-arrow"></i>
                    </a>
                    <a href="walletPayToUser.php" class="metric-card">
                        <div class="metric-icon mi-blue"><i class="fas fa-piggy-bank"></i></div>
                        <div>
                            <div class="metric-label">Portfolio Balance</div>
                            <div class="metric-val"><?= number_format($TotalBal, 2) ?><span>MAD</span></div>
                        </div>
                        <i class="fas fa-arrow-right metric-arrow"></i>
                    </a>
                    <a href="walletShopNeedMoney.php" class="metric-card">
                        <div class="metric-icon mi-purple"><i class="fas fa-store"></i></div>
                        <div>
                            <div class="metric-label">Shop Liability</div>
                            <div class="metric-val"><?= number_format($shopBal, 2) ?><span>MAD</span></div>
                        </div>
                        <i class="fas fa-arrow-right metric-arrow"></i>
                    </a>
                    <a href="walletDriverStopMoney.php" class="metric-card">
                        <div class="metric-icon mi-red"><i class="fas fa-motorcycle"></i></div>
                        <div>
                            <div class="metric-label">Fleet Debt</div>
                            <div class="metric-val"><?= number_format($MustPaidw, 2) ?><span>MAD</span></div>
                        </div>
                        <i class="fas fa-arrow-right metric-arrow"></i>
                    </a>
                </div>

                <!-- Two-Col Section -->
                <div class="two-col">

                    <!-- Highlight Banner -->
                    <div class="highlight-banner">
                        <div class="tag">Intelligence Engine</div>
                        <h2>Settling global transactions at the speed of thought.</h2>
                        <p>Every MAD processed through QOON is encrypted and tracked via our zero-latency financial engine — fully auditable in real-time.</p>
                        <a href="walletCharts.php" class="banner-cta">
                            <i class="fas fa-chart-bar"></i> Deep Audit Records
                        </a>
                    </div>

                    <!-- Capital Controls -->
                    <div class="action-panel">
                        <div class="action-panel-head">Capital Controls</div>
                        <div class="action-list">
                            <a href="ControlOdersPerc.php" class="action-item">
                                <div class="action-item-left">
                                    <div class="action-ico" style="color:#7C3AED;"><i class="fas fa-percentage"></i></div>
                                    <span>Fee Management</span>
                                </div>
                                <i class="fas fa-chevron-right arrow"></i>
                            </a>
                            <a href="walletErad.php" class="action-item">
                                <div class="action-item-left">
                                    <div class="action-ico" style="color:#059669;"><i class="fas fa-file-invoice-dollar"></i></div>
                                    <span>Income Stream Logs</span>
                                </div>
                                <i class="fas fa-chevron-right arrow"></i>
                            </a>
                            <a href="walletCharts.php" class="action-item">
                                <div class="action-item-left">
                                    <div class="action-ico" style="color:#2563EB;"><i class="fas fa-chart-pie"></i></div>
                                    <span>Ledger Analytics</span>
                                </div>
                                <i class="fas fa-chevron-right arrow"></i>
                            </a>
                            <a href="walletPayToUser.php" class="action-item">
                                <div class="action-item-left">
                                    <div class="action-ico" style="color:#D97706;"><i class="fas fa-hand-holding-usd"></i></div>
                                    <span>User Payouts</span>
                                </div>
                                <i class="fas fa-chevron-right arrow"></i>
                            </a>
                            <a href="walletDriverStopMoney.php" class="action-item">
                                <div class="action-item-left">
                                    <div class="action-ico" style="color:#DC2626;"><i class="fas fa-motorcycle"></i></div>
                                    <span>Driver Settlements</span>
                                </div>
                                <i class="fas fa-chevron-right arrow"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>