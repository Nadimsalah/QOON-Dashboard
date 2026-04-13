<?php
require "conn.php";

// 1. Date Range & City Logic (Match Dashboard)
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('this week Monday'));
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$cityID = $_GET['city_id'] ?? '';

$dateFilter = " AND CreatedAtUser BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59'";
$cityFilter = $cityID ? " AND CityID = '$cityID'" : "";

// 2. Filter Type Logic
$type = $_GET['type'] ?? 'all';
$typeFilter = "";
$pageTitle = "Total User Directory";
$subtitle = "Viewing all registered users";

switch ($type) {
    case 'new':
        $typeFilter = $dateFilter;
        $pageTitle = "New Registrations";
        $subtitle = "Users registered from " . date('M d', strtotime($startDate)) . " to " . date('M d', strtotime($endDate));
        break;
    case 'android':
        $typeFilter = " AND UserType='ANDROID'";
        $pageTitle = "Android Platform Users";
        $subtitle = "Viewing all users active on Android devices";
        break;
    case 'ios':
        $typeFilter = " AND UserType!='ANDROID'";
        $pageTitle = "iOS Platform Users";
        $subtitle = "Viewing all users active on iOS devices";
        break;
    case 'all':
    default:
        $typeFilter = ""; 
        break;
}

// Search
$userNameSearch = $_GET['UserName'] ?? '';
$searchFilter = $userNameSearch ? " AND name LIKE '%$userNameSearch%'" : "";

// Count total matching rows for pagination
$count_query = "SELECT COUNT(*) as total FROM Users WHERE name != '' $typeFilter $cityFilter $searchFilter";
$count_res = mysqli_query($con, $count_query);
$totalRows = mysqli_fetch_assoc($count_res)['total'] ?? 0;

// Pagination
$page = max(0, (int)($_GET['Page'] ?? 0));
$limit = 15;
$offset = $page * $limit;
$totalPages = ceil($totalRows / $limit);

$query = "SELECT * FROM Users WHERE name != '' $typeFilter $cityFilter $searchFilter ORDER BY UserID DESC LIMIT $offset, $limit";
$users_res = mysqli_query($con, $query);

$cities_res = mysqli_query($con, "SELECT CityID, CityName FROM Cities WHERE Status=1");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="fontawesome-kit-5/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #EFEAF8; --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-orange: #FF8A4C; --accent-blue: #007AFF;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --border-color: #F0F2F6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); height: 100vh; display: flex; overflow: hidden; }

        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }
        
        /* Sidebar Styles */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item:hover:not(.active) { color: var(--text-dark); background: #F8F9FB; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }
        
        .main-panel { flex: 1; padding: 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; background: var(--bg-app); }
        
        /* Unified Header */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 35px; }
        .search { display: flex; align-items: center; background: #EBEDF3; border-radius: 20px; padding: 12px 20px; width: 340px; gap: 12px; transition: 0.3s; }
        .search:focus-within { background: #FFF; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .search input { border: none; background: none; outline: none; width: 100%; color: var(--text-dark); font-size: 14px; font-weight: 500;}
        .search input::placeholder { color: var(--text-gray); }
        .search i { color: var(--text-gray); }

        .header-actions { display: flex; align-items: center; gap: 20px; font-size: 14px; font-weight: 500; }
        
        .action-combo { display: flex; align-items: center; gap: 8px; }
        .action-combo .label { color: var(--text-gray); }
        
        .back-btn { display: flex; align-items: center; gap: 10px; padding: 12px 20px; border-radius: 14px; background: var(--bg-white); color: var(--text-dark); text-decoration: none; font-weight: 700; font-size: 14px; box-shadow: var(--shadow-card); transition: 0.2s; border: 1px solid var(--border-color); }
        .back-btn:hover { background: #F8F9FB; transform: translateY(-2px); box-shadow: 0 12px 25px rgba(0,0,0,0.05); color: var(--accent-purple); }

        .profile { display: flex; align-items: center; gap: 10px; cursor: pointer; padding-left: 10px;}
        .profile img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }

        /* Page Layout */
        .page-header { margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; }
        .p-title { font-size: 24px; font-weight: 800; color: var(--text-dark); margin-bottom: 5px; letter-spacing: -0.5px;}
        .p-sub { color: var(--text-gray); font-size: 14px; font-weight: 500; }

        /* Card Table */
        .table-card { background: var(--bg-white); border-radius: 24px; padding: 0; box-shadow: var(--shadow-card); border: 1px solid rgba(255,255,255,0.8); overflow: hidden;}
        
        table { width: 100%; border-collapse: separate; border-spacing: 0; }
        th { padding: 18px 30px; text-align: left; background: #F8F9FA; color: var(--text-gray); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid var(--border-color);}
        td { padding: 18px 30px; border-bottom: 1px solid var(--border-color); font-size: 14px; color: var(--text-dark); transition: 0.2s; background: var(--bg-white); }
        tr:hover td { background: #FDFDFE; cursor: default;}
        
        /* User Profile Col */
        .user-info { display: flex; align-items: center; gap: 16px; }
        .u-avatar { width: 44px; height: 44px; border-radius: 14px; object-fit: cover; box-shadow: 0 6px 14px rgba(0,0,0,0.06); background: linear-gradient(135deg, var(--accent-purple), #FFC000); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 16px; }
        
        /* Badges */
        .platform-badge { padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 8px; border: 1px solid transparent;}
        .platform-android { background: rgba(0,122,255,0.05); color: var(--accent-blue); border-color: rgba(0,122,255,0.1); }
        .platform-ios { background: rgba(255,138,76,0.05); color: var(--accent-orange); border-color: rgba(255,138,76,0.1); }
        
        .balance-pill { background: rgba(98, 60, 234, 0.05); color: var(--accent-purple); padding: 8px 16px; border-radius: 12px; font-weight: 800; display: inline-block; border: 1px solid rgba(98, 60, 234, 0.1); }
        
        /* Actions */
        .action-btn { display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; background: #F8F9FA; color: var(--text-gray); border-radius: 12px; text-decoration: none; transition: 0.2s; font-size: 16px; }
        .action-btn:hover { background: var(--accent-purple-light); color: var(--accent-purple); transform: scale(1.08); }

        /* Pagination Footer */
        .table-footer { padding: 25px 30px; display: flex; justify-content: space-between; align-items: center; background: var(--bg-white); }
        .results-count { font-weight: 600; color: var(--text-gray); font-size: 13px; }
        
        .pagination { display: flex; gap: 8px; align-items: center; }
        .page-btn { display: flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-white); color: var(--text-dark); text-decoration: none; font-weight: 700; transition: 0.2s; font-size: 13px;}
        .page-btn:hover:not(.disabled) { background: #F8F9FB; border-color: #D1D5DF; }
        .page-btn.disabled { opacity: 0.4; cursor: not-allowed; }
        .page-btn.active { background: var(--accent-purple); color: #FFF; border-color: var(--accent-purple); box-shadow: 0 6px 15px rgba(98, 60, 234, 0.25); }

        .empty-state { padding: 60px 20px; display: flex; flex-direction: column; align-items: center; gap: 15px; color: var(--text-gray); text-align: center; }
        .empty-state i { font-size: 48px; color: #EBECEF; }
        /* ----- MOBILE RESPONSIVENESS ----- */
        @media (max-width: 991px) {
            .main-panel { padding: 15px; }
            .header { flex-direction: column; gap: 15px; align-items: stretch; margin-bottom: 25px; }
            .search { width: 100%; box-sizing: border-box; }
            .header-actions { justify-content: space-between; flex-wrap: wrap; gap: 10px; }
            .page-header { flex-direction: column; align-items: flex-start; gap: 15px; }
            .page-header .action-combo { width: 100%; margin-top: 10px; }
            .page-header select { width: 100%; }
            
            /* Responsive Swipeable Table Instead of Breaking Layout */
            .table-card { overflow-x: auto; -webkit-overflow-scrolling: touch; border-radius: 16px; }
            table { width: 800px; /* Force bounds so it smoothly scrolls instead of squishing text */ }
            .table-footer { flex-direction: column; gap: 20px; text-align: center; border-top: 1px solid var(--border-color); }
        }

        /* Shimmer Loading */
        @keyframes shimmer { 0% { background-position: -1000px 0; } 100% { background-position: 1000px 0; } }
        .shimmer-bg {
            animation: shimmer 2.5s infinite linear;
            background: linear-gradient(to right, #F8F9FA 4%, #F0F2F6 25%, #F8F9FA 36%);
            background-size: 1000px 100%;
        }
        .s-box { background: #F0F2F6; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>
        
        <main class="main-panel">
            <header class="header">
                <form action="user_list.php" method="GET" class="search">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($startDate) ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($endDate) ?>">
                    <input type="hidden" name="city_id" value="<?= htmlspecialchars($cityID) ?>">
                    <i class="fas fa-search"></i>
                    <input type="text" name="UserName" placeholder="Search users by name..." value="<?= htmlspecialchars($userNameSearch) ?>">
                </form>
                <div class="header-actions">
                    <a href="user.php?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>" class="back-btn">
                        <i class="fas fa-arrow-left" style="color:var(--text-gray);"></i> Dashboard Analytics
                    </a>
                    
                    <div style="width: 1px; height: 30px; background: var(--border-color); margin: 0 5px;"></div>
                    

                </div>
            </header>

            <div class="page-header">
                <div>
                    <h1 class="p-title"><?= $pageTitle ?></h1>
                    <div class="p-sub"><?= $subtitle ?> (<?= number_format($totalRows) ?> total)</div>
                </div>
                
                <!-- Secondary Filter - City Override -->
                <div class="action-combo">
                    <span class="label">Region:</span>
                    <select onchange="let url = new URL(window.location.href); url.searchParams.set('city_id', this.value); url.searchParams.set('Page', 0); window.location.href = url.href;" style="padding:10px 15px; border-radius:12px; border:1px solid var(--border-color); outline:none; font-weight:700; color:var(--text-dark); background:var(--bg-white); cursor:pointer; font-size:13px; box-shadow:0 2px 5px rgba(0,0,0,0.02);">
                        <option value="">Global (All Regions)</option>
                        <?php while($c = mysqli_fetch_assoc($cities_res)) { ?>
                            <option value="<?= $c['CityID'] ?>" <?= $cityID == $c['CityID'] ? 'selected' : '' ?>><?= $c['CityName'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <!-- Shimmer Loader Grid -->
            <div id="shimmerLoader" class="table-card">
                <table>
                    <thead>
                        <tr>
                            <th width="35%"><div class="s-box shimmer-bg" style="height:15px; width:100px;"></div></th>
                            <th width="20%"><div class="s-box shimmer-bg" style="height:15px; width:80px;"></div></th>
                            <th width="20%"><div class="s-box shimmer-bg" style="height:15px; width:80px;"></div></th>
                            <th width="15%"><div class="s-box shimmer-bg" style="height:15px; width:80px;"></div></th>
                            <th width="10%"><div class="s-box shimmer-bg" style="height:15px; width:40px;"></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($i=0; $i<6; $i++) { ?>
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:16px;">
                                    <div class="s-box shimmer-bg" style="width:44px; height:44px; border-radius:14px;"></div>
                                    <div style="display:flex; flex-direction:column; gap:8px;">
                                        <div class="s-box shimmer-bg" style="height:12px; width:120px;"></div>
                                        <div class="s-box shimmer-bg" style="height:10px; width:80px;"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display:flex; flex-direction:column; gap:8px;">
                                    <div class="s-box shimmer-bg" style="height:12px; width:90px;"></div>
                                    <div class="s-box shimmer-bg" style="height:10px; width:60px;"></div>
                                </div>
                            </td>
                            <td><div class="s-box shimmer-bg" style="height:25px; width:100px; border-radius:20px;"></div></td>
                            <td><div class="s-box shimmer-bg" style="height:35px; width:80px; border-radius:12px;"></div></td>
                            <td align="right"><div class="s-box shimmer-bg" style="height:38px; width:38px; border-radius:12px; display:inline-block;"></div></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Real Data Grid -->
            <div id="realContent" class="table-card" style="display:none; transition: opacity 0.4s ease;">
                <table>
                    <thead>
                        <tr>
                            <th width="35%">User Profile</th>
                            <th width="20%">Registration</th>
                            <th width="20%">Device Platform</th>
                            <th width="15%">Active Balance</th>
                            <th width="10%" style="text-align:right;">Manage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($u = mysqli_fetch_assoc($users_res)) { ?>
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <?php if (!empty($u['UserPhoto'])) { ?>
                                            <img src="<?= $u['UserPhoto'] ?>" class="u-avatar">
                                        <?php } else { ?>
                                            <div class="u-avatar"><?= strtoupper(substr(trim($u['name']), 0, 1) . (strpos(trim($u['name']), ' ') !== false ? substr(explode(' ', trim($u['name']))[1], 0, 1) : '')) ?></div>
                                        <?php } ?>
                                        <div style="display:flex; flex-direction:column; justify-content:center; gap:2px;">
                                            <span style="font-weight:800; font-size:14px;"><?= $u['name'] ?></span>
                                            <span style="color:var(--text-gray); font-size:12px; font-weight:600; display:flex; align-items:center; gap:5px;">
                                                <i class="fas fa-envelope" style="font-size:10px;"></i> <?= $u['Email'] ?: ($u['phone'] ?: 'No Contact Provided') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; justify-content:center; gap:3px;">
                                        <span style="font-weight:700; color:var(--text-dark); font-size:14px;">
                                            <?= date('M d, Y', strtotime($u['CreatedAtUser'])) ?>
                                        </span>
                                        <div style="color:var(--text-gray); font-size:12px; font-weight:600;">
                                            <?= date('h:i A', strtotime($u['CreatedAtUser'])) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="platform-badge <?= $u['UserType']=='ANDROID' ? 'platform-android' : 'platform-ios' ?>">
                                        <i class="fab <?= $u['UserType']=='ANDROID' ? 'fa-android' : 'fa-apple' ?>"></i> 
                                        <?= $u['UserType']=='ANDROID' ? 'Android Access' : 'iOS Access' ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="balance-pill"><?= number_format($u['Balance'] ?? 0) ?> MAD</span>
                                </td>
                                <td style="text-align:right;">
                                    <a href="user-profile.php?id=<?= $u['UserID'] ?>" class="action-btn" title="Edit User">
                                        <i class="fas fa-user-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                
                <?php if (mysqli_num_rows($users_res) == 0): ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <div>
                            <h3 style="font-weight:700; color:var(--text-dark); margin-bottom:5px;">No Users Found</h3>
                            <p>We couldn't find any users matching your current filters.</p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($totalRows > 0): ?>
                <div class="table-footer">
                    <div class="results-count">
                        Showing <?= $offset + 1 ?> to <?= min($offset + $limit, $totalRows) ?> of <?= number_format($totalRows) ?> entries
                    </div>
                    <div class="pagination">
                        <a href="?type=<?= $type ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>&UserName=<?= urlencode($userNameSearch) ?>&Page=<?= max(0, $page-1) ?>" class="page-btn <?= $page == 0 ? 'disabled' : '' ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        
                        <?php 
                        // Smart Pagination Logic (Show max 5 page buttons)
                        $startPage = max(0, min($page - 2, $totalPages - 5));
                        $endPage = min($totalPages - 1, $startPage + 4);
                        
                        for ($i = $startPage; $i <= $endPage; $i++): 
                        ?>
                            <a href="?type=<?= $type ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>&UserName=<?= urlencode($userNameSearch) ?>&Page=<?= $i ?>" class="page-btn <?= $page == $i ? 'active' : '' ?>">
                                <?= $i + 1 ?>
                            </a>
                        <?php endfor; ?>

                        <a href="?type=<?= $type ?>&start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&city_id=<?= $cityID ?>&UserName=<?= urlencode($userNameSearch) ?>&Page=<?= min($totalPages-1, $page+1) ?>" class="page-btn <?= $page >= $totalPages - 1 ? 'disabled' : '' ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        // Shimmer Transition Logic
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.getElementById('shimmerLoader').style.display = 'none';
                const rc = document.getElementById('realContent');
                rc.style.display = 'block';
                // Force browser reflow to ensure CSS transition triggers smoothly
                rc.offsetHeight;
                rc.style.opacity = '1';
            }, 400); // Small 400ms delay to make it feel deliberate and hide jitter
        });
    </script>
</body>
</html>
