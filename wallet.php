<?php
require "conn.php";

// Handle AJAX PIN verification
if (isset($_POST['action']) && $_POST['action'] === 'verify_pin') {
    $pin = $_POST['pin'] ?? '';
    if ($pin === '8808') {
        $_SESSION['wallet_auth'] = true;
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Invalid vault PIN code.']);
    }
    exit;
}

$showPin = !isset($_SESSION['wallet_auth']) || $_SESSION['wallet_auth'] !== true;

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
            grid-template-columns: 1fr;
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
        /* Controls Grid */
        .controls-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .control-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 18px;
            text-decoration: none;
            color: var(--text-strong);
            transition: 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .control-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: var(--border-focus);
        }
        .control-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .control-info { display: flex; flex-direction: column; gap: 4px; flex: 1; }
        .control-title { font-weight: 700; font-size: 15px; color: var(--text-strong); }
        .control-sub { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .control-arrow { font-size: 12px; color: var(--border-focus); transition: 0.2s; }
        .control-card:hover .control-arrow { color: var(--text-strong); }

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

            /* controls grid → 1-col on mobile */
            .controls-grid { grid-template-columns: 1fr; gap: 12px; }
        }
        @media (max-width: 600px) {
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

                <!-- Capital Controls Grid -->
                <div>
                    <div style="font-size:13px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.8px; margin-bottom:16px;">Capital Controls</div>
                    <div class="controls-grid">
                        <a href="ControlOdersPerc.php" class="control-card">
                            <div class="control-icon" style="background:var(--purple-bg); color:var(--purple-text);"><i class="fas fa-percentage"></i></div>
                            <div class="control-info">
                                <span class="control-title">Fee Management</span>
                                <span class="control-sub">Adjust commission rates</span>
                            </div>
                            <i class="fas fa-chevron-right control-arrow"></i>
                        </a>
                        <a href="walletErad.php" class="control-card">
                            <div class="control-icon" style="background:var(--green-bg); color:var(--green-text);"><i class="fas fa-file-invoice-dollar"></i></div>
                            <div class="control-info">
                                <span class="control-title">Income Stream Logs</span>
                                <span class="control-sub">Revenue transaction history</span>
                            </div>
                            <i class="fas fa-chevron-right control-arrow"></i>
                        </a>
                        <a href="walletCharts.php" class="control-card">
                            <div class="control-icon" style="background:var(--blue-bg); color:var(--blue-text);"><i class="fas fa-chart-pie"></i></div>
                            <div class="control-info">
                                <span class="control-title">Ledger Analytics</span>
                                <span class="control-sub">Visual financial reports</span>
                            </div>
                            <i class="fas fa-chevron-right control-arrow"></i>
                        </a>
                        <a href="walletPayToUser.php" class="control-card">
                            <div class="control-icon" style="background:#FFF7ED; color:#D97706;"><i class="fas fa-hand-holding-usd"></i></div>
                            <div class="control-info">
                                <span class="control-title">User Payouts</span>
                                <span class="control-sub">Transfer wallet balances</span>
                            </div>
                            <i class="fas fa-chevron-right control-arrow"></i>
                        </a>
                        <a href="walletDriverStopMoney.php" class="control-card">
                            <div class="control-icon" style="background:var(--red-bg); color:var(--red-text);"><i class="fas fa-motorcycle"></i></div>
                            <div class="control-info">
                                <span class="control-title">Driver Settlements</span>
                                <span class="control-sub">Manage fleet cash debts</span>
                            </div>
                            <i class="fas fa-chevron-right control-arrow"></i>
                        </a>
                        <a href="walletShopNeedMoney.php" class="control-card">
                            <div class="control-icon" style="background:#F0FDF4; color:#16A34A;"><i class="fas fa-store"></i></div>
                            <div class="control-info">
                                <span class="control-title">Shop Disbursements</span>
                                <span class="control-sub">Outstanding shop balances</span>
                            </div>
                            <i class="fas fa-chevron-right control-arrow"></i>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php if ($showPin): ?>
    <!-- ── PIN BLUR POPUP ── -->
    <style>
        #pinOverlay {
            position:fixed; inset:0; z-index:9999;
            background: rgba(10,10,20,0.6);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            display:flex; align-items:center; justify-content:center;
        }
        #pinCard {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            backdrop-filter: blur(24px);
            border-radius: 28px;
            padding: 44px 40px;
            width: 100%; max-width: 400px;
            text-align: center;
            box-shadow: 0 32px 64px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.06);
            animation: pinSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) forwards;
            transform: translateY(30px); opacity:0;
            position: relative;
        }
        @keyframes pinSlideUp { to { transform:translateY(0); opacity:1; } }

        #pinCard .pin-icon {
            width:68px; height:68px; border-radius:20px;
            background: linear-gradient(135deg, rgba(124,58,237,0.3), rgba(99,60,234,0.1));
            border: 1px solid rgba(124,58,237,0.3);
            display:flex; align-items:center; justify-content:center;
            font-size:26px; color:#a78bfa;
            margin: 0 auto 22px;
            box-shadow: 0 8px 24px rgba(124,58,237,0.2);
        }
        #pinCard h2 { font-size:22px; font-weight:800; color:#fff; letter-spacing:-0.5px; margin-bottom:8px; }
        #pinCard p  { font-size:13.5px; color:rgba(255,255,255,0.45); margin-bottom:28px; line-height:1.5; }

        .pin-dots { display:flex; justify-content:center; gap:14px; margin-bottom:28px; }
        .pin-dot {
            width:14px; height:14px; border-radius:50%;
            border: 2px solid rgba(255,255,255,0.2);
            background: transparent;
            transition: all 0.2s;
        }
        .pin-dot.filled { background:#7c3aed; border-color:#7c3aed; box-shadow:0 0 8px rgba(124,58,237,0.6); }

        .pin-keypad { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:20px; }
        .pin-key {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            height: 64px;
            font-size: 20px; font-weight:700; color:#fff;
            cursor: pointer;
            display:flex; align-items:center; justify-content:center;
            transition: all 0.15s;
            user-select: none;
        }
        .pin-key:hover  { background:rgba(124,58,237,0.2); border-color:rgba(124,58,237,0.4); }
        .pin-key:active { transform:scale(0.94); background:rgba(124,58,237,0.35); }
        .pin-key.del    { font-size:16px; color:rgba(255,255,255,0.5); }
        .pin-key.empty  { visibility:hidden; }

        .pin-error {
            color:#f87171; font-size:13px; font-weight:600;
            background:rgba(239,68,68,0.1);
            border:1px solid rgba(239,68,68,0.2);
            border-radius:10px; padding:10px 14px;
            margin-bottom:16px; display:none;
        }
        .pin-error.show { display:block; animation:shake 0.4s; }
        @keyframes shake {
            0%,100%{transform:translateX(0)} 20%{transform:translateX(-6px)} 60%{transform:translateX(6px)}
        }
    </style>

    <div id="pinOverlay">
        <div id="pinCard">
            <div class="pin-icon"><i class="fas fa-fingerprint"></i></div>
            <h2>Restricted Finance Core</h2>
            <p>Enter your 4-digit administrative PIN to unlock the wallet dashboard.</p>

            <div class="pin-dots">
                <div class="pin-dot" id="d0"></div>
                <div class="pin-dot" id="d1"></div>
                <div class="pin-dot" id="d2"></div>
                <div class="pin-dot" id="d3"></div>
            </div>

            <div class="pin-error" id="pinErr"><i class="fas fa-exclamation-triangle"></i> Invalid vault PIN code.</div>

            <div class="pin-keypad">
                <?php foreach([1,2,3,4,5,6,7,8,9] as $n): ?>
                <button class="pin-key" onclick="pinPress(<?= $n ?>)"><?= $n ?></button>
                <?php endforeach; ?>
                <button class="pin-key empty"></button>
                <button class="pin-key" onclick="pinPress(0)">0</button>
                <button class="pin-key del" onclick="pinDel()"><i class="fas fa-delete-left"></i></button>
            </div>
        </div>
    </div>

    <script>
        let pinVal = '';
        function pinPress(n) {
            if (pinVal.length >= 4) return;
            pinVal += n;
            updateDots();
            if (pinVal.length === 4) setTimeout(pinSubmit, 150);
        }
        function pinDel() {
            pinVal = pinVal.slice(0,-1);
            document.getElementById('pinErr').classList.remove('show');
            updateDots();
        }
        function updateDots() {
            for(let i=0;i<4;i++) {
                document.getElementById('d'+i).classList.toggle('filled', i < pinVal.length);
            }
        }
        async function pinSubmit() {
            const fd = new FormData();
            fd.append('action','verify_pin');
            fd.append('pin', pinVal);
            const res  = await fetch('wallet.php', {method:'POST', body:fd});
            const data = await res.json();
            if (data.ok) {
                const overlay = document.getElementById('pinOverlay');
                overlay.style.transition = 'opacity 0.4s ease';
                overlay.style.opacity = '0';
                setTimeout(() => overlay.remove(), 420);
            } else {
                document.getElementById('pinErr').classList.add('show');
                // Reset dots with shake
                pinVal = '';
                setTimeout(updateDots, 400);
            }
        }
        // Allow physical keyboard too
        document.addEventListener('keydown', e => {
            if (e.key >= '0' && e.key <= '9') pinPress(parseInt(e.key));
            if (e.key === 'Backspace') pinDel();
        });
    </script>
    <?php endif; ?>


    <!-- MAHJOUB AI ASSISTANT (Wallet) -->
    <style>
        .ai-fab {
            position: fixed;
            bottom: 25px; right: 25px;
            width: 62px; height: 62px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(217, 119, 6, 0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0;
            border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(217, 119, 6, 0.45); }
        .ai-fab img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .ai-fab-dot {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: #D97706;
            border: 2.5px solid #fff;
            border-radius: 50%;
            animation: fabPulse 1.8s ease-in-out infinite;
        }
        @keyframes fabPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(217,119,6,0.7); }
            50%      { box-shadow: 0 0 0 6px rgba(217,119,6,0); }
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
            background: linear-gradient(135deg, #D97706, #B45309);
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
        .ai-msg.user .ai-bubble { background:#D97706; color:#fff; border-bottom-right-radius:4px; }

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
        .ai-input:focus { border-color:#D97706; background:#fff; box-shadow:0 0 0 3px rgba(217,119,6,0.08); }
        .ai-send {
            width: 40px; height: 40px; border-radius: 50%;
            background:#D97706; color:white;
            border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#B45309; transform:scale(1.05); }

        /* Mobile */
        @media (max-width: 600px) {
            .ai-fab  { right: 16px; bottom: 80px; }
            .ai-popup { right: 0; left: 0; bottom: 0; width: 100%; height: 90dvh; border-radius: 24px 24px 0 0; transform: translateY(100%); }
            .ai-popup.open { transform: translateY(0); }
            .ai-foot { padding-bottom: max(12px, env(safe-area-inset-bottom)); }
        }
    </style>

    <div class="ai-fab" id="aiWalletFab" onclick="toggleWalletAI()">
        <img src="mahjoub.jpg" alt="Mahjoub"
             onerror="this.src='https://ui-avatars.com/api/?name=Mahjoub&background=FEF3C7&color=D97706&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>
    <div class="ai-popup" id="aiWalletPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="mahjoub.jpg" alt="Mahjoub" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);" onerror="this.src='https://ui-avatars.com/api/?name=Mahjoub&background=FEF3C7&color=D97706&bold=true'">
                    <div title="Online 24/24" style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#D97706; border:2px solid #fff; border-radius:50%;"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Mahjoub AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#fff; font-size:8px;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleWalletAI()"></i>
        </div>
        <div class="ai-body" id="aiWalletBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">👋 Hello! I am <b>Mahjoub</b>, your QOON Treasury assistant. I can help you monitor income, manage balances, and analyze financial reports. How can I assist you with your finances today?</div>
            </div>
        </div>
        <div class="ai-typing" id="aiWalletTyping">Mahjoub is calculating financial data...</div>
        <div class="ai-foot">
            <input type="text" id="aiWalletInput" class="ai-input" placeholder="Ask Mahjoub about finances..." onkeypress="if(event.key === 'Enter') sendWalletAIMessage()">
            <button class="ai-send" onclick="sendWalletAIMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        let walletChatHistory = [];
        
        function toggleWalletAI() {
            document.getElementById('aiWalletPopup').classList.toggle('open');
            document.getElementById('aiWalletInput').focus();
        }

        async function sendWalletAIMessage() {
            const input = document.getElementById('aiWalletInput');
            const msg = input.value.trim();
            if(!msg) return;

            addWalletAIMsg('user', msg);
            input.value = '';
            
            const typing = document.getElementById('aiWalletTyping');
            typing.style.display = 'block';
            scrollWalletAIBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        message: msg, 
                        history: walletChatHistory, 
                        page_data: { 
                            type: 'financial_core',
                            total_income: <?= (float)$TotalIncome ?>,
                            total_balance: <?= (float)$TotalBal ?>,
                            user_balance: <?= (float)$userBal ?>,
                            shop_balance: <?= (float)$shopBal ?>,
                            driver_debt: <?= (float)$MustPaidw ?>
                        } 
                    })
                });
                const textOutput = await res.text();
                typing.style.display = 'none';
                
                try {
                    const data = JSON.parse(textOutput);
                    if(data.reply) {
                        addWalletAIMsg('bot', data.reply);
                        walletChatHistory.push({ role: 'user', content: msg });
                        walletChatHistory.push({ role: 'ai', content: data.reply });
                    } else {
                        addWalletAIMsg('bot', 'AI connection issue.');
                    }
                } catch (e) {
                    addWalletAIMsg('bot', 'Error processing AI response.');
                }
            } catch(e) {
                typing.style.display = 'none';
                addWalletAIMsg('bot', 'Connection error.');
            }
        }

        function addWalletAIMsg(sender, text) {
            const body = document.getElementById('aiWalletBody');
            const div = document.createElement('div');
            div.className = `ai-msg ${sender}`;
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            div.innerHTML = `<div class="ai-bubble">${formattedText}</div>`;
            body.appendChild(div);
            scrollWalletAIBottom();
        }

        function scrollWalletAIBottom() {
            const body = document.getElementById('aiWalletBody');
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>
</html>