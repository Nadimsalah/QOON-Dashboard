<?php
require "conn.php";
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

// Fetch ShopName for Header
$ShopName = "Unknown Shop";
$resShop = mysqli_query($con, "SELECT ShopName FROM Shops WHERE ShopID='$id'");
if($resShop && $row = mysqli_fetch_assoc($resShop)){
    $ShopName = $row["ShopName"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Story | <?= htmlspecialchars($ShopName) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
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

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        /* Header / Breadcrumb */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; }
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        /* Form Container layout */
        .layout-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start; }
        
        .upload-card { background: var(--bg-white); border-radius: 20px; box-shadow: var(--shadow-card); padding: 40px; border: 1px solid var(--border-color); }
        
        .image-uploader { width: 100%; min-height: 250px; border: 2px dashed rgba(98, 60, 234, 0.4); border-radius: 16px; background: var(--accent-purple-light); display: flex; flex-direction: column; align-items: center; justify-content: center; position: relative; transition: 0.3s; cursor: pointer; color: var(--accent-purple); margin-bottom: 30px; text-align: center; }
        .image-uploader:hover { background: #E6E1FA; border-color: var(--accent-purple); }
        .image-uploader i { font-size: 32px; margin-bottom: 12px; }
        .image-uploader h3 { font-size: 15px; font-weight: 700; margin-bottom: 4px;}
        .image-uploader p { font-size: 12px; font-weight: 600; opacity: 0.7;}
        .image-uploader input[type="file"] { position: absolute; top:0; left:0; width:100%; height:100%; opacity: 0; cursor: pointer; }

        .input-group { margin-bottom: 25px; display: flex; flex-direction: column; gap: 8px; }
        .input-group label { font-size: 13px; font-weight: 700; color: var(--text-dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .input-ui { background: var(--bg-app); border: 1px solid var(--border-color); padding: 16px; border-radius: 12px; font-size: 14px; font-weight: 600; color: var(--text-dark); outline: none; transition: 0.3s; width: 100%; cursor: pointer; appearance: none;}
        .input-ui:focus { border-color: var(--accent-purple); box-shadow: 0 0 0 3px rgba(98, 60, 234, 0.1); background: #FFF; }

        .btn-submit { width: 100%; padding: 18px; border: none; border-radius: 12px; background: linear-gradient(135deg, var(--accent-purple), #4F28D1); color: #FFF; font-size: 16px; font-weight: 800; cursor: pointer; transition: 0.3s; box-shadow: 0 8px 20px rgba(98, 60, 234, 0.2); margin-top: 10px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(98, 60, 234, 0.3); }

        .graphic-panel { display: flex; justify-content: center; align-items: center; height: 100%; padding: 20px; }
        .graphic-panel img { max-width: 100%; align-self: center; drop-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="shop.php"><i class="fas fa-arrow-left"></i> &nbsp; Shops</a>
                    <span>/</span>
                    <a href="shop-profile.php?id=<?= $id ?>"><?= htmlspecialchars($ShopName) ?></a>
                    <span>/</span>
                    <a href="shop-story.php?id=<?= $id ?>">Story Media</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">Add Media</span>
                </div>
            </header>

            <div class="layout-grid">
                
                <!-- Upload Form -->
                <div class="upload-card">
                    <h2 style="font-size: 22px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px;">Upload To Story</h2>
                    
                    <form id="uploadForm" method="POST" action="AddStoryShopAPI.php" enctype="multipart/form-data">
                        <input type="hidden" name="ShopID" value="<?= $id ?>">

                        <div class="image-uploader" id="uploadDropzone">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h3>Click to browse or drag media</h3>
                            <p id="fileNameDisplay">Supports .JPG, .PNG, .MP4</p>
                            <input type="file" name="Photo" id="mediaInput" accept="image/*,video/mp4" required>
                        </div>

                        <div class="input-group">
                            <label>Media Format Setting</label>
                            <div style="position: relative;">
                                <select name="Type" class="input-ui" required>
                                    <option value="Photos">Static Photo / Banner</option>
                                    <option value="Video">Video File (MP4)</option>
                                </select>
                                <i class="fas fa-chevron-down" style="position:absolute; right:15px; top:18px; color:var(--text-gray); pointer-events:none;"></i>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            Publish To Feed <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>

                <!-- Graphic Rail -->
                <div class="graphic-panel">
                    <img src="images/product-graphics.png" alt="Upload Illustration" onerror="this.onerror=null; this.src='https://illustrations.popsy.co/amber/app-launch.svg';">
                </div>

            </div>

        </main>
    </div>

    <!-- Upload Progress Modal overlay -->
    <div id="uploadOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(245,246,250,0.9); z-index:9999; backdrop-filter:blur(3px); flex-direction:column; align-items:center; justify-content:center;">
        <div style="background:#FFF; padding:40px; border-radius:20px; box-shadow:0 10px 40px rgba(0,0,0,0.1); text-align:center; width:400px; max-width:90%;">
            <div style="width:60px; height:60px; background:var(--accent-purple-light); color:var(--accent-purple); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:24px; margin:0 auto 20px;">
                <i class="fas fa-cloud-upload-alt fade-anim"></i>
            </div>
            <h3 id="uploadTitle" style="font-size:18px; font-weight:800; color:var(--text-dark); margin-bottom:5px;">Uploading Media Pipeline</h3>
            <p style="font-size:12px; font-weight:600; color:var(--text-gray);">Please do not close this window</p>
            <div style="width:100%; height:10px; background:var(--bg-app); border-radius:8px; margin-top:25px; overflow:hidden;">
                <div id="uploadProgressBar" style="height:100%; width:0%; background:linear-gradient(90deg, var(--accent-purple), #4F28D1); border-radius:8px; transition:width 0.2s;"></div>
            </div>
            <p id="uploadPercentText" style="font-size:15px; font-weight:800; color:var(--text-dark); margin-top:15px;">0%</p>
        </div>
    </div>

    <!-- Script to update filename and intercept AJAX POST -->
    <script>
        const form = document.getElementById('uploadForm');
        const input = document.getElementById('mediaInput');
        const display = document.getElementById('fileNameDisplay');
        const dropzone = document.getElementById('uploadDropzone');

        const overlay = document.getElementById('uploadOverlay');
        const bar = document.getElementById('uploadProgressBar');
        const percentText = document.getElementById('uploadPercentText');

        // Style Animation
        const style = document.createElement('style');
        style.innerHTML = `@keyframes pulse { 0% { opacity: 0.5; } 50% { opacity: 1; transform:translateY(-2px); } 100% { opacity: 0.5; } } .fade-anim { animation: pulse 1.5s infinite; }`;
        document.head.appendChild(style);

        input.addEventListener('change', function(e) {
            if(this.files && this.files.length > 0) {
                display.innerText = "Selected: " + this.files[0].name;
                display.style.color = 'var(--text-dark)';
                dropzone.style.borderColor = "var(--accent-purple)";
            }
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Stop standard blank-page redirect

            const file = input.files[0];
            if(!file) return;

            // Show UI Modal
            overlay.style.display = 'flex';

            const payload = new FormData(form);
            const xhr = new XMLHttpRequest();

            xhr.open('POST', 'AddStoryShopAPI.php', true);

            // Track Upload Progress
            xhr.upload.onprogress = function(event) {
                if(event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    bar.style.width = percent + '%';
                    percentText.innerText = percent + '%';
                }
            };

            // Handle Response
            xhr.onload = function() {
                if(xhr.status === 200) {
                    let res;
                    try { res = JSON.parse(xhr.responseText); } catch(ex) {}
                    
                    document.getElementById('uploadTitle').innerText = "Upload Complete!";
                    document.getElementById('uploadTitle').style.color = "var(--accent-green)";
                    percentText.innerText = "Processing Redirect...";
                    bar.style.background = "var(--accent-green)";

                    setTimeout(() => {
                        window.location.href = "shop-story.php?id=<?= $id ?>";
                    }, 800);
                } else {
                    alert('Server error occurred during upload.');
                    overlay.style.display = 'none';
                }
            };

            xhr.send(payload);
        });
    </script>
</body>
</html>