<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$cityID         = isset($_GET['city_id'])  ? (int)$_GET['city_id']   : '';
$ShopNameFilter = $_GET['ShopName'] ?? '';
$Page           = isset($_GET['Page'])     ? (int)$_GET['Page']      : 0;
$limit          = 12;
$offset         = $Page * $limit;

// B2B Category Logic (Expandable if multiple IDs represent B2B)
$b2bCondition = "CategoryId = 112";

$totalRes      = mysqli_query($con, "SELECT COUNT(*) as c FROM Shops WHERE $b2bCondition");
$totalPartners = mysqli_fetch_assoc($totalRes)['c'];
$premiumRes    = mysqli_query($con, "SELECT COUNT(*) as c FROM Shops WHERE BakatID IN (2,3) AND $b2bCondition");
$premiumCount  = mysqli_fetch_assoc($premiumRes)['c'];
$freeCount     = $totalPartners - $premiumCount;
$premiumPct    = $totalPartners > 0 ? round(($premiumCount / $totalPartners) * 100, 1) : 0;

$where = $b2bCondition;
if ($ShopNameFilter) $where .= " AND ShopName LIKE '%" . mysqli_real_escape_string($con, $ShopNameFilter) . "%'";
if ($cityID)         $where .= " AND CityID = $cityID";

$query = "SELECT * FROM Shops WHERE $where ORDER BY ShopID DESC LIMIT $limit OFFSET $offset";
$shops = mysqli_query($con, $query);

// For AJAX partial loads
$isAjax = isset($_GET['ajax']);
if ($isAjax) {
    while ($row = mysqli_fetch_assoc($shops)) {
        $img = str_replace('https://jibler.app/db/db/', '', $row['ShopLogo']);
        if (empty($img)) $img = 'https://ui-avatars.com/api/?name=' . urlencode($row['ShopName']) . '&background=F3F4F6&color=111827&bold=true&size=128';
        echo renderCard($row, $img);
    }
    exit;
}

$cities_query = mysqli_query($con, "SELECT DeliveryZoneID, CityName FROM DeliveryZone ORDER BY CityName ASC");
$cities_data  = [];
$activeCityName = 'All Regions';
while ($c = mysqli_fetch_assoc($cities_query)) {
    $cities_data[] = $c;
    if ($cityID == $c['DeliveryZoneID']) $activeCityName = $c['CityName'];
}

function renderCard($row, $img) {
    $year = date('Y', strtotime($row['CreatedAtShops'] ?? 'now'));
    ob_start(); ?>
    <a href="shop-profile.php?id=<?= $row['ShopID'] ?>" class="shop-card">
        <div class="sc-glow"></div>
        <div class="sc-head">
            <img src="<?= $img ?>" class="sc-logo" alt="<?= htmlspecialchars($row['ShopName']) ?>"
                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($row['ShopName']) ?>&background=F3F4F6&color=111827&bold=true'">
            <div>
                <div class="sc-id">ENTITY #<?= $row['ShopID'] ?></div>
                <div class="sc-name"><?= htmlspecialchars($row['ShopName']) ?></div>
            </div>
        </div>
        <div class="sc-pills">
            <div class="sc-pill">
                <div class="sc-pill-val" style="color:#059669;">Active</div>
                <div class="sc-pill-lbl">Status</div>
            </div>
            <div class="sc-pill">
                <div class="sc-pill-val"><?= $year ?></div>
                <div class="sc-pill-lbl">Est.</div>
            </div>
        </div>
        <div class="sc-foot">
            <span class="sc-btn">View Profile</span>
            <i class="fas fa-arrow-right sc-arrow"></i>
        </div>
    </a>
    <?php return ob_get_clean();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>B2B | QOON</title>
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
            --shadow-sm:  0 1px 2px rgba(0,0,0,0.05);
            --shadow-md:  0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-xl:  0 20px 40px -10px rgba(0,0,0,0.08);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',-apple-system,sans-serif; -webkit-font-smoothing:antialiased; }
        body { background:var(--bg-master); color:var(--text-base); display:flex; height:100vh; overflow:hidden; }
        .layout-wrapper { display:flex; width:100%; height:100%; }

        main.content-area { flex:1; overflow-y:auto; display:flex; flex-direction:column; }
        main.content-area::-webkit-scrollbar { width:6px; }
        main.content-area::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:10px; }

        /* Sticky Header */
        .header-bar {
            position:sticky; top:0; z-index:20;
            background:rgba(255,255,255,0.9); backdrop-filter:blur(16px);
            border-bottom:1px solid var(--border);
            padding:18px 40px;
            display:flex; justify-content:space-between; align-items:center; gap:20px;
        }
        .header-left h1 { font-size:20px; font-weight:700; color:var(--text-strong); letter-spacing:-0.4px; }
        .header-left p  { font-size:13px; color:var(--text-muted); font-weight:500; margin-top:2px; }

        .header-right { display:flex; align-items:center; gap:12px; }

        /* Search */
        .search-bar {
            display:flex; align-items:center; gap:10px;
            padding:9px 16px; border-radius:8px;
            border:1px solid var(--border); background:var(--bg-surface);
            box-shadow:var(--shadow-sm); width:280px; transition:0.2s;
        }
        .search-bar:focus-within { border-color:var(--border-md); box-shadow:0 0 0 3px rgba(17,24,39,0.06); }
        .search-bar i  { color:var(--text-muted); font-size:13px; }
        .search-bar input { border:none; outline:none; flex:1; font-size:14px; font-weight:500; color:var(--text-strong); background:transparent; }
        .search-bar input::placeholder { color:var(--text-muted); }

        .btn-primary {
            display:inline-flex; align-items:center; gap:8px;
            padding:9px 18px; border-radius:8px;
            background:var(--text-strong); color:#fff;
            font-size:13px; font-weight:600; text-decoration:none;
            border:none; cursor:pointer; transition:0.2s; box-shadow:var(--shadow-sm);
            white-space:nowrap;
        }
        .btn-primary:hover { background:#1F2937; box-shadow:var(--shadow-md); }

        /* Region filter button */
        .region-btn {
            display:inline-flex; align-items:center; gap:8px;
            padding:9px 16px; border-radius:8px;
            border:1px solid var(--border); background:var(--bg-surface);
            font-size:13px; font-weight:600; color:var(--text-strong);
            cursor:pointer; box-shadow:var(--shadow-sm); transition:0.2s;
            white-space:nowrap;
        }
        .region-btn:hover { background:#F9FAFB; }
        .region-btn i { color:var(--text-muted); }
        .region-dot { width:7px; height:7px; border-radius:50%; background:var(--green-text); }

        /* Page body */
        .page-body { padding:32px 40px; display:flex; flex-direction:column; gap:28px; }

        /* KPI Metrics */
        .kpi-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
        .kpi-card { background:var(--bg-surface); border:1px solid var(--border); border-radius:12px; padding:20px; box-shadow:var(--shadow-sm); transition:0.2s; }
        .kpi-card:hover { box-shadow:var(--shadow-md); transform:translateY(-1px); }
        .kpi-icon { width:34px; height:34px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:14px; margin-bottom:12px; }
        .kpi-label { font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .kpi-val   { font-size:28px; font-weight:700; color:var(--text-strong); letter-spacing:-1px; margin-top:4px; line-height:1; }
        .ic-dark   { background:#F3F4F6; color:var(--text-strong); }
        .ic-purple { background:var(--purple-bg); color:var(--purple-text); }
        .ic-green  { background:var(--green-bg);  color:var(--green-text); }
        .ic-blue   { background:var(--blue-bg);   color:var(--blue-text); }

        /* Shop Grid */
        .shops-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(330px,1fr)); gap:24px; }

        /* ===== 3D SHOP CARD (preserved + refined) ===== */
        .shop-card {
            background:#FFFFFF;
            border-radius:28px;
            padding:32px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05),
                        0 20px 40px -12px rgba(0,0,0,0.08);
            border:1px solid rgba(255,255,255,0.8);
            text-decoration:none; color:inherit;
            display:flex; flex-direction:column; gap:24px;
            position:relative; overflow:hidden;
            transform-style:preserve-3d;
            transition:transform 0.4s cubic-bezier(0.16,1,0.3,1),
                       box-shadow 0.4s cubic-bezier(0.16,1,0.3,1);
            cursor:pointer;
        }
        .shop-card:hover {
            box-shadow: 0 8px 16px -4px rgba(0,0,0,0.06),
                        0 32px 60px -16px rgba(0,0,0,0.14);
        }

        /* Gradient glow on hover */
        .sc-glow {
            position:absolute; inset:-2px; border-radius:30px;
            background:linear-gradient(135deg, #E5E7EB, transparent, #E5E7EB);
            opacity:0; transition:0.5s; z-index:-1;
        }
        .shop-card:hover .sc-glow { opacity:1; }

        .sc-head { display:flex; align-items:center; gap:18px; }
        .sc-logo {
            width:72px; height:72px; border-radius:20px; object-fit:cover;
            border:2px solid var(--border);
            box-shadow:0 8px 20px rgba(0,0,0,0.07);
            flex-shrink:0;
        }
        .sc-id   { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1.5px; color:var(--text-muted); margin-bottom:4px; }
        .sc-name { font-size:20px; font-weight:700; color:var(--text-strong); letter-spacing:-0.5px; line-height:1.1; }

        .sc-pills { display:flex; gap:10px; }
        .sc-pill {
            flex:1; background:#F9FAFB; border:1px solid var(--border);
            border-radius:14px; padding:14px; text-align:center;
        }
        .sc-pill-val { font-size:16px; font-weight:700; color:var(--text-strong); }
        .sc-pill-lbl { font-size:10px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; margin-top:3px; }

        .sc-foot {
            display:flex; justify-content:space-between; align-items:center;
            padding-top:20px; border-top:1px solid var(--border);
        }
        .sc-btn {
            display:inline-flex; align-items:center;
            padding:10px 18px; border-radius:10px;
            background:var(--text-strong); color:#fff;
            font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;
            transition:0.2s;
        }
        .shop-card:hover .sc-btn { background:#1F2937; }
        .sc-arrow { font-size:16px; color:var(--text-muted); transition:0.3s; }
        .shop-card:hover .sc-arrow { color:var(--text-strong); transform:translateX(4px); }

        /* Load More */
        .load-more-wrap { display:flex; justify-content:center; padding-bottom:20px; }
        .btn-load-more {
            display:inline-flex; flex-direction:column; align-items:center; gap:2px;
            padding:14px 40px; border-radius:100px;
            background:var(--text-strong); color:#fff;
            border:none; cursor:pointer; transition:0.2s; box-shadow:var(--shadow-xl);
        }
        .btn-load-more:hover { background:#1F2937; transform:translateY(-2px); }
        .btn-load-more .lm-main { font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px; }
        .btn-load-more .lm-sub  { font-size:11px; font-weight:500; color:rgba(255,255,255,0.5); }
        .btn-load-more:disabled { opacity:0.5; cursor:not-allowed; transform:none; }

        /* Shimmer */
        .shimmer-card {
            background:linear-gradient(90deg,#F3F4F6 25%,#E5E7EB 50%,#F3F4F6 75%);
            background-size:400% 100%; animation:shimAnim 1.4s infinite ease-in-out; border-radius:12px;
        }
        @keyframes shimAnim { 0%{background-position:100% 0} 100%{background-position:-100% 0} }

        .shimmer-grid { display:none; grid-template-columns:repeat(auto-fill,minmax(330px,1fr)); gap:24px; }
        .shimmer-grid.visible { display:grid; }
        .shimmer-item { background:#fff; border-radius:28px; padding:32px; border:1px solid var(--border); display:flex; flex-direction:column; gap:20px; }

        /* Empty state */
        .empty-state { display:flex; flex-direction:column; align-items:center; gap:12px; padding:60px 0; color:var(--text-muted); text-align:center; }
        .empty-state i { font-size:48px; color:var(--border); }
        .empty-state h3 { font-size:16px; font-weight:700; color:var(--text-strong); }

        /* Region modal */
        .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:200; align-items:center; justify-content:center; backdrop-filter:blur(4px); }
        .modal-overlay.open { display:flex; }
        .modal-box { background:var(--bg-surface); border-radius:16px; width:100%; max-width:540px; padding:28px; box-shadow:0 25px 50px rgba(0,0,0,0.15); border:1px solid var(--border); max-height:85vh; overflow-y:auto; }
        .modal-head { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
        .modal-head h2 { font-size:18px; font-weight:700; color:var(--text-strong); }
        .modal-close { width:30px; height:30px; border-radius:6px; border:1px solid var(--border); background:var(--bg-surface); display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:16px; color:var(--text-muted); transition:0.15s; }
        .modal-close:hover { background:#F3F4F6; }
        .city-grid { display:flex; flex-wrap:wrap; gap:8px; margin-top:8px; }
        .city-chip {
            padding:8px 16px; border-radius:20px; border:1px solid var(--border);
            font-size:13px; font-weight:600; color:var(--text-muted);
            text-decoration:none; transition:0.15s; background:var(--bg-surface);
        }
        .city-chip:hover { background:#F3F4F6; color:var(--text-strong); border-color:var(--border-md); }
        .city-chip.active { background:var(--text-strong); color:#fff; border-color:var(--text-strong); }

        /* ── TABLET ≤ 900px ──────────────────────────────────────────────── */
        @media (max-width: 900px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }

            /* Hide desktop sidebar rail */
            .sb-container { display: none !important; }

            main.content-area { overflow-y: visible; }

            /* Header: wrap to 2 rows */
            .header-bar {
                padding: 14px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }
            .header-left h1 { font-size: 18px; }
            .header-left p  { font-size: 12px; }

            .header-right { flex-wrap: wrap; gap: 8px; width: 100%; }

            /* Search: full width */
            .search-bar { width: 100%; flex: 1; min-width: 0; }

            .region-btn { flex: 1; justify-content: center; }
            .btn-primary { flex: 1; justify-content: center; }

            /* Page body */
            .page-body { padding: 16px; gap: 16px; padding-bottom: 80px; }

            /* KPI: 2×2 */
            .kpi-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
            .kpi-val  { font-size: 22px; }

            /* Shop cards: flexible min size */
            .shops-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
            .shimmer-grid { grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .header-bar { padding: 12px 14px; }
            .header-left h1 { font-size: 16px; }

            /* Single-col shop cards */
            .shops-grid  { grid-template-columns: 1fr; gap: 14px; }
            .shimmer-grid{ grid-template-columns: 1fr; gap: 14px; }

            /* Tighter card padding */
            .shop-card { padding: 20px; border-radius: 20px; gap: 16px; }
            .sc-logo   { width: 56px; height: 56px; border-radius: 14px; }
            .sc-name   { font-size: 17px; }
            .sc-foot   { padding-top: 14px; }

            .kpi-grid { gap: 10px; }
            .kpi-card { padding: 14px; }
            .kpi-val  { font-size: 20px; }

            /* Modal full-width on phone */
            .modal-box { border-radius: 16px 16px 0 0; position: fixed; bottom: 0; left: 0; right: 0; max-width: 100%; max-height: 70vh; }
            .modal-overlay { align-items: flex-end; }
        }

    </style>

    <style>
        /* ----- FAIROZ AI ASSISTANT (QOON Pro) ----- */
        .ai-fab {
            position: fixed;
            bottom: 25px; right: 25px;
            width: 62px; height: 62px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(20, 184, 166, 0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0;
            border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(20, 184, 166, 0.45); }
        .ai-fab img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .ai-fab-dot {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: #14B8A6;
            border: 2.5px solid #fff;
            border-radius: 50%;
            animation: fabPulse 1.8s ease-in-out infinite;
        }
        @keyframes fabPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(20,184,166,0.7); }
            50%      { box-shadow: 0 0 0 6px rgba(20,184,166,0); }
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
            background: linear-gradient(135deg, #14B8A6, #0D9488);
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
        .ai-msg.user .ai-bubble { background:#14B8A6; color:#fff; border-bottom-right-radius:4px; }

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
        .ai-input:focus { border-color:#14B8A6; background:#fff; box-shadow:0 0 0 3px rgba(20,184,166,0.08); }
        .ai-send {
            width: 40px; height: 40px; border-radius: 50%;
            background:#14B8A6; color:white;
            border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#0D9488; transform:scale(1.05); }

        /* Mobile */
        @media (max-width: 600px) {
            .ai-fab  { right: 16px; bottom: 80px; }
            .ai-popup { right: 0; left: 0; bottom: 0; width: 100%; height: 90dvh; border-radius: 24px 24px 0 0; transform: translateY(100%); }
            .ai-popup.open { transform: translateY(0); }
            .ai-foot { padding-bottom: max(12px, env(safe-area-inset-bottom)); }
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
                    <h1>B2B</h1>
                    <p>B2B Vendor network — <?= number_format($totalPartners) ?> registered stores</p>
                </div>
                <div class="header-right">
                    <!-- Search -->
                    <form action="shop.php" method="GET" class="search-bar">
                        <?php if($cityID): ?><input type="hidden" name="city_id" value="<?= $cityID ?>"><?php endif; ?>
                        <i class="fas fa-search"></i>
                        <input type="text" name="ShopName" placeholder="Search shops..." value="<?= htmlspecialchars($ShopNameFilter) ?>">
                    </form>
                    <!-- Region Filter -->
                    <button class="region-btn" onclick="document.getElementById('regionModal').classList.add('open')">
                        <div class="region-dot"></div>
                        <i class="fas fa-map-marker-alt" style="font-size:12px;"></i>
                        <?= htmlspecialchars($activeCityName) ?>
                        <i class="fas fa-chevron-down" style="font-size:11px;"></i>
                    </button>
                    <!-- Add -->
                    <a href="add-shop.php" class="btn-primary"><i class="fas fa-plus"></i> Add Shop</a>
                </div>
            </header>

            <div class="page-body">

                <!-- KPI Metrics -->
                <div class="kpi-grid">
                    <div class="kpi-card">
                        <div class="kpi-icon ic-dark"><i class="fas fa-store"></i></div>
                        <div class="kpi-label">Total Shops</div>
                        <div class="kpi-val"><?= number_format($totalPartners) ?></div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon ic-purple"><i class="fas fa-gem"></i></div>
                        <div class="kpi-label">Premium Tier</div>
                        <div class="kpi-val"><?= number_format($premiumCount) ?></div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon ic-green"><i class="fas fa-seedling"></i></div>
                        <div class="kpi-label">Free Tier</div>
                        <div class="kpi-val"><?= number_format($freeCount) ?></div>
                    </div>
                    <div class="kpi-card">
                        <div class="kpi-icon ic-blue"><i class="fas fa-chart-line"></i></div>
                        <div class="kpi-label">Premium Rate</div>
                        <div class="kpi-val"><?= $premiumPct ?>%</div>
                    </div>
                </div>

                <!-- Shop Cards Grid -->
                <div class="shops-grid" id="shopGrid">
                    <?php
                    $count = 0;
                    while ($row = mysqli_fetch_assoc($shops)) {
                        $img = str_replace('https://jibler.app/db/db/', '', $row['ShopLogo']);
                        if (empty($img)) $img = 'https://ui-avatars.com/api/?name=' . urlencode($row['ShopName']) . '&background=F3F4F6&color=111827&bold=true&size=128';
                        echo renderCard($row, $img);
                        $count++;
                    }
                    if ($count === 0): ?>
                    <div class="empty-state" style="grid-column:1/-1;">
                        <i class="fas fa-store-slash"></i>
                        <h3>No shops found</h3>
                        <p>Try adjusting your search or region filter.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Shimmer Grid (during load more) -->
                <div class="shimmer-grid" id="shimmerGrid">
                    <?php for($s=0;$s<3;$s++): ?>
                    <div class="shimmer-item">
                        <div style="display:flex;align-items:center;gap:16px;">
                            <div class="shimmer-card" style="width:72px;height:72px;border-radius:20px;flex-shrink:0;"></div>
                            <div style="flex:1;display:flex;flex-direction:column;gap:8px;">
                                <div class="shimmer-card" style="height:10px;width:80px;"></div>
                                <div class="shimmer-card" style="height:18px;width:140px;"></div>
                            </div>
                        </div>
                        <div style="display:flex;gap:10px;">
                            <div class="shimmer-card" style="flex:1;height:60px;border-radius:14px;"></div>
                            <div class="shimmer-card" style="flex:1;height:60px;border-radius:14px;"></div>
                        </div>
                        <div class="shimmer-card" style="height:42px;border-radius:10px;"></div>
                    </div>
                    <?php endfor; ?>
                </div>

                <!-- Load More -->
                <?php if(($Page + 1) * $limit < $totalPartners): ?>
                <div class="load-more-wrap" id="loadMoreWrap">
                    <button class="btn-load-more" id="loadMoreBtn" onclick="loadMore()">
                        <span class="lm-main">Load More</span>
                        <span class="lm-sub"><?= min(($Page+1)*$limit, $totalPartners) ?> / <?= number_format($totalPartners) ?> shops</span>
                    </button>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <!-- Region Modal -->
    <div class="modal-overlay" id="regionModal">
        <div class="modal-box">
            <div class="modal-head">
                <h2>Switch Region</h2>
                <button class="modal-close" onclick="document.getElementById('regionModal').classList.remove('open')">×</button>
            </div>
            <p style="font-size:13px;color:var(--text-muted);font-weight:500;margin-bottom:16px;">Filter shops by delivery zone region.</p>
            <div class="city-grid">
                <a href="shopB2B.php?ShopName=<?= urlencode($ShopNameFilter) ?>" class="city-chip <?= $cityID == '' ? 'active' : '' ?>">All Regions</a>
                <?php foreach($cities_data as $c): ?>
                    <a href="shopB2B.php?city_id=<?= $c['DeliveryZoneID'] ?>&ShopName=<?= urlencode($ShopNameFilter) ?>"
                       class="city-chip <?= $cityID == $c['DeliveryZoneID'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($c['CityName']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- FAIROZ AI ASSISTANT (QOON Pro) -->
    <div class="ai-fab" id="aiShopFab" onclick="toggleShopAI()" style="position:fixed;">
        <img src="fairoz.avif" alt="Fairoz"
             onerror="this.src='https://ui-avatars.com/api/?name=Fairoz&background=E0F2F1&color=009688&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>
    <div class="ai-popup" id="aiShopPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="fairoz.avif" alt="Fairoz" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);" onerror="this.src='https://ui-avatars.com/api/?name=Fairoz&background=E0F2F1&color=009688&bold=true'">
                    <div title="Online 24/24" style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#14B8A6; border:2px solid #fff; border-radius:50%; box-shadow:0 2px 4px rgba(20,184,166,0.4);"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Fairoz AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#14B8A6; font-size:8px;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleShopAI()"></i>
        </div>
        <div class="ai-body" id="aiShopBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">👋 Hello! I am <b>Fairoz</b>, your QOON Pro AI assistant. I can help you manage your B2B network and vendors. How can I help you?</div>
            </div>
        </div>
        <div class="ai-typing" id="aiShopTyping" style="font-size:12px; color:#9CA3AF; display:none; padding:10px 16px; background:#F5F6FA;">Fairoz is thinking...</div>
        <div class="ai-foot">
            <input type="text" id="aiShopInput" class="ai-input" placeholder="Ask Fairoz..." onkeypress="if(event.key === 'Enter') sendShopAIMessage()">
            <button class="ai-send" onclick="sendShopAIMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        let shopChatHistory = [];
        
        function toggleShopAI() {
            document.getElementById('aiShopPopup').classList.toggle('open');
            document.getElementById('aiShopInput').focus();
        }

        async function sendShopAIMessage() {
            const input = document.getElementById('aiShopInput');
            const msg = input.value.trim();
            if(!msg) return;

            addShopAIMsg('user', msg);
            input.value = '';
            
            const typing = document.getElementById('aiShopTyping');
            typing.style.display = 'block';
            scrollShopAIBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        message: msg, 
                        history: shopChatHistory, 
                        page_data: { 
                            type: 'qoon_pro',
                            total_partners: <?= (int)$totalPartners ?>,
                            premium_count: <?= (int)$premiumCount ?>
                        } 
                    })
                });
                const textOutput = await res.text();
                typing.style.display = 'none';
                
                try {
                    const data = JSON.parse(textOutput);
                    if(data.reply) {
                        addShopAIMsg('bot', data.reply);
                        shopChatHistory.push({ role: 'user', content: msg });
                        shopChatHistory.push({ role: 'ai', content: data.reply });
                    } else {
                        addShopAIMsg('bot', 'AI connection issue.');
                    }
                } catch (e) {
                    addShopAIMsg('bot', 'Error processing AI response.');
                }
            } catch(e) {
                typing.style.display = 'none';
                addShopAIMsg('bot', 'Connection error.');
            }
        }

        function addShopAIMsg(sender, text) {
            const body = document.getElementById('aiShopBody');
            const div = document.createElement('div');
            div.className = `ai-msg ${sender}`;
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            div.innerHTML = `<div class="ai-bubble">${formattedText}</div>`;
            body.appendChild(div);
            scrollShopAIBottom();
        }

        function scrollShopAIBottom() {
            const body = document.getElementById('aiShopBody');
            body.scrollTop = body.scrollHeight;
        }

        // Existing tilt and loadMore scripts...
        let currentPage = <?= $Page ?>;
        const totalShops = <?= $totalPartners ?>;
        const limit = <?= $limit ?>;

        function applyTilt(cards) {
            cards.forEach(card => {
                card.addEventListener('mousemove', e => {
                    const rect = card.getBoundingClientRect();
                    const x = e.clientX - rect.left;
                    const y = e.clientY - rect.top;
                    const dx = (x - rect.width / 2) / 18;
                    const dy = (y - rect.height / 2) / 18;
                    card.style.transform = `perspective(1000px) rotateY(${dx}deg) rotateX(${-dy}deg) translateY(-8px)`;
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'perspective(1000px) rotateY(0deg) rotateX(0deg) translateY(0)';
                });
            });
        }
        applyTilt(document.querySelectorAll('.shop-card'));

        document.getElementById('regionModal').addEventListener('click', function(e) {
            if(e.target === this) this.classList.remove('open');
        });

        async function loadMore() {
            const btn = document.getElementById('loadMoreBtn');
            const shimmer = document.getElementById('shimmerGrid');
            const grid = document.getElementById('shopGrid');
            shimmer.classList.add('visible');
            btn.disabled = true;
            btn.querySelector('.lm-main').textContent = 'Loading...';
            currentPage++;
            try {
                const params = new URLSearchParams({ Page: currentPage, ShopName: '<?= $ShopNameFilter ?>', city_id: '<?= $cityID ?>', ajax: 1 });
                const res = await fetch(`shopB2B.php?${params}`);
                const html = await res.text();
                shimmer.classList.remove('visible');
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const cards = doc.querySelectorAll('.shop-card');
                cards.forEach(card => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(24px)';
                    grid.appendChild(card);
                    requestAnimationFrame(() => {
                        card.style.transition = '0.5s cubic-bezier(0.16,1,0.3,1)';
                        card.style.opacity = '1';
                        card.style.transform = '';
                    });
                });
                applyTilt(grid.querySelectorAll('.shop-card'));
                const loaded = Math.min((currentPage + 1) * limit, totalShops);
                if(loaded >= totalShops || cards.length === 0) {
                    const wrap = document.getElementById('loadMoreWrap');
                    if(wrap) wrap.remove();
                } else {
                    btn.disabled = false;
                    btn.querySelector('.lm-main').textContent = 'Load More';
                    btn.querySelector('.lm-sub').textContent = `${loaded} / ${totalShops} shops`;
                }
            } catch(e) {
                shimmer.classList.remove('visible');
                btn.disabled = false;
                btn.querySelector('.lm-main').textContent = 'Retry';
            }
        }
    </script>
</body>
</html>