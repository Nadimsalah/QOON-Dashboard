<?php
require_once "conn.php";
$startDate     = $_GET['start_date'] ?? '2015-01-01';
$endDate       = $_GET['end_date']   ?? date('Y-m-d');
$cityID        = $_GET['city_id']    ?? '';
$displayPeriod = ($startDate === '2015-01-01') ? 'Ecosystem Overview' : date('d M', strtotime($startDate)) . ' – ' . date('d M', strtotime($endDate));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <style>
        :root {
            --bg-master:   #F3F4F6;
            --bg-surface:  #FFFFFF;
            --border:      #E5E7EB;
            --border-md:   #D1D5DB;
            --text-strong: #111827;
            --text-base:   #374151;
            --text-muted:  #6B7280;

            --green-bg:   #ECFDF5; --green-text:  #059669;
            --blue-bg:    #EFF6FF; --blue-text:   #2563EB;
            --purple-bg:  #F5F3FF; --purple-text: #7C3AED;
            --red-bg:     #FEF2F2; --red-text:    #DC2626;

            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',-apple-system,sans-serif; -webkit-font-smoothing:antialiased; }

        body { background:var(--bg-master); color:var(--text-base); display:flex; height:100vh; overflow:hidden; }
        .layout-wrapper { display:flex; width:100%; height:100%; }

        /* Scrollable main */
        main.content-area { flex:1; overflow-y:auto; display:flex; flex-direction:column; }
        main.content-area::-webkit-scrollbar { width:6px; }
        main.content-area::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:10px; }

        /* Sticky Header */
        .header-bar {
            position:sticky; top:0; z-index:20;
            background:rgba(255,255,255,0.9); backdrop-filter:blur(16px);
            border-bottom:1px solid var(--border);
            padding:20px 40px;
            display:flex; justify-content:space-between; align-items:center;
        }
        .header-left h1  { font-size:22px; font-weight:700; color:var(--text-strong); letter-spacing:-0.5px; }
        .header-left p   { font-size:13px; color:var(--text-muted); font-weight:500; margin-top:3px; }

        .header-right { display:flex; align-items:center; gap:12px; }
        .live-pill {
            display:inline-flex; align-items:center; gap:6px;
            font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.8px;
            padding:6px 14px; border-radius:20px;
            background:var(--green-bg); color:var(--green-text); border:1px solid #BBF7D0;
        }
        .live-dot { width:6px; height:6px; border-radius:50%; background:var(--green-text); animation:pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:0.4} }

        .date-btn {
            display:inline-flex; align-items:center; gap:8px;
            padding:9px 18px; border-radius:8px;
            border:1px solid var(--border); background:var(--bg-surface);
            font-size:13px; font-weight:600; color:var(--text-strong);
            cursor:pointer; box-shadow:var(--shadow-sm); transition:0.2s;
        }
        .date-btn:hover { background:#F9FAFB; box-shadow:var(--shadow-md); }

        /* Body */
        .page-body { padding:40px; display:flex; flex-direction:column; gap:28px; }

        /* KPI Row */

        .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }

        .ai-card {
            background: var(--bg-surface);
            border: 1px solid var(--border);
            border-radius: 18px; padding: 16px 20px;
            display: flex; align-items: center; gap: 16px;
            box-shadow: var(--shadow-sm); transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer; position: relative; overflow: hidden;
        }
        .ai-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: var(--text-strong); }
        .ai-card::after {
            content: 'Click to Consult';
            position: absolute; bottom: 0; left: 0; right: 0;
            background: var(--text-strong); color: #fff;
            font-size: 9px; font-weight: 700; text-transform: uppercase;
            text-align: center; padding: 4px 0;
            transform: translateY(100%); transition: 0.2s;
        }
        .ai-card:hover::after { transform: translateY(0); }

        .ai-card .ai-avatar {
            width: 52px; height: 52px; border-radius: 14px;
            overflow: hidden; border: 2px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); flex-shrink: 0;
        }
        .ai-card .ai-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .ai-card .ai-info { display: flex; flex-direction: column; gap: 2px; }
        .ai-card .ai-label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .ai-card .ai-val   { font-size: 22px; font-weight: 800; color: var(--text-strong); letter-spacing: -0.5px; }
        .ai-card .ai-agent { font-size: 12px; font-weight: 600; color: var(--text-muted); }

        /* Agent Themes */
        .card-mahjoub:hover { border-color: #D97706; }
        .card-adam:hover    { border-color: #7C3AED; }
        .card-tamo:hover    { border-color: #E11D48; }
        .card-ali:hover     { border-color: #2563EB; }

        /* Main grid */
        .main-grid { display:grid; grid-template-columns:1fr 360px; gap:24px; }

        /* White panel */
        .panel {
            background:var(--bg-surface);
            border:1px solid var(--border);
            border-radius:14px; box-shadow:var(--shadow-sm);
            overflow:hidden; display:flex; flex-direction:column;
        }
        .panel-head {
            padding:20px 24px; border-bottom:1px solid var(--border);
            background:#F9FAFB;
            display:flex; justify-content:space-between; align-items:center;
        }
        .panel-head h2 { font-size:15px; font-weight:700; color:var(--text-strong); }
        .panel-head .badge {
            font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;
            padding:4px 10px; border-radius:20px;
            background:var(--green-bg); color:var(--green-text);
        }
        .panel-body { padding:24px; flex:1; }

        /* Revenue card */
        .rev-card {
            background:var(--text-strong);
            border-radius:14px; padding:28px;
            color:#fff; overflow:hidden; position:relative;
            box-shadow:var(--shadow-lg);
        }
        .rev-card .rc-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:rgba(255,255,255,0.45); margin-bottom:12px; }
        .rev-card .rc-val   { font-size:32px; font-weight:700; letter-spacing:-1px; line-height:1; }
        .rev-card .rc-sub   { font-size:12px; font-weight:500; color:rgba(255,255,255,0.4); margin-top:12px; display:flex; align-items:center; justify-content:space-between; }
        .rev-card .rc-icon  { position:absolute; right:-16px; bottom:-16px; font-size:90px; opacity:0.05; }

        /* Debt card */
        .debt-card {
            border-radius:14px; padding:28px;
            border:1px solid var(--border);
            background:var(--bg-surface);
            box-shadow:var(--shadow-sm);
            display:flex; flex-direction:column; gap:12px;
        }
        .debt-card .dc-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:var(--text-muted); }
        .debt-card .dc-val   { font-size:28px; font-weight:700; color:var(--red-text); letter-spacing:-1px; line-height:1; }
        .debt-card .dc-sub   { font-size:12px; font-weight:500; color:var(--text-muted); }

        /* Demographic panel */
        .demo-panel {
            background:var(--bg-surface);
            border:1px solid var(--border); border-radius:14px;
            padding:24px; box-shadow:var(--shadow-sm);
            display:flex; flex-direction:column; gap:20px;
        }
        .demo-panel h3 { font-size:13px; font-weight:700; color:var(--text-strong); }
        .donut-wrap { position:relative; width:160px; height:160px; margin:0 auto; }
        .donut-center {
            position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);
            display:flex; flex-direction:column; align-items:center;
        }
        .donut-center .dc-num  { font-size:22px; font-weight:700; color:var(--text-strong); }
        .donut-center .dc-lbl  { font-size:10px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .demo-stats { display:flex; gap:16px; justify-content:center; }
        .demo-stat  { display:flex; flex-direction:column; align-items:center; gap:4px; }
        .demo-stat .ds-val   { font-size:20px; font-weight:700; color:var(--text-strong); }
        .demo-stat .ds-label { font-size:10px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .demo-stat.new   .ds-val { color:var(--blue-text); }
        .demo-stat.fleet .ds-val { color:var(--red-text); }

        /* Shimmer */
        .shimmer {
            display:inline-block; min-width:80px; min-height:1.1em; border-radius:4px;
            background:linear-gradient(90deg,#F3F4F6 25%,#E5E7EB 50%,#F3F4F6 75%);
            background-size:200% 100%;
            animation:shimAnim 1.5s infinite linear;
        }
        @keyframes shimAnim { 0%{background-position:-200% 0} 100%{background-position:200% 0} }

        .side-stack { display:flex; flex-direction:column; gap:20px; }

        /* ── Mobile Responsive ──────────────────────────────────────────── */

        /* Tablet: ≤ 900px */
        @media (max-width: 900px) {
            /* Collapse sidebar off-screen on tablet */
            body { overflow-y: auto; height: auto; }
            .layout-wrapper { flex-direction: column; }
            .content-area { overflow-y: visible; }

            .header-bar { padding: 14px 20px; flex-wrap: wrap; gap: 10px; }
            .header-left h1 { font-size: 18px; }
            .header-right { gap: 8px; }
            .date-btn { padding: 7px 12px; font-size: 12px; }

            .page-body { padding: 20px; gap: 20px; }

            /* 2×2 KPI grid on tablet */
            .kpi-grid { grid-template-columns: repeat(2, 1fr); }

            /* Main grid: stack chart above side stack */
            .main-grid { grid-template-columns: 1fr; }

            /* Second row: stack charts */
            div[style*="grid-template-columns:1fr 1fr"] {
                display: grid !important;
                grid-template-columns: 1fr !important;
            }

            /* Third row: stack 3 panels */
            div[style*="grid-template-columns:1fr 1.4fr 1fr"] {
                display: grid !important;
                grid-template-columns: 1fr !important;
            }
        }

        /* Phone: ≤ 600px */
        @media (max-width: 600px) {
            .header-bar { padding: 12px 16px; }
            .header-left h1 { font-size: 16px; }
            .live-pill { display: none; } /* hide on very small screens */

            .page-body { padding: 14px; gap: 14px; }

            /* Single column KPI on phone */
            .kpi-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .kpi-card { padding: 16px; }
            .kpi-val { font-size: 24px; }

            /* Charts: shorter height on phone */
            #velocityChart { height: 220px !important; }
            #revenueChart  { height: 180px !important; }
            #signupChart   { height: 180px !important; }
            #citiesChart   { height: 200px !important; }

            .rev-card { padding: 20px; }
            .rev-card .rc-val { font-size: 26px; }
            .debt-card { padding: 20px; }

            .panel-head { padding: 14px 16px; }
            .panel-body { padding: 16px; }
        }

    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">

            <!-- Sticky Header -->
            <header class="header-bar">
                <div class="header-left">
                    <h1>Dashboard</h1>
                    <p><?= $displayPeriod ?></p>
                </div>
                <div class="header-right">
                    <div class="live-pill">
                        <span class="live-dot"></span> Live Engine
                    </div>
                    <button id="dateTrigger" class="date-btn">
                        <i class="fas fa-calendar-alt" style="color:var(--text-muted);"></i>
                        Select Period
                    </button>
                </div>
            </header>

            <div class="page-body">

                <!-- AI AGENT KPIs -->
                <div class="kpi-grid">
                    <!-- Mahjoub: Finance -->
                    <div class="ai-card card-mahjoub" onclick="openGlobalAI('Mahjoub')">
                        <div class="ai-avatar">
                            <img src="mahjoub.jpg" onerror="this.src='https://ui-avatars.com/api/?name=Mahjoub&background=FEF3C7&color=D97706&bold=true'">
                        </div>
                        <div class="ai-info">
                            <span class="ai-label">Settled Revenue</span>
                            <div class="ai-val" id="SalesR"><span class="shimmer"></span></div>
                            <span class="ai-agent">Consult <b>Mahjoub</b></span>
                        </div>
                    </div>

                    <!-- Adam: Users -->
                    <div class="ai-card card-adam" onclick="openGlobalAI('Adam')">
                        <div class="ai-avatar">
                            <img src="adam.jpg" onerror="this.src='https://ui-avatars.com/api/?name=Adam&background=F5F3FF&color=7C3AED&bold=true'">
                        </div>
                        <div class="ai-info">
                            <span class="ai-label">Newly Joined</span>
                            <div class="ai-val" id="UserNumber"><span class="shimmer"></span></div>
                            <span class="ai-agent">Consult <b>Adam</b></span>
                        </div>
                    </div>

                    <!-- Tamo: Drivers -->
                    <div class="ai-card card-tamo" onclick="openGlobalAI('Tamo')">
                        <div class="ai-avatar">
                            <img src="tamo.jpg" onerror="this.src='https://ui-avatars.com/api/?name=Tamo&background=FFF1F2&color=E11D48&bold=true'">
                        </div>
                        <div class="ai-info">
                            <span class="ai-label">Active Fleet</span>
                            <div class="ai-val" id="driverCountSide"><span class="shimmer"></span></div>
                            <span class="ai-agent">Consult <b>Tamo</b></span>
                        </div>
                    </div>

                    <!-- Ali: Orders -->
                    <div class="ai-card card-ali" onclick="openGlobalAI('Ali')">
                        <div class="ai-avatar">
                            <img src="ali.webp" onerror="this.src='https://ui-avatars.com/api/?name=Ali&background=EFF6FF&color=2563EB&bold=true'">
                        </div>
                        <div class="ai-info">
                            <span class="ai-label">Total Volume</span>
                            <div class="ai-val" id="OrdersNumber"><span class="shimmer"></span></div>
                            <span class="ai-agent">Consult <b>Ali</b></span>
                        </div>
                    </div>
                </div>

                <!-- Main Content Grid -->
                <div class="main-grid">

                    <!-- Chart Panel -->
                    <div class="panel">
                        <div class="panel-head">
                            <h2>Growth Velocity</h2>
                            <span class="badge">Calculating Live</span>
                        </div>
                        <div class="panel-body">
                            <canvas id="velocityChart" style="width:100%; height:320px;"></canvas>
                        </div>
                    </div>

                    <!-- Right Side Stack -->
                    <div class="side-stack">

                        <!-- Revenue -->
                        <div class="rev-card">
                            <div class="rc-label">Primary Revenue Floor</div>
                            <div class="rc-val" id="SalesR"><span class="shimmer" style="opacity:0.15; min-width:140px;"></span></div>
                            <div class="rc-sub">
                                <span>Settled MAD</span>
                                <i class="fas fa-arrow-trend-up"></i>
                            </div>
                            <i class="fas fa-vault rc-icon"></i>
                        </div>

                        <!-- Fleet Debt -->
                        <div class="debt-card">
                            <div class="dc-label">Fleet Debt Liability</div>
                            <div class="dc-val" id="DriverDebt"><span class="shimmer"></span></div>
                            <div class="dc-sub">Current unpaid cash from drivers</div>
                        </div>

                        <!-- Demographics -->
                        <div class="demo-panel">
                            <h3>Demographics</h3>
                            <div class="donut-wrap">
                                <canvas id="genderChart"></canvas>
                                <div class="donut-center">
                                    <span class="dc-num" id="ActiveUsers"><span class="shimmer" style="min-width:40px;"></span></span>
                                    <span class="dc-lbl">Active</span>
                                </div>
                            </div>
                            <div class="demo-stats">
                                <div class="demo-stat new">
                                    <span class="ds-val" id="NewUsers"><span class="shimmer" style="min-width:36px;"></span></span>
                                    <span class="ds-label">New</span>
                                </div>
                                <div class="demo-stat fleet">
                                    <span class="ds-val" id="driverCountSide2"><span class="shimmer" style="min-width:36px;"></span></span>
                                    <span class="ds-label">Drivers</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Second Row: Revenue + User Signups -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">

                    <!-- Revenue Trend -->
                    <div class="panel">
                        <div class="panel-head">
                            <h2>Revenue Trend <span style="font-size:12px;font-weight:500;color:var(--text-muted);margin-left:6px;">7 days · MAD</span></h2>
                            <span class="badge" id="totalRevBadge">Live</span>
                        </div>
                        <div class="panel-body">
                            <canvas id="revenueChart" style="width:100%;height:240px;"></canvas>
                        </div>
                    </div>

                    <!-- User Signups Trend -->
                    <div class="panel">
                        <div class="panel-head">
                            <h2>User Signups <span style="font-size:12px;font-weight:500;color:var(--text-muted);margin-left:6px;">7 days</span></h2>
                            <span class="badge" style="background:var(--blue-bg);color:var(--blue-text);border:1px solid #BFDBFE;" id="totalSignupBadge">Live</span>
                        </div>
                        <div class="panel-body">
                            <canvas id="signupChart" style="width:100%;height:240px;"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Third Row: Order Status + Top Cities + Shop Tiers -->
                <div style="display:grid; grid-template-columns:1fr 1.4fr 1fr; gap:24px;">

                    <!-- Order Status Donut -->
                    <div class="panel">
                        <div class="panel-head"><h2>Order Status</h2></div>
                        <div class="panel-body" style="display:flex;flex-direction:column;gap:16px;">
                            <div style="position:relative;width:160px;height:160px;margin:0 auto;">
                                <canvas id="statusChart"></canvas>
                                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                                    <div id="statusTotal" style="font-size:20px;font-weight:700;color:var(--text-strong);" class="shimmer" style="min-width:50px;"></div>
                                    <div style="font-size:10px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Total</div>
                                </div>
                            </div>
                            <div id="statusLegend" style="display:flex;flex-direction:column;gap:7px;"></div>
                        </div>
                    </div>

                    <!-- Top Cities Bar -->
                    <div class="panel">
                        <div class="panel-head"><h2>Top Cities by Orders</h2></div>
                        <div class="panel-body">
                            <canvas id="citiesChart" style="width:100%;height:260px;"></canvas>
                        </div>
                    </div>

                    <!-- Shop Tier Donut -->
                    <div class="panel">
                        <div class="panel-head"><h2>Shop Tiers</h2></div>
                        <div class="panel-body" style="display:flex;flex-direction:column;align-items:center;gap:20px;">
                            <div style="position:relative;width:160px;height:160px;">
                                <canvas id="tierChart"></canvas>
                                <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);text-align:center;">
                                    <div id="tierTotal" style="font-size:20px;font-weight:700;color:var(--text-strong);"></div>
                                    <div style="font-size:10px;font-weight:600;color:var(--text-muted);text-transform:uppercase;">Shops</div>
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:8px;width:100%;">
                                <div class="tier-row"><div style="display:flex;align-items:center;gap:8px;"><span style="width:10px;height:10px;border-radius:50%;background:#111827;flex-shrink:0;"></span><span style="font-size:13px;font-weight:600;color:var(--text-strong);">Free Tier</span></div><span id="tier1" style="font-size:13px;font-weight:700;color:var(--text-muted);">—</span></div>
                                <div class="tier-row"><div style="display:flex;align-items:center;gap:8px;"><span style="width:10px;height:10px;border-radius:50%;background:#7C3AED;flex-shrink:0;"></span><span style="font-size:13px;font-weight:600;color:var(--text-strong);">Premium Pro</span></div><span id="tier2" style="font-size:13px;font-weight:700;color:var(--text-muted);">—</span></div>
                                <div class="tier-row"><div style="display:flex;align-items:center;gap:8px;"><span style="width:10px;height:10px;border-radius:50%;background:#2563EB;flex-shrink:0;"></span><span style="font-size:13px;font-weight:600;color:var(--text-strong);">Premium Plus</span></div><span id="tier3" style="font-size:13px;font-weight:700;color:var(--text-muted);">—</span></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <style>
        .tier-row { display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid var(--border); }
        .tier-row:last-child { border-bottom:none; }
    </style>

    <script>
        const CHART_DEFAULTS = {
            font: { family: 'Inter', size: 12, weight: '600' },
            color: '#9CA3AF'
        };
        const TOOLTIP = {
            backgroundColor: '#111827',
            titleFont: { size: 12, weight: '700', family: 'Inter' },
            bodyFont: { size: 13, weight: '700', family: 'Inter' },
            padding: 10, cornerRadius: 8, displayColors: false
        };


        let DASHBOARD_DATA = {};

        async function loadDashboard() {
            try {
                const res = await fetch(`ajax_dashboard_data.php?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>`);
                const d   = await res.json();
                DASHBOARD_DATA = d;

                // KPIs
                document.getElementById('UserNumber').innerText    = d.UserNumber;
                document.getElementById('ShopsNumber').innerText   = d.ShopsNumber;
                document.getElementById('OrdersNumber').innerText  = d.OrdersNumber;
                document.getElementById('SalesR').innerText        = d.SalesR + ' MAD';
                document.getElementById('DriverDebt').innerText    = d.DriverDebt + ' MAD';
                document.getElementById('ActiveUsers').innerText   = d.ActiveUsers;
                document.getElementById('NewUsers').innerText      = d.NewUsers;
                document.getElementById('driverCountSide').innerText  = d.DriverNumber;
                document.getElementById('driverCountSide2').innerText = d.DriverNumber;

                const total7dRev = d.chartRevenue.reduce((a,b)=>a+b,0);
                document.getElementById('totalRevBadge').textContent = total7dRev.toFixed(0)+' MAD';
                const total7dSig = d.chartUsers.reduce((a,b)=>a+b,0);
                document.getElementById('totalSignupBadge').textContent = '+'+total7dSig+' users';

                // ── 1. Growth Velocity (Orders/day)
                new Chart(document.getElementById('velocityChart'), {
                    type: 'line',
                    data: {
                        labels: d.chartLabels,
                        datasets: [{
                            data: d.chartOrders,
                            borderColor: '#111827', borderWidth: 2.5,
                            pointRadius: 4, pointBackgroundColor: '#111827', pointHoverRadius: 6,
                            tension: 0.45, fill: true, backgroundColor: 'rgba(17,24,39,0.04)'
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend:{ display:false }, tooltip:{ ...TOOLTIP, callbacks:{ label: c => c.parsed.y+' orders' }}},
                        scales: {
                            x: { grid:{ display:false }, ticks:{ ...CHART_DEFAULTS }},
                            y: { grid:{ color:'#F3F4F6' }, beginAtZero:true, ticks:{ ...CHART_DEFAULTS, precision:0 }}
                        }
                    }
                });

                // ── 2. Revenue Trend (MAD/day)
                new Chart(document.getElementById('revenueChart'), {
                    type: 'line',
                    data: {
                        labels: d.chartLabels,
                        datasets: [{
                            data: d.chartRevenue,
                            borderColor: '#059669', borderWidth: 2.5,
                            pointRadius: 4, pointBackgroundColor: '#059669', pointHoverRadius: 6,
                            tension: 0.45, fill: true, backgroundColor: 'rgba(5,150,105,0.06)'
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend:{ display:false }, tooltip:{ ...TOOLTIP, backgroundColor:'#059669', callbacks:{ label: c => c.parsed.y.toFixed(2)+' MAD' }}},
                        scales: {
                            x: { grid:{ display:false }, ticks:{ ...CHART_DEFAULTS }},
                            y: { grid:{ color:'#F3F4F6' }, beginAtZero:true, ticks:{ ...CHART_DEFAULTS, precision:0 }}
                        }
                    }
                });

                // ── 3. User Signups (bar)
                new Chart(document.getElementById('signupChart'), {
                    type: 'bar',
                    data: {
                        labels: d.chartLabels,
                        datasets: [{
                            data: d.chartUsers,
                            backgroundColor: d.chartUsers.map((_,i) => i === d.chartUsers.length-1 ? '#2563EB' : '#BFDBFE'),
                            borderRadius: 6, borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend:{ display:false }, tooltip:{ ...TOOLTIP, backgroundColor:'#2563EB', callbacks:{ label: c => c.parsed.y+' signups' }}},
                        scales: {
                            x: { grid:{ display:false }, ticks:{ ...CHART_DEFAULTS }},
                            y: { grid:{ color:'#F3F4F6' }, beginAtZero:true, ticks:{ ...CHART_DEFAULTS, precision:0 }}
                        }
                    }
                });

                // ── 4. Order Status Doughnut
                const statusColors = ['#111827','#2563EB','#059669','#DC2626','#7C3AED','#D97706'];
                const statusTotal  = d.statusCounts.reduce((a,b)=>a+b,0);
                document.getElementById('statusTotal').textContent = statusTotal.toLocaleString();
                document.getElementById('statusTotal').classList.remove('shimmer');
                new Chart(document.getElementById('statusChart'), {
                    type: 'doughnut',
                    data: {
                        labels: d.statusLabels,
                        datasets: [{ data: d.statusCounts, backgroundColor: statusColors.slice(0, d.statusCounts.length), borderWidth: 0, cutout: '75%' }]
                    },
                    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false }, tooltip:{ ...TOOLTIP, callbacks:{ label: c => c.label+': '+c.parsed.toLocaleString() }}}}
                });
                const legend = document.getElementById('statusLegend');
                d.statusLabels.forEach((lbl, i) => {
                    legend.innerHTML += `<div style="display:flex;justify-content:space-between;align-items:center;font-size:12px;">
                        <div style="display:flex;align-items:center;gap:7px;"><span style="width:8px;height:8px;border-radius:50%;background:${statusColors[i]};flex-shrink:0;"></span><span style="font-weight:600;color:var(--text-strong);">${lbl}</span></div>
                        <span style="font-weight:700;color:var(--text-muted);">${d.statusCounts[i].toLocaleString()}</span>
                    </div>`;
                });

                // ── 5. Top Cities Horizontal Bar
                new Chart(document.getElementById('citiesChart'), {
                    type: 'bar',
                    data: {
                        labels: d.cityLabels,
                        datasets: [{
                            data: d.cityOrders,
                            backgroundColor: ['#111827','#374151','#6B7280','#9CA3AF','#D1D5DB'],
                            borderRadius: 6, borderSkipped: false
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true, maintainAspectRatio: false,
                        plugins: { legend:{ display:false }, tooltip:{ ...TOOLTIP, callbacks:{ label: c => c.parsed.x.toLocaleString()+' orders' }}},
                        scales: {
                            x: { grid:{ color:'#F3F4F6' }, beginAtZero:true, ticks:{ ...CHART_DEFAULTS, precision:0 }},
                            y: { grid:{ display:false }, ticks:{ ...CHART_DEFAULTS, color:'#374151' }}
                        }
                    }
                });

                // ── 6. Shop Tier Doughnut
                const tierTotal = d.tierCounts.reduce((a,b)=>a+b,0);
                document.getElementById('tierTotal').textContent = tierTotal.toLocaleString();
                document.getElementById('tier1').textContent = d.tierCounts[0].toLocaleString();
                document.getElementById('tier2').textContent = d.tierCounts[1].toLocaleString();
                document.getElementById('tier3').textContent = d.tierCounts[2].toLocaleString();
                new Chart(document.getElementById('tierChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Free Tier','Premium Pro','Premium Plus'],
                        datasets: [{ data: d.tierCounts, backgroundColor: ['#111827','#7C3AED','#2563EB'], borderWidth: 0, cutout: '75%' }]
                    },
                    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false }, tooltip:{ ...TOOLTIP, callbacks:{ label: c => c.label+': '+c.parsed.toLocaleString() }}}}
                });

                // Demographics Doughnut
                new Chart(document.getElementById('genderChart'), {
                    type: 'doughnut',
                    data: {
                        datasets: [{
                            data: [
                                parseInt(d.ActiveUsers.toString().replace(/,/g,'')) || 1,
                                parseInt(d.NewUsers.toString().replace(/,/g,''))    || 1
                            ],
                            backgroundColor: ['#111827','#E5E7EB'], borderWidth:0, cutout:'80%'
                        }]
                    },
                    options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ display:false }}}
                });

            } catch(e) { console.error('Dashboard load failed:', e); }
        }

        window.onload = loadDashboard;

        flatpickr('#dateTrigger', {
            mode: 'range', dateFormat: 'Y-m-d',
            onClose: (s, str, i) => {
                if (s.length === 2)
                    location.href = `index.php?start_date=${i.formatDate(s[0],'Y-m-d')}&end_date=${i.formatDate(s[1],'Y-m-d')}`;
            }
        });
    </script>

    <!-- UNIFIED AI CHAT MODAL -->
    <style>
        .g-ai-popup {
            position: fixed;
            bottom: 30px; right: 30px;
            width: 400px; height: 600px;
            background: #fff; border-radius: 24px;
            box-shadow: 0 24px 70px rgba(0,0,0,0.22);
            display: flex; flex-direction: column;
            z-index: 10000;
            transform: translateY(30px) scale(0.95);
            opacity: 0; pointer-events: none;
            transition: all 0.38s cubic-bezier(0.19, 1, 0.22, 1);
            border: 1px solid rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .g-ai-popup.open { transform: translateY(0) scale(1); opacity: 1; pointer-events: all; }

        .g-ai-head {
            padding: 18px 20px; color: #fff;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0; transition: background 0.3s;
        }
        .g-ai-agent-pic { width: 44px; height: 44px; border-radius: 12px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3); }
        .g-ai-meta { flex: 1; margin-left: 12px; }
        .g-ai-meta b { display: block; font-size: 15px; }
        .g-ai-meta span { font-size: 11px; opacity: 0.8; }

        .g-ai-body { flex: 1; padding: 18px; overflow-y: auto; background: #F8F9FC; display: flex; flex-direction: column; gap: 12px; }
        .g-msg { display: flex; max-width: 85%; line-height: 1.5; font-size: 13.5px; }
        .g-msg.bot { align-self: flex-start; }
        .g-msg.user { align-self: flex-end; }
        .g-bubble { padding: 12px 16px; border-radius: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.04); }
        .g-msg.bot .g-bubble { background: #fff; color: #111827; border-bottom-left-radius: 4px; border: 1px solid #E5E7EB; }
        .g-msg.user .g-bubble { background: var(--ai-theme, #111827); color: #fff; border-bottom-right-radius: 4px; }

        .g-ai-foot { padding: 14px; background: #fff; border-top: 1px solid #F0F0F0; display: flex; gap: 10px; }
        .g-ai-input { flex: 1; border: 1.5px solid #E5E7EB; border-radius: 25px; padding: 10px 18px; font-size: 14px; outline: none; }
        .g-ai-input:focus { border-color: var(--ai-theme); }
        .g-ai-send { width: 42px; height: 42px; border-radius: 50%; background: var(--ai-theme); color: #fff; border: none; cursor: pointer; display:flex; align-items:center; justify-content:center; }

        @media (max-width: 600px) {
            .g-ai-popup { width: 100%; height: 100%; bottom: 0; right: 0; border-radius: 0; }
        }
    </style>

    <div class="g-ai-popup" id="globalAIPopup">
        <div class="g-ai-head" id="globalAIHead">
            <div style="display:flex; align-items:center;">
                <img src="" id="globalAIPic" class="g-ai-agent-pic">
                <div class="g-ai-meta">
                    <b id="globalAIName">Agent Name</b>
                    <span id="globalAITitle">Specialist</span>
                </div>
            </div>
            <i class="fas fa-times" style="cursor:pointer;opacity:0.7;" onclick="closeGlobalAI()"></i>
        </div>
        <div class="g-ai-body" id="globalAIBody"></div>
        <div id="globalAITyping" style="padding:0 20px 10px; font-size:12px; color:#9CA3AF; display:none; background:#F8F9FC;">Thinking...</div>
        <div class="g-ai-foot">
            <input type="text" id="globalAIInput" class="g-ai-input" placeholder="Ask me anything...">
            <button class="g-ai-send" id="globalAISendBtn" onclick="sendGlobalAIMsg()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        const AGENTS = {
            'Mahjoub': { title: 'Finance & Treasury', color: '#D97706', img: 'mahjoub.jpg', prompt: 'I am Mahjoub, your financial assistant. I handle revenue, income, and debts.', context: 'financial_analyst' },
            'Adam':    { title: 'User Management', color: '#7C3AED', img: 'adam.jpg', prompt: 'I am Adam, your users specialist. I can help you with member growth and behavior.', context: 'user_manager' },
            'Tamo':    { title: 'Fleet Logistics', color: '#E11D48', img: 'tamo.jpg', prompt: 'I am Tamo, your driver manager. I oversee the fleet performance.', context: 'driver_manager' },
            'Ali':     { title: 'Operations Lead', color: '#2563EB', img: 'ali.webp', prompt: 'I am Ali, your order operations expert. I monitor order flow and delivery states.', context: 'order_manager' }
        };

        let currentAgentKey = null;
        let globalChatHistory = {};



        function openGlobalAI(key) {
            const a = AGENTS[key];
            currentAgentKey = key;
            
            document.getElementById('globalAIName').textContent = key;
            document.getElementById('globalAITitle').textContent = a.title;
            document.getElementById('globalAIPic').src = a.img;
            document.getElementById('globalAIHead').style.backgroundColor = a.color;
            document.documentElement.style.setProperty('--ai-theme', a.color);
            
            const popup = document.getElementById('globalAIPopup');
            popup.classList.add('open');
            
            const body = document.getElementById('globalAIBody');
            if (!globalChatHistory[key]) {
                globalChatHistory[key] = [];
                let liveIntro = "";
                let agentHeader = `
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px; padding-bottom:8px; border-bottom:1px solid rgba(0,0,0,0.05);">
                        <img src="${a.img}" style="width:32px; height:32px; border-radius:8px; object-fit:cover; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                        <span style="font-weight:800; font-size:13px; color:${a.color}; text-transform:uppercase; letter-spacing:0.5px;">${key} Intelligence</span>
                    </div>
                `;
                
                if (key === 'Adam') {
                    liveIntro = agentHeader + `Total Registered Users: <b>${DASHBOARD_DATA.UserNumber || 0}</b>.<br><br>This is the current live count of all registered user accounts on the QOON platform. How can I help you with user analytics?`;
                } else if (key === 'Mahjoub') {
                    liveIntro = agentHeader + `Today's Settled Revenue: <b>${DASHBOARD_DATA.SalesR || 0} MAD</b>.<br><br>This reflects the total volume of successful transactions within the selected period. How can I assist with financial tracking?`;
                } else if (key === 'Tamo') {
                    liveIntro = agentHeader + `Active Fleet: <b>${DASHBOARD_DATA.DriverNumber || 0}</b> drivers online.<br><br>This is the real-time count of couriers ready for allocation. Need a logistics report?`;
                } else if (key === 'Ali') {
                    liveIntro = agentHeader + `System Orders: <b>${DASHBOARD_DATA.OrdersNumber || 0}</b> processed.<br><br>This accounts for all orders across the ecosystem. How can I help monitor operations?`;
                } else {
                    liveIntro = agentHeader + `👋 Hello! I am <b>${key}</b>, your ${a.title}. ${a.prompt} How can I help you today?`;
                }

                body.innerHTML = `<div class="g-msg bot"><div class="g-bubble">${liveIntro}</div></div>`;
            } else {
                renderGlobalHistory(key);
            }
            
            document.getElementById('globalAIInput').focus();
        }

        function closeGlobalAI() { document.getElementById('globalAIPopup').classList.remove('open'); }

        async function sendGlobalAIMsg() {
            const input = document.getElementById('globalAIInput');
            const msg = input.value.trim();
            if (!msg) return;

            addMsgToBody('user', msg);
            input.value = '';
            
            const typing = document.getElementById('globalAITyping');
            typing.style.display = 'block';
            scrollGlobalBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        message: msg,
                        history: globalChatHistory[currentAgentKey],
                        page_data: { 
                            type: 'ecosystem_dashboard',
                            agent: currentAgentKey,
                            context: AGENTS[currentAgentKey].context
                        }
                    })
                });
                const data = await res.json();
                typing.style.display = 'none';
                if (data.reply) {
                    addMsgToBody('bot', data.reply);
                    globalChatHistory[currentAgentKey].push({ role: 'user', content: msg });
                    globalChatHistory[currentAgentKey].push({ role: 'ai', content: data.reply });
                }
            } catch(e) { typing.style.display = 'none'; }
        }

        function addMsgToBody(sender, text) {
            const body = document.getElementById('globalAIBody');
            const div = document.createElement('div');
            div.className = `g-msg ${sender}`;
            let fmt = text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>').replace(/\n/g, '<br>');
            div.innerHTML = `<div class="g-bubble">${fmt}</div>`;
            body.appendChild(div);
            scrollGlobalBottom();
        }

        function renderGlobalHistory(key) {
            const body = document.getElementById('globalAIBody');
            body.innerHTML = `<div class="g-msg bot"><div class="g-bubble">👋 Welcome back! I am <b>${key}</b>. How can we continue?</div></div>`;
            globalChatHistory[key].forEach(m => {
                addMsgToBody(m.role === 'ai' ? 'bot' : 'user', m.content);
            });
        }

        function scrollGlobalBottom() {
            const b = document.getElementById('globalAIBody');
            b.scrollTop = b.scrollHeight;
        }

        document.getElementById('globalAIInput').onkeypress = (e) => { if(e.key==='Enter') sendGlobalAIMsg(); };
    </script>
</body>
</html>