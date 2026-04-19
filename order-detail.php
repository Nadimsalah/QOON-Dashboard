<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$OrderID = isset($_GET["OrderID"]) ? (int) $_GET["OrderID"] : 0;
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

$UserLat = (float) $orderData["UserLat"];
$UserLongt = (float) $orderData["UserLongt"];
$DestLat = (float) $orderData["DestnationLat"];
$DestLongt = (float) $orderData["DestnationLongt"];

$DelvryId = isset($orderData["DelvryId"]) ? $orderData["DelvryId"] : "0";

function haversineDist($lat1, $lon1, $lat2, $lon2)
{
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
            --bg-app: #F5F6FA;
            --bg-white: #FFFFFF;
            --text-dark: #2A3042;
            --text-gray: #A6A9B6;
            --accent-purple: #623CEA;
            --accent-purple-light: #F0EDFD;
            --accent-green: #10B981;
            --accent-blue: #007AFF;
            --accent-orange: #F59E0B;
            --accent-red: #EF4444;
            --border-color: #F0F2F6;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--bg-app);
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        .app-envelope {
            width: 100%;
            height: 100%;
            display: flex;
            overflow: hidden;
        }

        /* Sidebar CSS */
        .sidebar {
            width: 260px;
            background: var(--bg-white);
            display: flex;
            flex-direction: column;
            padding: 40px 0;
            border-right: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .logo-box {
            display: flex;
            align-items: center;
            padding: 0 30px;
            gap: 12px;
            margin-bottom: 50px;
            text-decoration: none;
        }

        .logo-box img {
            max-height: 50px;
            width: auto;
            object-fit: contain;
        }

        .nav-list {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 0 20px;
            flex: 1;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 20px;
            border-radius: 12px;
            color: var(--text-gray);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .nav-item i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        .nav-item.active {
            background: var(--accent-purple-light);
            color: var(--accent-purple);
            position: relative;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: -20px;
            top: 50%;
            transform: translateY(-50%);
            height: 60%;
            width: 4px;
            background: var(--accent-purple);
            border-radius: 0 4px 4px 0;
        }

        .main-panel {
            flex: 1;
            padding: 30px 40px;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Header / Breadcrumb */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            background: var(--bg-white);
            padding: 15px 25px;
            border-radius: 16px;
            box-shadow: var(--shadow-card);
            flex-shrink: 0;
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .breadcrumb a {
            color: var(--text-gray);
            text-decoration: none;
            transition: 0.2s;
        }

        .breadcrumb a:hover {
            color: var(--accent-purple);
        }

        /* Complex Grid Layout */
        .tracking-grid {
            display: grid;
            grid-template-columns: 350px 1fr 400px;
            gap: 20px;
            flex: 1;
            min-height: 0;
        }

        .t-card {
            background: var(--bg-white);
            border-radius: 20px;
            box-shadow: var(--shadow-card);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .t-card-head {
            padding: 20px 25px;
            border-bottom: 2px solid var(--border-color);
            font-size: 16px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Profile Chain */
        .participant-chain {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 25px;
            flex: 1;
            overflow-y: auto;
        }

        .par-box {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: var(--bg-app);
            border-radius: 16px;
            border: 1px solid var(--border-color);
            position: relative;
        }

        .par-box img {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            object-fit: cover;
        }

        .par-text h4 {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-gray);
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .par-text h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
        }

        .par-distance {
            height: 30px;
            width: 2px;
            background: var(--border-color);
            margin: -10px 0 -10px 40px;
            position: relative;
        }

        /* Metrics Deck */
        .metrics-deck {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            padding: 25px;
            background: #FFF;
            border-top: 1px solid var(--border-color);
        }

        .m-pill {
            background: var(--accent-purple-light);
            padding: 15px;
            border-radius: 14px;
            text-align: center;
        }

        .m-pill.dark {
            background: var(--text-dark);
            color: #FFF;
        }

        .m-pill h5 {
            font-size: 11px;
            font-weight: 700;
            opacity: 0.8;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .m-pill h2 {
            font-size: 18px;
            font-weight: 800;
        }

        /* Firebase Chat Stream */
        .chat-area {
            flex: 1;
            padding: 25px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            overflow-y: auto;
            background: #F9FAFB;
        }

        .bubble {
            padding: 12px 18px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 600;
            max-width: 85%;
            line-height: 1.5;
        }

        .bubble-recever {
            background: var(--bg-white);
            border: 1px solid var(--border-color);
            color: var(--text-dark);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
        }

        .bubble-sender {
            background: var(--accent-purple);
            color: #FFF;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 15px rgba(98, 60, 234, 0.2);
        }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }

            /* Hide desktop sidebar */
            .sidebar { display: none !important; }

            .main-panel {
                padding: 16px 16px 80px;
                overflow-y: visible;
                overflow-x: hidden;
            }

            /* Header */
            .header { flex-wrap: wrap; gap: 8px; margin-bottom: 16px; padding: 12px 16px; }
            .breadcrumb { font-size: 13px; flex-wrap: wrap; }

            /* 3-col → single column */
            .tracking-grid {
                grid-template-columns: 1fr;
                gap: 16px;
                flex: none;
                min-height: unset;
            }

            /* t-card: auto height, no overflow clipping */
            .t-card { overflow: visible; }

            /* Map: fixed height since flex:1 needs a constrained parent */
            #map2 { flex: none; height: 320px; border-radius: 0 0 20px 20px; }

            /* Chat: max-height so it doesn't expand infinitely */
            .chat-area { max-height: 350px; }

            /* Participant chain: no flex scroll needed */
            .participant-chain { overflow-y: visible; flex: none; }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .main-panel { padding: 12px 12px 80px; }

            .header { padding: 10px 14px; }
            .breadcrumb { font-size: 12px; }

            #map2 { height: 240px; }
            .chat-area { max-height: 280px; }

            .par-box img { width: 44px; height: 44px; }
            .par-text h3 { font-size: 14px; }

            .m-pill h2 { font-size: 16px; }
            .t-card-head { font-size: 14px; padding: 14px 18px; }
        }
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
                    <span
                        style="background:var(--accent-purple); color:#FFF; padding:8px 16px; border-radius:10px; font-size:12px; font-weight:700;">
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
                        if ($st == 'Done' || $st == 'Rated') {
                            $ds = 'Delivered';
                            $bgc = 'var(--accent-green)';
                        } elseif ($st == 'Cancelled') {
                            $bgc = 'var(--accent-red)';
                        } elseif ($st == 'waiting') {
                            $bgc = 'var(--accent-orange)';
                        }
                        ?>
                        <span
                            style="padding:4px 10px; background:<?= $bgc ?>; color:#FFF; border-radius:6px; font-size:10px; font-weight:800; text-transform:uppercase;">
                            <?= htmlspecialchars($ds) ?>
                        </span>
                    </div>
                    <div class="participant-chain">
                        <div class="par-box">
                            <img src="<?= $UserPhoto ?>">
                            <div class="par-text">
                                <h4>Buyer</h4>
                                <h3><?= $name ?></h3>
                            </div>
                        </div>
                        <div class="par-distance"></div>
                        <div class="par-box">
                            <img src="<?= $DestnationPhoto ?>">
                            <div class="par-text">
                                <h4>Shop Designation</h4>
                                <h3><?= $DestinationName ?></h3>
                            </div>
                        </div>
                        <div class="par-distance"></div>
                        <div class="par-box" style="border-color:var(--accent-purple);">
                            <img src="<?= $PersonalPhoto ?>">
                            <div class="par-text">
                                <h4 style="color:var(--accent-purple);">Assigned Driver</h4>
                                <h3><?= $FName ?></h3>
                            </div>
                        </div>

                        <!-- Order Payload -->
                        <div style="background:var(--bg-app); border-radius:12px; padding:15px; margin-top:10px;">
                            <h4 style="font-size:11px; font-weight:800; color:var(--text-gray); margin-bottom:8px;">
                                ORDER PAYLOAD</h4>
                            <p style="font-size:13px; font-weight:600; color:var(--text-dark); line-height:1.5;">
                                <?= $OrderDetails ?>
                            </p>
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
                        <i class="fas fa-satellite-dish"
                            style="color:var(--accent-purple); animation: pulse 2s infinite;"></i>
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
                    { "elementType": "geometry", "stylers": [{ "color": "#f5f5f5" }] },
                    { "elementType": "labels.icon", "stylers": [{ "visibility": "off" }] },
                    { "featureType": "water", "stylers": [{ "color": "#c9c9c9" }] }
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
        if (driverId && driverId !== "0") {
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

        ref2.orderByChild("height").on("child_added", function (snapshot) {
            let val = snapshot.val();
            if (val.sender === 'driver' || val.sender === 'vendor') {
                container.innerHTML += `<div class="bubble bubble-recever">${val.message}</div>`;
            } else {
                container.innerHTML += `<div class="bubble bubble-sender">${val.message}</div>`;
            }
            container.scrollTop = container.scrollHeight;
        });

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1DPGIuuuJKZMXlK_ehSH07-5Ab2ab9-8&v=weekly"
        defer></script>

    <!-- ALI AI ASSISTANT (Order Details) -->
    <style>
        .ai-fab {
            position: fixed;
            bottom: 25px; right: 25px;
            width: 62px; height: 62px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(59, 130, 246, 0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0;
            border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(59, 130, 246, 0.45); }
        .ai-fab img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .ai-fab-dot {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: #3B82F6;
            border: 2.5px solid #fff;
            border-radius: 50%;
            animation: fabPulse 1.8s ease-in-out infinite;
        }
        @keyframes fabPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            50%      { box-shadow: 0 0 0 6px rgba(59, 130, 246, 0); }
        }

        /* ── AI CHAT POPUP ── */
        .ai-popup {
            position: fixed;
            bottom: 100px; right: 25px;
            width: 390px; height: 580px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            display: flex; flex-direction: column;
            overflow: hidden;
            z-index: 9998;
            transform: translateY(20px) scale(0.97);
            opacity: 0;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(0,0,0,0.06);
        }
        .ai-popup.open {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: all;
        }

        /* Header */
        .ai-head {
            background: linear-gradient(135deg, #3B82F6, #2563EB);
            color: #fff;
            padding: 16px 18px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .ai-head-titles { display:flex; flex-direction:column; line-height:1.3; }
        .ai-head-titles span { font-weight:700; font-size:15px; }
        .ai-head-titles small { font-size:11px; opacity:0.85; margin-top:2px; }
        .ai-close {
            cursor:pointer; font-size:18px; opacity:0.8;
            transition:0.2s; width:32px; height:32px;
            display:flex; align-items:center; justify-content:center;
            border-radius:50%; background:rgba(255,255,255,0.15);
        }
        .ai-close:hover { opacity:1; background:rgba(255,255,255,0.25); }

        /* Messages */
        .ai-body {
            flex: 1; padding: 16px;
            overflow-y: auto;
            display: flex; flex-direction: column; gap: 12px;
            background: #F5F6FA;
            scroll-behavior: smooth;
        }
        .ai-body::-webkit-scrollbar { width: 4px; }
        .ai-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 4px; }

        .ai-msg { display:flex; max-width:82%; line-height:1.55; font-size:13.5px; }
        .ai-msg.bot  { align-self: flex-start; }
        .ai-msg.user { align-self: flex-end; }
        .ai-bubble {
            padding: 11px 15px; border-radius: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            word-break: break-word;
        }
        .ai-msg.bot  .ai-bubble { background:#fff; color:#111827; border-bottom-left-radius:4px; border:1px solid #E5E7EB; }
        .ai-msg.user .ai-bubble { background:#3B82F6; color:#fff; border-bottom-right-radius:4px; }

        /* Typing */
        .ai-typing {
            font-size:12px; color:#9CA3AF;
            display:none; padding:0 16px 10px;
            background:#F5F6FA; flex-shrink:0;
        }
        .ai-typing span { display:inline-block; animation: typBounce 1.2s infinite; }
        .ai-typing span:nth-child(2) { animation-delay:.2s; }
        .ai-typing span:nth-child(3) { animation-delay:.4s; }
        @keyframes typBounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-5px)} }

        /* Input foot */
        .ai-foot {
            padding: 12px 14px;
            background: #fff; border-top: 1px solid #F0F0F0;
            display:flex; gap:10px; align-items:center;
            flex-shrink: 0;
        }
        .ai-input {
            flex: 1; border: 1.5px solid #E5E7EB; border-radius: 22px;
            padding: 10px 16px; font-size:13.5px;
            outline:none; background:#F9FAFB;
            transition:0.2s; font-family:inherit;
            resize: none; line-height: 1.4;
        }
        .ai-input:focus { border-color:#3B82F6; background:#fff; box-shadow:0 0 0 3px rgba(59,130,246,0.08); }
        .ai-send {
            width: 40px; height: 40px; border-radius: 50%;
            background:#3B82F6; color:white;
            border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#2563EB; transform:scale(1.05); }

        /* Mobile */
        @media (max-width: 600px) {
            .ai-fab  { right: 16px; bottom: 80px; }
            .ai-popup { right: 0; left: 0; bottom: 0; width: 100%; height: 90dvh; border-radius: 24px 24px 0 0; transform: translateY(100%); }
            .ai-popup.open { transform: translateY(0); }
            .ai-foot { padding-bottom: max(12px, env(safe-area-inset-bottom)); }
        }
    </style>

    <div class="ai-fab" id="aiOrdersFab" onclick="toggleOrdersAI()">
        <img src="ali.webp" alt="Ali"
             onerror="this.src='https://ui-avatars.com/api/?name=Ali&background=DBEAFE&color=2563EB&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>
    <div class="ai-popup" id="aiOrdersPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="ali.webp" alt="Ali" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);" onerror="this.src='https://ui-avatars.com/api/?name=Ali&background=DBEAFE&color=2563EB&bold=true'">
                    <div title="Online 24/24" style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#3B82F6; border:2px solid #fff; border-radius:50%;"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Ali AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#fff; font-size:8px;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleOrdersAI()"></i>
        </div>
        <div class="ai-body" id="aiOrdersBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">👋 Hello! I am <b>Ali</b>, your QOON Tracking assistant. I'm monitoring Order #<?= $OrderID ?>. How can I assist you?</div>
            </div>
        </div>
        <div class="ai-typing" id="aiOrdersTyping">Ali is checking order telemetry...</div>
        <div class="ai-foot">
            <input type="text" id="aiOrdersInput" class="ai-input" placeholder="Ask Ali about this order..." onkeypress="if(event.key === 'Enter') sendOrdersAIMessage()">
            <button class="ai-send" onclick="sendOrdersAIMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        let ordersChatHistory = [];
        
        function toggleOrdersAI() {
            document.getElementById('aiOrdersPopup').classList.toggle('open');
            document.getElementById('aiOrdersInput').focus();
        }

        async function sendOrdersAIMessage() {
            const input = document.getElementById('aiOrdersInput');
            const msg = input.value.trim();
            if(!msg) return;

            addOrdersAIMsg('user', msg);
            input.value = '';
            
            const typing = document.getElementById('aiOrdersTyping');
            typing.style.display = 'block';
            scrollOrdersAIBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        message: msg, 
                        history: ordersChatHistory, 
                        page_data: { 
                            type: 'order_tracking',
                            order_id: <?= $OrderID ?>,
                            state: '<?= $OrderState ?>',
                            destination: '<?= $DestinationName ?>',
                            buyer: '<?= $name ?>',
                            driver: '<?= $FName ?>',
                            price: '<?= $OrderPrice ?>'
                        } 
                    })
                });
                const textOutput = await res.text();
                typing.style.display = 'none';
                
                try {
                    const data = JSON.parse(textOutput);
                    if(data.reply) {
                        addOrdersAIMsg('bot', data.reply);
                        ordersChatHistory.push({ role: 'user', content: msg });
                        ordersChatHistory.push({ role: 'ai', content: data.reply });
                    } else {
                        addOrdersAIMsg('bot', 'AI connection issue.');
                    }
                } catch (e) {
                    addOrdersAIMsg('bot', 'Error processing AI response.');
                }
            } catch(e) {
                typing.style.display = 'none';
                addOrdersAIMsg('bot', 'Connection error.');
            }
        }

        function addOrdersAIMsg(sender, text) {
            const body = document.getElementById('aiOrdersBody');
            const div = document.createElement('div');
            div.className = `ai-msg ${sender}`;
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            div.innerHTML = `<div class="ai-bubble">${formattedText}</div>`;
            body.appendChild(div);
            scrollOrdersAIBottom();
        }

        function scrollOrdersAIBottom() {
            const body = document.getElementById('aiOrdersBody');
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>

</html>