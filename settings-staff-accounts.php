<?php
require "conn.php";
$AdminID   = $_COOKIE["AdminID"]   ?? '';
$AdminName = $_COOKIE["AdminName"] ?? '';

$res = mysqli_query($con, "SELECT * FROM Admin");
$staff = [];
while($row = mysqli_fetch_assoc($res)) { $staff[] = $row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Accounts | QOON</title>
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
            --red-bg:     #FEF2F2; --red-text: #DC2626;
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
            padding:40px; max-width:1100px; margin:0 auto; width:100%;
            display:grid; grid-template-columns:240px 1fr; gap:24px; align-items:start;
        }

        /* Left Nav (same as settings-profile) */
        .settings-nav-card { background:var(--bg-surface); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden; }
        .nav-card-head { padding:16px 20px; border-bottom:1px solid var(--border); font-size:13px; font-weight:700; color:var(--text-strong); background:#F9FAFB; display:flex; align-items:center; gap:8px; }
        .nav-card-head i { color:var(--text-muted); }
        .nav-list-inner { padding:8px; display:flex; flex-direction:column; gap:2px; }
        .nav-link { display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:8px; font-size:14px; font-weight:600; color:var(--text-muted); text-decoration:none; transition:0.15s; }
        .nav-link i { width:16px; text-align:center; font-size:14px; }
        .nav-link:hover { background:#F3F4F6; color:var(--text-strong); }
        .nav-link.active { background:#F3F4F6; color:var(--text-strong); font-weight:700; }

        /* Right Content */
        .right-col { display:flex; flex-direction:column; gap:20px; }

        /* Panel */
        .panel { background:var(--bg-surface); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden; }
        .panel-head { padding:18px 24px; border-bottom:1px solid var(--border); background:#F9FAFB; display:flex; justify-content:space-between; align-items:center; }
        .panel-head h2 { font-size:15px; font-weight:700; color:var(--text-strong); display:flex; align-items:center; gap:8px; }
        .panel-head h2 i { font-size:13px; color:var(--text-muted); }

        /* Table */
        table { width:100%; border-collapse:collapse; }
        th { background:#F9FAFB; padding:14px 24px; text-align:left; font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border); }
        td { padding:18px 24px; border-bottom:1px solid var(--border); font-size:14px; background:#FFFFFF; vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#F9FAFB; }

        .staff-name { display:flex; align-items:center; gap:12px; }
        .staff-avatar {
            width:34px; height:34px; border-radius:8px;
            background:var(--text-strong); color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-size:13px; font-weight:700; flex-shrink:0;
        }
        .staff-label  { font-size:14px; font-weight:600; color:var(--text-strong); }
        .role-pill { display:inline-flex; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; background:#F3F4F6; color:var(--text-muted); }

        .btn-icon {
            display:inline-flex; align-items:center; justify-content:center;
            width:32px; height:32px; border-radius:7px;
            font-size:13px; text-decoration:none; transition:0.15s; border:1px solid var(--border);
        }
        .btn-icon-del { background:var(--bg-surface); color:var(--red-text); border-color:var(--border); }
        .btn-icon-del:hover { background:var(--red-bg); border-color:#FECACA; }

        /* Add Staff Button */
        .btn-primary {
            display:inline-flex; align-items:center; gap:8px;
            padding:9px 18px; border-radius:8px;
            background:var(--text-strong); color:#fff;
            font-size:13px; font-weight:600; border:none; cursor:pointer;
            transition:0.2s; box-shadow:var(--shadow-sm);
        }
        .btn-primary:hover { background:#1F2937; box-shadow:var(--shadow-md); }

        /* Modal Overlay */
        .modal-overlay {
            display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4);
            z-index:100; align-items:center; justify-content:center;
            backdrop-filter:blur(4px);
        }
        .modal-overlay.open { display:flex; }
        .modal-box {
            background:var(--bg-surface); border-radius:16px; width:100%; max-width:700px;
            max-height:90vh; overflow-y:auto; box-shadow:0 25px 50px rgba(0,0,0,0.15);
            border:1px solid var(--border);
        }
        .modal-box::-webkit-scrollbar { width:6px; }
        .modal-box::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:10px; }
        .modal-head { padding:20px 24px; border-bottom:1px solid var(--border); background:#F9FAFB; display:flex; justify-content:space-between; align-items:center; }
        .modal-head h3 { font-size:16px; font-weight:700; color:var(--text-strong); display:flex; align-items:center; gap:8px; }
        .modal-close { width:30px; height:30px; border-radius:6px; border:1px solid var(--border); background:var(--bg-surface); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:16px; color:var(--text-muted); transition:0.15s; }
        .modal-close:hover { background:#F3F4F6; color:var(--text-strong); }
        .modal-body { padding:28px 24px; display:flex; flex-direction:column; gap:20px; }
        .modal-foot { padding:20px 24px; border-top:1px solid var(--border); background:#F9FAFB; display:flex; justify-content:flex-end; gap:12px; }

        /* Form elements */
        .field-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .field-row-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px; }
        .inp-group { display:flex; flex-direction:column; gap:6px; }
        .inp-group label { font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .inp-field { padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:14px; font-weight:500; color:var(--text-strong); background:var(--bg-surface); outline:none; transition:0.2s; box-shadow:var(--shadow-sm); width:100%; }
        .inp-field:focus { border-color:var(--border-md); box-shadow:0 0 0 3px rgba(17,24,39,0.06); }

        /* Permissions */
        .perm-section-title { font-size:12px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; padding-bottom:10px; border-bottom:1px solid var(--border); margin-bottom:12px; }
        .perm-grid { display:grid; grid-template-columns:1fr 1fr; gap:8px; }
        .perm-row {
            display:flex; align-items:center; justify-content:space-between;
            padding:10px 14px; border-radius:8px; background:#F9FAFB; border:1px solid var(--border);
        }
        .perm-row-label { display:flex; align-items:center; gap:10px; font-size:13px; font-weight:600; color:var(--text-strong); }
        .perm-row-label i { width:14px; text-align:center; font-size:12px; color:var(--text-muted); }
        /* Toggle */
        .toggle { position:relative; width:38px; height:20px; flex-shrink:0; }
        .toggle input { opacity:0; width:0; height:0; }
        .toggle-slider {
            position:absolute; cursor:pointer; inset:0;
            border-radius:20px; background:var(--border-md); transition:0.2s;
        }
        .toggle-slider:before {
            content:''; position:absolute; width:14px; height:14px; border-radius:50%;
            left:3px; top:3px; background:#fff; transition:0.2s;
        }
        .toggle input:checked + .toggle-slider { background:var(--text-strong); }
        .toggle input:checked + .toggle-slider:before { transform:translateX(18px); }

        /* Grand access row */
        .grand-row {
            display:flex; align-items:center; justify-content:space-between;
            padding:14px 16px; border-radius:10px;
            background:#F3F4F6; border:1px solid var(--border);
        }
        .grand-row-label { font-size:13px; font-weight:700; color:var(--text-strong); display:flex; align-items:center; gap:8px; }

        .btn-cancel { display:inline-flex; align-items:center; padding:10px 20px; border-radius:8px; font-size:14px; font-weight:600; border:1px solid var(--border); background:var(--bg-surface); color:var(--text-muted); cursor:pointer; transition:0.2s; box-shadow:var(--shadow-sm); }
        .btn-cancel:hover { background:#F3F4F6; }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }
            .sb-container { display: none !important; }
            main.content-area { overflow-y: visible; }

            .header-bar { padding: 14px 16px; position: static; }
            .header-bar h1 { font-size: 18px; }

            /* Stack nav + content */
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

            /* Table: scroll horizontally */
            table { min-width: 480px; }

            /* Panel head: wrap button */
            .panel-head { flex-wrap: wrap; gap: 10px; padding: 14px 16px; }

            /* Modal: full-screen on mobile */
            .modal-overlay { align-items: flex-end; }
            .modal-box {
                max-width: 100%;
                border-radius: 20px 20px 0 0;
                max-height: 92vh;
            }
            .modal-head { padding: 16px 18px; }
            .modal-body { padding: 16px 18px; gap: 14px; }
            .modal-foot { padding: 14px 18px; }

            /* Form grids → single column in modal */
            .field-row { grid-template-columns: 1fr; gap: 0; }
            .field-row-3 { grid-template-columns: 1fr; gap: 0; }
            .perm-grid { grid-template-columns: 1fr; gap: 6px; }
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
                    <h1>Staff Accounts</h1>
                    <p>Manage administrator access and permissions.</p>
                </div>
            </header>

            <div class="page-body">

                <!-- Left: Settings Nav -->
                <div class="settings-nav-card">
                    <div class="nav-card-head"><i class="fas fa-sliders-h"></i> System Settings</div>
                    <div class="nav-list-inner">
                        <a href="settings-profile.php" class="nav-link">
                            <i class="fas fa-user-shield"></i> Master Profile
                        </a>
                        <?php if($AdminID == 1): ?>
                        <a href="settings-staff-accounts.php" class="nav-link active">
                            <i class="fas fa-users-cog"></i> Staff Accounts
                        </a>
                        <a href="settings-delivery-zone.php" class="nav-link">
                            <i class="fas fa-map-marked-alt"></i> Delivery Zones
                        </a>
                        <a href="bakat.php" class="nav-link">
                            <i class="fas fa-box-open"></i> App Packages
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="right-col">
                    <div class="panel">
                        <div class="panel-head">
                            <h2><i class="fas fa-users"></i> Authorized Staff</h2>
                            <button class="btn-primary" onclick="document.getElementById('staffModal').classList.add('open')">
                                <i class="fas fa-plus"></i> New Staff
                            </button>
                        </div>
                        <div style="overflow-x:auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Staff Member</th>
                                        <th>Role / Function</th>
                                        <th>Password</th>
                                        <th style="text-align:right; padding-right:24px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($staff as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="staff-name">
                                                <div class="staff-avatar"><?= strtoupper(substr($row['AdminName'], 0, 1)) ?></div>
                                                <span class="staff-label"><?= htmlspecialchars($row['AdminName']) ?></span>
                                            </div>
                                        </td>
                                        <td><span class="role-pill"><?= htmlspecialchars($row['Functionn'] ?? '—') ?></span></td>
                                        <td><span style="letter-spacing:3px; color:var(--text-muted); font-weight:700;">••••••••</span></td>
                                        <td style="text-align:right; padding-right:24px;">
                                            <a href="deleteAdminAPI.php?id=<?= $row['AdminID'] ?>" title="Revoke Access"
                                               onclick="return confirm('Revoke access for <?= htmlspecialchars($row['AdminName']) ?>?')"
                                               class="btn-icon btn-icon-del">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($staff)): ?>
                                    <tr><td colspan="4" style="text-align:center; padding:40px; color:var(--text-muted); font-weight:500;">No staff accounts found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Staff Modal -->
    <div class="modal-overlay" id="staffModal">
        <div class="modal-box">
            <div class="modal-head">
                <h3><i class="fas fa-user-plus" style="color:var(--text-muted);"></i> Provision New Staff Account</h3>
                <button class="modal-close" onclick="document.getElementById('staffModal').classList.remove('open')">×</button>
            </div>
            <form action="AddAdminApi.php" method="POST">
                <div class="modal-body">

                    <!-- Basic Info -->
                    <div class="field-row">
                        <div class="inp-group">
                            <label>Full Name</label>
                            <input type="text" name="AdminName" class="inp-field" placeholder="e.g. Sara Alami" required>
                        </div>
                        <div class="inp-group">
                            <label>Email Address</label>
                            <input type="email" name="Email" class="inp-field" placeholder="e.g. sara@qoon.app" required>
                        </div>
                    </div>
                    <div class="field-row-3">
                        <div class="inp-group">
                            <label>Department / Role</label>
                            <input type="text" name="Function" class="inp-field" placeholder="e.g. Support">
                        </div>
                        <div class="inp-group">
                            <label>Phone Number</label>
                            <input type="text" name="Phone" class="inp-field" placeholder="+212...">
                        </div>
                        <div class="inp-group">
                            <label>Password</label>
                            <input type="password" name="AdminPassword" class="inp-field" placeholder="Secure password" required>
                        </div>
                    </div>

                    <!-- Grand Access -->
                    <div class="grand-row">
                        <span class="grand-row-label"><i class="fas fa-shield-alt"></i> Grant Master Rights (All Systems)</span>
                        <label class="toggle">
                            <input type="checkbox" name="all">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <!-- Permissions: Users & Drivers -->
                    <div>
                        <div class="perm-section-title">Users &amp; Drivers</div>
                        <div class="perm-grid">
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-users"></i> Users Page</span><label class="toggle"><input type="checkbox" name="Userspage"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-id-card"></i> User Info</span><label class="toggle"><input type="checkbox" name="UserInformation"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-download"></i> Download Data</span><label class="toggle"><input type="checkbox" name="DownloadUsersData"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-motorcycle"></i> Drivers Page</span><label class="toggle"><input type="checkbox" name="DriversPage"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-plus"></i> Add Driver</span><label class="toggle"><input type="checkbox" name="AddNewDriver"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-address-card"></i> Driver Profile</span><label class="toggle"><input type="checkbox" name="DriverProfile"><span class="toggle-slider"></span></label></div>
                        </div>
                    </div>

                    <!-- Permissions: Stores & Commerce -->
                    <div>
                        <div class="perm-section-title">Stores &amp; Commerce</div>
                        <div class="perm-grid">
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-store"></i> Shops Page</span><label class="toggle"><input type="checkbox" name="ShopsPage"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-store-alt"></i> Add Shop</span><label class="toggle"><input type="checkbox" name="AddNewShop"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-store-alt-slash"></i> Shop Profile</span><label class="toggle"><input type="checkbox" name="ShopProfile"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-box"></i> Orders Page</span><label class="toggle"><input type="checkbox" name="OrdersPage"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-search-dollar"></i> Order Details</span><label class="toggle"><input type="checkbox" name="OrderDetails"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-wallet"></i> Wallet Page</span><label class="toggle"><input type="checkbox" name="WalletPage"><span class="toggle-slider"></span></label></div>
                        </div>
                    </div>

                    <!-- Permissions: Core System -->
                    <div>
                        <div class="perm-section-title">Core System &amp; Settings</div>
                        <div class="perm-grid">
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-sliders-h"></i> Add Slides</span><label class="toggle"><input type="checkbox" name="AddSlides"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-route"></i> Controls</span><label class="toggle"><input type="checkbox" name="ControleDistance"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-tags"></i> Categories</span><label class="toggle"><input type="checkbox" name="Categores"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-bell"></i> Notifications</span><label class="toggle"><input type="checkbox" name="Notification"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-ban"></i> Blacklist</span><label class="toggle"><input type="checkbox" name="blacklistr"><span class="toggle-slider"></span></label></div>
                            <div class="perm-row"><span class="perm-row-label"><i class="fas fa-money-check-alt"></i> Payments</span><label class="toggle"><input type="checkbox" name="Payments"><span class="toggle-slider"></span></label></div>
                        </div>
                    </div>

                </div>
                <div class="modal-foot">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('staffModal').classList.remove('open')">Cancel</button>
                    <button type="submit" class="btn-primary"><i class="fas fa-shield-alt"></i> Provision Staff Account</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Close modal when clicking outside
        document.getElementById('staffModal').addEventListener('click', function(e) {
            if(e.target === this) this.classList.remove('open');
        });
    </script>
</body>
</html>