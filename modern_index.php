<?php
require_once "includes/layout_header.php";
include "index_logic.php";
?>

<div class="container-fluid p-0">
    <div class="row g-4 mb-4">
        <!-- Metric Cards -->
        <div class="col-md-3">
            <div class="modern-card">
                <div class="stat-widget">
                    <div class="stat-icon bg-primary-light text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Users</h3>
                        <div class="value skeleton" id="userCount"><?= number_format($UserNumber) ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="modern-card">
                <div class="stat-widget">
                    <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Orders</h3>
                        <div class="value skeleton" id="orderCount"><?= number_format($OrdersNumber) ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="modern-card">
                <div class="stat-widget">
                    <div class="stat-icon" style="background: #fffbeb; color: #f59e0b;">
                        <i class="fas fa-store"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Shops Count</h3>
                        <div class="value skeleton" id="shopCount"><?= number_format($ShopsNumber) ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="modern-card">
                <div class="stat-widget">
                    <div class="stat-icon" style="background: #fef2f2; color: #ef4444;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Revenue</h3>
                        <?php
                        $moneyRes = mysqli_query($con,"SELECT TotalIncome FROM Money LIMIT 1");
                        $m = mysqli_fetch_assoc($moneyRes);
                        ?>
                        <div class="value skeleton" id="revenueCount"><?= number_format($m['TotalIncome']) ?> MAD</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="modern-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold h5 mb-0">Platform Activity</h4>
                    <div class="btn-group btn-group-sm">
                        <a href="index.php?Type=DAY" class="btn btn-outline-secondary <?= $Type == 'DAY' ? 'active' : '' ?>">Daily</a>
                        <a href="index.php?Type=WEEK" class="btn btn-outline-secondary <?= $Type == 'WEEK' ? 'active' : '' ?>">Weekly</a>
                        <a href="index.php?Type=MONTH" class="btn btn-outline-secondary <?= $Type == 'MONTH' ? 'active' : '' ?>">Monthly</a>
                    </div>
                </div>
                <div style="height: 400px;">
                    <canvas id="canvas"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="modern-card p-4 h-100">
                <h4 class="fw-bold h5 mb-4">Latest Insights</h4>
                <div class="insight-list">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 bg-light p-3">
                            <i class="fas fa-chart-pie text-primary"></i>
                        </div>
                        <div>
                            <p class="mb-0 fw-bold small">Traffic surge</p>
                            <p class="mb-0 x-small text-muted">12% increase from yesterday</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 bg-light p-3">
                            <i class="fas fa-user-plus text-success"></i>
                        </div>
                        <div>
                            <p class="mb-0 fw-bold small">New registrations</p>
                            <p class="mb-0 x-small text-muted">24 new users joined today</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once "includes/index_chart_js.php";
require_once "includes/layout_footer.php"; 
?>