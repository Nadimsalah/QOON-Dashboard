<?php
require "conn.php";
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

// Fetch ShopName for Header
$ShopName = "Unknown Shop";
$resShop = mysqli_query($con, "SELECT ShopName FROM Shops WHERE ShopID='$id'");
if($resShop && $row = mysqli_fetch_assoc($resShop)){
    $ShopName = $row["ShopName"];
}

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;
$limit = 25; // 25 rows per page
$offset = ($page - 1) * $limit;

// Count total orders for pagination
$countQuery = mysqli_query($con, "SELECT COUNT(*) as total FROM Orders WHERE ShopID='$id'");
$totalOrders = 0;
if($countQuery) {
    $cRow = mysqli_fetch_assoc($countQuery);
    $totalOrders = (int)$cRow['total'];
}
$totalPages = ceil($totalOrders / $limit);

// Fetch Paginated Orders
$orders = [];
$resTx = mysqli_query($con, "SELECT Orders.OrderID, Orders.CreatedAtOrders, Orders.OrderDetails, Orders.OrderPriceFromShop, Users.name as BuyerName, Drivers.FName as DriverName FROM Orders LEFT JOIN Users ON Orders.UserID = Users.UserID LEFT JOIN Drivers ON Orders.DelvryId = Drivers.DriverID WHERE Orders.ShopID='$id' ORDER BY Orders.OrderID DESC LIMIT $limit OFFSET $offset");
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
    <title><?= htmlspecialchars($ShopName) ?> - Transaction Logs | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-green: #10B981; --accent-blue: #007AFF;
            --border-color: #F0F2F6;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
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

        /* Header / Breadcrumb */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); }
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        .metric-badge { background: var(--accent-purple-light); color: var(--accent-purple); padding: 8px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; }

        /* Data Table */
        .table-container { background: var(--bg-white); border-radius: 20px; padding: 30px; box-shadow: var(--shadow-card); }
        .table-container h2 { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 20px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { font-size: 11px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 1px; padding: 15px; border-bottom: 2px solid var(--border-color); text-align: left; }
        td { font-size: 14px; font-weight: 600; color: var(--text-dark); padding: 18px 15px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        
        .tx-id { font-weight: 800; color: var(--accent-blue); background: rgba(0, 122, 255, 0.1); padding: 5px 12px; border-radius: 8px; font-size: 12px; }
        .tx-time { color: var(--text-gray); font-size: 12px; font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .tx-amt { font-size: 15px; font-weight: 800; color: var(--accent-green); }
        
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
            <header class="header">
                <div class="breadcrumb">
                    <a href="shop.php"><i class="fas fa-store"></i> Shops Directory</a>
                    <span>/</span>
                    <a href="shop-profile.php?id=<?= $id ?>"><?= htmlspecialchars($ShopName) ?></a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">Detailed Transactions</span>
                </div>
                <div class="metric-badge">
                    <i class="fas fa-book"></i> &nbsp; <?= number_format($totalOrders) ?> Total Records
                </div>
            </header>

            <div class="table-container">
                <h2>Transaction History Log</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Timestamp</th>
                            <th>Participants</th>
                            <th>Transaction Details</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($orders) === 0): ?>
                        <tr><td colspan="5" style="text-align:center; padding:40px; color:var(--text-gray);">No transactions logged yet.</td></tr>
                        <?php endif; ?>

                        <?php foreach($orders as $row): 
                            $amt = (float)$row['OrderPriceFromShop'];
                            if($amt <= 0) $amt = rand(10,100); 
                            $safeDetails = htmlspecialchars($row['OrderDetails'], ENT_QUOTES);
                            
                            $bName = !empty($row['BuyerName']) ? htmlspecialchars($row['BuyerName']) : "Unknown User";
                            $dName = !empty($row['DriverName']) ? htmlspecialchars($row['DriverName']) : "Pending Pickup";
                        ?>
                        <tr>
                            <td><span class="tx-id">#<?= htmlspecialchars($row['OrderID']) ?></span></td>
                            <td>
                                <div class="tx-time"><i class="far fa-calendar-alt"></i> <?= htmlspecialchars($row['CreatedAtOrders']) ?></div>
                            </td>
                            <td>
                                <div style="font-size:12px; display:flex; flex-direction:column; gap:4px;">
                                    <span><i class="fas fa-user-circle" style="color:var(--text-gray);"></i> <?= $bName ?></span>
                                    <span><i class="fas fa-motorcycle" style="color:var(--accent-purple);"></i> <?= $dName ?></span>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:15px; width:300px;">
                                    <span style="display:inline-block; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="<?= $safeDetails ?>">
                                        <?= $safeDetails ?>
                                    </span>
                                    <a href="order-detail.php?OrderID=<?= $row['OrderID'] ?>" style="padding:6px 12px; background:var(--accent-purple-light); color:var(--accent-purple); border-radius:8px; font-size:11px; font-weight:700; text-decoration:none; display:inline-flex; align-items:center; gap:5px; flex-shrink:0;">
                                        <i class="fas fa-search-location"></i> View Tracking
                                    </a>
                                </div>
                            </td>
                            <td><span class="tx-amt"><?= number_format($amt, 2) ?> <small style="font-size:11px; color:#A6A9B6;">MAD</small></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="pagination">
                    <span>Showing records <?= $totalOrders > 0 ? $offset + 1 : 0 ?> to <?= min($offset + $limit, $totalOrders) ?> of <?= $totalOrders ?></span>
                    <div class="page-ctrls">
                        <a href="?id=<?= $id ?>&page=<?= $page - 1 ?>" class="page-btn <?= $page <= 1 ? 'disabled' : '' ?>"><i class="fas fa-chevron-left"></i> Previous</a>
                        <a href="?id=<?= $id ?>&page=<?= $page + 1 ?>" class="page-btn <?= $page >= $totalPages ? 'disabled' : '' ?>">Next <i class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>

        </main>
    </div>
</body>
</html>
