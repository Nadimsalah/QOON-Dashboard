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
</body>
</html>