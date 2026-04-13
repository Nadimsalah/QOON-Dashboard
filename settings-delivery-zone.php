<?php
require "conn.php";
$AdminID   = $_COOKIE["AdminID"]   ?? '';
$AdminName = $_COOKIE["AdminName"] ?? '';

$resCountries = mysqli_query($con, "SELECT * FROM Countries");
$countries = [];
while($row = mysqli_fetch_assoc($resCountries)) {
    if(!empty($row['FrenshName'])) $countries[] = $row;
}

$resZones = mysqli_query($con, "SELECT * FROM DeliveryZone JOIN Countries ON DeliveryZone.CountryID = Countries.CountryID");
$zones = [];
while($row = mysqli_fetch_assoc($resZones)) { $zones[] = $row; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Zones | QOON</title>
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
            --red-bg:     #FEF2F2; --red-text:    #DC2626;
            --shadow-sm:  0 1px 2px rgba(0,0,0,0.05);
            --shadow-md:  0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',-apple-system,sans-serif; -webkit-font-smoothing:antialiased; }
        body { background:var(--bg-master); color:var(--text-base); display:flex; height:100vh; overflow:hidden; }
        .layout-wrapper { display:flex; width:100%; height:100%; }

        main.content-area { flex:1; overflow-y:auto; display:flex; flex-direction:column; }
        main.content-area::-webkit-scrollbar { width:6px; }
        main.content-area::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:10px; }

        .header-bar {
            position:sticky; top:0; z-index:20;
            background:rgba(255,255,255,0.9); backdrop-filter:blur(16px);
            border-bottom:1px solid var(--border);
            padding:20px 40px;
        }
        .header-bar h1 { font-size:18px; font-weight:700; color:var(--text-strong); }
        .header-bar p  { font-size:13px; color:var(--text-muted); font-weight:500; margin-top:3px; }

        .page-body {
            padding:40px; max-width:1200px; margin:0 auto; width:100%;
            display:grid; grid-template-columns:240px 1fr; gap:24px; align-items:start;
        }

        /* Left Nav */
        .settings-nav-card { background:var(--bg-surface); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden; }
        .nav-card-head { padding:16px 20px; border-bottom:1px solid var(--border); font-size:13px; font-weight:700; color:var(--text-strong); background:#F9FAFB; display:flex; align-items:center; gap:8px; }
        .nav-card-head i { color:var(--text-muted); }
        .nav-list-inner { padding:8px; display:flex; flex-direction:column; gap:2px; }
        .nav-link { display:flex; align-items:center; gap:12px; padding:11px 14px; border-radius:8px; font-size:14px; font-weight:600; color:var(--text-muted); text-decoration:none; transition:0.15s; }
        .nav-link i { width:16px; text-align:center; font-size:14px; }
        .nav-link:hover { background:#F3F4F6; color:var(--text-strong); }
        .nav-link.active { background:#F3F4F6; color:var(--text-strong); font-weight:700; }

        /* Right column */
        .right-col { display:flex; flex-direction:column; gap:20px; }

        /* Panel */
        .panel { background:var(--bg-surface); border:1px solid var(--border); border-radius:14px; box-shadow:var(--shadow-sm); overflow:hidden; }
        .panel-head { padding:18px 24px; border-bottom:1px solid var(--border); background:#F9FAFB; display:flex; justify-content:space-between; align-items:center; }
        .panel-head h2 { font-size:15px; font-weight:700; color:var(--text-strong); display:flex; align-items:center; gap:8px; }
        .panel-head h2 i { font-size:13px; color:var(--text-muted); }
        .panel-body { padding:24px; }

        /* Form grid */
        .form-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:16px; align-items:end; }
        .inp-group { display:flex; flex-direction:column; gap:6px; }
        .inp-group label { font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .inp-field { padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:14px; font-weight:500; color:var(--text-strong); background:var(--bg-surface); outline:none; transition:0.2s; box-shadow:var(--shadow-sm); width:100%; }
        .inp-field:focus { border-color:var(--border-md); box-shadow:0 0 0 3px rgba(17,24,39,0.06); }
        select.inp-field { cursor:pointer; }

        .divider { border:0; border-top:1px solid var(--border); margin:20px 0; }

        /* Excel import row */
        .import-row {
            display:flex; align-items:center; gap:16px;
            padding:16px; border-radius:10px;
            background:#F9FAFB; border:1px solid var(--border);
        }
        .import-row .import-label { font-size:13px; font-weight:600; color:var(--text-strong); display:flex; align-items:center; gap:8px; white-space:nowrap; }
        .import-row .import-label i { color:var(--green-text); }
        input[type="file"] { font-size:13px; font-weight:500; color:var(--text-muted); flex:1; }
        input[type="file"]::file-selector-button {
            border:1px solid var(--border); background:var(--bg-surface); border-radius:6px;
            padding:6px 12px; color:var(--text-strong); font-weight:600; font-size:12px; cursor:pointer; margin-right:10px;
        }

        /* Buttons */
        .btn-primary { display:inline-flex; align-items:center; gap:8px; padding:10px 18px; border-radius:8px; background:var(--text-strong); color:#fff; font-size:13px; font-weight:600; border:none; cursor:pointer; transition:0.2s; box-shadow:var(--shadow-sm); white-space:nowrap; }
        .btn-primary:hover { background:#1F2937; box-shadow:var(--shadow-md); }
        .btn-green { background:var(--green-text); color:#fff; display:inline-flex; align-items:center; gap:8px; padding:9px 16px; border-radius:8px; font-size:13px; font-weight:600; border:none; cursor:pointer; transition:0.2s; }
        .btn-green:hover { opacity:0.85; }

        /* Zones Table */
        table { width:100%; border-collapse:collapse; }
        th { background:#F9FAFB; padding:14px 20px; text-align:left; font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; border-bottom:1px solid var(--border); }
        td { padding:16px 20px; border-bottom:1px solid var(--border); font-size:14px; background:#FFFFFF; vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:#F9FAFB; }

        .zone-photo { width:56px; height:42px; border-radius:7px; object-fit:cover; background:#F3F4F6; border:1px solid var(--border); display:block; }
        .photo-cell { display:flex; align-items:center; gap:12px; }
        .photo-update-form { display:flex; flex-direction:column; gap:4px; }
        .photo-update-form input[type="file"] { font-size:10px; width:90px; }
        .photo-update-btn { font-size:10px; font-weight:700; padding:3px 8px; border-radius:4px; border:none; background:var(--text-strong); color:#fff; cursor:pointer; }

        .city-name { font-size:14px; font-weight:700; color:var(--text-strong); }
        .city-country { font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.4px; }

        .coords-pill {
            display:inline-flex; align-items:center; gap:6px;
            padding:5px 10px; border-radius:6px;
            background:#F3F4F6; border:1px solid var(--border);
            font-family:ui-monospace,monospace; font-size:12px; font-weight:600; color:var(--text-muted);
        }
        .radius-val { font-size:14px; font-weight:700; color:var(--green-text); }

        .status-badge { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; }
        .st-active  { background:var(--blue-bg);  color:var(--blue-text); }
        .st-pending { background:var(--red-bg);   color:var(--red-text); }

        .btn-icon { display:inline-flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:7px; font-size:13px; text-decoration:none; transition:0.15s; border:1px solid var(--border); margin-left:4px; }
        .btn-icon-draw { background:var(--bg-surface); color:var(--blue-text); border-color:var(--border); }
        .btn-icon-draw:hover { background:var(--blue-bg); border-color:#BFDBFE; }
        .btn-icon-del  { background:var(--bg-surface); color:var(--red-text);  border-color:var(--border); }
        .btn-icon-del:hover  { background:var(--red-bg);  border-color:#FECACA; }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }
            .sb-container { display: none !important; }
            main.content-area { overflow-y: visible; }

            .header-bar { padding: 14px 16px; position: static; }
            .header-bar h1 { font-size: 18px; }

            .page-body {
                padding: 12px 12px 80px;
                grid-template-columns: 1fr;
                gap: 12px;
            }

            /* Settings nav → horizontal scrollable pill bar */
            .settings-nav-card { border-radius: 12px; }
            .nav-card-head { display: none; }
            .nav-list-inner {
                flex-direction: row;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scroll-snap-type: x mandatory;
                padding: 6px;
                gap: 4px;
                scrollbar-width: none;
            }
            .nav-list-inner::-webkit-scrollbar { display: none; }
            .nav-link {
                flex: 0 0 auto;
                scroll-snap-align: start;
                white-space: nowrap;
                padding: 10px 16px;
                border-radius: 8px;
                font-size: 13px;
            }

            /* Panel */
            .panel-head { flex-wrap: wrap; gap: 8px; padding: 14px 16px; }
            .panel-body { padding: 16px; }

            /* Import row: stack label + file + button vertically */
            .import-row { flex-direction: column; align-items: flex-start; gap: 10px; }
            input[type="file"] { width: 100%; }

            /* Table horizontal scroll */
            table { min-width: 560px; }
        }
        @media (max-width: 600px) {
            .nav-link { font-size: 12px; padding: 9px 12px; }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">
            <header class="header-bar">
                <div>
                    <h1>Delivery Zones</h1>
                    <p>Configure geographic operating zones and delivery boundaries.</p>
                </div>
            </header>

            <div class="page-body">

                <!-- Left: Settings Nav -->
                <div class="settings-nav-card">
                    <div class="nav-card-head"><i class="fas fa-sliders-h"></i> System Settings</div>
                    <div class="nav-list-inner">
                        <a href="settings-profile.php" class="nav-link">
                            <i class="fas fa-user-shield"></i> Master Profile
                        </a>
                        <?php if($AdminID == 1): ?>
                        <a href="settings-staff-accounts.php" class="nav-link">
                            <i class="fas fa-users-cog"></i> Staff Accounts
                        </a>
                        <a href="settings-delivery-zone.php" class="nav-link active">
                            <i class="fas fa-map-marked-alt"></i> Delivery Zones
                        </a>
                        <a href="bakat.php" class="nav-link">
                            <i class="fas fa-box-open"></i> App Packages
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="right-col">

                    <!-- Register New Zone -->
                    <div class="panel">
                        <div class="panel-head">
                            <h2><i class="fas fa-plus-circle"></i> Register New Zone</h2>
                        </div>
                        <div class="panel-body">
                            <form action="addCity.php" method="POST">
                                <div class="form-grid">
                                    <div class="inp-group">
                                        <label>Country</label>
                                        <select name="CountryID" class="inp-field">
                                            <?php foreach($countries as $c): ?>
                                                <option value="<?= $c['CountryID'] ?>"><?= htmlspecialchars($c['FrenshName']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="inp-group">
                                        <label>City Name</label>
                                        <input type="text" name="CityName" class="inp-field" placeholder="e.g. Casablanca" required>
                                    </div>
                                    <div class="inp-group">
                                        <label>Center Coordinates</label>
                                        <input type="text" name="Coordinates" class="inp-field" placeholder="Lat, Long" required>
                                    </div>
                                    <div class="inp-group">
                                        <label>Radius (KM)</label>
                                        <input type="number" name="Deliveryzone" class="inp-field" placeholder="e.g. 25">
                                    </div>
                                    <div class="inp-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn-primary"><i class="fas fa-map-marker-alt"></i> Create Zone</button>
                                    </div>
                                </div>
                            </form>

                            <hr class="divider">

                            <!-- Excel Import -->
                            <form action="uploadExel.php" method="POST" enctype="multipart/form-data">
                                <div class="import-row">
                                    <div class="import-label"><i class="fas fa-file-excel"></i> Batch Import via Excel</div>
                                    <input type="file" name="file" required>
                                    <button type="submit" name="submit_file" class="btn-green"><i class="fas fa-upload"></i> Process</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Active Zones Table -->
                    <div class="panel">
                        <div class="panel-head">
                            <h2><i class="fas fa-map"></i> Active Operating Geofences</h2>
                            <span style="font-size:13px; font-weight:600; color:var(--text-muted);"><?= count($zones) ?> zone<?= count($zones) != 1 ? 's' : '' ?></span>
                        </div>
                        <div style="overflow-x:auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Visual</th>
                                        <th>Location</th>
                                        <th>GPS Anchor</th>
                                        <th>Radius</th>
                                        <th>Border Status</th>
                                        <th style="text-align:right; padding-right:20px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($zones as $row):
                                        $zoneID  = $row['DeliveryZoneID'];
                                        $res2    = mysqli_query($con, "SELECT 1 FROM CityBoders WHERE DeliveryZoneID = $zoneID LIMIT 1");
                                        $hasVec  = mysqli_num_rows($res2) > 0;
                                        $stClass = $hasVec ? 'st-active'  : 'st-pending';
                                        $stLabel = $hasVec ? 'Active Vector' : 'Pending Upload';
                                    ?>
                                    <tr>
                                        <!-- Photo + Upload -->
                                        <td>
                                            <div class="photo-cell">
                                                <img src="<?= htmlspecialchars($row['Photo'] ?? '') ?>" class="zone-photo" onerror="this.style.display='none'">
                                                <form method="POST" action="UpdateCityPhoto.php" enctype="multipart/form-data" class="photo-update-form">
                                                    <input type="file" name="Photo" required>
                                                    <input type="hidden" name="id" value="<?= $zoneID ?>">
                                                    <button type="submit" class="photo-update-btn">Update</button>
                                                </form>
                                            </div>
                                        </td>
                                        <!-- Location -->
                                        <td>
                                            <div class="city-country"><?= htmlspecialchars($row['FrenshName']) ?></div>
                                            <div class="city-name"><?= htmlspecialchars($row['CityName']) ?></div>
                                        </td>
                                        <!-- GPS -->
                                        <td>
                                            <span class="coords-pill">
                                                <i class="fas fa-location-arrow" style="font-size:10px;"></i>
                                                <?= htmlspecialchars($row['CityLat']) ?>, <?= htmlspecialchars($row['CityLongt']) ?>
                                            </span>
                                        </td>
                                        <!-- Radius -->
                                        <td><span class="radius-val"><?= htmlspecialchars($row['Deliveryzone']) ?> KM</span></td>
                                        <!-- Border Status -->
                                        <td><span class="status-badge <?= $stClass ?>"><?= $stLabel ?></span></td>
                                        <!-- Actions -->
                                        <td style="text-align:right; padding-right:20px;">
                                            <a href="DrowBorders.php?Lat=<?= $row['CityLat'] ?>&Long=<?= $row['CityLongt'] ?>&d=<?= $zoneID ?>"
                                               class="btn-icon btn-icon-draw" title="Draw Boundary Vectors">
                                                <i class="fas fa-draw-polygon"></i>
                                            </a>
                                            <a href="deleteCityAPI.php?id=<?= $zoneID ?>"
                                               onclick="return confirm('Delete zone <?= htmlspecialchars($row['CityName']) ?>?')"
                                               class="btn-icon btn-icon-del" title="Delete Zone">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($zones)): ?>
                                    <tr><td colspan="6" style="text-align:center; padding:40px; color:var(--text-muted); font-weight:500;">No delivery zones configured yet.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</body>
</html>