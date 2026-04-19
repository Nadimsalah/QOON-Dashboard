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
                        <a href="settings-profile.php" class="nav-link">
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
                        <a href="settings-ai-agents.php" class="nav-link active">
                            <i class="fas fa-robot"></i> AI Agents
                        </a>
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="form-section">



                    <!-- AI Agents Grid -->
                    <div id="ai-agents" style="margin-top:32px;">
                        <h3 style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:16px; display:flex; align-items:center; gap:8px;"><i class="fas fa-microchip" style="color:var(--text-muted);"></i> Active System AI Agents</h3>
                        
                        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px;">
                            
                            <!-- 1. QOON OS (Analytics) -->
                            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px; transition:0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <img src="qoon-intelligence.png" style="width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                                    <span style="font-size:11px; font-weight:700; color:#059669; background:#D1FAE5; padding:4px 10px; border-radius:20px; display:flex; align-items:center; gap:4px;"><i class="fas fa-circle" style="font-size:6px;"></i> Online</span>
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:4px;">QOON Intelligence</div>
                                    <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">Master AI Assistant. Analytics & full ecosystem database context.</div>
                                </div>
                                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--text-base); background:#F3F4F6; padding:4px 8px; border-radius:6px;">Core System</span>
                                    <i class="fas fa-arrow-right" style="color:var(--border-md); font-size:12px;"></i>
                                </div>
                            </div>

                            <!-- 2. Adam (Users) -->
                            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px; transition:0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <img src="adam.png" style="width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                                    <span style="font-size:11px; font-weight:700; color:#059669; background:#D1FAE5; padding:4px 10px; border-radius:20px; display:flex; align-items:center; gap:4px;"><i class="fas fa-circle" style="font-size:6px;"></i> Online</span>
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:4px;">Adam</div>
                                    <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">QOON Users Agent. Specializes in CRM, behavior, and user analytics.</div>
                                </div>
                                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--text-base); background:#F3F4F6; padding:4px 8px; border-radius:6px;">Users Module</span>
                                    <i class="fas fa-arrow-right" style="color:var(--border-md); font-size:12px;"></i>
                                </div>
                            </div>

                            <!-- 3. Chemsy (Express) -->
                            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px; transition:0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <img src="chemsy.webp" style="width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                                    <span style="font-size:11px; font-weight:700; color:#059669; background:#D1FAE5; padding:4px 10px; border-radius:20px; display:flex; align-items:center; gap:4px;"><i class="fas fa-circle" style="font-size:6px;"></i> Online</span>
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:4px;">Chemsy</div>
                                    <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">QOON Express Agent. Focuses on fleets, couriers, and delivery flow.</div>
                                </div>
                                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--text-base); background:#F3F4F6; padding:4px 8px; border-radius:6px;">Express Module</span>
                                    <i class="fas fa-arrow-right" style="color:var(--border-md); font-size:12px;"></i>
                                </div>
                            </div>

                            <!-- 4. Tamo (Seller) -->
                            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px; transition:0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <img src="tamo.jpg" style="width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                                    <span style="font-size:11px; font-weight:700; color:#059669; background:#D1FAE5; padding:4px 10px; border-radius:20px; display:flex; align-items:center; gap:4px;"><i class="fas fa-circle" style="font-size:6px;"></i> Online</span>
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:4px;">Tamo</div>
                                    <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">QOON Seller Agent. Tracks vendors, live inventory, and performance.</div>
                                </div>
                                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--text-base); background:#F3F4F6; padding:4px 8px; border-radius:6px;">Seller Module</span>
                                    <i class="fas fa-arrow-right" style="color:var(--border-md); font-size:12px;"></i>
                                </div>
                            </div>

                            <!-- 5. Ali (QOON Pro) -->
                            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px; transition:0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <img src="ali.webp" style="width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                                    <span style="font-size:11px; font-weight:700; color:#059669; background:#D1FAE5; padding:4px 10px; border-radius:20px; display:flex; align-items:center; gap:4px;"><i class="fas fa-circle" style="font-size:6px;"></i> Online</span>
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:4px;">Ali</div>
                                    <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">QOON Pro Agent. Orchestrates B2B operations and bulk supplies.</div>
                                </div>
                                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--text-base); background:#F3F4F6; padding:4px 8px; border-radius:6px;">Pro Module</span>
                                    <i class="fas fa-arrow-right" style="color:var(--border-md); font-size:12px;"></i>
                                </div>
                            </div>

                            <!-- 6. Mahjobe (Orders) -->
                            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px; transition:0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <img src="mahjoub.jpg" style="width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                                    <span style="font-size:11px; font-weight:700; color:#059669; background:#D1FAE5; padding:4px 10px; border-radius:20px; display:flex; align-items:center; gap:4px;"><i class="fas fa-circle" style="font-size:6px;"></i> Online</span>
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:4px;">Mahjobe</div>
                                    <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">Orders Agent. Tracks live transactions, routing, and checkout flows.</div>
                                </div>
                                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--text-base); background:#F3F4F6; padding:4px 8px; border-radius:6px;">Orders Module</span>
                                    <i class="fas fa-arrow-right" style="color:var(--border-md); font-size:12px;"></i>
                                </div>
                            </div>

                            <!-- 7. Fairoz (Notification) -->
                            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px; transition:0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <img src="fairoz.avif" style="width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                                    <span style="font-size:11px; font-weight:700; color:#059669; background:#D1FAE5; padding:4px 10px; border-radius:20px; display:flex; align-items:center; gap:4px;"><i class="fas fa-circle" style="font-size:6px;"></i> Online</span>
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:4px;">Fairoz</div>
                                    <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">Notification Agent. Master of alerts, pushes, and broadcast campaigns.</div>
                                </div>
                                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--text-base); background:#F3F4F6; padding:4px 8px; border-radius:6px;">Notif Module</span>
                                    <i class="fas fa-arrow-right" style="color:var(--border-md); font-size:12px;"></i>
                                </div>
                            </div>

                            <!-- 8. Amine (Financial Core) -->
                            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px; transition:0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <img src="amine.avif" style="width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                                    <span style="font-size:11px; font-weight:700; color:#059669; background:#D1FAE5; padding:4px 10px; border-radius:20px; display:flex; align-items:center; gap:4px;"><i class="fas fa-circle" style="font-size:6px;"></i> Online</span>
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:4px;">Amine</div>
                                    <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">Financial Core Agent. Reconciles revenue, debt, and deep ledgers.</div>
                                </div>
                                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--text-base); background:#F3F4F6; padding:4px 8px; border-radius:6px;">Finance Module</span>
                                    <i class="fas fa-arrow-right" style="color:var(--border-md); font-size:12px;"></i>
                                </div>
                            </div>

                            <!-- 9. Warda (Integration) -->
                            <div style="background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; padding:20px; box-shadow:var(--shadow-sm); display:flex; flex-direction:column; gap:16px; transition:0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='var(--shadow-sm)';">
                                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                    <img src="warda.jpg" style="width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                                    <span style="font-size:11px; font-weight:700; color:#059669; background:#D1FAE5; padding:4px 10px; border-radius:20px; display:flex; align-items:center; gap:4px;"><i class="fas fa-circle" style="font-size:6px;"></i> Online</span>
                                </div>
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:var(--text-strong); margin-bottom:4px;">Warda</div>
                                    <div style="font-size:13px; color:var(--text-muted); line-height:1.4;">Integration Agent. Specializes in Webhooks, ERP connections, & plugins.</div>
                                </div>
                                <div style="margin-top:auto; padding-top:16px; border-top:1px solid var(--border); display:flex; justify-content:space-between; align-items:center;">
                                    <span style="font-size:12px; font-weight:600; color:var(--text-base); background:#F3F4F6; padding:4px 8px; border-radius:6px;">Integration</span>
                                    <i class="fas fa-arrow-right" style="color:var(--border-md); font-size:12px;"></i>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>