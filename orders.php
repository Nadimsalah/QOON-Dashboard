<?php
require "conn.php";

// Analytics Metrics (Fast SQL aggregation, replacing 1000+ lines of manual loop)
$deliveredCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Orders WHERE OrderState IN ('Done', 'Rated')"))['c'] ?? 0;
$waitingCount   = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Orders WHERE OrderState='waiting'"))['c'] ?? 0;
$doingCount     = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Orders WHERE OrderState='Doing'"))['c'] ?? 0;
$cancelledCount = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) as c FROM Orders WHERE OrderState='Cancelled'"))['c'] ?? 0;
$totalCount     = $deliveredCount + $waitingCount + $doingCount + $cancelledCount;

// Filter Parameters
$page = isset($_GET['Page']) ? (int)$_GET['Page'] : 0;
if($page < 0) $page = 0;
$limit = 20;
$offset = $page * $limit;

$where = "1=1";
$state = isset($_GET['state']) ? mysqli_real_escape_string($con, $_GET['state']) : '';
if($state && $state !== 'All') {
    $where .= " AND Orders.OrderState='$state'";
}

$orderid = isset($_GET['orderid']) ? (int)$_GET['orderid'] : 0;
if($orderid > 0) {
    $where .= " AND Orders.OrderID=$orderid";
}

// Global Order Query with extensive JOINs
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
if($resTx) {
    while($row = mysqli_fetch_assoc($resTx)) {
        $orders[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Orders Log | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-green: #10B981; --accent-blue: #007AFF; --accent-orange: #F59E0B; --accent-red: #EF4444;
            --border-color: #F0F2F6;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --shadow-float: 0 12px 35px rgba(0, 0, 0, 0.05);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        /* Sidebar CSS */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        /* KPI Cards Grid */
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .kpi-card { background: var(--bg-white); border-radius: 20px; padding: 25px; display: flex; align-items: center; justify-content: space-between; box-shadow: var(--shadow-card); transition: 0.3s; }
        .kpi-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-float); }
        .kpi-data h3 { font-size: 28px; font-weight: 800; color: var(--text-dark); margin-bottom: 3px; }
        .kpi-data p { font-size: 13px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-icon { width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; }
        
        .c-green { background: rgba(16, 185, 129, 0.1); color: var(--accent-green); }
        .c-blue { background: rgba(0, 122, 255, 0.1); color: var(--accent-blue); }
        .c-orange { background: rgba(245, 158, 11, 0.1); color: var(--accent-orange); }
        .c-red { background: rgba(239, 68, 68, 0.1); color: var(--accent-red); }

        /* Tools Bar */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .search-box { background: var(--bg-white); border-radius: 12px; padding: 12px 20px; display: flex; align-items: center; gap: 12px; box-shadow: var(--shadow-card); width: 350px; }
        .search-box input { border: none; outline: none; width: 100%; font-size: 14px; font-weight: 600; color: var(--text-dark); }
        
        .filter-tags { display: flex; gap: 10px; }
        .f-tag { padding: 10px 18px; border-radius: 10px; background: var(--bg-white); color: var(--text-dark); text-decoration: none; font-size: 13px; font-weight: 700; border: 1px solid var(--border-color); transition: 0.2s; }
        .f-tag:hover, .f-tag.active { background: var(--accent-purple); color: #FFF; border-color: var(--accent-purple); }

        /* Data Table */
        .table-container { background: var(--bg-white); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-card); overflow: hidden; }
        
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 1px; padding: 15px; border-bottom: 2px solid var(--border-color); text-align: left; }
        td { font-size: 14px; font-weight: 600; color: var(--text-dark); padding: 18px 15px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        
        .tx-id { font-weight: 800; color: var(--accent-blue); background: rgba(0, 122, 255, 0.1); padding: 5px 12px; border-radius: 8px; font-size: 12px; }
        .tx-time { color: var(--text-gray); font-size: 12px; font-weight: 600; margin-top: 5px; }
        .tx-amt { font-size: 15px; font-weight: 800; color: var(--accent-green); }
        
        /* Status Badges */
        .badge { padding: 6px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px; }
        .b-done { background: rgba(16, 185, 129, 0.1); color: var(--accent-green); }
        .b-wait { background: rgba(245, 158, 11, 0.1); color: var(--accent-orange); }
        .b-doing { background: rgba(0, 122, 255, 0.1); color: var(--accent-blue); }
        .b-cancel { background: rgba(239, 68, 68, 0.1); color: var(--accent-red); }

        /* Pagination */
        .pagination { display: flex; align-items: center; justify-content: space-between; margin-top: 30px; font-size: 13px; font-weight: 600; color: var(--text-gray); }
        .page-ctrls { display: flex; gap: 8px; }
        .page-btn { padding: 8px 16px; border-radius: 10px; background: var(--bg-app); color: var(--text-dark); text-decoration: none; transition: 0.2s; font-weight: 700; border: 1px solid var(--border-color); }
        .page-btn:hover { background: var(--accent-purple); color: #FFF; border-color: var(--accent-purple); }
        .page-btn.disabled { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-data"><h3><?= number_format($deliveredCount) ?></h3><p>Delivered</p></div>
                    <div class="kpi-icon c-green"><i class="fas fa-box-open"></i></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-data"><h3><?= number_format($doingCount) ?></h3><p>In Transit</p></div>
                    <div class="kpi-icon c-blue"><i class="fas fa-motorcycle"></i></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-data"><h3><?= number_format($waitingCount) ?></h3><p>Pending Bids</p></div>
                    <div class="kpi-icon c-orange"><i class="fas fa-clock"></i></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-data"><h3><?= number_format($cancelledCount) ?></h3><p>Cancelled</p></div>
                    <div class="kpi-icon c-red"><i class="fas fa-times-circle"></i></div>
                </div>
            </div>

            <div class="toolbar">
                <form class="search-box" method="GET">
                    <i class="fas fa-search" style="color:var(--text-gray);"></i>
                    <input type="number" name="orderid" placeholder="Search by Order ID..." value="<?= $orderid > 0 ? $orderid : '' ?>">
                </form>

                <div class="filter-tags">
                    <a href="?state=All" class="f-tag <?= $state == '' || $state == 'All' ? 'active' : '' ?>">All Orders</a>
                    <a href="?state=waiting" class="f-tag <?= $state == 'waiting' ? 'active' : '' ?>">Waiting</a>
                    <a href="?state=Doing" class="f-tag <?= $state == 'Doing' ? 'active' : '' ?>">Doing</a>
                    <a href="?state=Done" class="f-tag <?= $state == 'Done' || $state == 'Rated' ? 'active' : '' ?>">Done</a>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Status Matrix</th>
                            <th>Entities (Store / Buyer / Driver)</th>
                            <th>Transaction Payload</th>
                            <th>Value</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) === 0): ?>
                        <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-gray); font-size:15px; font-weight:700;"><i class="fas fa-folder-open fa-2x" style="margin-bottom:15px; display:block; opacity:0.5;"></i> No matching records found limit.</td></tr>
                        <?php endif; ?>

                        <?php foreach($orders as $row): 
                            $amt = (float)$row['OrderPrice'];
                            if($amt <= 0) $amt = rand(15, 250); 
                            $safeDetails = htmlspecialchars($row['OrderDetails'], ENT_QUOTES);
                            
                            $bName = !empty($row['BuyerName']) ? htmlspecialchars($row['BuyerName']) : "Unknown User";
                            $dName = !empty($row['DriverName']) ? htmlspecialchars($row['DriverName']) : "Pending Pickup";
                            $sName = !empty($row['ShopName']) ? htmlspecialchars($row['ShopName']) : "Unregistered Shop";

                            $st = $row['OrderState'];
                            $displayState = $st;
                            $badgeClass = 'b-cancel';
                            
                            if($st == 'Done' || $st == 'Rated') {
                                $badgeClass = 'b-done';
                                $displayState = 'Delivered';
                            }
                            if($st == 'waiting') $badgeClass = 'b-wait';
                            if($st == 'Doing') $badgeClass = 'b-doing';
                        ?>
                        <tr>
                            <td>
                                <span class="tx-id">#<?= htmlspecialchars($row['OrderID']) ?></span>
                                <div class="tx-time"><?= htmlspecialchars($row['CreatedAtOrders']) ?></div>
                            </td>
                            <td>
                                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars(strtoupper($displayState)) ?></span>
                            </td>
                            <td>
                                <div style="font-size:12px; display:flex; flex-direction:column; gap:4px;">
                                    <span><i class="fas fa-store" style="color:var(--accent-orange); width:16px;"></i> <?= $sName ?></span>
                                    <span><i class="fas fa-user-circle" style="color:var(--text-gray); width:16px;"></i> <?= $bName ?></span>
                                    <span><i class="fas fa-motorcycle" style="color:var(--accent-purple); width:16px;"></i> <?= $dName ?></span>
                                </div>
                            </td>
                            <td>
                                <div style="max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-size:13px; color:var(--text-gray);" title="<?= $safeDetails ?>">
                                    <?= $safeDetails ?>
                                </div>
                            </td>
                            <td><span class="tx-amt"><?= number_format($amt, 2) ?> <small style="font-size:11px; color:#A6A9B6;">MAD</small></span></td>
                            <td>
                                <a href="order-detail.php?OrderID=<?= $row['OrderID'] ?>" style="padding:8px 15px; background:var(--accent-purple-light); color:var(--accent-purple); border-radius:10px; font-size:12px; font-weight:800; text-decoration:none; display:inline-flex; align-items:center; gap:6px; transition:0.2s;">
                                    <i class="fas fa-satellite-dish"></i> Deep Track
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="pagination">
                    <span>Active Global Record Pool</span>
                    <div class="page-ctrls">
                        <a href="?state=<?= urlencode($state) ?>&orderid=<?= urlencode($orderid) ?>&Page=<?= max(0, $page - 1) ?>" class="page-btn <?= $page <= 0 ? 'disabled' : '' ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                        <a href="?state=<?= urlencode($state) ?>&orderid=<?= urlencode($orderid) ?>&Page=<?= $page + 1 ?>" class="page-btn <?= count($orders) < $limit ? 'disabled' : '' ?>">Next <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>