<?php
require "conn.php";
$AdminID   = $_COOKIE["AdminID"]   ?? '';
$AdminName = $_COOKIE["AdminName"] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | QOON</title>
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
            --shadow-sm:  0 1px 2px rgba(0,0,0,0.05);
            --shadow-md:  0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',-apple-system,sans-serif; -webkit-font-smoothing:antialiased; }
        body { background:var(--bg-master); color:var(--text-base); display:flex; height:100vh; overflow:hidden; }
        .layout-wrapper { display:flex; width:100%; height:100%; }

        main.content-area { flex:1; overflow-y:auto; display:flex; flex-direction:column; }
        main.content-area::-webkit-scrollbar { width:6px; }
        main.content-area::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:10px; }

        /* Header */
        .header-bar {
            position:sticky; top:0; z-index:20;
            background:rgba(255,255,255,0.9); backdrop-filter:blur(16px);
            border-bottom:1px solid var(--border);
            padding:20px 40px;
            display:flex; align-items:center; gap:8px;
        }
        .header-bar i  { font-size:14px; color:var(--text-muted); }
        .header-bar h1 { font-size:18px; font-weight:700; color:var(--text-strong); }
        .header-bar p  { font-size:13px; color:var(--text-muted); font-weight:500; margin-top:3px; }

        /* Page body */
        .page-body {
            padding:40px; max-width:1100px; margin:0 auto; width:100%;
            display:grid; grid-template-columns:240px 1fr; gap:24px; align-items:start;
        }

        /* Left Nav */
        .settings-nav-card {
            background:var(--bg-surface);
            border:1px solid var(--border); border-radius:14px;
            box-shadow:var(--shadow-sm); overflow:hidden;
        }
        .nav-card-head {
            padding:16px 20px; border-bottom:1px solid var(--border);
            font-size:13px; font-weight:700; color:var(--text-strong);
            background:#F9FAFB; display:flex; align-items:center; gap:8px;
        }
        .nav-card-head i { color:var(--text-muted); }
        .nav-list-inner { padding:8px; display:flex; flex-direction:column; gap:2px; }

        .nav-link {
            display:flex; align-items:center; gap:12px;
            padding:11px 14px; border-radius:8px;
            font-size:14px; font-weight:600; color:var(--text-muted);
            text-decoration:none; transition:0.15s;
        }
        .nav-link i { width:16px; text-align:center; font-size:14px; }
        .nav-link:hover { background:#F3F4F6; color:var(--text-strong); }
        .nav-link.active { background:#F3F4F6; color:var(--text-strong); font-weight:700; }
        .nav-link.active i { color:var(--text-strong); }

        /* Form Section */
        .form-section {
            display:flex; flex-direction:column; gap:20px;
        }

        .form-card {
            background:var(--bg-surface);
            border:1px solid var(--border); border-radius:14px;
            box-shadow:var(--shadow-sm); overflow:hidden;
        }
        .form-card-head {
            padding:18px 24px; border-bottom:1px solid var(--border);
            background:#F9FAFB; font-size:14px; font-weight:700; color:var(--text-strong);
            display:flex; align-items:center; gap:8px;
        }
        .form-card-head i { font-size:13px; color:var(--text-muted); }
        .form-card-body { padding:28px 24px; display:flex; flex-direction:column; gap:20px; }

        /* Inputs */
        .inp-group { display:flex; flex-direction:column; gap:6px; }
        .inp-group label { font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .inp-field {
            padding:10px 14px; border:1px solid var(--border); border-radius:8px;
            font-size:14px; font-weight:500; color:var(--text-strong);
            background:var(--bg-surface); outline:none; transition:0.2s; box-shadow:var(--shadow-sm);
            width:100%;
        }
        .inp-field:focus { border-color:var(--border-md); box-shadow:0 0 0 3px rgba(17,24,39,0.06); }
        .inp-hint { font-size:12px; color:var(--text-muted); font-weight:500; margin-top:4px; }

        /* Admin identity badge */
        .identity-badge {
            display:inline-flex; align-items:center; gap:10px;
            padding:12px 16px; border-radius:10px;
            background:#F3F4F6; border:1px solid var(--border);
        }
        .identity-avatar {
            width:36px; height:36px; border-radius:8px;
            background:var(--text-strong); color:#fff;
            display:flex; align-items:center; justify-content:center;
            font-size:14px; font-weight:700;
        }
        .identity-name  { font-size:14px; font-weight:700; color:var(--text-strong); }
        .identity-role  { font-size:12px; font-weight:500; color:var(--text-muted); }

        /* Footer */
        .form-footer {
            padding:20px 24px; border-top:1px solid var(--border);
            background:#F9FAFB; display:flex; justify-content:flex-end; gap:12px;
        }
        .btn-submit {
            display:inline-flex; align-items:center; gap:8px;
            padding:10px 24px; border-radius:8px;
            background:var(--text-strong); color:#fff;
            border:none; font-size:14px; font-weight:600; cursor:pointer;
            transition:0.2s; box-shadow:var(--shadow-sm);
        }
        .btn-submit:hover { background:#1F2937; box-shadow:var(--shadow-md); }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }
            .sb-container { display: none !important; }
            main.content-area { overflow-y: visible; }

            .header-bar { padding: 14px 16px; position: static; }
            .header-bar h1 { font-size: 18px; }

            /* Stack nav above form */
            .page-body {
                padding: 12px 12px 80px;
                grid-template-columns: 1fr;
                gap: 12px;
            }

            /* Settings nav → horizontal scrollable pill bar */
            .settings-nav-card { border-radius: 12px; }
            .nav-card-head { display: none; } /* hide "System Settings" label — no space */
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
                gap: 8px;
            }

            /* Form cards */
            .form-card-body { padding: 18px 16px; gap: 16px; }
            .form-card-head { padding: 14px 16px; font-size: 13px; }
            .form-footer { padding: 14px 16px; }
            .identity-badge { flex-wrap: wrap; }
        }
        @media (max-width: 600px) {
            .nav-link span, .nav-link { font-size: 12px; }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">
            <header class="header-bar">
                <div>
                    <h1>Settings</h1>
                    <p>Configuration &amp; system defaults</p>
                </div>
            </header>

            <div class="page-body">

                <!-- Left: Settings Nav -->
                <div class="settings-nav-card">
                    <div class="nav-card-head"><i class="fas fa-sliders-h"></i> System Settings</div>
                    <div class="nav-list-inner">
                        <a href="settings-profile.php" class="nav-link active">
                            <i class="fas fa-user-shield"></i>
                            <?= $AdminID == 1 ? 'Master Profile' : 'Agent Profile' ?>
                        </a>
                        <?php if($AdminID == 1): ?>
                            <a href="settings-staff-accounts.php" class="nav-link">
                                <i class="fas fa-users-cog"></i> Staff Accounts
                            </a>
                            <a href="settings-delivery-zone.php" class="nav-link">
                                <i class="fas fa-map-marked-alt"></i> Delivery Zones
                            </a>
                            <a href="bakat.php" class="nav-link">
                                <i class="fas fa-box-open"></i> App Packages
                            </a>
                        <?php endif; ?>
                        <a href="settings-ai-agents.php" class="nav-link">
                            <i class="fas fa-robot"></i> AI Agents
                        </a>
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="form-section">

                    <!-- Identity Card -->
                    <div class="form-card">
                        <div class="form-card-head"><i class="fas fa-id-badge"></i> Current Session</div>
                        <div class="form-card-body">
                            <div class="identity-badge">
                                <div class="identity-avatar"><?= strtoupper(substr($AdminName, 0, 1)) ?></div>
                                <div>
                                    <div class="identity-name"><?= htmlspecialchars($AdminName) ?></div>
                                    <div class="identity-role"><?= $AdminID == 1 ? 'Master Administrator' : 'Staff Agent' ?></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Form -->
                    <div class="form-card">
                        <div class="form-card-head"><i class="fas fa-lock"></i> Security &amp; Identity</div>
                        <form action="UpdateAdminAPI.php" method="POST">
                            <div class="form-card-body">
                                <div class="inp-group">
                                    <label>Username</label>
                                    <input type="text" name="AdminName" class="inp-field" value="<?= htmlspecialchars($AdminName) ?>">
                                    <span class="inp-hint">This name appears in admin logs and session records.</span>
                                </div>
                                <div class="inp-group">
                                    <label>New Password</label>
                                    <input type="password" name="AdminPassword" class="inp-field" placeholder="Enter new password..." required>
                                    <span class="inp-hint">Leave blank to keep your current password.</span>
                                </div>
                                <input type="hidden" name="AdminID" value="<?= htmlspecialchars($AdminID) ?>">
                            </div>
                            <div class="form-footer">
                                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Save Changes</button>
                            </div>
                        </form>
                    </div>



                </div>
            </div>
        </main>
    </div>
</body>
</html>