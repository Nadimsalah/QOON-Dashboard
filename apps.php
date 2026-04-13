<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$DistanceValue = 0;
$resDist = mysqli_query($con, "SELECT DistanceValue FROM Distance LIMIT 1");
if($row = mysqli_fetch_assoc($resDist)) { $DistanceValue = $row["DistanceValue"]; }

$percent = 0; $disUser = 0;
$resPerc = mysqli_query($con, "SELECT percent, disUser FROM OrdersJiblerpercentage LIMIT 1");
if($row = mysqli_fetch_assoc($resPerc)) { $percent = $row["percent"]; $disUser = $row["disUser"]; }

$percentDriver = 0;
$resPercD = mysqli_query($con, "SELECT percentage FROM OrdersJiblerpercentageDriver LIMIT 1");
if($row = mysqli_fetch_assoc($resPercD)) { $percentDriver = $row["percentage"]; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Configuration | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="js/jquery-3.2.1.min.js"></script>

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

            --accent: #111827;
            --accent-hover: #1F2937;

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

        /* Sticky Header */
        .header-bar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 24px 40px;
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

        /* Body */
        .page-body {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
        }

        /* Two-col layout */
        .matrix-layout {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 24px;
            height: 100%;
        }

        /* Left Sidebar Nav */
        .tab-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 8px;
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            align-self: start;
        }

        .tab-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-muted);
            transition: 0.15s;
            text-align: left;
            width: 100%;
        }
        .tab-btn .tab-icon {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: #F3F4F6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            border: 1px solid var(--border-subtle);
            transition: 0.15s;
        }
        .tab-btn:hover { color: var(--text-strong); background: #F9FAFB; }
        .tab-btn:hover .tab-icon { background: #E5E7EB; }
        .tab-btn.active { color: var(--text-strong); background: #F3F4F6; }
        .tab-btn.active .tab-icon {
            background: var(--text-strong);
            color: #FFFFFF;
            border-color: var(--text-strong);
        }

        /* Right Content Pane */
        .tab-content-area {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .tab-panel { display: none; padding: 36px; animation: fadeSlide 0.2s ease; }
        .tab-panel.active { display: block; }
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Section Headings */
        .sec-head {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-strong);
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sec-head i { font-size: 14px; color: var(--text-muted); }

        /* Form System */
        .form-block {
            background: #F9FAFB;
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .inp-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .inp-group { display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px; }
        .inp-group label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .inp-field {
            padding: 10px 14px;
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-strong);
            background: var(--bg-surface);
            outline: none;
            transition: 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .inp-field:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px rgba(17,24,39,0.06);
        }
        .btn-update {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--text-strong);
            color: #FFFFFF;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .btn-update:hover { background: var(--accent-hover); box-shadow: var(--shadow-md); }

        /* Async Image Grid */
        .async-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        /* Categories use square tiles */
        #async-categories .thumb-box {
            aspect-ratio: 1 / 1;
        }
        .shimmer-card {
            height: 120px;
            border-radius: 10px;
            background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%);
            background-size: 200% 100%;
            animation: shimAnim 1.5s infinite linear;
        }
        @keyframes shimAnim {
            0%   { background-position: -200% 0; }
            100% { background-position:  200% 0; }
        }

        /* Helper for mt spacing */
        .mt-6 { margin-top: 20px; }

        /* ── Slider / Category Thumb Cards (injected by ajax_apps_data.php) ── */
        .thumb-box {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;   /* Wide banner ratio — matches slide format */
            border-radius: 10px;
            overflow: hidden;
            background: #F3F4F6;
            border: 1px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
            transition: 0.2s;
        }
        .thumb-box:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

        .thumb-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .thumb-box img.img-loaded { opacity: 1; }

        /* "Add" button tile */
        .thumb-box.add-new {
            text-decoration: none;
            flex-direction: column;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            border: 2px dashed var(--border-focus);
            background: #F9FAFB;
            cursor: pointer;
        }
        .thumb-box.add-new i { font-size: 20px; }
        .thumb-box.add-new:hover { border-color: var(--text-strong); color: var(--text-strong); background: #F3F4F6; }

        /* Delete button on each tile */
        .trash-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: rgba(0,0,0,0.55);
            color: #FFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            text-decoration: none;
            z-index: 5;
            transition: 0.15s;
        }
        .trash-btn:hover { background: #DC2626; }

        /* Shimmer on individual card while image loads */
        .thumb-box.shimmer {
            background: linear-gradient(90deg, #F3F4F6 25%, #E5E7EB 50%, #F3F4F6 75%);
            background-size: 200% 100%;
            animation: shimAnim 1.5s infinite linear;
        }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }
            .sb-container { display: none !important; }
            main.content-area { overflow-y: visible; }

            .header-bar { padding: 14px 16px; position: static; }
            .page-title h1 { font-size: 20px; }
            .page-title p { font-size: 13px; }
            .page-body { padding: 12px 12px 80px; }

            /* Stack tab nav on top of content vertically */
            .matrix-layout {
                grid-template-columns: 1fr;
                gap: 12px;
                height: auto;
            }

            /* Left nav → horizontal scrollable pill bar, 3 tabs showing at once */
            .tab-nav {
                flex-direction: row;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scroll-snap-type: x mandatory;
                padding: 6px;
                gap: 6px;
                border-radius: 12px;
                align-self: auto;
                scrollbar-width: none;
            }
            .tab-nav::-webkit-scrollbar { display: none; }

            .tab-btn {
                /* Show exactly 3 tabs + tiny peek of 4th */
                flex: 0 0 calc(33.33% - 8px);
                min-width: calc(33.33% - 8px);
                scroll-snap-align: start;
                flex-direction: column;
                gap: 4px;
                padding: 10px 6px;
                border-radius: 10px;
                white-space: nowrap;
                font-size: 11px;
                align-items: center;
                justify-content: center;
                text-align: center;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            .tab-btn .tab-icon { width: 26px; height: 26px; font-size: 12px; flex-shrink: 0; }

            /* Content panel */
            .tab-panel { padding: 20px; }
            .sec-head { font-size: 14px; margin-bottom: 14px; }

            /* Input row: 2-col → 1-col */
            .inp-row { grid-template-columns: 1fr; gap: 0; }

            /* Async image grid: min tile size smaller */
            .async-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 12px; }
        }
        @media (max-width: 600px) {
            .async-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; }
            .tab-panel { padding: 16px; }
        }

        /* ── MOBILE TAB BAR ENHANCEMENTS ───────────────────────────────── */
        @media (max-width: 991px) {
            /* Active tab: colored filled pill */
            .tab-btn.active {
                background: var(--text-strong);
                color: #FFFFFF;
            }
            .tab-btn.active .tab-icon {
                background: rgba(255,255,255,0.15);
                border-color: rgba(255,255,255,0.2);
                color: #FFFFFF;
            }
            /* Smooth content panel entry */
            .tab-panel.active {
                animation: fadeSlide 0.18s ease;
            }
            /* Tab content area rounded more on mobile */
            .tab-content-area { border-radius: 12px; }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">

            <header class="header-bar">
                <div class="page-title">
                    <h1>App Configuration</h1>
                    <p>Configure global application behavior across all client surfaces.</p>
                </div>
            </header>

            <div class="page-body">
                <div class="matrix-layout">

                    <!-- Left Nav -->
                    <nav class="tab-nav">
                        <button class="tab-btn active" onclick="switchTab('t-jibler', this)">
                            <div class="tab-icon"><i class="fas fa-mobile-alt"></i></div>
                            Client OS
                        </button>
                        <button class="tab-btn" onclick="switchTab('t-partner', this)">
                            <div class="tab-icon"><i class="fas fa-store"></i></div>
                            Partner Hub
                        </button>
                        <button class="tab-btn" onclick="switchTab('t-driver', this)">
                            <div class="tab-icon"><i class="fas fa-motorcycle"></i></div>
                            Courier Hub
                        </button>
                        <button class="tab-btn" onclick="switchTab('t-categories', this)">
                            <div class="tab-icon"><i class="fas fa-th-large"></i></div>
                            Categories
                        </button>
                        <button class="tab-btn" onclick="switchTab('t-perc-sys', this)">
                            <div class="tab-icon"><i class="fas fa-percentage"></i></div>
                            Fee Matrix
                        </button>
                        <button class="tab-btn" onclick="switchTab('t-perc-drv', this)">
                            <div class="tab-icon"><i class="fas fa-coins"></i></div>
                            Royalties
                        </button>
                    </nav>

                    <!-- Right Content -->
                    <div class="tab-content-area">

                        <!-- Client OS -->
                        <div id="t-jibler" class="tab-panel active">
                            <div class="sec-head"><i class="fas fa-image"></i> Layout Controllers</div>
                            <div class="async-grid" id="async-sliders">
                                <div class="shimmer-card"></div>
                                <div class="shimmer-card"></div>
                                <div class="shimmer-card"></div>
                                <div class="shimmer-card"></div>
                            </div>

                            <div class="sec-head" style="margin-top:8px;"><i class="fas fa-compass"></i> Operation Radius</div>
                            <form class="form-block" method="POST" action="updateDistance.php">
                                <div class="inp-group">
                                    <label>Max Shop Distance (KM)</label>
                                    <input type="number" step="0.1" name="dis" class="inp-field" value="<?= $DistanceValue ?>" style="max-width:260px;">
                                </div>
                                <button type="submit" class="btn-update"><i class="fas fa-save"></i> Update Boundary</button>
                            </form>
                        </div>

                        <!-- Partner Hub -->
                        <div id="t-partner" class="tab-panel">
                            <div class="sec-head"><i class="fas fa-store"></i> Partner Advertising</div>
                            <div class="async-grid" id="async-partners">
                                <div class="shimmer-card"></div>
                                <div class="shimmer-card"></div>
                                <div class="shimmer-card"></div>
                            </div>
                        </div>

                        <!-- Courier Hub -->
                        <div id="t-driver" class="tab-panel">
                            <div class="sec-head"><i class="fas fa-route"></i> Courier Allocation</div>
                            <form class="form-block" method="POST" action="updateDistance.php">
                                <div class="inp-group">
                                    <label>Driver Request Allocation Radius (KM)</label>
                                    <input type="number" step="0.1" name="dis" class="inp-field" value="<?= $DistanceValue ?>" style="max-width:260px;">
                                </div>
                                <button type="submit" class="btn-update"><i class="fas fa-save"></i> Enforce Courier Radius</button>
                            </form>
                        </div>

                        <!-- Categories -->
                        <div id="t-categories" class="tab-panel">
                            <div class="sec-head"><i class="fas fa-th-large"></i> App Categories</div>
                            <div class="async-grid" id="async-categories">
                                <div class="shimmer-card"></div>
                                <div class="shimmer-card"></div>
                                <div class="shimmer-card"></div>
                                <div class="shimmer-card"></div>
                            </div>
                        </div>

                        <!-- Fee Matrix -->
                        <div id="t-perc-sys" class="tab-panel">
                            <div class="sec-head"><i class="fas fa-percent"></i> System Fee Matrix</div>
                            <form class="form-block" method="POST" action="updatepercent.php">
                                <div class="inp-row">
                                    <div class="inp-group">
                                        <label>Default Shop Fee %</label>
                                        <input name="dis" class="inp-field" value="<?= $percent ?>">
                                    </div>
                                    <div class="inp-group">
                                        <label>User Convenience Fee %</label>
                                        <input name="disUser" class="inp-field" value="<?= $disUser ?>">
                                    </div>
                                </div>
                                <button type="submit" class="btn-update mt-6"><i class="fas fa-save"></i> Apply Fee Changes</button>
                            </form>
                        </div>

                        <!-- Royalties -->
                        <div id="t-perc-drv" class="tab-panel">
                            <div class="sec-head"><i class="fas fa-coins"></i> Courier Gain Mechanics</div>
                            <form class="form-block" method="POST" action="updatepercentDrivers.php">
                                <div class="inp-group">
                                    <label>Courier Order Revenue Cut %</label>
                                    <input name="dis" class="inp-field" value="<?= $percentDriver ?>" style="max-width:260px;">
                                </div>
                                <button type="submit" class="btn-update mt-6"><i class="fas fa-save"></i> Update Royalty Formula</button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let loaded = {};

        function loadMatrix(id, action) {
            if (loaded[action]) return;
            $.ajax({
                url: 'ajax_apps_data.php?action=' + action,
                success: function(d) {
                    document.getElementById(id).innerHTML = d;
                    loaded[action] = true;
                }
            });
        }

        function switchTab(id, btn) {
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(id).classList.add('active');
            btn.classList.add('active');

            if(id === 't-jibler')      loadMatrix('async-sliders',    'sliders');
            if(id === 't-partner')     loadMatrix('async-partners',   'partners');
            if(id === 't-categories')  loadMatrix('async-categories', 'categories');

            // Mobile UX: scroll active button into center of horizontal tab bar
            if (window.innerWidth <= 991) {
                btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
                // Small delay so the tab renders before scroll
                setTimeout(() => {
                    document.querySelector('.tab-content-area').scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 120);
            }
        }

        window.onload = function() {
            switchTab('t-jibler', document.querySelector('.tab-btn'));
        };
    </script>
</body>
</html>