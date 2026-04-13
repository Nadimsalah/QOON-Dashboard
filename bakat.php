<?php
require "conn.php";
$AdminID   = $_COOKIE["AdminID"]   ?? '';
$AdminName = $_COOKIE["AdminName"] ?? '';

$features = [
    'DigitalStoreCreation'   => ['label' => 'Digital Store Creation',      'icon' => 'fa-store'],
    'FullControlOfStore'     => ['label' => 'Full Control Of Store',        'icon' => 'fa-sliders-h'],
    'AddMoreThanFiveProduct' => ['label' => 'Add > 5 Products',             'icon' => 'fa-boxes'],
    'ReceiveOrder'           => ['label' => 'Receive Orders',               'icon' => 'fa-inbox'],
    'TrackAndManageOrder'    => ['label' => 'Track & Manage Orders',        'icon' => 'fa-map-marker-alt'],
    'DeliveryServiceRequest' => ['label' => 'Delivery Service Request',     'icon' => 'fa-motorcycle'],
    'JiblerPay'              => ['label' => 'QOON Pay Integration',         'icon' => 'fa-credit-card'],
    'JiblerCard'             => ['label' => 'QOON Card Access',             'icon' => 'fa-id-card'],
    'WithdrawProfits'        => ['label' => 'Withdraw Profits',             'icon' => 'fa-wallet'],
    'JiblerBoost'            => ['label' => 'QOON Ad Boost',                'icon' => 'fa-rocket'],
    'BoostNowPayLater'       => ['label' => 'Boost Now Pay Later',          'icon' => 'fa-clock'],
    'OrganicCEO'             => ['label' => 'Organic CEO Dashboard',        'icon' => 'fa-chart-line'],
    '5StoriesPerMonth'       => ['label' => '5 Stories / Month',            'icon' => 'fa-film'],
    '5PublicationMonth'      => ['label' => '5 Posts / Month',              'icon' => 'fa-newspaper'],
    'InteractionWithCustomers' => ['label' => 'Customer Interactions',      'icon' => 'fa-comments'],
    'Hosting'                => ['label' => 'Cloud Hosting Server',         'icon' => 'fa-server'],
];

$packages = [
    1 => ['name' => 'Free Tier',    'icon' => 'fa-seedling'],
    2 => ['name' => 'Premium Pro',  'icon' => 'fa-gem'],
    3 => ['name' => 'Premium Plus', 'icon' => 'fa-crown'],
];

$bakatData = [];
$res = mysqli_query($con, "SELECT * FROM Bakat WHERE BakatID IN (1,2,3)");
while($row = mysqli_fetch_assoc($res)) { $bakatData[$row['BakatID']] = $row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Packages | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-master:  #F3F4F6;
            --bg-surface: #FFFFFF;
            --border:     #E5E7EB;
            --border-md:  #D1D5DB;
            --text-strong:#111827;
            --text-base:  #374151;
            --text-muted: #6B7280;
            --green-bg:   #ECFDF5; --green-text: #059669;
            --red-bg:     #FEF2F2; --red-text:   #DC2626;
            --shadow-sm:  0 1px 2px rgba(0,0,0,0.05);
            --shadow-md:  0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',-apple-system,sans-serif; -webkit-font-smoothing:antialiased; }
        body { background:var(--bg-master); color:var(--text-base); display:flex; height:100vh; overflow:hidden; }
        .layout-wrapper { display:flex; width:100%; height:100%; }

        main.content-area { flex:1; overflow-y:auto; display:flex; flex-direction:column; }
        main.content-area::-webkit-scrollbar { width:6px; }
        main.content-area::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:10px; }

        .header-bar {
            position:sticky; top:0; z-index:20;
            background:rgba(255,255,255,0.9); backdrop-filter:blur(16px);
            border-bottom:1px solid var(--border);
            padding:20px 40px;
        }
        .header-bar h1 { font-size:18px; font-weight:700; color:var(--text-strong); }
        .header-bar p  { font-size:13px; color:var(--text-muted); font-weight:500; margin-top:3px; }

        .page-body {
            padding:40px; max-width:1300px; margin:0 auto; width:100%;
            display:grid; grid-template-columns:240px 1fr; gap:24px; align-items:start;
        }

        /* Left nav — identical across all settings pages */
        .settings-nav-card { background:var(--bg-surface); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden; }
        .nav-card-head { padding:16px 20px; border-bottom:1px solid var(--border); font-size:13px; font-weight:700; color:var(--text-strong); background:#F9FAFB; display:flex; align-items:center; gap:8px; }
        .nav-card-head i { color:var(--text-muted); }
        .nav-list-inner { padding:8px; display:flex; flex-direction:column; gap:2px; }
        .nav-link { display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:8px; font-size:14px; font-weight:600; color:var(--text-muted); text-decoration:none; transition:0.15s; }
        .nav-link i { width:16px; text-align:center; font-size:14px; }
        .nav-link:hover { background:#F3F4F6; color:var(--text-strong); }
        .nav-link.active { background:#F3F4F6; color:var(--text-strong); font-weight:700; }

        /* Package panel */
        .panel { background:var(--bg-surface); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden; }
        .panel-head { padding:18px 24px; border-bottom:1px solid var(--border); background:#F9FAFB; }
        .panel-head h2 { font-size:15px; font-weight:700; color:var(--text-strong); display:flex; align-items:center; gap:8px; }
        .panel-head h2 i { font-size:13px; color:var(--text-muted); }

        /* Comparison table */
        table { width:100%; border-collapse:collapse; }
        th { padding:16px 20px; background:#F9FAFB; text-align:left; font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border); }
        th.pkg-head { text-align:center; min-width:160px; }
        td { padding:14px 20px; border-bottom:1px solid var(--border); background:#FFFFFF; vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#FAFAFA; }

        .feature-row { display:flex; align-items:center; gap:12px; }
        .feature-icon { width:28px; height:28px; border-radius:7px; background:#F3F4F6; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:12px; color:var(--text-muted); flex-shrink:0; }
        .feature-label { font-size:14px; font-weight:600; color:var(--text-strong); }

        .td-center { text-align:center; }

        .status-yes { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; background:var(--green-bg); color:var(--green-text); }
        .status-no  { display:inline-flex; align-items:center; gap:5px; padding:5px 12px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; background:#F3F4F6; color:var(--text-muted); }

        /* Configure button */
        .btn-configure {
            display:inline-flex; align-items:center; gap:7px;
            padding:7px 14px; border-radius:7px;
            border:1px solid var(--border); background:var(--bg-surface);
            font-size:12px; font-weight:600; color:var(--text-strong);
            cursor:pointer; transition:0.15s; box-shadow:var(--shadow-sm);
        }
        .btn-configure:hover { background:#F3F4F6; box-shadow:var(--shadow-md); }

        /* Package column header */
        .pkg-col-head { display:flex; flex-direction:column; align-items:center; gap:8px; }
        .pkg-icon { width:36px; height:36px; border-radius:9px; background:#F3F4F6; border:1px solid var(--border); display:flex; align-items:center; justify-content:center; font-size:14px; color:var(--text-strong); }
        .pkg-name  { font-size:13px; font-weight:700; color:var(--text-strong); }

        /* Modal */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:100; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
        .modal-overlay.open { display:flex; }
        .modal-box { background:var(--bg-surface); border-radius:16px; width:100%; max-width:520px; max-height:85vh; overflow-y:auto; box-shadow:0 25px 50px rgba(0,0,0,0.15); border:1px solid var(--border); }
        .modal-box::-webkit-scrollbar { width:6px; }
        .modal-box::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:10px; }
        .modal-head { padding:20px 24px; border-bottom:1px solid var(--border); background:#F9FAFB; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; z-index:5; }
        .modal-head h3 { font-size:15px; font-weight:700; color:var(--text-strong); display:flex; align-items:center; gap:9px; }
        .modal-close { width:30px; height:30px; border-radius:6px; border:1px solid var(--border); background:var(--bg-surface); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:16px; color:var(--text-muted); transition:0.15s; }
        .modal-close:hover { background:#F3F4F6; color:var(--text-strong); }
        .modal-body { padding:20px 24px; display:flex; flex-direction:column; gap:6px; }
        .modal-foot { padding:16px 24px; border-top:1px solid var(--border); background:#F9FAFB; display:flex; justify-content:flex-end; gap:12px; position:sticky; bottom:0; }

        /* Toggle rows in modal */
        .tog-row { display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-radius:9px; background:#F9FAFB; border:1px solid var(--border); }
        .tog-label { display:flex; align-items:center; gap:10px; font-size:13px; font-weight:600; color:var(--text-strong); }
        .tog-label i { width:14px; text-align:center; font-size:12px; color:var(--text-muted); }
        .toggle { position:relative; width:38px; height:20px; flex-shrink:0; }
        .toggle input { opacity:0; width:0; height:0; }
        .toggle-slider { position:absolute; cursor:pointer; inset:0; border-radius:20px; background:var(--border-md); transition:0.2s; }
        .toggle-slider:before { content:''; position:absolute; width:14px; height:14px; border-radius:50%; left:3px; top:3px; background:#fff; transition:0.2s; }
        .toggle input:checked + .toggle-slider { background:var(--text-strong); }
        .toggle input:checked + .toggle-slider:before { transform:translateX(18px); }

        .btn-primary { display:inline-flex; align-items:center; gap:8px; padding:10px 20px; border-radius:8px; background:var(--text-strong); color:#fff; font-size:14px; font-weight:600; border:none; cursor:pointer; transition:0.2s; box-shadow:var(--shadow-sm); }
        .btn-primary:hover { background:#1F2937; box-shadow:var(--shadow-md); }
        .btn-ghost { display:inline-flex; align-items:center; padding:10px 20px; border-radius:8px; font-size:14px; font-weight:600; border:1px solid var(--border); background:var(--bg-surface); color:var(--text-muted); cursor:pointer; transition:0.2s; box-shadow:var(--shadow-sm); }
        .btn-ghost:hover { background:#F3F4F6; }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }
            .sb-container { display: none !important; }
            main.content-area { overflow-y: visible; }

            .header-bar { padding: 14px 16px; position: static; }
            .header-bar h1 { font-size: 18px; }

            .page-body {
                padding: 12px 12px 80px;
                grid-template-columns: 1fr;
                gap: 12px;
            }

            /* Settings nav → horizontal scrollable pill bar */
            .settings-nav-card { border-radius: 12px; }
            .nav-card-head { display: none; }
            .nav-list-inner {
                flex-direction: row;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scroll-snap-type: x mandatory;
                padding: 6px;
                gap: 4px;
                scrollbar-width: none;
            }
            .nav-list-inner::-webkit-scrollbar { display: none; }
            .nav-link {
                flex: 0 0 auto;
                scroll-snap-align: start;
                white-space: nowrap;
                padding: 10px 16px;
                border-radius: 8px;
                font-size: 13px;
            }

            /* Package matrix table: horizontal scroll */
            table { min-width: 520px; }
            th.pkg-head { min-width: 120px; }

            /* Modal: bottom sheet on mobile */
            .modal-overlay { align-items: flex-end; }
            .modal-box {
                max-width: 100%;
                border-radius: 20px 20px 0 0;
                max-height: 88vh;
            }
            .modal-head { padding: 16px 18px; }
            .modal-body { padding: 14px 16px; }
            .modal-foot { padding: 14px 18px; }
        }
        @media (max-width: 600px) {
            .nav-link { font-size: 12px; padding: 9px 12px; }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">
            <header class="header-bar">
                <div>
                    <h1>App Packages</h1>
                    <p>Configure feature capabilities for each subscription tier.</p>
                </div>
            </header>

            <div class="page-body">

                <!-- Left Nav -->
                <div class="settings-nav-card">
                    <div class="nav-card-head"><i class="fas fa-sliders-h"></i> System Settings</div>
                    <div class="nav-list-inner">
                        <a href="settings-profile.php" class="nav-link">
                            <i class="fas fa-user-shield"></i> Master Profile
                        </a>
                        <?php if($AdminID == 1): ?>
                        <a href="settings-staff-accounts.php" class="nav-link">
                            <i class="fas fa-users-cog"></i> Staff Accounts
                        </a>
                        <a href="settings-delivery-zone.php" class="nav-link">
                            <i class="fas fa-map-marked-alt"></i> Delivery Zones
                        </a>
                        <a href="bakat.php" class="nav-link active">
                            <i class="fas fa-box-open"></i> App Packages
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Packages Table -->
                <div class="panel">
                    <div class="panel-head">
                        <h2><i class="fas fa-cubes"></i> Package Capabilities Matrix</h2>
                    </div>
                    <div style="overflow-x:auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:40%;">Feature</th>
                                    <?php foreach($packages as $pid => $pkg): ?>
                                    <th class="pkg-head">
                                        <div class="pkg-col-head">
                                            <div class="pkg-icon"><i class="fas <?= $pkg['icon'] ?>"></i></div>
                                            <div class="pkg-name"><?= $pkg['name'] ?></div>
                                            <button class="btn-configure" onclick="openModal(<?= $pid ?>)">
                                                <i class="fas fa-sliders-h"></i> Configure
                                            </button>
                                        </div>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($features as $key => $feat): ?>
                                <tr>
                                    <td>
                                        <div class="feature-row">
                                            <div class="feature-icon"><i class="fas <?= $feat['icon'] ?>"></i></div>
                                            <span class="feature-label"><?= htmlspecialchars($feat['label']) ?></span>
                                        </div>
                                    </td>
                                    <?php foreach($packages as $pid => $pkg):
                                        $val = $bakatData[$pid][$key] ?? 'NO';
                                    ?>
                                    <td class="td-center">
                                        <?php if($val === 'YES'): ?>
                                            <span class="status-yes"><i class="fas fa-check"></i> Active</span>
                                        <?php else: ?>
                                            <span class="status-no"><i class="fas fa-minus"></i> Off</span>
                                        <?php endif; ?>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Configure Modals (one per package) -->
    <?php foreach($packages as $pid => $pkg): ?>
    <div class="modal-overlay" id="modal-<?= $pid ?>">
        <div class="modal-box">
            <div class="modal-head">
                <h3><i class="fas <?= $pkg['icon'] ?>" style="color:var(--text-muted);"></i> Configure: <?= $pkg['name'] ?></h3>
                <button class="modal-close" onclick="closeModal(<?= $pid ?>)">×</button>
            </div>
            <form action="UpdateBakaApi.php" method="POST">
                <input type="hidden" name="BakatID" value="<?= $pid ?>">
                <div class="modal-body">
                    <?php foreach($features as $key => $feat):
                        $checked = ($bakatData[$pid][$key] ?? 'NO') === 'YES' ? 'checked' : '';
                    ?>
                    <div class="tog-row">
                        <span class="tog-label">
                            <i class="fas <?= $feat['icon'] ?>"></i>
                            <?= htmlspecialchars($feat['label']) ?>
                        </span>
                        <label class="toggle">
                            <input type="checkbox" name="<?= $key ?>" <?= $checked ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-ghost" onclick="closeModal(<?= $pid ?>)">Cancel</button>
                    <button type="submit" class="btn-primary"><i class="fas fa-cloud-upload-alt"></i> Save Matrix</button>
                </div>
            </form>
        </div>
    </div>
    <?php endforeach; ?>

    <script>
        function openModal(id)  { document.getElementById('modal-' + id).classList.add('open'); }
        function closeModal(id) { document.getElementById('modal-' + id).classList.remove('open'); }
        // Close on backdrop click
        document.querySelectorAll('.modal-overlay').forEach(el => {
            el.addEventListener('click', e => { if(e.target === el) el.classList.remove('open'); });
        });
    </script>
</body>
</html>