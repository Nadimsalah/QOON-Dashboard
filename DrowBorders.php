<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaflet Draw Map with Form Submission</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-draw/dist/leaflet.draw.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-draw/dist/leaflet.draw.js"></script>
	
	 <style>
        

        .snackbar {
            display: none;
            padding: 15px;
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
	
	
</head>

<?php $Lat = $_GET["Lat"];$Long = $_GET["Long"]; $d = $_GET["d"]; ?>




<body>

    <div id="map" style="height: 700px;"></div>
	<div class="snackbar" id="snackbar"><center>Success</center></div>
    <button onclick="submitForm()">إرسال</button>

    <script>
        var map = L.map('map').setView([<?php echo $Lat; ?>, <?php echo $Long; ?>], 8);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            edit: {
                featureGroup: drawnItems
            },
            draw: {
                polygon: true,
                circle: false,
                rectangle: false,
                marker: false,
                polyline: false
            }
        });
        map.addControl(drawControl);

        map.on('draw:created', function (e) {
            var layer = e.layer;
            drawnItems.addLayer(layer);
        });

        function submitForm() {
            // جمع النقاط المدن من drawnItems
            var cityBounds = [];
            drawnItems.eachLayer(function (layer) {
                if (layer instanceof L.Polygon) {
                    cityBounds = layer.getLatLngs()[0].map(function (point) {
                        return [point.lat, point.lng];
                    });
                }
            });

            // طباعة النقاط في alert قبل الإرسال
          

            // قم بإرسال النقاط إلى صفحة PHP عبر AJAX أو ضمن نموذج HTML
            // يمكنك استخدام jQuery.ajax أو fetch API لإجراء طلب AJAX
            // هنا يتم استخدام تعليمات بسيطة للتوضيح
            var formData = new FormData();
            formData.append('cityBounds', JSON.stringify(cityBounds));
			
			formData.append('idw', <?php echo $d; ?>);

            // هنا يتم استخدام fetch API لإرسال البيانات
            fetch('SaveLocations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // هنا يمكنك التعامل مع الرد من صفحة PHP
                showSnackbar();
				
            })
            .catch(error => console.error('Error:', error));
        }
		
		function showSnackbar() {
                snackbar.style.display = 'block';
                setTimeout(function () {
                    snackbar.style.display = 'none';
					window.history.back();
                }, 2000); // 2000 مللي ثانية (2 ثانية)
            }
    </script>
</body>
</html>
