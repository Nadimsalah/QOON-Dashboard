<?php
require "conn.php";

$deliveredCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Orders WHERE OrderState IN ('Done', 'Rated')"))['c'] ?? 0;
$waitingCount   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Orders WHERE OrderState='waiting'"))['c'] ?? 0;
$doingCount     = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Orders WHERE OrderState='Doing'"))['c'] ?? 0;
$cancelledCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Orders WHERE OrderState='Cancelled'"))['c'] ?? 0;
$totalCount     = $deliveredCount + $waitingCount + $doingCount + $cancelledCount;

$page = isset($_GET['Page']) ? (int)$_GET['Page'] : 0;
if($page < 0) $page = 0; $limit = 20; $offset = $page * $limit;

$where = "1=1";
$state = isset($_GET['state']) ? mysqli_real_escape_string($con, $_GET['state']) : '';
if($state && $state !== 'All') { $where .= " AND Orders.OrderState='$state'"; }
$orderid = isset($_GET['orderid']) ? (int)$_GET['orderid'] : 0;
if($orderid > 0) { $where .= " AND Orders.OrderID=$orderid"; }

$sql = "SELECT Orders.OrderID, Orders.CreatedAtOrders, Orders.OrderDetails, Orders.OrderPrice, Orders.OrderState, 
               Users.name as BuyerName, Drivers.FName as DriverName, 
               Orders.DestinationName as ShopName, Orders.DestnationPhoto, Users.UserPhoto
        FROM Orders 
        LEFT JOIN Users ON Orders.UserID = Users.UserID 
        LEFT JOIN Drivers ON Orders.DelvryId = Drivers.DriverID 
        WHERE $where 
        ORDER BY Orders.OrderID DESC LIMIT $limit OFFSET $offset";
$resTx = mysqli_query($con, $sql);
$orders = [];
if($resTx) { while($row = mysqli_fetch_assoc($resTx)) { $orders[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Dashboard</title>
    <!-- Premium Fonts -->
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
            --text-on-accent: #FFFFFF;
            
            --accent-primary: #111827;
            --accent-hover: #1F2937;
            
            --status-green-bg: #ECFDF5; --status-green-text: #059669; --status-green-dot: #10B981;
            --status-blue-bg: #EFF6FF; --status-blue-text: #2563EB; --status-blue-dot: #3B82F6;
            --status-orange-bg: #FFFBEB; --status-orange-text: #D97706; --status-orange-dot: #F59E0B;
            --status-red-bg: #FEF2F2; --status-red-text: #DC2626; --status-red-dot: #EF4444;

            --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-float: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', -apple-system, sans-serif; }
        
        body {
            background-color: var(--bg-master);
            color: var(--text-base);
            display: flex;
            height: 100vh;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .layout-wrapper { display: flex; width: 100%; height: 100%; }
        
        main.content-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            position: relative;
        }

        .header-bar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            display: flex;
            flex-direction: column;
            gap: 4px;
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
        }

        .search-container {
            position: relative;
        }
        .search-container input {
            background: #F9FAFB;
            border: 1px solid var(--border-subtle);
            padding: 10px 16px 10px 42px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-strong);
            width: 280px;
            transition: all 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .search-container input:focus {
            outline: none;
            border-color: var(--border-focus);
            box-shadow: 0 0 0 4px rgba(17, 24, 39, 0.05);
            width: 320px;
            background: var(--bg-surface);
        }
        .search-container i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 14px;
        }

        .page-body {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        /* Nav Pills */
        .filter-nav {
            display: flex;
            gap: 4px;
            padding: 4px;
            background: #E5E7EB;
            border-radius: 10px;
            align-self: flex-start;
        }
        .filter-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: 0.2s;
        }
        .filter-pill:hover {
            color: var(--text-strong);
        }
        .filter-pill.active {
            background: var(--bg-surface);
            color: var(--text-strong);
            box-shadow: var(--shadow-sm);
        }

        /* Minimal Metrics */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 24px;
        }
        .metric-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            box-shadow: var(--shadow-sm);
            transition: 0.2s;
            position: relative;
            overflow: hidden;
        }
        .metric-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }
        .metric-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .metric-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
        }
        .metric-val {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-strong);
            letter-spacing: -1px;
            line-height: 1;
        }
        .mc-green { background: var(--status-green-bg); color: var(--status-green-text); }
        .mc-blue { background: var(--status-blue-bg); color: var(--status-blue-text); }
        .mc-orange { background: var(--status-orange-bg); color: var(--status-orange-text); }
        .mc-black { background: #F3F4F6; color: var(--text-strong); }

        /* Beautiful Table Engine */
        .table-container {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .table-toolbar {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #FFFFFF;
        }
        .table-toolbar h2 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-strong);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background: #F9FAFB;
            padding: 16px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-subtle);
        }
        td {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-subtle);
            vertical-align: middle;
            background: #FFFFFF;
        }
        tr:last-child td { border-bottom: none; }
        
        /* Subtle Row Hover */
        tr:hover td { background: #F9FAFB; }

        /* Cell Styling */
        .td-id {
            font-weight: 600;
            color: var(--text-strong);
            font-size: 14px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
        .td-time {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
            font-weight: 500;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
        }
        .status-badge i { font-size: 8px; }
        .st-done { background: var(--status-green-bg); color: var(--status-green-text); }
        .st-transit { background: var(--status-blue-bg); color: var(--status-blue-text); }
        .st-wait { background: var(--status-orange-bg); color: var(--status-orange-text); }
        .st-cancel { background: var(--status-red-bg); color: var(--status-red-text); }

        .entity-stack {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .entity-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .entity-avatar {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: #F3F4F6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 12px;
            border: 1px solid var(--border-subtle);
        }
        .entity-avatar.round { border-radius: 50%; }
        
        .entity-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-strong);
        }

        .td-amount {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-strong);
            text-align: right;
            font-variant-numeric: tabular-nums;
        }
        .td-amount span {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            margin-left: 4px;
        }

        .btn-inspect {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
            color: var(--text-strong);
            background: #FFFFFF;
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-sm);
            transition: 0.2s;
            text-decoration: none;
        }
        .btn-inspect:hover {
            background: #F9FAFB;
            box-shadow: var(--shadow-md);
        }

        /* Pagination Clean */
        .page-footer {
            padding: 16px 24px;
            background: #FFFFFF;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .page-info {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-muted);
        }
        .page-controls {
            display: flex;
            gap: 8px;
        }
        .page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 36px;
            padding: 0 16px;
            border-radius: 8px;
            border: 1px solid var(--border-subtle);
            background: #FFFFFF;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-strong);
            text-decoration: none;
            transition: 0.2s;
            box-shadow: var(--shadow-sm);
        }
        .page-btn:hover { background: #F9FAFB; }
        .page-btn.disabled { opacity: 0.5; pointer-events: none; }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }
            .sb-container { display: none !important; }
            main.content-area { overflow-y: visible; }

            /* Header */
            .header-bar { flex-direction: column; align-items: flex-start; gap: 10px; padding: 14px 16px; position: static; }
            .page-title h1 { font-size: 20px; }
            .page-title p { font-size: 13px; }
            .search-container { width: 100%; }
            .search-container input { width: 100%; }
            .search-container input:focus { width: 100%; }

            /* Page body */
            .page-body { padding: 12px 12px 80px; gap: 16px; }

            /* Filter nav: horizontal scrollable strip */
            .filter-nav {
                display: flex;
                flex-direction: row;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scroll-snap-type: x mandatory;
                scrollbar-width: none;
                align-self: stretch;
                padding: 4px;
                gap: 4px;
                border-radius: 10px;
            }
            .filter-nav::-webkit-scrollbar { display: none; }
            .filter-pill {
                flex: 0 0 auto;
                scroll-snap-align: start;
                white-space: nowrap;
                padding: 8px 14px;
                font-size: 13px;
            }

            /* Metrics: 4-col → 2-col */
            .metrics-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
            .metric-card { padding: 16px; gap: 10px; border-radius: 12px; }
            .metric-val { font-size: 24px; }

            /* Table toolbar */
            .table-toolbar { padding: 14px 16px; flex-wrap: wrap; gap: 8px; }

            /* Table horizontal scroll */
            table { min-width: 480px; }
            td, th { padding: 12px 14px; font-size: 13px; }

            /* Pagination */
            .page-footer { flex-direction: column; gap: 10px; align-items: flex-start; padding: 14px 16px; }
        }

        @media (max-width: 600px) {
            /* Metrics: 2-col → 1-col */
            .metrics-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .metric-val { font-size: 22px; }

            /* Hide Participants column — keep ID, Status, Amount, Action */
            table thead tr th:nth-child(3),
            table tbody tr td:nth-child(3) { display: none; }

            td { font-size: 12px; padding: 10px 10px; }
            th { font-size: 11px; padding: 10px 10px; }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">
            
            <header class="header-bar">
                <div class="page-title">
                    <h1>Transaction Ledger</h1>
                    <p>Overview of the entire order ecosystem.</p>
                </div>
                <div class="search-container">
                    <form action="orders.php" method="GET">
                        <i class="fas fa-search"></i>
                        <input type="number" name="orderid" placeholder="Locate by Order #..." value="<?= $orderid > 0 ? $orderid : '' ?>">
                    </form>
                </div>
            </header>

            <div class="page-body">
                
                <!-- Filters -->
                <nav class="filter-nav">
                    <?php 
                        $filters = [
                            ['label'=>'Everything', 'val'=>'All'],
                            ['label'=>'Pending Queue', 'val'=>'waiting'],
                            ['label'=>'In Transit', 'val'=>'Doing'],
                            ['label'=>'Delivered', 'val'=>'Done'],
                            ['label'=>'Cancelled', 'val'=>'Cancelled']
                        ];
                        foreach($filters as $f): 
                            $isActive = ($state === $f['val']) || ($state=='' && $f['val']=='All') || ($state=='Rated' && $f['val']=='Done');
                            $class = $isActive ? 'active' : '';
                    ?>
                        <a href="?state=<?= $f['val'] ?>" class="filter-pill <?= $class ?>">
                            <?= $f['label'] ?>
                        </a>
                    <?php endforeach; ?>
                </nav>

                <!-- Key Metrics -->
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-icon mc-green"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <div class="metric-val"><?= number_format($deliveredCount) ?></div>
                            <div class="metric-label">Delivered</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon mc-blue"><i class="fas fa-motorcycle"></i></div>
                        <div>
                            <div class="metric-val"><?= number_format($doingCount) ?></div>
                            <div class="metric-label">In Transit</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon mc-orange"><i class="fas fa-clock"></i></div>
                        <div>
                            <div class="metric-val"><?= number_format($waitingCount) ?></div>
                            <div class="metric-label">Waiting</div>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-icon mc-black"><i class="fas fa-times-circle"></i></div>
                        <div>
                            <div class="metric-val"><?= number_format($cancelledCount) ?></div>
                            <div class="metric-label">Cancelled</div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-container">
                    <div class="table-toolbar">
                        <h2>Order Log</h2>
                    </div>
                    <div style="overflow-x:auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ref ID</th>
                                    <th>Status</th>
                                    <th>Participants</th>
                                    <th style="text-align: right;">Amount</th>
                                    <th style="text-align: right; padding-right:24px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($orders as $row): 
                                    $st = $row['OrderState']; 
                                    $displaySt = $st;
                                    if ($st == 'Rated') $displaySt = 'Delivered';

                                    $statusClass = 'st-transit';
                                    if($st == 'Done' || $st == 'Rated') $statusClass = 'st-done';
                                    if($st == 'waiting') $statusClass = 'st-wait';
                                    if($st == 'Doing') $statusClass = 'st-transit';
                                    if($st == 'Cancelled') $statusClass = 'st-cancel';
                                ?>
                                    <tr>
                                        <td>
                                            <div class="td-id">#<?= $row['OrderID'] ?></div>
                                            <div class="td-time"><?= date('M j, Y - H:i', strtotime($row['CreatedAtOrders'])) ?></div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?= $statusClass ?>">
                                                <i class="fas fa-circle"></i> <?= $displaySt ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="entity-stack">
                                                <div class="entity-row">
                                                    <div class="entity-avatar"><i class="fas fa-store"></i></div>
                                                    <span class="entity-name"><?= htmlspecialchars($row['ShopName']??'Unknown Vendor') ?></span>
                                                </div>
                                                <div class="entity-row">
                                                    <div class="entity-avatar round"><i class="fas fa-user"></i></div>
                                                    <span class="entity-name"><?= htmlspecialchars($row['BuyerName']??'Unknown User') ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="td-amount">
                                            <?= number_format($row['OrderPrice'], 2) ?><span>MAD</span>
                                        </td>
                                        <td style="text-align: right; padding-right:24px;">
                                            <a href="order-detail.php?OrderID=<?= $row['OrderID'] ?>" class="btn-inspect">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="page-footer">
                        <div class="page-info">
                            Showing index <?= $offset ?> - <?= $offset+$limit ?>
                        </div>
                        <div class="page-controls">
                            <?php if($page > 0): ?>
                                <a href="?Page=<?= $page-1 ?>&state=<?= urlencode($state) ?>&orderid=<?= $orderid ?>" class="page-btn">Previous</a>
                            <?php endif; ?>
                            <a href="?Page=<?= $page+1 ?>&state=<?= urlencode($state) ?>&orderid=<?= $orderid ?>" class="page-btn">Next Segment</a>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- ALI AI ASSISTANT (Orders Page) -->
    <style>
        .ai-fab {
            position: fixed;
            bottom: 25px; right: 25px;
            width: 62px; height: 62px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(59, 130, 246, 0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0;
            border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(59, 130, 246, 0.45); }
        .ai-fab img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .ai-fab-dot {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: #3B82F6;
            border: 2.5px solid #fff;
            border-radius: 50%;
            animation: fabPulse 1.8s ease-in-out infinite;
        }
        @keyframes fabPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            50%      { box-shadow: 0 0 0 6px rgba(59, 130, 246, 0); }
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
            background: linear-gradient(135deg, #3B82F6, #2563EB);
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
        .ai-msg.user .ai-bubble { background:#3B82F6; color:#fff; border-bottom-right-radius:4px; }

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
        .ai-input:focus { border-color:#3B82F6; background:#fff; box-shadow:0 0 0 3px rgba(59,130,246,0.08); }
        .ai-send {
            width: 40px; height: 40px; border-radius: 50%;
            background:#3B82F6; color:white;
            border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#2563EB; transform:scale(1.05); }

        /* Mobile */
        @media (max-width: 600px) {
            .ai-fab  { right: 16px; bottom: 80px; }
            .ai-popup { right: 0; left: 0; bottom: 0; width: 100%; height: 90dvh; border-radius: 24px 24px 0 0; transform: translateY(100%); }
            .ai-popup.open { transform: translateY(0); }
            .ai-foot { padding-bottom: max(12px, env(safe-area-inset-bottom)); }
        }
    </style>

    <div class="ai-fab" id="aiOrdersFab" onclick="toggleOrdersAI()">
        <img src="ali.webp" alt="Ali"
             onerror="this.src='https://ui-avatars.com/api/?name=Ali&background=DBEAFE&color=2563EB&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>
    <div class="ai-popup" id="aiOrdersPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="ali.webp" alt="Ali" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);" onerror="this.src='https://ui-avatars.com/api/?name=Ali&background=DBEAFE&color=2563EB&bold=true'">
                    <div title="Online 24/24" style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#3B82F6; border:2px solid #fff; border-radius:50%;"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Ali AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#fff; font-size:8px;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleOrdersAI()"></i>
        </div>
        <div class="ai-body" id="aiOrdersBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">👋 Hello! I am <b>Ali</b>, your QOON Ledger assistant. I can help you track orders, verify statuses, and analyze transaction history. How can I assist you today?</div>
            </div>
        </div>
        <div class="ai-typing" id="aiOrdersTyping">Ali is checking order logs...</div>
        <div class="ai-foot">
            <input type="text" id="aiOrdersInput" class="ai-input" placeholder="Ask Ali about orders..." onkeypress="if(event.key === 'Enter') sendOrdersAIMessage()">
            <button class="ai-send" onclick="sendOrdersAIMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        let ordersChatHistory = [];
        
        function toggleOrdersAI() {
            document.getElementById('aiOrdersPopup').classList.toggle('open');
            document.getElementById('aiOrdersInput').focus();
        }

        async function sendOrdersAIMessage() {
            const input = document.getElementById('aiOrdersInput');
            const msg = input.value.trim();
            if(!msg) return;

            addOrdersAIMsg('user', msg);
            input.value = '';
            
            const typing = document.getElementById('aiOrdersTyping');
            typing.style.display = 'block';
            scrollOrdersAIBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        message: msg, 
                        history: ordersChatHistory, 
                        page_data: { 
                            type: 'orders_ledger',
                            total_orders: <?= (int)$totalCount ?>,
                            delivered: <?= (int)$deliveredCount ?>,
                            waiting: <?= (int)$waitingCount ?>,
                            transit: <?= (int)$doingCount ?>,
                            cancelled: <?= (int)$cancelledCount ?>
                        } 
                    })
                });
                const textOutput = await res.text();
                typing.style.display = 'none';
                
                try {
                    const data = JSON.parse(textOutput);
                    if(data.reply) {
                        addOrdersAIMsg('bot', data.reply);
                        ordersChatHistory.push({ role: 'user', content: msg });
                        ordersChatHistory.push({ role: 'ai', content: data.reply });
                    } else {
                        addOrdersAIMsg('bot', 'AI connection issue.');
                    }
                } catch (e) {
                    addOrdersAIMsg('bot', 'Error processing AI response.');
                }
            } catch(e) {
                typing.style.display = 'none';
                addOrdersAIMsg('bot', 'Connection error.');
            }
        }

        function addOrdersAIMsg(sender, text) {
            const body = document.getElementById('aiOrdersBody');
            const div = document.createElement('div');
            div.className = `ai-msg ${sender}`;
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            div.innerHTML = `<div class="ai-bubble">${formattedText}</div>`;
            body.appendChild(div);
            scrollOrdersAIBottom();
        }

        function scrollOrdersAIBottom() {
            const body = document.getElementById('aiOrdersBody');
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>
</html>