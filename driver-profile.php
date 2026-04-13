<?php
require "conn.php";
$id = (int)$_GET["id"];

// 1. Fetch Driver Information
$res = mysqli_query($con, "SELECT * FROM Drivers WHERE DriverID='$id'");
$driver = mysqli_fetch_assoc($res);
if (!$driver) die("Driver Not Found");

// Fix Local Paths Flaw
$fieldsToFix = ['PersonalPhoto', 'CIN', 'CV', 'Contract', 'CartOwnership', 'Insurance'];
foreach ($fieldsToFix as $field) {
    if (strpos($driver[$field], 'https://jibler.app/db/db/photo/') !== false) {
        $driver[$field] = str_replace('https://jibler.app/db/db/', '', $driver[$field]);
    }
}

$FName = $driver["FName"];
$LName = $driver["LName"];
$FullName = $FName . ' ' . $LName;
$DriverEmail = $driver["DriverEmail"];
$Ckey = $driver["Ckey"];
$DriverPhoneRaw = $driver["DriverPhone"];
$DriverPhone = str_replace($Ckey, "", $DriverPhoneRaw);
$AGE = $driver["AGE"];
$NationalID = $driver["NationalID"];
$City = $driver["City"];
$DriverRate = $driver["DriverRate"];
$DriverPassword = $driver["DriverPassword"];
$PersonalPhoto = !empty($driver['PersonalPhoto']) ? $driver['PersonalPhoto'] : 'images/jiblers.jpg';
$AvatarFallback = "https://ui-avatars.com/api/?name=" . urlencode($FullName) . "&background=EFEAF8&color=623CEA&bold=true";

// 2. Compute Orders & Finance
$MustPaid = 0;
$OrdersNumber = 0;
$OrdersNumberLastweek = 0;
$lastweek = date('Y-m-d', strtotime("-7 days"));

$resOrders = mysqli_query($con, "SELECT * FROM Orders WHERE DelvryId='$id'");
while ($row = mysqli_fetch_assoc($resOrders)) {
    $OrdersNumber++;
    if ($lastweek < $row["CreatedAtOrders"]) {
        $OrdersNumberLastweek++;
    }
    if (($row['OrderState'] == 'Rated' || $row['OrderState'] == 'Done') && $row['PaidForDriver'] == 'NotPaid') {
        $MustPaid += $row["OrderPriceFromShop"];
    }
}

// 3. Transactions & Notes & Reviews
$transactions = mysqli_query($con, "SELECT * FROM DriverTransactions WHERE DriverID='$id' ORDER BY DriverTransactionsID DESC LIMIT 10");
$notes = mysqli_query($con, "SELECT * FROM DriverNotes WHERE DriverID='$id' ORDER BY CreatedAtDriverNotes DESC LIMIT 5");
$reviews = mysqli_query($con, "SELECT * FROM Orders JOIN Users ON Orders.UserID = Users.UserID WHERE DelvryId='$id' AND UserReview != '' ORDER BY Orders.CreatedAtOrders DESC LIMIT 5");

$countries_res = mysqli_query($con, "SELECT * FROM Countries");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($FullName) ?> | Driver Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-green: #10B981; --accent-blue: #007AFF; --accent-red: #E11D48;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --border-color: #F0F2F6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); height: 100vh; display: flex; overflow: hidden; }

        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        /* Unified Sidebar CSS */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item:hover:not(.active) { color: var(--text-dark); background: #F8F9FB; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        /* Shared Components */
        .back-btn { display: inline-flex; align-items: center; gap: 10px; padding: 10px 18px; border-radius: 12px; background: var(--bg-white); color: var(--text-dark); text-decoration: none; font-weight: 700; font-size: 14px; box-shadow: var(--shadow-card); transition: 0.2s; border: 1px solid var(--border-color); align-self: flex-start; margin-bottom: 25px; }
        .back-btn:hover { background: #F8F9FB; transform: translateY(-2px); color: var(--accent-purple); box-shadow: 0 12px 25px rgba(0,0,0,0.05); }

        .card { background: var(--bg-white); border-radius: 24px; padding: 30px; box-shadow: var(--shadow-card); border: 1px solid rgba(255,255,255,0.8); }
        .card-header { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        
        .btn-primary { background: linear-gradient(135deg, var(--accent-purple), #4F28D1); color: #FFF; border: none; padding: 14px 20px; border-radius: 14px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; box-shadow: 0 8px 20px rgba(98, 60, 234, 0.2); width: 100%; display: flex; justify-content: center; align-items: center; gap: 8px;}
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(98, 60, 234, 0.3); }

        /* Top Identity Banner */
        .identity-banner { display: flex; gap: 30px; align-items: center; margin-bottom: 30px; }
        .identity-avatar { width: 110px; height: 110px; border-radius: 24px; object-fit: cover; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 4px solid #FFF;}
        .identity-text h1 { font-size: 28px; font-weight: 800; color: var(--text-dark); margin-bottom: 4px; letter-spacing: -0.5px;}
        .identity-text p { font-size: 14px; font-weight: 600; color: var(--text-gray); }
        .identity-rating { display: flex; gap: 5px; color: #F59E0B; font-size: 14px; margin-top: 10px; }

        /* Grid Layout */
        .dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }

        /* Form Inputs */
        .input-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 6px; }
        .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-group label { font-size: 12px; font-weight: 700; color: var(--text-dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .input-group input, .input-group select { width: 100%; padding: 14px 18px; border-radius: 12px; border: 1px solid var(--border-color); background: #F8F9FA; color: var(--text-dark); font-size: 14px; font-weight: 600; outline: none; transition: 0.2s; font-family: 'Inter', sans-serif; }
        .input-group input:focus, .input-group select:focus { background: #FFF; border-color: var(--accent-purple); box-shadow: 0 0 0 3px var(--accent-purple-light); }

        /* Finance Stats */
        .stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 25px; }
        .stat-box { background: #F8F9FA; border-radius: 16px; padding: 20px; border: 1px solid var(--border-color); }
        .stat-box.debt { background: rgba(225, 29, 72, 0.05); border-color: rgba(225, 29, 72, 0.1); }
        .stat-box h5 { font-size: 12px; font-weight: 700; color: var(--text-gray); text-transform: uppercase; margin-bottom: 5px;}
        .stat-box.debt h5 { color: var(--accent-red); margin-bottom: 5px;}
        .stat-box h3 { font-size: 24px; font-weight: 800; color: var(--text-dark); letter-spacing: -0.5px; }
        .stat-box.debt h3 { color: var(--accent-red); }

        /* Lists */
        .feed-list { display: flex; flex-direction: column; gap: 15px; }
        .feed-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #FFF; border: 1px solid var(--border-color); border-radius: 16px; transition: 0.2s;}
        .feed-item:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.03); border-color: #D1D5DF; }
        .feed-info { display: flex; gap: 12px; align-items: center; }
        .feed-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; }
        .feed-text h6 { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 3px; }
        .feed-text p { font-size: 12px; font-weight: 500; color: var(--text-gray); }

        .doc-btn { padding: 8px 14px; border-radius: 10px; background: var(--bg-white); border: 1px solid var(--border-color); color: var(--text-dark); font-size: 13px; font-weight: 700; text-decoration: none; transition: 0.2s; cursor: pointer;}
        .doc-btn:hover { background: var(--accent-purple-light); color: var(--accent-purple); border-color: var(--accent-purple); }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }

            /* Hide desktop sidebar rail */
            .sidebar { display: none !important; }

            .main-panel {
                padding: 16px;
                overflow-y: visible;
                overflow-x: hidden;
            }

            /* Back button: full width feel */
            .back-btn { font-size: 13px; padding: 9px 14px; margin-bottom: 16px; }

            /* Identity banner: stack avatar + text */
            .identity-banner {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
                margin-bottom: 20px;
            }
            .identity-avatar { width: 80px; height: 80px; border-radius: 18px; }
            .identity-text h1 { font-size: 22px; }
            .identity-text p  { font-size: 13px; }

            /* Main grid: single column */
            .dashboard-grid { grid-template-columns: 1fr; gap: 16px; }

            /* Cards */
            .card { padding: 20px; border-radius: 18px; }
            .card-header { font-size: 15px; margin-bottom: 18px; }

            /* Form input-row: single column */
            .input-row { grid-template-columns: 1fr; gap: 0; }

            /* Stat grid: 2 columns still fine on tablet */
            .stat-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
            .stat-box { padding: 16px; }
            .stat-box h3 { font-size: 20px; }

            /* Push notification form: stack inputs */
            .notif-form-row { flex-direction: column !important; gap: 10px !important; }
            .notif-form-row input[name="PostTitle"] { width: 100% !important; }

            /* Feed items */
            .feed-item { padding: 12px; }
            .feed-text h6 { font-size: 13px; }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .main-panel { padding: 12px; }

            .identity-avatar { width: 68px; height: 68px; }
            .identity-text h1 { font-size: 19px; }

            /* Stat grid: full single column on tiny phones */
            .stat-grid { grid-template-columns: 1fr; gap: 10px; }
            .stat-box h3 { font-size: 22px; }

            /* Doc buttons stack */
            .feed-item { flex-direction: column; align-items: flex-start; gap: 10px; }

            .card { padding: 16px; }
        }

    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <a href="driver.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Drivers Directory</a>

            <!-- Overview Header -->
            <div class="identity-banner">
                <img src="<?= $PersonalPhoto ?>" class="identity-avatar" onerror="this.onerror=null; this.src='<?= $AvatarFallback ?>'">
                <div class="identity-text">
                    <h1><?= htmlspecialchars($FullName) ?></h1>
                    <p>Driver ID: #<?= $id ?> &nbsp;•&nbsp; Active since <?= date('F Y') ?></p>
                    <div class="identity-rating">
                        <?php for($i = 0; $i < 5; $i++): ?>
                            <i class="fa<?= $i < round($DriverRate) ? 's' : 'r' ?> fa-star" <?= $i >= round($DriverRate) ? 'style="color:#EBECEF;"' : '' ?>></i>
                        <?php endfor; ?>
                        <span style="color:var(--text-dark); font-weight:800; margin-left:5px;"><?= number_format($DriverRate, 1) ?></span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                
                <!-- Left Column: Settings & Profile Details -->
                <div style="display:flex; flex-direction:column; gap:25px;">
                    <div class="card">
                        <div class="card-header"><i class="fas fa-user-edit" style="color:var(--accent-purple);"></i> Edit Profile Details</div>
                        <form action="UpdateDriverJiblerAPI.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="DriverID" value="<?= $id ?>">
                            
                            <div class="input-row">
                                <div class="input-group">
                                    <label>First Name</label>
                                    <input type="text" name="FName" value="<?= htmlspecialchars($FName) ?>">
                                </div>
                                <div class="input-group">
                                    <label>Last Name</label>
                                    <input type="text" name="LName" value="<?= htmlspecialchars($LName) ?>">
                                </div>
                            </div>

                            <div class="input-group">
                                <label>Email Address</label>
                                <input type="email" name="DriverEmail" value="<?= htmlspecialchars($DriverEmail) ?>">
                            </div>

                            <div class="input-row">
                                <div class="input-group">
                                    <label>Country</label>
                                    <select name="CountryKey">
                                        <?php while($row = mysqli_fetch_assoc($countries_res)): ?>
                                            <option value="<?= $row['country_code'] ?>" <?= $Ckey == $row['country_code'] ? 'selected' : '' ?>>
                                                <?= $row['EnglishName'] ?> (<?= $row['country_code'] ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="input-group">
                                    <label>Phone Number</label>
                                    <input type="number" name="DriverPhone" value="<?= htmlspecialchars($DriverPhone) ?>">
                                </div>
                            </div>

                            <div class="input-row">
                                <div class="input-group">
                                    <label>City Hub</label>
                                    <input type="text" name="City" value="<?= htmlspecialchars($City) ?>">
                                </div>
                                <div class="input-group">
                                    <label>Age</label>
                                    <input type="number" name="AGE" value="<?= htmlspecialchars($AGE) ?>">
                                </div>
                            </div>

                            <div class="input-group" style="margin-top: 10px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                                <label>Reset Driver Password</label>
                                <input type="text" name="Password" value="<?= htmlspecialchars($DriverPassword) ?>">
                            </div>

                            <button type="submit" class="btn-primary" style="margin-top: 15px;">
                                <i class="fas fa-save"></i> Save Profile Changes
                            </button>
                        </form>
                    </div>

                    <!-- Verified Documents -->
                    <div class="card">
                        <div class="card-header" style="margin-bottom:15px;">
                            <span><i class="fas fa-file-contract" style="color:var(--accent-purple);"></i> Verified Documents</span>
                            <button onclick="downloadAllDocs()" class="doc-btn" style="background:var(--accent-purple-light); color:var(--accent-purple); border-color:var(--accent-purple);"><i class="fas fa-download"></i> Extract</button>
                        </div>
                        <div class="feed-list" style="gap:10px;">
                            <?php 
                                $docs = [
                                    "CIN" => ["icon" => "fa-id-card", "val" => $driver['CIN']],
                                    "CV" => ["icon" => "fa-file-alt", "val" => $driver['CV']],
                                    "Contract" => ["icon" => "fa-file-signature", "val" => $driver['Contract']],
                                    "Ownership" => ["icon" => "fa-car-side", "val" => $driver['CartOwnership']],
                                    "Insurance" => ["icon" => "fa-file-medical-alt", "val" => $driver['Insurance']]
                                ];
                                foreach($docs as $name => $data):
                                    $hasVal = !empty($data['val']);
                            ?>
                            <div class="feed-item" style="padding: 10px 15px;">
                                <div class="feed-info">
                                    <i class="fas <?= $data['icon'] ?> text-gray" style="font-size:18px;"></i>
                                    <span style="font-size:14px; font-weight:700; color:var(--text-dark);"><?= $name ?> Document</span>
                                </div>
                                <?php if($hasVal): ?>
                                    <a href="<?= $data['val'] ?>" target="_blank" class="doc-btn"><i class="fas fa-external-link-alt"></i> View</a>
                                <?php else: ?>
                                    <span style="font-size:11px; font-weight:700; color:var(--text-gray); background:#F0F2F6; padding:4px 8px; border-radius:6px;">Missing</span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Analytics & Transactions -->
                <div style="display:flex; flex-direction:column; gap:25px;">
                    <div class="card">
                        <div class="card-header"><i class="fas fa-chart-pie" style="color:var(--accent-purple);"></i> Telemetry & Performance</div>
                        <div class="stat-grid">
                            <div class="stat-box">
                                <h5>Total Orders</h5>
                                <h3><?= number_format($OrdersNumber) ?></h3>
                            </div>
                            <div class="stat-box debt">
                                <h5>Outstanding Cash Debt</h5>
                                <h3><?= number_format($MustPaid) ?> <span style="font-size:14px; font-weight:600;">MAD</span></h3>
                            </div>
                            <div class="stat-box">
                                <h5>Delivery Speed Avg</h5>
                                <h3>43 <span style="font-size:16px;">MIN</span></h3>
                            </div>
                            <div class="stat-box">
                                <h5>Network Status</h5>
                                <span style="display:inline-flex; align-items:center; gap:6px; background:rgba(16,185,129,0.1); color:var(--accent-green); padding:6px 12px; border-radius:8px; font-size:13px; font-weight:700;"><i class="fas fa-circle" style="font-size:8px;"></i> Cleared</span>
                            </div>
                        </div>

                        <!-- System Notify -->
                        <form method="POST" action="notificationsSendNotfToDriversID.php" style="background:#F8F9FA; padding:20px; border-radius:16px; border:1px solid var(--border-color);">
                            <h5 style="font-size:13px; font-weight:700; color:var(--text-dark); margin-bottom:12px; text-transform:uppercase;">Push Notification</h5>
                            <input type="hidden" name="DriverID" value="<?= $id ?>">
                            <div style="display:flex; gap:10px;" class="notif-form-row">
                                <input type="text" name="PostTitle" placeholder="Title" required style="width:30%; padding:10px; border-radius:8px; border:1px solid var(--border-color); outline:none;">
                                <input type="text" name="Message" placeholder="Message content..." required style="flex:1; padding:10px; border-radius:8px; border:1px solid var(--border-color); outline:none;">
                                <button type="submit" style="background:var(--accent-purple); color:#FFF; border:none; padding:10px 15px; border-radius:8px; font-weight:700; cursor:pointer;"><i class="fas fa-paper-plane"></i></button>
                            </div>
                        </form>
                    </div>

                    <div class="card" style="flex:1;">
                        <div class="card-header"><i class="fas fa-list-ul" style="color:var(--accent-purple);"></i> Recent Activity Feed</div>
                        <div class="feed-list">
                            <?php if(mysqli_num_rows($transactions) == 0): ?>
                                <p style="font-size:13px; color:var(--text-gray); font-weight:600; text-align:center; padding:20px;">No payout transactions available.</p>
                            <?php endif; ?>
                            <?php while($t = mysqli_fetch_assoc($transactions)): ?>
                                <div class="feed-item">
                                    <div class="feed-info">
                                        <div class="feed-icon" style="background:rgba(225, 29, 72, 0.1); color:var(--accent-red);"><i class="fas fa-minus"></i></div>
                                        <div class="feed-text">
                                            <h6>System Payout Completed</h6>
                                            <p><?= date('M j, Y • g:i a', strtotime($t['CreatedAtDriverTransactions'])) ?></p>
                                        </div>
                                    </div>
                                    <span style="font-weight:800; color:var(--text-dark);">-<?= number_format($t["Money"]) ?> MAD</span>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        function downloadAllDocs() {
            const urls = [
                <?php foreach($docs as $data) { if(!empty($data['val'])) echo '"'.$data['val'].'", '; } ?>
            ];
            if(urls.length === 0) return alert('No documents available to extract.');
            
            const zip = new JSZip();
            const folder = zip.folder("<?= htmlspecialchars(str_replace(' ', '_', $FullName)) ?>_Documents");
            
            const fetchPromises = urls.map(url => {
                return fetch(url).then(r => {
                    if (r.status === 200) return r.blob();
                    return Promise.reject(new Error(r.statusText));
                }).then(blob => {
                    const name = url.substring(url.lastIndexOf("/") + 1);
                    folder.file(name, blob);
                }).catch(e => console.warn('Could not load ' + url));
            });

            Promise.all(fetchPromises).then(() => {
                zip.generateAsync({ type: "blob" }).then((content) => {
                    saveAs(content, "<?= htmlspecialchars(str_replace(' ', '_', $FullName)) ?>_Documents.zip");
                });
            });
        }
    </script>
</body>
</html>