<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

// 1. Total Platform Balance Logic
$resMoney = mysqli_query($con, "SELECT TotalIncome FROM Money");
$TotalIncome = mysqli_fetch_assoc($resMoney)['TotalIncome'] ?? 0;

$resTotalOrder = mysqli_query($con, "SELECT SUM(OrderPrice) as total FROM Orders");
$TotalOrderPrice = mysqli_fetch_assoc($resTotalOrder)['total'] ?? 0;

// 2. Aggregate Last 30 Days of Revenue Performance (For Line Chart)
$thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
$salesDataQuery = mysqli_query($con, "
    SELECT DATE(CreatedAtOrders) as date, SUM(OrderPriceFromShop) as daily_profit, SUM(OrderPrice) as daily_gross 
    FROM Orders 
    WHERE OrderState IN ('Rated', 'Done') AND CreatedAtOrders >= '$thirtyDaysAgo'
    GROUP BY DATE(CreatedAtOrders)
    ORDER BY date ASC
");

$labels_30d = [];
$profit_30d = [];
$gross_30d  = [];

if($salesDataQuery) {
    while($row = mysqli_fetch_assoc($salesDataQuery)) {
        $labels_30d[] = date('M d', strtotime($row['date']));
        $profit_30d[] = $row['daily_profit'] ?? 0;
        $gross_30d[]  = $row['daily_gross'] ?? 0;
    }
}

// 3. Category Sector Breakdown (For Doughnut Chart)
$categoryQuery = mysqli_query($con, "
    SELECT c.EnglishCategory AS cat_name, SUM(o.OrderPriceFromShop) AS cat_profit 
    FROM Orders o 
    JOIN Shops s ON o.ShopID = s.ShopID 
    JOIN Categories c ON s.CategoryID = c.CategoryID 
    WHERE o.OrderState IN ('Rated', 'Done')
    GROUP BY c.EnglishCategory
    ORDER BY cat_profit DESC
    LIMIT 6
");

$cat_labels = [];
$cat_profits = [];
if($categoryQuery) {
    while($row = mysqli_fetch_assoc($categoryQuery)) {
        $cat_labels[] = $row['cat_name'];
        $cat_profits[] = $row['cat_profit'];
    }
}

// 4. Financial Health Flow Matrix (Bar Chart)
$qEradDrivers = mysqli_query($con, "SELECT SUM(Money) as v FROM EradTrans WHERE PayOwnerType='DRIVER'");
$eradDrivers = mysqli_fetch_assoc($qEradDrivers)['v'] ?? 0;

$qEradShops = mysqli_query($con, "SELECT SUM(Money) as v FROM EradTrans WHERE PayOwnerType!='DRIVER'");
$eradShops = mysqli_fetch_assoc($qEradShops)['v'] ?? 0;

$qSales = mysqli_query($con, "SELECT SUM(CutPers) as v FROM SlasesRevTransaction");
$salesCut = mysqli_fetch_assoc($qSales)['v'] ?? 0;

$qDelivery = mysqli_query($con, "SELECT SUM(Money) as v FROM DriverRevTransaction");
$deliveryCut = mysqli_fetch_assoc($qDelivery)['v'] ?? 0;

$flow_labels = ['Seller Subs', 'Driver Subs', 'Sales Comm', 'Deliv Comm'];
$flow_data = [$eradShops, $eradDrivers, $salesCut, $deliveryCut];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Intelligence & Telemetry | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js Engine -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink: 0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        .panel-grid-top { display: grid; grid-template-columns: 2.5fr 1fr; gap: 30px; margin-bottom: 30px; flex-shrink:0; }
        .panel-grid-bot { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; flex-shrink:0; margin-bottom: 30px;}

        .chart-card { background: var(--bg-white); border-radius: 24px; padding: 30px; box-shadow: var(--shadow-card); border: 1px solid var(--border-color); display:flex; flex-direction:column; }
        .ch-title { font-size: 16px; font-weight: 800; color: var(--text-dark); margin-bottom: 5px; display:flex; align-items:center; gap:10px; }
        .ch-sub { font-size: 13px; font-weight: 600; color: var(--text-gray); margin-bottom: 25px; }

        .kpi-board { background: linear-gradient(135deg, var(--accent-purple), #4A2BBF); border-radius: 24px; padding: 40px 30px; color: #FFF; box-shadow: var(--shadow-float); position:relative; overflow:hidden; }
        .kpi-board::after { content:'\f200'; font-family:'Font Awesome 5 Free'; font-weight:900; position:absolute; bottom:-20px; right:-20px; font-size:150px; opacity:0.1; }
        .kpi-item { margin-bottom: 30px; position:relative; z-index:2;}
        .kpi-item:last-child { margin-bottom: 0; }
        .kpi-item p { font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; margin-bottom: 5px; }
        .kpi-item h2 { font-size: 36px; font-weight: 800; }
        .kpi-item h2 span { font-size: 16px; font-weight: 700; opacity: 0.7; }
        
        canvas { width: 100% !important; flex:1; max-height:350px; }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="wallet.php"><i class="fas fa-wallet"></i> Financial Overview</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">Financial Intelligence Board</span>
                </div>
                
                <a href="pdf_export_fallback.php" target="_blank" style="background:var(--accent-purple); color:#FFF; padding:10px 20px; border-radius:12px; font-size:13px; font-weight:700; text-decoration:none; display:flex; gap:8px; align-items:center;">
                    <i class="fas fa-file-pdf"></i> Generate Master Ledger
                </a>
            </header>

            <div class="panel-grid-top">
                
                <!-- Main Growth Time-Series -->
                <div class="chart-card">
                    <h2 class="ch-title"><i class="fas fa-chart-area" style="color:var(--accent-blue);"></i> Platform Revenue Velocity</h2>
                    <p class="ch-sub">Tracking gross merchant sales vs. realized platform net cuts over 30 days.</p>
                    <div style="position:relative; flex:1; width:100%;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>

                <!-- KPI Quick Board -->
                <div class="kpi-board">
                    <div class="kpi-item">
                        <p>Total Life-Time Revenue</p>
                        <h2><?= number_format($TotalIncome, 2) ?> <span>MAD</span></h2>
                    </div>
                    <div class="kpi-item" style="border-top:1px solid rgba(255,255,255,0.2); padding-top:30px;">
                        <p>Store Gross Market Value</p>
                        <h2><?= number_format($TotalOrderPrice, 2) ?> <span>MAD</span></h2>
                    </div>
                </div>

            </div>

            <div class="panel-grid-bot">
                <!-- Sector Dominance Doughnut -->
                <div class="chart-card">
                    <h2 class="ch-title"><i class="fas fa-chart-pie" style="color:var(--accent-orange);"></i> Market Sector Dominance</h2>
                    <p class="ch-sub">Top 6 performing categories by platform profitability margins.</p>
                    <div style="position:relative; flex:1; width:100%; display:flex; justify-content:center;">
                        <canvas id="sectorChart" style="max-height:280px;"></canvas>
                    </div>
                </div>

                <!-- Revenue Flow Array Bar -->
                <div class="chart-card">
                    <h2 class="ch-title"><i class="fas fa-chart-bar" style="color:var(--accent-green);"></i> Revenue Stream Distribution</h2>
                    <p class="ch-sub">Identifying core fiscal arteries funding platform operations.</p>
                    <div style="position:relative; flex:1; width:100%;">
                        <canvas id="flowChart"></canvas>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Chart Configuration Script -->
    <script>
        // Data Payloads
        const labels30d = <?= json_encode($labels_30d) ?>;
        const profit30d = <?= json_encode($profit_30d) ?>;
        const gross30d  = <?= json_encode($gross_30d) ?>;

        const catLabels = <?= json_encode($cat_labels) ?>;
        const catProfits = <?= json_encode($cat_profits) ?>;

        const flowLabels = <?= json_encode($flow_labels) ?>;
        const flowData = <?= json_encode($flow_data) ?>;

        // Custom Gradient Logic
        let ctxGrow = document.getElementById('growthChart').getContext('2d');
        let gradGross = ctxGrow.createLinearGradient(0, 0, 0, 400);
        gradGross.addColorStop(0, 'rgba(0, 122, 255, 0.2)');
        gradGross.addColorStop(1, 'rgba(0, 122, 255, 0)');

        let gradProfit = ctxGrow.createLinearGradient(0, 0, 0, 400);
        gradProfit.addColorStop(0, 'rgba(16, 185, 129, 0.4)');
        gradProfit.addColorStop(1, 'rgba(16, 185, 129, 0)');

        // 1. Time Series Growth Chart
        new Chart(ctxGrow, {
            type: 'line',
            data: {
                labels: labels30d,
                datasets: [
                    {
                        label: 'Gross Network Sales',
                        data: gross30d,
                        borderColor: '#007AFF',
                        backgroundColor: gradGross,
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0
                    },
                    {
                        label: 'Platform Net Retained',
                        data: profit30d,
                        borderColor: '#10B981',
                        backgroundColor: gradProfit,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 3,
                        pointBackgroundColor: '#FFF',
                        pointBorderColor: '#10B981'
                    }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: {family:'Inter', weight:'bold', size:12} } } },
                scales: {
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: {family:'Inter'} } },
                    y: { border: {dash: [5, 5]}, grid: { color: '#F0F2F6' }, ticks: { callback: function(value){return value+' MAD'}, font: {family:'Inter'} } }
                },
                interaction: { intersect: false, mode: 'index' }
            }
        });

        // 2. Sector Dominance Doughnut
        new Chart(document.getElementById('sectorChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catProfits,
                    backgroundColor: ['#623CEA', '#007AFF', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'],
                    borderWidth: 4,
                    borderColor: '#FFF',
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '75%',
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 10, font: {family:'Inter', weight:'600'}, padding:20 } },
                    tooltip: { callbacks: { label: function(ctx) { return ' ' + ctx.raw.toLocaleString() + ' MAD'; } } }
                }
            }
        });

        // 3. Revenue Flow Bar
        new Chart(document.getElementById('flowChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: flowLabels,
                datasets: [{
                    label: 'Generated Revenue',
                    data: flowData,
                    backgroundColor: ['rgba(98, 60, 234, 0.8)', 'rgba(0, 122, 255, 0.8)', 'rgba(16, 185, 129, 0.8)', 'rgba(245, 158, 11, 0.8)'],
                    borderRadius: 8,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: {family:'Inter', weight:'bold'} } },
                    y: { border: {dash: [5, 5]}, grid: { color: '#F0F2F6' }, ticks: { callback: function(value){return value+' MAD'}, font: {family:'Inter'} } }
                }
            }
        });

    </script>
</body>
</html>