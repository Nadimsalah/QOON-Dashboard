<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$tab = $_GET['tab'] ?? 'users';

// Audience counts
$totalUsers   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Users"))['c'] ?? 0;
$noOrderUsers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Users WHERE UserOrdersNum = 0"))['c'] ?? 0;
$activeUsers  = $totalUsers - $noOrderUsers;

$totalShops   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Shops"))['c'] ?? 0;
$totalDrivers = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Drivers"))['c'] ?? 0;

// History — last 30
$historyQ = mysqli_query($con, "
    SELECT N.*, A.AdminName
    FROM NotificationsSentByAdmin N
    JOIN Admin A ON N.AdminID = A.AdminID
    ORDER BY N.CreatedAtNotificationsSentByAdmin DESC
    LIMIT 30
");
$history = [];
while ($row = mysqli_fetch_assoc($historyQ)) $history[] = $row;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Push Notifications | QOON</title>
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
            --green-bg:   #ECFDF5; --green-text:  #059669;
            --blue-bg:    #EFF6FF; --blue-text:   #2563EB;
            --purple-bg:  #F5F3FF; --purple-text: #7C3AED;
            --red-bg:     #FEF2F2; --red-text:    #DC2626;
            --amber-bg:   #FFFBEB; --amber-text:  #D97706;
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
            background:rgba(255,255,255,0.92); backdrop-filter:blur(16px);
            border-bottom:1px solid var(--border);
            padding:18px 40px;
            display:flex; justify-content:space-between; align-items:center;
        }
        .header-bar h1 { font-size:20px; font-weight:700; color:var(--text-strong); letter-spacing:-0.3px; }
        .header-bar p  { font-size:13px; color:var(--text-muted); font-weight:500; margin-top:2px; }

        /* Audience tabs */
        .aud-tabs { display:flex; gap:6px; background:var(--bg-master); padding:5px; border-radius:10px; }
        .aud-tab {
            display:flex; align-items:center; gap:7px;
            padding:8px 18px; border-radius:7px;
            font-size:13px; font-weight:600; color:var(--text-muted);
            cursor:pointer; border:none; background:none;
            font-family:'Inter',sans-serif; text-decoration:none;
            transition:0.15s; white-space:nowrap;
        }
        .aud-tab:hover { background:rgba(255,255,255,0.7); color:var(--text-strong); }
        .aud-tab.active { background:var(--bg-surface); color:var(--text-strong); box-shadow:var(--shadow-sm); }

        /* Page body */
        .page-body { padding:32px 40px; display:grid; grid-template-columns:1fr 380px; gap:28px; align-items:start; }

        /* Panel */
        .panel { background:var(--bg-surface); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden; }
        .panel-head { padding:18px 24px; border-bottom:1px solid var(--border); background:#F9FAFB; display:flex; justify-content:space-between; align-items:center; }
        .panel-head h2 { font-size:15px; font-weight:700; color:var(--text-strong); }
        .panel-body { padding:24px; }

        /* Audience stat cards */
        .stat-row { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:24px; }
        .stat-card { padding:16px; border-radius:10px; border:1px solid var(--border); display:flex; flex-direction:column; gap:4px; cursor:pointer; transition:0.15s; }
        .stat-card:hover { box-shadow:var(--shadow-md); }
        .stat-card.selected { border-width:2px; }
        .stat-card .sc-val { font-size:24px; font-weight:700; letter-spacing:-0.5px; }
        .stat-card .sc-lbl { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.4px; }

        /* Form */
        .inp-group { display:flex; flex-direction:column; gap:6px; margin-bottom:18px; }
        .inp-group label { font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.4px; }
        .inp-field, .inp-textarea, .inp-select {
            padding:11px 14px; border:1px solid var(--border); border-radius:8px;
            font-size:14px; font-weight:500; color:var(--text-strong);
            background:var(--bg-surface); outline:none; transition:0.2s;
            box-shadow:var(--shadow-sm); width:100%; font-family:'Inter',sans-serif;
        }
        .inp-field:focus, .inp-textarea:focus, .inp-select:focus {
            border-color:var(--border-md); box-shadow:0 0 0 3px rgba(17,24,39,0.06);
        }
        .inp-textarea { min-height:110px; resize:vertical; }
        .inp-select { cursor:pointer; }

        /* Range slider */
        .range-section { background:#F9FAFB; border:1px solid var(--border); border-radius:10px; padding:16px; margin-bottom:18px; display:none; }
        .range-section.visible { display:block; }
        .range-label { font-size:12px; font-weight:600; color:var(--text-muted); margin-bottom:12px; text-transform:uppercase; letter-spacing:0.4px; }
        .range-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .range-val-row { display:flex; justify-content:space-between; margin-top:8px; }
        .range-val-row span { font-size:12px; font-weight:700; color:var(--text-strong); }
        input[type=range] { width:100%; accent-color:var(--text-strong); height:5px; }

        /* Char counter */
        .char-row { display:flex; justify-content:space-between; align-items:center; }
        #charCount { font-size:11px; font-weight:600; color:var(--text-muted); }
        #charCount.warn { color:var(--amber-text); }

        /* Preview phone card */
        .phone-preview {
            background:#F9FAFB; border:1px solid var(--border); border-radius:12px;
            padding:20px; margin-bottom:18px;
        }
        .pp-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:var(--text-muted); margin-bottom:12px; }
        .pp-notif {
            background:#fff; border-radius:12px; padding:14px; border:1px solid var(--border);
            box-shadow:var(--shadow-sm); display:flex; align-items:flex-start; gap:12px;
        }
        .pp-icon { width:36px; height:36px; border-radius:9px; background:var(--text-strong); display:flex; align-items:center; justify-content:center; font-size:16px; color:#fff; flex-shrink:0; }
        .pp-title { font-size:13px; font-weight:700; color:var(--text-strong); }
        .pp-body  { font-size:12px; font-weight:500; color:var(--text-muted); margin-top:2px; }
        .pp-time  { font-size:11px; font-weight:600; color:#9CA3AF; margin-top:4px; }

        /* Submit button */
        .btn-send {
            display:flex; align-items:center; justify-content:center; gap:10px;
            width:100%; padding:13px 24px; border-radius:9px;
            background:var(--text-strong); color:#fff;
            font-size:14px; font-weight:700; border:none; cursor:pointer;
            font-family:'Inter',sans-serif; transition:0.2s; box-shadow:var(--shadow-sm);
        }
        .btn-send:hover { background:#1F2937; box-shadow:var(--shadow-md); transform:translateY(-1px); }
        .btn-send i { font-size:15px; }

        /* History table */
        .hist-table { width:100%; border-collapse:collapse; }
        .hist-table th { padding:12px 16px; text-align:left; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; border-bottom:2px solid var(--border); }
        .hist-table td { padding:14px 16px; font-size:13px; font-weight:500; color:var(--text-base); border-bottom:1px solid var(--border); vertical-align:middle; }
        .hist-table tr:last-child td { border-bottom:none; }
        .hist-table tr:hover td { background:#FAFAFA; }
        .tid { font-size:11px; font-weight:700; background:var(--bg-master); padding:3px 8px; border-radius:5px; color:var(--text-strong); }
        .htitle { font-weight:700; color:var(--text-strong); }
        .hbody  { max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:var(--text-muted); }
        .type-pill { font-size:10px; font-weight:700; text-transform:uppercase; padding:3px 9px; border-radius:20px; }
        .type-ALL    { background:var(--green-bg);  color:var(--green-text); }
        .type-NO_ORDERS { background:var(--amber-bg); color:var(--amber-text); }
        .type-HAS_ORDERS { background:var(--blue-bg); color:var(--blue-text); }
        .type-SINGLE { background:var(--purple-bg); color:var(--purple-text); }
        .hdate { font-size:12px; color:var(--text-muted); white-space:nowrap; }
        .author-chip { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:600; }
        .author-av { width:22px; height:22px; border-radius:6px; background:var(--text-strong); color:#fff; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; }
        .btn-del { background:none; border:none; color:var(--text-muted); cursor:pointer; padding:4px; }
        .btn-del:hover { color:var(--red-text); }

        /* Empty state */
        .empty-row td { text-align:center; padding:48px; color:var(--text-muted); }
        .empty-row i { font-size:32px; color:var(--border); display:block; margin-bottom:8px; }

        /* Confirm overlay */
        .confirm-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:300; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
        .confirm-overlay.open { display:flex; }
        .confirm-box { background:var(--bg-surface); border-radius:16px; padding:28px; max-width:420px; width:calc(100% - 40px); box-shadow:0 25px 50px rgba(0,0,0,0.15); border:1px solid var(--border); text-align:center; }
        .confirm-box h3 { font-size:18px; font-weight:700; color:var(--text-strong); margin-bottom:8px; }
        .confirm-box p  { font-size:14px; color:var(--text-muted); font-weight:500; margin-bottom:24px; }
        .confirm-actions { display:flex; gap:10px; }
        .btn-cancel { flex:1; padding:11px; border-radius:8px; border:1px solid var(--border); background:var(--bg-surface); font-size:14px; font-weight:600; color:var(--text-muted); cursor:pointer; font-family:'Inter',sans-serif; transition:0.15s; }
        .btn-cancel:hover { background:#F3F4F6; }
        .btn-confirm-send { flex:1; padding:11px; border-radius:8px; border:none; background:var(--text-strong); color:#fff; font-size:14px; font-weight:700; cursor:pointer; font-family:'Inter',sans-serif; transition:0.15s; }
        .btn-confirm-send:hover { background:#1F2937; }

        @media (max-width:1100px) { .page-body { grid-template-columns:1fr; } }
        @media (max-width:700px)  { .stat-row { grid-template-columns:1fr 1fr; } .header-bar { padding:16px 20px; } .page-body { padding:20px; } }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }
            .sb-container { display: none !important; }
            main.content-area { overflow-y: visible; }

            .header-bar { flex-wrap: wrap; gap: 12px; padding: 14px 16px; }
            .aud-tabs { flex-wrap: wrap; }

            .page-body { padding: 16px 16px 80px; gap: 16px; }
            .panel-body { padding: 16px; }
        }
        @media (max-width: 600px) {
            .stat-row { grid-template-columns: 1fr; }
            .stat-card .sc-val { font-size: 20px; }
            .range-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="layout-wrapper">
    <?php include 'sidebar.php'; ?>

    <main class="content-area">

        <!-- Header -->
        <header class="header-bar">
            <div>
                <h1>Push Notifications</h1>
                <p>Broadcast messages to your users, shops and drivers.</p>
            </div>
            <!-- Audience tabs -->
            <div class="aud-tabs">
                <a href="notifications.php?tab=users"   class="aud-tab <?= $tab=='users'   ? 'active':'' ?>"><i class="fas fa-user-group"></i>Users</a>
                <a href="notifications.php?tab=shops"   class="aud-tab <?= $tab=='shops'   ? 'active':'' ?>"><i class="fas fa-store"></i>Shops</a>
                <a href="notifications.php?tab=drivers" class="aud-tab <?= $tab=='drivers' ? 'active':'' ?>"><i class="fas fa-motorcycle"></i>Drivers</a>
            </div>
        </header>

        <div class="page-body">

            <!-- LEFT: History Log -->
            <div class="panel">
                <div class="panel-head">
                    <h2><i class="fas fa-history" style="margin-right:8px;font-size:13px;color:var(--text-muted);"></i>Broadcast History</h2>
                    <span style="font-size:11px;font-weight:700;color:var(--green-text);background:var(--green-bg);padding:4px 10px;border-radius:20px;"><?= count($history) ?> records</span>
                </div>
                <div style="overflow-x:auto;">
                    <table class="hist-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Message</th>
                                <th>Audience</th>
                                <th>Sent</th>
                                <th>By</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($history)): ?>
                            <tr class="empty-row"><td colspan="7"><i class="fas fa-bell-slash"></i>No notifications sent yet.</td></tr>
                            <?php else: foreach ($history as $h): ?>
                            <tr id="nrow-<?= $h['NotificationsSentByAdminID'] ?>">
                                <td><span class="tid">#<?= $h['NotificationsSentByAdminID'] ?></span></td>
                                <td class="htitle"><?= htmlspecialchars($h['Title']) ?></td>
                                <td><div class="hbody" title="<?= htmlspecialchars($h['Bodyy']) ?>"><?= htmlspecialchars($h['Bodyy']) ?></div></td>
                                <td>
                                    <?php
                                    $typeClass = 'type-' . ($h['Type'] ?? 'ALL');
                                    $typeLabel = match($h['Type'] ?? '') {
                                        'ALL'        => 'All Users',
                                        'NO_ORDERS'  => 'No Orders',
                                        'HAS_ORDERS' => 'Active Buyers',
                                        default      => $h['Type'] ?? '—'
                                    };
                                    ?>
                                    <span class="type-pill <?= $typeClass ?>"><?= $typeLabel ?></span>
                                </td>
                                <td class="hdate"><i class="far fa-clock" style="margin-right:4px;"></i><?= date('M d, H:i', strtotime($h['CreatedAtNotificationsSentByAdmin'])) ?></td>
                                <td>
                                    <div class="author-chip">
                                        <div class="author-av"><?= strtoupper(substr($h['AdminName'], 0, 1)) ?></div>
                                        <?= htmlspecialchars($h['AdminName']) ?>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn-del" title="Delete" onclick="deleteNotif(<?= $h['NotificationsSentByAdminID'] ?>, this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- RIGHT: Composer -->
            <div style="display:flex; flex-direction:column; gap:16px;">

                <!-- Audience overview -->
                <div class="panel">
                    <div class="panel-head">
                        <h2><i class="fas fa-bullseye" style="margin-right:8px;font-size:13px;color:var(--text-muted);"></i>Audience Overview</h2>
                    </div>
                    <div class="panel-body">
                        <?php if ($tab === 'users'): ?>
                        <div class="stat-row">
                            <div class="stat-card" onclick="setAudience('ALL')" id="sc-all" style="border-color:#111827;background:#F9FAFB;">
                                <div class="sc-val" style="color:#111827;"><?= number_format($totalUsers) ?></div>
                                <div class="sc-lbl" style="color:var(--text-muted);">All Users</div>
                            </div>
                            <div class="stat-card" onclick="setAudience('HAS_ORDERS')" id="sc-has" style="border-color:var(--border);">
                                <div class="sc-val" style="color:var(--blue-text);"><?= number_format($activeUsers) ?></div>
                                <div class="sc-lbl" style="color:var(--text-muted);">Active Buyers</div>
                            </div>
                            <div class="stat-card" onclick="setAudience('NO_ORDERS')" id="sc-no" style="border-color:var(--border);">
                                <div class="sc-val" style="color:var(--amber-text);"><?= number_format($noOrderUsers) ?></div>
                                <div class="sc-lbl" style="color:var(--text-muted);">No Orders</div>
                            </div>
                        </div>
                        <?php elseif ($tab === 'shops'): ?>
                        <div class="stat-row" style="grid-template-columns:1fr;">
                            <div class="stat-card selected" style="border-color:#111827;background:#F9FAFB;">
                                <div class="sc-val" style="color:#111827;"><?= number_format($totalShops) ?></div>
                                <div class="sc-lbl" style="color:var(--text-muted);">All Shops</div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="stat-row" style="grid-template-columns:1fr;">
                            <div class="stat-card selected" style="border-color:#111827;background:#F9FAFB;">
                                <div class="sc-val" style="color:#111827;"><?= number_format($totalDrivers) ?></div>
                                <div class="sc-lbl" style="color:var(--text-muted);">All Drivers</div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Composer card -->
                <div class="panel">
                    <div class="panel-head">
                        <h2><i class="fas fa-paper-plane" style="margin-right:8px;font-size:13px;color:var(--text-muted);"></i>Compose Notification</h2>
                    </div>
                    <div class="panel-body">

                        <!-- Live preview -->
                        <div class="phone-preview">
                            <div class="pp-label">Live Preview</div>
                            <div class="pp-notif">
                                <div class="pp-icon"><i class="fas fa-bell"></i></div>
                                <div>
                                    <div class="pp-title" id="pp-title">Notification Title</div>
                                    <div class="pp-body"  id="pp-body">Your message will appear here...</div>
                                    <div class="pp-time">just now · QOON</div>
                                </div>
                            </div>
                        </div>

                        <?php if ($tab === 'users'): ?>
                        <!-- USERS form -->
                        <form id="notifForm" onsubmit="confirmSend(event)" data-action="SendNotfToallUsers.php">
                            <input type="hidden" name="UserTypes" id="hiddenUserTypes" value="ALL">

                            <div class="inp-group">
                                <label>Target Audience</label>
                                <select class="inp-select" id="userTypeSel" onchange="handleTypeChange(this.value)">
                                    <option value="ALL">All Users (<?= number_format($totalUsers) ?>)</option>
                                    <option value="HAS_ORDERS">Active Buyers — Order range</option>
                                    <option value="NO_ORDERS">No Orders (<?= number_format($noOrderUsers) ?>)</option>
                                </select>
                            </div>

                            <div class="range-section" id="rangeSection">
                                <div class="range-label"><i class="fas fa-filter" style="margin-right:6px;"></i>Order count range</div>
                                <div class="range-row">
                                    <div class="inp-group" style="margin-bottom:0;">
                                        <label>Min Orders</label>
                                        <input class="inp-field" type="number" name="rangeMin" id="rangeMin" min="1" value="1">
                                    </div>
                                    <div class="inp-group" style="margin-bottom:0;">
                                        <label>Max Orders</label>
                                        <input class="inp-field" type="number" name="rangeMax" id="rangeMax" min="1" value="10">
                                    </div>
                                </div>
                            </div>

                            <div class="inp-group">
                                <label>Title</label>
                                <input type="text" class="inp-field" name="PostTitle" id="inpTitle" placeholder="e.g. Special offer today 🎉" maxlength="80" oninput="updatePreview()">
                            </div>
                            <div class="inp-group">
                                <div class="char-row">
                                    <label>Message Body</label>
                                    <span id="charCount">0 / 200</span>
                                </div>
                                <textarea class="inp-textarea" name="Message" id="inpBody" placeholder="Write your message here..." maxlength="200" required oninput="updatePreview(); countChars()"></textarea>
                            </div>
                            <button type="submit" class="btn-send"><i class="fas fa-rocket"></i> Send to Users</button>
                        </form>

                        <?php elseif ($tab === 'shops'): ?>
                        <!-- SHOPS form -->
                        <form id="notifForm" onsubmit="confirmSend(event)" data-action="SendNotfToallUsers.php">
                            <input type="hidden" name="UserTypes" value="SHOPS">
                            <input type="hidden" name="rangeMin" value="0">
                            <input type="hidden" name="rangeMax" value="9999">
                            <div class="inp-group">
                                <label>Title</label>
                                <input type="text" class="inp-field" name="PostTitle" id="inpTitle" placeholder="e.g. New feature available 🚀" maxlength="80" oninput="updatePreview()">
                            </div>
                            <div class="inp-group">
                                <div class="char-row">
                                    <label>Message Body</label>
                                    <span id="charCount">0 / 200</span>
                                </div>
                                <textarea class="inp-textarea" name="Message" id="inpBody" placeholder="Write your message here..." maxlength="200" required oninput="updatePreview(); countChars()"></textarea>
                            </div>
                            <button type="submit" class="btn-send"><i class="fas fa-store"></i> Send to Shops</button>
                        </form>

                        <?php else: ?>
                        <!-- DRIVERS form -->
                        <form id="notifForm" onsubmit="confirmSend(event)" data-action="SendNotfToallUsers.php">
                            <input type="hidden" name="UserTypes" value="DRIVERS">
                            <input type="hidden" name="rangeMin" value="0">
                            <input type="hidden" name="rangeMax" value="9999">
                            <div class="inp-group">
                                <label>Title</label>
                                <input type="text" class="inp-field" name="PostTitle" id="inpTitle" placeholder="e.g. New zone available 📍" maxlength="80" oninput="updatePreview()">
                            </div>
                            <div class="inp-group">
                                <div class="char-row">
                                    <label>Message Body</label>
                                    <span id="charCount">0 / 200</span>
                                </div>
                                <textarea class="inp-textarea" name="Message" id="inpBody" placeholder="Write your message here..." maxlength="200" required oninput="updatePreview(); countChars()"></textarea>
                            </div>
                            <button type="submit" class="btn-send"><i class="fas fa-motorcycle"></i> Send to Drivers</button>
                        </form>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Confirm Modal -->
<div class="confirm-overlay" id="confirmOverlay">
    <div class="confirm-box">
        <div style="width:52px;height:52px;border-radius:14px;background:#FEF2F2;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:22px;color:#EF4444;">
            <i class="fas fa-paper-plane"></i>
        </div>
        <h3>Send Notification?</h3>
        <p id="confirmDesc">This will send a push notification to the selected audience.</p>
        <div class="confirm-actions">
            <button class="btn-cancel" onclick="closeConfirm()">Cancel</button>
            <button class="btn-confirm-send" onclick="doSend()">Yes, Send Now</button>
        </div>
    </div>
</div>

<script>
    let pendingForm = null;

    // Live phone preview
    function updatePreview() {
        const title = document.getElementById('inpTitle')?.value || 'Notification Title';
        const body  = document.getElementById('inpBody')?.value  || 'Your message will appear here...';
        document.getElementById('pp-title').textContent = title;
        document.getElementById('pp-body').textContent  = body;
    }

    // Char counter
    function countChars() {
        const v = document.getElementById('inpBody')?.value?.length || 0;
        const el = document.getElementById('charCount');
        el.textContent = v + ' / 200';
        el.classList.toggle('warn', v > 150);
    }

    // Audience type toggle (users tab)
    function handleTypeChange(val) {
        const rs = document.getElementById('rangeSection');
        if (rs) rs.classList.toggle('visible', val === 'HAS_ORDERS');
        const hid = document.getElementById('hiddenUserTypes');
        if (hid) hid.value = val;
        // Highlight stat card
        ['sc-all','sc-has','sc-no'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            el.style.borderColor = 'var(--border)'; el.style.background = '';
        });
        const map = { ALL:'sc-all', HAS_ORDERS:'sc-has', NO_ORDERS:'sc-no' };
        const active = document.getElementById(map[val]);
        if (active) { active.style.borderColor = '#111827'; active.style.background = '#F9FAFB'; }
    }

    function setAudience(type) {
        const sel = document.getElementById('userTypeSel');
        if (sel) { sel.value = type; handleTypeChange(type); }
    }

    // Confirm flow
    function confirmSend(e) {
        e.preventDefault();
        pendingForm = e.target;
        const title  = document.getElementById('inpTitle')?.value || '(no title)';
        const selEl  = document.getElementById('userTypeSel');
        const target = selEl ? selEl.options[selEl.selectedIndex]?.text : 'selected audience';
        document.getElementById('confirmDesc').textContent =
            `"${title}" will be sent to: ${target}. This cannot be undone.`;
        document.getElementById('confirmOverlay').classList.add('open');
    }
    function closeConfirm() {
        document.getElementById('confirmOverlay').classList.remove('open');
        pendingForm = null;
    }
    function doSend() {
        if (!pendingForm) return;
        pendingForm.submit();
    }
    document.getElementById('confirmOverlay').addEventListener('click', function(e) {
        if (e.target === this) closeConfirm();
    });

    // Delete notification row
    async function deleteNotif(id, btn) {
        if (!confirm('Delete this notification from history?')) return;
        const row = document.getElementById('nrow-' + id);
        row.classList.add('deleting');
        try {
            const fd = new FormData();
            fd.append('id', id);
            const res  = await fetch('ajax_delete_notification.php', { method:'POST', body: fd });
            const data = await res.json();
            if (data.ok) {
                setTimeout(() => row.remove(), 300);
            } else {
                row.classList.remove('deleting');
                alert('Could not delete.');
            }
        } catch(e) {
            row.classList.remove('deleting');
            alert('Network error.');
        }
    }
</script>

    <!-- WARDA AI ASSISTANT (Notifications) -->
    <style>
        .ai-fab {
            position: fixed;
            bottom: 25px; right: 25px;
            width: 62px; height: 62px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(225, 29, 72, 0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0;
            border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(225, 29, 72, 0.45); }
        .ai-fab img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .ai-fab-dot {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: #E11D48;
            border: 2.5px solid #fff;
            border-radius: 50%;
            animation: fabPulse 1.8s ease-in-out infinite;
        }
        @keyframes fabPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(225,29,72,0.7); }
            50%      { box-shadow: 0 0 0 6px rgba(225,29,72,0); }
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
            background: linear-gradient(135deg, #E11D48, #BE185D);
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
        .ai-msg.user .ai-bubble { background:#E11D48; color:#fff; border-bottom-right-radius:4px; }

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
        .ai-input:focus { border-color:#E11D48; background:#fff; box-shadow:0 0 0 3px rgba(225,29,72,0.08); }
        .ai-send {
            width: 40px; height: 40px; border-radius: 50%;
            background:#E11D48; color:white;
            border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#BE185D; transform:scale(1.05); }

        /* Mobile */
        @media (max-width: 600px) {
            .ai-fab  { right: 16px; bottom: 80px; }
            .ai-popup { right: 0; left: 0; bottom: 0; width: 100%; height: 90dvh; border-radius: 24px 24px 0 0; transform: translateY(100%); }
            .ai-popup.open { transform: translateY(0); }
            .ai-foot { padding-bottom: max(12px, env(safe-area-inset-bottom)); }
        }
    </style>

    <div class="ai-fab" id="aiNotifFab" onclick="toggleNotifAI()">
        <img src="warda.jpg" alt="Warda"
             onerror="this.src='https://ui-avatars.com/api/?name=Warda&background=FFE4E6&color=E11D48&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>
    <div class="ai-popup" id="aiNotifPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="warda.jpg" alt="Warda" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);" onerror="this.src='https://ui-avatars.com/api/?name=Warda&background=FFE4E6&color=E11D48&bold=true'">
                    <div title="Online 24/24" style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#E11D48; border:2px solid #fff; border-radius:50%;"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Warda AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#fff; font-size:8px;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleNotifAI()"></i>
        </div>
        <div class="ai-body" id="aiNotifBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">👋 Hello! I am <b>Warda</b>, your QOON Notifications assistant. I can help you target the right audience and craft the perfect broadcast message. How can I assist you today?</div>
            </div>
        </div>
        <div class="ai-typing" id="aiNotifTyping">Warda is analyzing audience segments...</div>
        <div class="ai-foot">
            <input type="text" id="aiNotifInput" class="ai-input" placeholder="Ask Warda..." onkeypress="if(event.key === 'Enter') sendNotifAIMessage()">
            <button class="ai-send" onclick="sendNotifAIMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        let notifChatHistory = [];
        
        function toggleNotifAI() {
            document.getElementById('aiNotifPopup').classList.toggle('open');
            document.getElementById('aiNotifInput').focus();
        }

        async function sendNotifAIMessage() {
            const input = document.getElementById('aiNotifInput');
            const msg = input.value.trim();
            if(!msg) return;

            addNotifAIMsg('user', msg);
            input.value = '';
            
            const typing = document.getElementById('aiNotifTyping');
            typing.style.display = 'block';
            scrollNotifAIBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        message: msg, 
                        history: notifChatHistory, 
                        page_data: { 
                            type: 'broadcasting_center',
                            tab: '<?= $tab ?>',
                            total_users: <?= (int)$totalUsers ?>,
                            active_users: <?= (int)$activeUsers ?>,
                            total_shops: <?= (int)$totalShops ?>,
                            total_drivers: <?= (int)$totalDrivers ?>
                        } 
                    })
                });
                const textOutput = await res.text();
                typing.style.display = 'none';
                
                try {
                    const data = JSON.parse(textOutput);
                    if(data.reply) {
                        addNotifAIMsg('bot', data.reply);
                        notifChatHistory.push({ role: 'user', content: msg });
                        notifChatHistory.push({ role: 'ai', content: data.reply });
                    } else {
                        addNotifAIMsg('bot', 'AI connection issue.');
                    }
                } catch (e) {
                    addNotifAIMsg('bot', 'Error processing AI response.');
                }
            } catch(e) {
                typing.style.display = 'none';
                addNotifAIMsg('bot', 'Connection error.');
            }
        }

        function addNotifAIMsg(sender, text) {
            const body = document.getElementById('aiNotifBody');
            const div = document.createElement('div');
            div.className = `ai-msg ${sender}`;
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            div.innerHTML = `<div class="ai-bubble">${formattedText}</div>`;
            body.appendChild(div);
            scrollNotifAIBottom();
        }

        function scrollNotifAIBottom() {
            const body = document.getElementById('aiNotifBody');
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>
</html>