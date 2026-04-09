<?php require "conn.php"; 
$AdminID = $_COOKIE["AdminID"] ?? '';
$AdminName = $_COOKIE["AdminName"] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Zones | QOON Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="fontawesome-kit-5/css/all.css" rel="stylesheet">
    <style>
        :root {
            --bg-app: #F4F7FE;
            --bg-white: #FFFFFF;
            --text-dark: #2B3674;
            --text-gray: #A3AED0;
            --accent-purple: #4318FF;
            --accent-purple-light: #F4F7FE;
            --accent-green: #05CD99;
            --accent-red: #EE5D50;
            --border-color: #E2E8F0;
            --shadow-card: 0px 18px 40px rgba(112, 144, 176, 0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink: 0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb i { color: var(--accent-purple); font-size: 18px; }

        .layout-grid { display: grid; grid-template-columns: 320px 1fr; gap: 30px; }

        .glass-card { background: var(--bg-white); border-radius: 20px; padding: 25px; box-shadow: var(--shadow-card); border: 1px solid var(--border-color); align-self: start; }
        .card-header { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display:flex; align-items:center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom:15px; }

        .settings-nav { display: flex; flex-direction: column; gap: 10px; }
        .settings-nav a { display: flex; align-items: center; gap: 15px; padding: 16px 20px; border-radius: 12px; font-size: 14px; font-weight: 700; color: var(--text-gray); text-decoration: none; transition: 0.3s; border: 2px solid transparent;}
        .settings-nav a i { font-size: 18px; width: 24px; text-align: center; }
        .settings-nav a:hover { background: var(--bg-app); color: var(--accent-purple); }
        .settings-nav a.active { background: var(--accent-purple); color: #FFF; box-shadow: 0 10px 20px rgba(67,24,255,0.2); pointer-events: none; }

        .premium-table { width: 100%; border-collapse: collapse; }
        .premium-table th { padding: 15px; text-align: left; font-size: 12px; font-weight: 800; color: var(--text-gray); text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
        .premium-table td { padding: 15px; font-size: 14px; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .premium-table tr:hover td { background: var(--bg-app); }

        .form-control { width: 100%; background: var(--bg-app); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 18px; font-size: 14px; font-weight: 600; color: var(--text-dark); outline:none; transition:0.3s; }
        .form-control:focus { border-color: var(--accent-purple); box-shadow: 0 0 0 4px var(--accent-purple-light); background: #FFF; }
        select.form-control { appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%232B3674%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 15px top 50%; background-size: 10px auto; padding-right: 40px; }

        .btn-submit { display: flex; justify-content: center; align-items: center; gap: 8px; width: 100%; padding: 14px; background: var(--text-dark); color: #FFF; font-weight: 800; font-size: 14px; border-radius: 12px; border: none; cursor: pointer; transition: 0.3s; box-shadow: 0 5px 15px rgba(43, 54, 116, 0.2); }
        .btn-submit:hover { background: var(--accent-purple); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(67, 24, 255, 0.3); }

        .action-icon { display:inline-flex; width:32px; height:32px; align-items:center; justify-content:center; border-radius:8px; cursor:pointer; transition:0.3s; margin:0 3px; font-size:14px; color:#FFF; text-decoration:none; }
        .action-edit { background: var(--accent-purple); box-shadow: 0 4px 10px rgba(67,24,255,0.2); }
        .action-del { background: var(--accent-red); box-shadow: 0 4px 10px rgba(238,93,80,0.2); }
        .action-edit:hover, .action-del:hover { transform: translateY(-2px); color:#FFF; opacity:0.9; }

        input[type="file"]::file-selector-button { border: none; background: var(--bg-app); border-radius: 8px; padding: 6px 12px; color: var(--text-dark); font-weight: 700; cursor: pointer; margin-right: 10px; }

        /* SIDEBAR SUPPORT */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i, .nav-item img { width: 22px; text-align: center; }
        .nav-item:hover, .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <i class="fas fa-cog"></i> 
                    <span>Configuration & Defaults / Geographic Delivery Zones</span>
                </div>
            </header>

            <div class="layout-grid">
                
                <!-- Left: Settings Menu -->
                <div class="glass-card">
                    <div class="card-header">
                        <div><i class="fas fa-sliders-h" style="color:var(--text-gray); margin-right:8px;"></i> System Settings</div>
                    </div>
                    
                    <?php if($AdminID == 1){ ?>
                    <div class="settings-nav">
                        <a href="settings-profile.php"><i class="fas fa-user-shield"></i> Master Profile</a>
                        <a href="settings-staff-accounts.php"><i class="fas fa-users-cog"></i> Staff Accounts</a>
                        <a href="settings-delivery-zone.php" class="active"><i class="fas fa-map-marked-alt"></i> Delivery Zones</a>
                        <a href="bakat.php"><i class="fas fa-box-open"></i> App Packages</a>
                    </div>
                    <?php } else { ?>
                    <div class="settings-nav">
                        <a href="settings-profile.php"><i class="fas fa-user-shield"></i> Agent Profile</a>
                    </div>
                    <?php } ?>
                </div>

                <!-- Right: Zones Manager -->
                <div style="display:flex; flex-direction:column; gap:30px;">
                    
                    <div class="glass-card">
                        <div class="card-header">
                            <div><i class="fas fa-plus-circle" style="color:var(--accent-purple); margin-right:8px;"></i> Register New Zone</div>
                        </div>

                        <form action="addCity.php" method="POST" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:15px; align-items:end;">
                            <div>
                                <label style="font-size:13px; font-weight:700; color:var(--text-dark); margin-bottom:8px; display:block;">Country</label>
                                <select name="CountryID" class="form-control">
                                    <?php  
                                        $res = mysqli_query($con,"SELECT * FROM Countries");
                                        while($row = mysqli_fetch_assoc($res)){ if($row["FrenshName"] !=""){ 
                                    ?>
                                        <option value="<?php echo $row["CountryID"] ?>"><?php echo $row["FrenshName"] ?></option>
                                    <?php } } ?>			
                                </select>
                            </div>
                            <div>
                                <label style="font-size:13px; font-weight:700; color:var(--text-dark); margin-bottom:8px; display:block;">City Name</label>
                                <input type="text" placeholder="e.g. Casablanca" class="form-control" name="CityName" required>   
                            </div>
                            <div>
                                <label style="font-size:13px; font-weight:700; color:var(--text-dark); margin-bottom:8px; display:block;">Center Coordinates</label>
                                <input type="text" placeholder="Lat, Long" class="form-control" name="Coordinates" required>   
                            </div>
                            <div>
                                <label style="font-size:13px; font-weight:700; color:var(--text-dark); margin-bottom:8px; display:block;">Radius (Km)</label>
                                <input type="number" placeholder="Delivery zone" class="form-control" name="Deliveryzone">   
                            </div>
                            <div>
                                <button type="submit" class="btn-submit"><i class="fas fa-map-marker-alt"></i> Create Zone</button>
                            </div>
                        </form>

                        <hr style="border:0; border-top:1px solid var(--border-color); margin:25px 0;">

                        <form action="uploadExel.php" method="post" enctype="multipart/form-data" style="display:flex; align-items:center; gap:15px; background:var(--bg-app); padding:15px; border-radius:12px;">
                            <div style="font-size:14px; font-weight:700; color:var(--text-dark);"><i class="fas fa-file-excel" style="color:var(--accent-green);"></i> Batch Import via Excel:</div>
                            <input type="file" name="file" required style="flex:1;">
                            <button type="submit" name="submit_file" class="btn-submit" style="width:auto; padding:8px 20px; background:var(--accent-green);"><i class="fas fa-upload"></i> Process</button>
                        </form>

                    </div>

                    <div class="glass-card">
                        <div class="card-header">
                            <div><i class="fas fa-map" style="color:var(--accent-purple); margin-right:8px;"></i> Active Operating Geofences</div>
                        </div>

                        <div style="overflow-x:auto;">
                            <table class="premium-table">
                                <thead>
                                    <tr>
                                        <th>Visual Identifier</th>
                                        <th>Location</th>
                                        <th>Core GPS Anchor</th>
                                        <th>Coverage</th>
                                        <th>Geofence Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $res = mysqli_query($con,"SELECT * FROM DeliveryZone JOIN Countries ON DeliveryZone.CountryID = Countries.CountryID");
                                    while($row = mysqli_fetch_assoc($res)){ 
                                    ?>
                                    <tr>
                                        <td style="min-width:180px;">
                                            <div style="display:flex; align-items:center; gap:15px;">
                                                <img src="<?php echo $row['Photo']; ?>" style="width:60px; height:45px; border-radius:8px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.05); background:#E2E8F0;">
                                                <form method="POST" action="UpdateCityPhoto.php" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:5px;">
                                                    <input type="file" name="Photo" style="font-size:10px; width:80px;" required>
                                                    <input type="hidden" name="id" value="<?php echo $row['DeliveryZoneID']?>">
                                                    <button type="submit" style="background:var(--accent-purple); color:#FFF; font-size:10px; font-weight:700; border:none; border-radius:4px; padding:4px; cursor:pointer;">Update</button>
                                                </form>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size:11px; font-weight:700; color:var(--text-gray); text-transform:uppercase;"><?php echo $row["FrenshName"]; ?></div>
                                            <div style="font-size:15px; font-weight:800; color:var(--text-dark);"><?php echo $row["CityName"]; ?></div>
                                        </td>
                                        <td>
                                            <span style="background:var(--bg-app); padding:6px 12px; border-radius:8px; font-family:monospace; font-weight:700; font-size:12px; color:var(--accent-purple);">
                                                <i class="fas fa-location-arrow" style="margin-right:5px; color:var(--text-gray);"></i>
                                                <?php echo $row["CityLat"] . ', ' . $row["CityLongt"]; ?>
                                            </span>
                                        </td>
                                        <td><span style="font-weight:800; color:var(--accent-green);"><?php echo $row["Deliveryzone"]; ?> KMs</span></td>
                                        <td> 
                                            <?php
                                            $id = $row["DeliveryZoneID"];
                                            $borders = 'Pending Upload';
                                            $color = 'var(--accent-red)';
                                            $bg = 'rgba(238,93,80,0.1)';
                                            $res2 = mysqli_query($con,"SELECT * FROM CityBoders WHERE DeliveryZoneID = $id");                           
                                            while($row2 = mysqli_fetch_assoc($res2)){ 
                                                $borders = 'Active Vector'; 
                                                $color = 'var(--accent-blue)'; 
                                                $bg = 'rgba(57,101,255,0.1)'; 
                                                break; 
                                            }
                                            ?>
                                            <span style="background:<?php echo $bg; ?>; color:<?php echo $color; ?>; padding:4px 10px; border-radius:20px; font-size:11px; font-weight:800; text-transform:uppercase;">
                                                <?php echo $borders; ?>
                                            </span>
                                        </td>
                                        <td class="text-center">  
                                            <a href="DrowBorders.php?Lat=<?php echo $row["CityLat"] ?>&Long=<?php echo $row["CityLongt"]; ?>&d=<?php echo $row["DeliveryZoneID"] ?>" class="action-icon action-edit" title="Draw Vectors"><i class="fas fa-draw-polygon"></i></a>
                                            <a href="deleteCityAPI.php?id=<?php echo $row["DeliveryZoneID"] ?>" class="action-icon action-del" title="Delete Zone"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php } ?> 
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