<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

// -- CORE CONFIG VARS --
$DistanceValue = 0;
$resDist = mysqli_query($con, "SELECT DistanceValue FROM Distance LIMIT 1");
if($row = mysqli_fetch_assoc($resDist)) { $DistanceValue = $row["DistanceValue"]; }

$percent = 0; $disUser = 0;
$resPerc = mysqli_query($con, "SELECT percent, disUser FROM OrdersJiblerpercentage LIMIT 1");
if($row = mysqli_fetch_assoc($resPerc)) { $percent = $row["percent"]; $disUser = $row["disUser"]; }

$percentDriver = 0;
$resPercD = mysqli_query($con, "SELECT percentage FROM OrdersJiblerpercentageDriver LIMIT 1");
if($row = mysqli_fetch_assoc($resPercD)) { $percentDriver = $row["percentage"]; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platform App Matrix | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="js/jquery-3.2.1.min.js"></script>

    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-green: #10B981; --accent-blue: #007AFF; --accent-orange: #F59E0B; --accent-red: #EF4444;
            --border-color: #F0F2F6;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        /* ----- SIDEBAR ----- */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i, .nav-item img { width: 22px; text-align: center; }
        .nav-item:hover, .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink: 0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        /* Unified Grid & Tabs */
        .workspace { display: grid; grid-template-columns: 260px 1fr; gap: 30px; flex: 1; }
        
        .tab-menu { background: var(--bg-white); border-radius: 20px; padding: 20px 15px; box-shadow: var(--shadow-card); height: fit-content; display: flex; flex-direction: column; gap: 8px; }
        .tab-btn { background: transparent; border: none; padding: 14px 20px; text-align: left; border-radius: 12px; font-size: 14px; font-weight: 700; color: var(--text-gray); cursor: pointer; transition: 0.2s; display:flex; align-items:center; gap:12px; }
        .tab-btn:hover { background: var(--bg-app); color: var(--text-dark); }
        .tab-btn.active { background: var(--accent-purple); color: #FFF; box-shadow: 0 4px 15px rgba(98, 60, 234, 0.3); }

        .tab-content-area { background: var(--bg-white); border-radius: 20px; padding: 35px; box-shadow: var(--shadow-card); overflow-y: auto; }
        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* General Forms & Grids Inside Tabs */
        .sec-title { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display:flex; align-items:center; gap:10px; border-bottom: 1px solid var(--border-color); padding-bottom:15px; }
        .action-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px; margin-bottom:40px; }
        .form-cluster { background: var(--bg-app); padding: 25px; border-radius: 16px; border: 1px solid var(--border-color); margin-bottom: 30px; }
        
        /* Shimmer Base Class */
        .shimmer { animation: shimmer 2s infinite linear; background: linear-gradient(to right, #f6f7f8 4%, #edeef1 25%, #f6f7f8 36%); background-size: 1000px 100%; border: none !important; }
        @keyframes shimmer { 0% { background-position: -1000px 0; } 100% { background-position: 1000px 0; } }

        /* Slide & Image Thumbnails */
        .thumb-box { position: relative; border-radius: 20px; overflow: hidden; border: 1px solid var(--border-color); display:flex; justify-content:center; align-items:center; background:#FFF; min-height:130px; padding:10px; }
        .thumb-box img { width: 100%; height: 110px; object-fit: cover; border-radius: 12px; opacity: 0; transition: opacity 0.3s ease; }
        .thumb-box img.img-loaded { opacity: 1; }
        .thumb-box .trash-btn { position: absolute; top: 12px; right: 12px; background: rgba(239, 68, 68, 0.1); color: var(--accent-red); width: 30px; height: 30px; display: flex; justify-content: center; align-items: center; border-radius: 8px; text-decoration: none; transition: 0.2s; opacity:0; z-index:10; }
        .thumb-box:hover .trash-btn { opacity:1; }
        .thumb-box .trash-btn:hover { background: var(--accent-red); color: #FFF; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.4); }
        .thumb-box.add-new { background: var(--accent-purple-light); border: 2px dashed var(--accent-purple); color: var(--accent-purple); cursor:pointer; text-decoration:none; flex-direction:column; gap:10px; font-weight:700; font-size:13px; }
        .thumb-box.add-new:hover { background: var(--accent-purple); color: #FFF; border-style:solid; }

        /* Social Feeds (Posts & Stories) */
        .social-card { background: var(--bg-white); border: 1px solid var(--border-color); border-radius: 16px; padding: 20px; box-shadow: var(--shadow-card); margin-bottom:20px; }
        .social-header { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .social-header img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid var(--border-color); }
        .social-meta { flex: 1; }
        .social-meta h5 { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom:3px; }
        .social-meta p { font-size: 12px; font-weight: 600; color: var(--text-gray); }
        .social-media { display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:10px; margin-bottom: 15px; border-radius:12px; overflow:hidden;}
        .social-media img, .social-media video { width: 100%; height: auto; max-height: 700px; object-fit: contain; background: #F8F9FA; border-radius:10px;}
        .social-body { font-size: 14px; font-weight: 500; color: var(--text-dark); line-height: 1.6; margin-bottom: 20px; }
        .social-actions { display: flex; gap: 10px; justify-content: flex-end; padding-top: 15px; border-top: 1px solid var(--border-color); }
        .pill-btn { padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; border: none; cursor: pointer; display:flex; align-items:center; gap:6px; transition:0.2s;}
        .pill-green { background: rgba(16, 185, 129, 0.1); color: var(--accent-green); } .pill-green:hover { background: var(--accent-green); color: #FFF; }
        .pill-red { background: rgba(239, 68, 68, 0.1); color: var(--accent-red); } .pill-red:hover { background: var(--accent-red); color: #FFF; }

        /* Tables */
        .premium-table { width: 100%; border-collapse: collapse; }
        .premium-table th { padding: 15px; text-align: left; font-size: 12px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; letter-spacing: 1px; border-bottom: 2px solid var(--border-color); }
        .premium-table td { padding: 15px; font-size: 14px; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .premium-table tr:hover td { background: var(--bg-app); }
        
        .input-group { margin-bottom: 15px; }
        .input-group label { display: block; font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
        .inp-wrap { position: relative; display:flex; align-items:center; }
        .inp-wrap input { width: 100%; background: #FFF; border: 1px solid var(--border-color); border-radius: 10px; padding: 12px 15px; font-size: 14px; font-weight: 700; color: var(--text-dark); outline:none; }
        .inp-wrap input:focus { border-color: var(--accent-purple); box-shadow: 0 0 0 3px var(--accent-purple-light); }
        .inp-wrap span { position: absolute; right: 15px; font-size: 13px; font-weight: 800; color: var(--text-gray); pointer-events:none;}
        .btn-update { width: fit-content; border: none; background: var(--text-dark); color: #FFF; padding: 12px 25px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; transition: 0.2s; margin-top:10px; }
        .btn-update:hover { background: var(--accent-purple); transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="index.php"><i class="fas fa-home"></i> Core Dashboard</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">App Matrix & Engagements</span>
                </div>
            </header>

            <div class="workspace">
                
                <!-- Navigation Pills -->
                <div class="tab-menu">
                    <button class="tab-btn active" onclick="switchTab('t-jibler', this)"><i class="fas fa-mobile-alt"></i> Jibler Client App</button>
                    <button class="tab-btn" onclick="switchTab('t-partner', this)"><i class="fas fa-store-alt"></i> Jibler Partner App</button>
                    <button class="tab-btn" onclick="switchTab('t-driver', this)"><i class="fas fa-motorcycle"></i> Jibler Driver App</button>
                    <hr style="border:0; border-top:1px solid var(--border-color); margin:10px 0;">
                    <button class="tab-btn" onclick="switchTab('t-posts', this)"><i class="fas fa-images"></i> Posts Moderation</button>
                    <button class="tab-btn" onclick="switchTab('t-stories', this)"><i class="fas fa-history"></i> Stories Moderation</button>
                    <button class="tab-btn" onclick="switchTab('t-boosts', this)"><i class="fas fa-rocket"></i> Premium Boosts</button>
                    <hr style="border:0; border-top:1px solid var(--border-color); margin:10px 0;">
                    <button class="tab-btn" onclick="switchTab('t-perc-sys', this)"><i class="fas fa-percentage"></i> General Percentages</button>
                    <button class="tab-btn" onclick="switchTab('t-perc-drv', this)"><i class="fas fa-coins"></i> Driver Comm. Gains</button>
                </div>

                <!-- Work Area -->
                <div class="tab-content-area">

                    <!-- TAB 1: Jibler -->
                    <div id="t-jibler" class="tab-panel active">
                        <h2 class="sec-title"><i class="fas fa-sliders-h" style="color:var(--accent-purple)"></i> Client Layout Toggles</h2>
                        
                        <h4 style="margin-bottom:15px; font-weight:700; color:var(--text-gray); font-size:13px;">MAIN SLIDERS</h4>
                        <div class="action-grid" id="async-sliders">
                            <a class="thumb-box shimmer" style="min-height:130px; width:100%;"><div class="shimmer" style="height:100px; width:100%;"></div></a>
                            <div class="thumb-box shimmer" style="min-height:130px; width:100%;"></div>
                            <div class="thumb-box shimmer" style="min-height:130px; width:100%;"></div>
                        </div>

                        <h4 style="margin-bottom:15px; font-weight:700; color:var(--text-gray); font-size:13px; border-top:1px solid var(--border-color); padding-top:25px;">CATEGORY VISUAL ASSETS</h4>
                        <div class="action-grid" id="async-categories">
                            <a class="thumb-box shimmer" style="min-height:130px; width:100%;"><div class="shimmer" style="height:100px; width:100%;"></div></a>
                            <div class="thumb-box shimmer" style="min-height:130px; width:100%;"></div>
                            <div class="thumb-box shimmer" style="min-height:130px; width:100%;"></div>
                        </div>

                        <h4 style="margin-bottom:15px; font-weight:700; color:var(--text-gray); font-size:13px; border-top:1px solid var(--border-color); padding-top:25px;">CLIENT DOMAIN BOUNDARY</h4>
                        <form class="form-cluster" method="POST" action="updateDistance.php">
                            <div class="input-group">
                                <label>Maximum Allowed Shop Indexing Distance</label>
                                <div class="inp-wrap">
                                    <input type="number" step="0.1" name="dis" value="<?= $DistanceValue ?>">
                                    <span>KM</span>
                                </div>
                            </div>
                            <button type="submit" class="btn-update"><i class="fas fa-save"></i> Save Boundary Limit</button>
                        </form>
                    </div>

                    <!-- TAB 2: Partner -->
                    <div id="t-partner" class="tab-panel">
                        <h2 class="sec-title"><i class="fas fa-store" style="color:var(--accent-orange)"></i> Partner App Advertising Layout</h2>
                        <div class="action-grid" id="async-partners"></div>
                    </div>

                    <!-- TAB 3: Driver -->
                    <div id="t-driver" class="tab-panel">
                        <h2 class="sec-title"><i class="fas fa-motorcycle" style="color:var(--accent-green)"></i> Driver Geographic Boundaries</h2>
                        <form class="form-cluster" method="POST" action="updateDistance.php">
                            <div class="input-group">
                                <label>Driver Request Allocation Radius</label>
                                <div class="inp-wrap">
                                    <input type="number" step="0.1" name="dis" value="<?= $DistanceValue ?>">
                                    <span>KM</span>
                                </div>
                            </div>
                            <button type="submit" class="btn-update"><i class="fas fa-save"></i> Enforce Radius Boundary</button>
                        </form>
                    </div>

                    <!-- TAB 4: POSTS -->
                    <div id="t-posts" class="tab-panel">
                        <h2 class="sec-title"><i class="fas fa-images" style="color:var(--accent-blue)"></i> Feed Posts Moderation</h2>
                        <div id="async-posts"></div>
                    </div>

                    <!-- TAB 5: STORIES -->
                    <div id="t-stories" class="tab-panel">
                        <h2 class="sec-title"><i class="fas fa-fire" style="color:var(--accent-orange)"></i> Fleet Stories Moderation</h2>
                        <div id="async-stories"></div>
                    </div>

                    <!-- TAB 6: PERC SYS -->
                    <div id="t-perc-sys" class="tab-panel">
                        <h2 class="sec-title"><i class="fas fa-percentage" style="color:var(--accent-purple)"></i> System Fee Overrides</h2>
                        <form class="form-cluster" method="POST" action="updatepercent.php">
                            <div style="display:flex; gap:20px;">
                                <div class="input-group" style="flex:1;">
                                    <label>Global Default Shop Fee</label>
                                    <div class="inp-wrap">
                                        <input name="dis" value="<?= $percent ?>">
                                        <span>%</span>
                                    </div>
                                </div>
                                <div class="input-group" style="flex:1;">
                                    <label>Checkout User Convenience Fee</label>
                                    <div class="inp-wrap">
                                        <input name="disUser" value="<?= $disUser ?>">
                                        <span>%</span>
                                    </div>
                                </div>
                            </div>
                            <button class="btn-update"><i class="fas fa-save"></i> Save Override Params</button>
                        </form>
                    </div>

                    <!-- TAB 7: PERC DRV -->
                    <div id="t-perc-drv" class="tab-panel">
                        <h2 class="sec-title"><i class="fas fa-coins" style="color:var(--accent-green)"></i> Driver Royalty Mechanics</h2>
                        <form class="form-cluster" method="POST" action="updatepercentDrivers.php">
                            <div class="input-group">
                                <label>Driver Order Volume Cut</label>
                                <div class="inp-wrap">
                                    <input name="dis" value="<?= $percentDriver ?>">
                                    <span>%</span>
                                </div>
                            </div>
                            <button class="btn-update"><i class="fas fa-save"></i> Save Royalty Override</button>
                        </form>
                    </div>

                    <!-- TAB 8: BOOSTS -->
                    <div id="t-boosts" class="tab-panel">
                        <h2 class="sec-title"><i class="fas fa-rocket" style="color:var(--accent-red)"></i> Store Promotion Campaigns</h2>
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Store</th>
                                    <th>Campaign</th>
                                    <th>Media Asset</th>
                                    <th>City Bound</th>
                                    <th>Dur.</th>
                                    <th>Budget</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="async-boosts"></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </main>
    </div>

    <!-- UI / Engine Logic -->
    <script>
        let loadedTabs = {};

        function loadAsync(targetId, action) {
            if (loadedTabs[action]) return; 
            let container = document.getElementById(targetId);
            
            let skeletons = '';
            if(action === 'sliders' || action === 'categories' || action === 'partners') {
                skeletons = '<a class="thumb-box shimmer" style="min-height:130px; width:100%;"><div class="shimmer" style="height:100px; width:100%;"></div></a><div class="thumb-box shimmer" style="min-height:130px; width:100%;"></div><div class="thumb-box shimmer" style="min-height:130px; width:100%;"></div>';
            } else if(action === 'posts' || action === 'stories') {
                skeletons = '<div class="social-card shimmer" style="height:350px;"></div><div class="social-card shimmer" style="height:350px;"></div>';
            } else if(action === 'boosts') {
                skeletons = '<tr><td colspan="8"><div class="shimmer" style="height:40px; border-radius:10px; margin-bottom:10px;"></div><div class="shimmer" style="height:40px; border-radius:10px; margin-bottom:10px;"></div></td></tr>';
            }
            container.innerHTML = skeletons;
            
            $.ajax({
                url: 'ajax_apps_data.php?action=' + action,
                success: function(data) {
                    container.innerHTML = data;
                    loadedTabs[action] = true;
                }
            });
        }

        function switchTab(targetId, btnObj) {
            document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(targetId).classList.add('active');
            
            if(btnObj) {
                btnObj.classList.add('active');
            } else {
                document.querySelector('.tab-btn').classList.add('active');
            }

            // Trigger Async Loading Based on Tab
            if(targetId === 't-jibler') {
                loadAsync('async-sliders', 'sliders');
                loadAsync('async-categories', 'categories');
            } else if (targetId === 't-partner') {
                loadAsync('async-partners', 'partners');
            } else if (targetId === 't-posts') {
                loadAsync('async-posts', 'posts');
            } else if (targetId === 't-stories') {
                loadAsync('async-stories', 'stories');
            } else if (targetId === 't-boosts') {
                loadAsync('async-boosts', 'boosts');
            }
        }

        // Auto-load default active tab on boot
        window.onload = function() {
            switchTab('t-jibler', null); // Trigger load without DOM target reference
        };

        function modAction(url, btnId, targetStatus, textId) {
            if(!confirm("Execute administrative override on this object?")) return;
            
            $.ajax({
                url: url,
                type: "POST",
                data: { SellerEmail: 'SYS_OVERRIDE' },
                cache: false,
                success: function(res) {
                    let label = document.getElementById(textId);
                    if(label) {
                        label.innerText = targetStatus;
                        label.style.color = targetStatus === 'ACTIVE' ? 'var(--accent-green)' : 'var(--accent-red)';
                    }
                }
            });
        }
    </script>
</body>
</html>