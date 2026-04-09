<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$OrderID = isset($_GET["OrderID"]) ? (int)$_GET["OrderID"] : 0;
// Note: We use LEFT JOIN for Drivers since some orders might not have a driver yet.
$res = mysqli_query($con, "SELECT Orders.*, Users.name as BuyerName, Users.UserPhoto, Drivers.FName as DriverName, Drivers.PersonalPhoto 
                           FROM Orders 
                           LEFT JOIN Users ON Orders.UserID = Users.UserID 
                           LEFT JOIN Drivers ON Orders.DelvryId = Drivers.DriverID 
                           WHERE Orders.OrderID = $OrderID");

$orderData = [];
if ($res && mysqli_num_rows($res) > 0) {
    $orderData = mysqli_fetch_assoc($res);
} else {
    // Failsafe if not found
    die("<h2>Order not found within the database registry.</h2>");
}

$OrderDetails = htmlspecialchars($orderData["OrderDetails"]); 
$CreatedAtOrders = htmlspecialchars($orderData["CreatedAtOrders"]); 
$DestinationName = htmlspecialchars($orderData["DestinationName"] ?? 'N/A');
$DestnationPhoto = htmlspecialchars($orderData["DestnationPhoto"] ?? 'images/ensan.jpg');
$OrderPrice = $orderData["OrderPrice"]; 
$OrderState = $orderData["OrderState"];
$Method = $orderData["Method"]; 
                 
$UserPhoto = (!empty($orderData["UserPhoto"])) ? $orderData["UserPhoto"] : 'images/ensan.jpg'; 
$name = (!empty($orderData["BuyerName"])) ? htmlspecialchars($orderData["BuyerName"]) : 'Unknown Buyer';
                 
$FName = (!empty($orderData["DriverName"])) ? htmlspecialchars($orderData["DriverName"]) : 'Pending Pickup';
$PersonalPhoto = (!empty($orderData["PersonalPhoto"])) ? $orderData["PersonalPhoto"] : 'imgg/2.png';

$UserLat = (float)$orderData["UserLat"]; 
$UserLongt = (float)$orderData["UserLongt"];
$DestLat = (float)$orderData["DestnationLat"]; 
$DestLongt = (float)$orderData["DestnationLongt"];

$DelvryId = isset($orderData["DelvryId"]) ? $orderData["DelvryId"] : "0";

function haversineDist($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km
    $latDelta = deg2rad($lat2 - $lat1);
    $lonDelta = deg2rad($lon2 - $lon1);
    $a = sin($latDelta / 2) * sin($latDelta / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDelta / 2) * sin($lonDelta / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return $earthRadius * $c;
}

$distanceKm = round(haversineDist($UserLat, $UserLongt, $DestLat, $DestLongt), 2);
$estTime = round($distanceKm * 4); // Assuming ~15km/h city driving
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Order #<?= $OrderID ?> | QOON</title>
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

        .main-panel { flex: 1; padding: 30px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        /* Header / Breadcrumb */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink:0; }
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        /* Complex Grid Layout */
        .tracking-grid { display: grid; grid-template-columns: 350px 1fr 400px; gap: 20px; flex: 1; min-height: 0; }
        .t-card { background: var(--bg-white); border-radius: 20px; box-shadow: var(--shadow-card); display: flex; flex-direction: column; overflow: hidden; }
        .t-card-head { padding: 20px 25px; border-bottom: 2px solid var(--border-color); font-size: 16px; font-weight: 800; color: var(--text-dark); display: flex; justify-content: space-between; align-items: center;}
        
        /* Profile Chain */
        .participant-chain { display: flex; flex-direction: column; gap: 20px; padding: 25px; flex:1; overflow-y:auto; }
        .par-box { display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--bg-app); border-radius: 16px; border: 1px solid var(--border-color); position:relative; }
        .par-box img { width: 55px; height: 55px; border-radius: 12px; object-fit: cover; }
        .par-text h4 { font-size: 11px; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 3px; }
        .par-text h3 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
        .par-distance { height: 30px; width: 2px; background: var(--border-color); margin: -10px 0 -10px 40px; position:relative;}

        /* Metrics Deck */
        .metrics-deck { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; padding: 25px; background: #FFF; border-top: 1px solid var(--border-color); }
        .m-pill { background: var(--accent-purple-light); padding: 15px; border-radius: 14px; text-align: center; }
        .m-pill.dark { background: var(--text-dark); color: #FFF; }
        .m-pill h5 { font-size: 11px; font-weight: 700; opacity: 0.8; margin-bottom: 5px; text-transform:uppercase; }
        .m-pill h2 { font-size: 18px; font-weight: 800; }

        /* Firebase Chat Stream */
        .chat-area { flex: 1; padding: 25px; display: flex; flex-direction: column; gap: 15px; overflow-y: auto; background: #F9FAFB; }
        .bubble { padding: 12px 18px; border-radius: 16px; font-size: 13px; font-weight: 600; max-width: 85%; line-height: 1.5; }
        .bubble-recever { background: var(--bg-white); border: 1px solid var(--border-color); color: var(--text-dark); align-self: flex-start; border-bottom-left-radius: 4px; box-shadow: 0 4px 10px rgba(0,0,0,0.02);}
        .bubble-sender { background: var(--accent-purple); color: #FFF; align-self: flex-end; border-bottom-right-radius: 4px; box-shadow: 0 4px 15px rgba(98, 60, 234, 0.2); }
        
        /* Map API Container */
        #map2 { flex: 1; width: 100%; height: 100%; background: #E5E7EB; }

    </style>
</head>
<body>

    <!-- Firebase Realtime DB Drivers -->
    <script src='https://cdn.firebase.com/js/client/2.2.1/firebase.js'></script>
    
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="orders.php"><i class="fas fa-boxes"></i> Master Log</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">Tracking Node #<?= $OrderID ?></span>
                </div>
                <div>
                   <span style="background:var(--accent-purple); color:#FFF; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700;">
                       <i class="far fa-clock"></i> <?= $CreatedAtOrders ?>
                   </span>
                </div>
            </header>

            <div class="tracking-grid">
                
                <!-- Col 1: Participants Chain -->
                <div class="t-card">
                    <div class="t-card-head">
                        <span>Participants</span>
                        <?php
                            $st = $OrderState;
                            $ds = $st;
                            $bgc = 'var(--accent-blue)';
                            if($st == 'Done' || $st == 'Rated') {
                                $ds = 'Delivered';
                                $bgc = 'var(--accent-green)';
                            } elseif ($st == 'Cancelled') {
                                $bgc = 'var(--accent-red)';
                            } elseif ($st == 'waiting') {
                                $bgc = 'var(--accent-orange)';
                            }
                        ?>
                        <span style="padding:4px 10px; background:<?= $bgc ?>; color:#FFF; border-radius:6px; font-size:10px; font-weight:800; text-transform:uppercase;">
                            <?= htmlspecialchars($ds) ?>
                        </span>
                    </div>
                    <div class="participant-chain">
                        <div class="par-box">
                            <img src="<?= $UserPhoto ?>">
                            <div class="par-text"><h4>Buyer</h4><h3><?= $name ?></h3></div>
                        </div>
                        <div class="par-distance"></div>
                        <div class="par-box">
                            <img src="<?= $DestnationPhoto ?>">
                            <div class="par-text"><h4>Shop Designation</h4><h3><?= $DestinationName ?></h3></div>
                        </div>
                        <div class="par-distance"></div>
                        <div class="par-box" style="border-color:var(--accent-purple);">
                            <img src="<?= $PersonalPhoto ?>">
                            <div class="par-text"><h4 style="color:var(--accent-purple);">Assigned Driver</h4><h3><?= $FName ?></h3></div>
                        </div>
                        
                        <!-- Order Payload -->
                        <div style="background:var(--bg-app); border-radius:12px; padding:15px; margin-top:10px;">
                            <h4 style="font-size:11px; font-weight:800; color:var(--text-gray); margin-bottom:8px;">ORDER PAYLOAD</h4>
                            <p style="font-size:13px; font-weight:600; color:var(--text-dark); line-height:1.5;"><?= $OrderDetails ?></p>
                        </div>
                    </div>
                    
                    <div class="metrics-deck">
                        <div class="m-pill dark">
                            <h5>Delivery Fee</h5>
                            <h2><?= $OrderPrice ?> MAD</h2>
                        </div>
                        <div class="m-pill">
                            <h5 style="color:var(--accent-purple);">Est. Traverse</h5>
                            <h2 style="color:var(--accent-purple);"><?= $distanceKm ?> KM</h2>
                        </div>
                    </div>
                </div>

                <!-- Col 2: Live Tracking Satellite -->
                <div class="t-card" style="border:1px solid var(--border-color);">
                    <div class="t-card-head" style="justify-content:flex-start; gap:10px;">
                        <i class="fas fa-satellite-dish" style="color:var(--accent-purple); animation: pulse 2s infinite;"></i> 
                        GPS Telemetry Link
                    </div>
                    <div id="map2"></div>
                </div>

                <!-- Col 3: Encrypted Chat Node -->
                <div class="t-card">
                    <div class="t-card-head">
                        <i class="fas fa-comment-dots" style="color:var(--text-gray);"></i> Operations Comms
                    </div>
                    <div class="chat-area" id="chats">
                        <!-- Inserted dynamically via Firebase -->
                    </div>
                </div>

            </div>

        </main>
    </div>

    <!-- Firebase Script Architecture -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/9.6.11/firebase-app.js";
        import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/9.6.11/firebase-database.js";

        const firebaseConfig = {
            apiKey: "AIzaSyBJgv2Ltzm5ZMdgKNUcs8stCTJ9lHgFxBQ",
            authDomain: "jibler-37339.firebaseapp.com",
            databaseURL: "https://jibler-37339-default-rtdb.firebaseio.com",
            projectId: "jibler-37339",
            storageBucket: "jibler-37339.firebasestorage.app",
            messagingSenderId: "874793508550",
            appId: "1:874793508550:web:1e16215a9b53f2314a41c7",
            measurementId: "G-6NWSEM7BK9"
        };
        const app = initializeApp(firebaseConfig);
        const db = getDatabase(app);

        /* ============================
           Google Maps Hook
        ============================ */
        let map2;
        let marker2;
        function initMap2(lat, lng) {
            const driverLoc = { lat: lat, lng: lng };
            map2 = new google.maps.Map(document.getElementById('map2'), {
                center: driverLoc, zoom: 14,
                styles: [
                    { "elementType": "geometry", "stylers": [{"color": "#f5f5f5"}] },
                    { "elementType": "labels.icon", "stylers": [{"visibility": "off"}] },
                    { "featureType": "water", "stylers": [{"color": "#c9c9c9"}] }
                ]
            });
            // Marker
            marker2 = new google.maps.Marker({
                position: driverLoc,
                map: map2,
                icon: {
                    url: 'https://qoon.app//userDriver/UserDriverApi/photo/68041732089.png',
                    scaledSize: new google.maps.Size(40, 40)
                }
            });
        }
        function updateMarker(lat, lng) {
            const newLoc = { lat: lat, lng: lng };
            marker2.setPosition(newLoc);
            map2.panTo(newLoc);
        }

        /* Activate Driver Polling */
        const driverId = "<?= $DelvryId ?>";
        if(driverId && driverId !== "0") {
            const driverRef = ref(db, 'drivers/' + driverId);
            onValue(driverRef, (snapshot) => {
                const data = snapshot.val();
                if (data && data.latitude && data.longitude) {
                    const lat = parseFloat(data.latitude);
                    const lng = parseFloat(data.longitude);
                    if (!map2) initMap2(lat, lng);
                    else updateMarker(lat, lng);
                }
            });
        } else {
            document.getElementById('map2').innerHTML = '<div style="display:flex; height:100%; align-items:center; justify-content:center; color:#A6A9B6; font-size:14px; font-weight:700;">No Driver Assidned. Offline.</div>';
        }

        /* ============================
           Firebase Chat Injection
        ============================ */
        var container = document.getElementById('chats');
        var ref2 = new Firebase("https://jibler-37339-default-rtdb.firebaseio.com/Messages/<?= $OrderID ?>");
        
        ref2.orderByChild("height").on("child_added", function(snapshot) {
            let val = snapshot.val();
            if(val.sender === 'driver' || val.sender === 'vendor') {
                container.innerHTML += `<div class="bubble bubble-recever">${val.message}</div>`;
            } else {
                container.innerHTML += `<div class="bubble bubble-sender">${val.message}</div>`;
            }
            container.scrollTop = container.scrollHeight;
        });

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1DPGIuuuJKZMXlK_ehSH07-5Ab2ab9-8&v=weekly" defer></script>
</body>
</html>