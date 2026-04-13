<?php
$Lat  = (float)($_GET['Lat']  ?? 0);
$Long = (float)($_GET['Long'] ?? 0);
$d    = (int)  ($_GET['d']    ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Draw Zone Boundary | QOON</title>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>

    <!-- Inter font + FA -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-master:  #F3F4F6;
            --bg-surface: #FFFFFF;
            --border:     #E5E7EB;
            --text-strong:#111827;
            --text-muted: #6B7280;
            --shadow-sm:  0 1px 2px rgba(0,0,0,0.05);
            --shadow-md:  0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --green-text: #059669; --green-bg: #ECFDF5;
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',-apple-system,sans-serif; -webkit-font-smoothing:antialiased; }

        body {
            background:var(--bg-master);
            display:flex;
            flex-direction:column;
            height:100vh;
            overflow:hidden;
        }

        /* Top bar */
        .top-bar {
            background:rgba(255,255,255,0.95);
            backdrop-filter:blur(16px);
            border-bottom:1px solid var(--border);
            padding:16px 32px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            flex-shrink:0;
            z-index:500;
        }
        .top-left { display:flex; align-items:center; gap:14px; }
        .back-btn {
            display:inline-flex; align-items:center; justify-content:center;
            width:34px; height:34px; border-radius:8px;
            border:1px solid var(--border); background:var(--bg-surface);
            color:var(--text-muted); text-decoration:none;
            box-shadow:var(--shadow-sm); transition:0.2s;
        }
        .back-btn:hover { border-color:var(--text-strong); color:var(--text-strong); }
        .top-title { font-size:16px; font-weight:700; color:var(--text-strong); }
        .top-sub   { font-size:12px; color:var(--text-muted); font-weight:500; margin-top:2px; }

        .top-right { display:flex; align-items:center; gap:10px; }

        .coords-tag {
            display:inline-flex; align-items:center; gap:6px;
            padding:7px 12px; border-radius:8px;
            background:#F3F4F6; border:1px solid var(--border);
            font-size:12px; font-weight:600; color:var(--text-muted);
            font-family:ui-monospace,monospace;
        }
        .coords-tag i { font-size:10px; }

        .btn-save {
            display:inline-flex; align-items:center; gap:8px;
            padding:10px 20px; border-radius:8px;
            background:var(--text-strong); color:#fff;
            font-size:14px; font-weight:600; border:none; cursor:pointer;
            transition:0.2s; box-shadow:var(--shadow-sm);
        }
        .btn-save:hover { background:#1F2937; box-shadow:var(--shadow-md); }
        .btn-save:disabled { opacity:0.4; cursor:not-allowed; }

        /* Instruction bar */
        .hint-bar {
            background:#F9FAFB;
            border-bottom:1px solid var(--border);
            padding:10px 32px;
            display:flex;
            align-items:center;
            gap:24px;
            flex-shrink:0;
        }
        .hint-item {
            display:flex; align-items:center; gap:7px;
            font-size:12px; font-weight:600; color:var(--text-muted);
        }
        .hint-item i { font-size:13px; }
        .hint-step {
            display:inline-flex; align-items:center; justify-content:center;
            width:20px; height:20px; border-radius:50%;
            background:var(--text-strong); color:#fff;
            font-size:10px; font-weight:700;
        }

        /* Map fills remaining height */
        #map {
            flex:1;
            width:100%;
        }

        /* Success toast */
        .toast {
            position:fixed; bottom:32px; left:50%; transform:translateX(-50%) translateY(80px);
            background:var(--text-strong); color:#fff;
            padding:14px 24px; border-radius:10px;
            font-size:14px; font-weight:600;
            display:flex; align-items:center; gap:10px;
            box-shadow:0 10px 25px rgba(0,0,0,0.2);
            transition:transform 0.3s ease, opacity 0.3s ease;
            opacity:0; z-index:9999; pointer-events:none;
        }
        .toast.show { transform:translateX(-50%) translateY(0); opacity:1; }
        .toast.success { background:var(--green-text); }
        .toast i { font-size:16px; }

        /* Polygon count badge */
        #polyCount {
            display:none;
            padding:6px 12px; border-radius:8px;
            background:var(--green-bg); border:1px solid #BBF7D0;
            font-size:12px; font-weight:700; color:var(--green-text);
        }
        #polyCount.visible { display:inline-flex; align-items:center; gap:6px; }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <div class="top-bar">
        <div class="top-left">
            <a href="settings-delivery-zone.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <div>
                <div class="top-title">Draw Zone Boundary</div>
                <div class="top-sub">Zone ID #<?= $d ?> &mdash; Draw a polygon to define the delivery boundary</div>
            </div>
        </div>
        <div class="top-right">
            <div class="coords-tag">
                <i class="fas fa-location-arrow"></i>
                <?= $Lat ?>, <?= $Long ?>
            </div>
            <div id="polyCount"><i class="fas fa-draw-polygon"></i> <span id="polyLabel">Polygon ready</span></div>
            <button class="btn-save" id="saveBtn" onclick="submitBoundary()" disabled>
                <i class="fas fa-save"></i> Save Boundary
            </button>
        </div>
    </div>

    <!-- Instruction Hints -->
    <div class="hint-bar">
        <div class="hint-item"><span class="hint-step">1</span> Click the polygon tool in the top-left of the map</div>
        <div class="hint-item"><span class="hint-step">2</span> Click to place each boundary point</div>
        <div class="hint-item"><span class="hint-step">3</span> Close the shape by clicking the first point</div>
        <div class="hint-item"><span class="hint-step">4</span><i class="fas fa-save" style="color:var(--text-strong);"></i> Click <strong>Save Boundary</strong> above</div>
    </div>

    <!-- Map -->
    <div id="map"></div>

    <!-- Toast notification -->
    <div class="toast success" id="toast">
        <i class="fas fa-check-circle"></i>
        <span>Boundary saved successfully!</span>
    </div>

    <script>
        const LAT  = <?= $Lat ?>;
        const LONG = <?= $Long ?>;
        const ZONE_ID = <?= $d ?>;

        // Initialize map
        const map = L.map('map').setView([LAT, LONG], 10);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        // Draw feature group
        const drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        // Draw controls - polygon only
        const drawControl = new L.Control.Draw({
            edit:  { featureGroup: drawnItems },
            draw:  {
                polygon:   true,
                circle:    false,
                rectangle: false,
                marker:    false,
                polyline:  false,
                circlemarker: false
            }
        });
        map.addControl(drawControl);

        // When a polygon is drawn
        map.on('draw:created', function(e) {
            drawnItems.clearLayers(); // Only one polygon at a time
            drawnItems.addLayer(e.layer);
            document.getElementById('saveBtn').disabled = false;
            document.getElementById('polyCount').classList.add('visible');
            document.getElementById('polyLabel').textContent = 'Polygon ready — ' + e.layer.getLatLngs()[0].length + ' points';
        });

        // Reset save button if polygon deleted
        map.on('draw:deleted', function() {
            if(drawnItems.getLayers().length === 0) {
                document.getElementById('saveBtn').disabled = true;
                document.getElementById('polyCount').classList.remove('visible');
            }
        });

        // Submit polygon to backend
        function submitBoundary() {
            let cityBounds = [];
            drawnItems.eachLayer(function(layer) {
                if(layer instanceof L.Polygon) {
                    cityBounds = layer.getLatLngs()[0].map(p => [p.lat, p.lng]);
                }
            });

            if(cityBounds.length === 0) return;

            const btn = document.getElementById('saveBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

            const formData = new FormData();
            formData.append('cityBounds', JSON.stringify(cityBounds));
            formData.append('idw', ZONE_ID);

            fetch('SaveLocations.php', { method:'POST', body:formData })
                .then(r => r.json())
                .then(() => showToast())
                .catch(err => {
                    console.error('Error:', err);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save"></i> Save Boundary';
                });
        }

        function showToast() {
            const t = document.getElementById('toast');
            t.classList.add('show');
            setTimeout(() => {
                t.classList.remove('show');
                setTimeout(() => window.history.back(), 400);
            }, 2000);
        }
    </script>
</body>
</html>
