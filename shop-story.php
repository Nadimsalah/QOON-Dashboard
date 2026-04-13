<?php
require "conn.php";
$id = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

// Fetch ShopName for Header
$ShopName = "Unknown Shop";
$resShop = mysqli_query($con, "SELECT ShopName FROM Shops WHERE ShopID='$id'");
if($resShop && $row = mysqli_fetch_assoc($resShop)){
    $ShopName = $row["ShopName"];
}

// Fetch Stories
$stories = [];
$resStory = mysqli_query($con, "SELECT * FROM ShopStory WHERE ShopID='$id'"); 
if($resStory) {
    while($row = mysqli_fetch_assoc($resStory)) {
        $stories[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ShopName) ?> - Story Media | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-red: #E11D48;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --border-color: #F0F2F6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); height: 100vh; display: flex; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        /* Unified Sidebar CSS */
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
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; }
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        /* Story Grid */
        .story-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; align-items: start; }
        
        .story-card { background: var(--bg-white); border-radius: 20px; box-shadow: var(--shadow-card); overflow: hidden; position: relative; height: 350px; display: flex; flex-direction: column; border: 1px solid var(--border-color); transition: 0.3s; }
        .story-card:hover { transform: translateY(-5px); box-shadow: 0 15px 40px rgba(0,0,0,0.08); }
        
        .story-media-box { flex: 1; background: #000; position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;}
        .story-media-box img, .story-media-box video { width: 100%; height: 100%; object-fit: cover; }
        
        /* Media Type Badge */
        .media-badge { position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); color: #FFF; padding: 6px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; display: flex; align-items: center; gap: 6px; z-index: 2; }
        
        /* Delete Button */
        .del-btn { position: absolute; top: 15px; right: 15px; width: 32px; height: 32px; background: rgba(225, 29, 72, 0.9); backdrop-filter: blur(5px); color: #FFF; border: none; border-radius: 50%; font-size: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.2s; z-index: 2; }
        .del-btn:hover { background: var(--accent-red); transform: scale(1.1); }

        /* Add New Card */
        .add-card { background: var(--accent-purple-light); border: 2px dashed var(--accent-purple); justify-content: center; align-items: center; color: var(--accent-purple); text-decoration: none; cursor: pointer; }
        .add-card:hover { background: #E6E1FA; }
        .add-icon-wrapper { width: 60px; height: 60px; background: #FFF; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(98, 60, 234, 0.1); }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }
            .sidebar { display: none !important; }
            .main-panel { padding: 16px 16px 80px; overflow-y: visible; overflow-x: hidden; }
            .header { flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }
            .breadcrumb { font-size: 13px; flex-wrap: wrap; }
            .story-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 14px; }
            .story-card { height: 280px; }
        }
        @media (max-width: 600px) {
            .story-grid { grid-template-columns: 1fr 1fr; gap: 12px; }
            .story-card { height: 240px; border-radius: 14px; }
        }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="shop.php"><i class="fas fa-arrow-left"></i> &nbsp; Shops Directory</a>
                    <span>/</span>
                    <a href="shop-profile.php?id=<?= $id ?>"><?= htmlspecialchars($ShopName) ?></a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">Story Media</span>
                </div>
            </header>

            <div class="story-grid">
                
                <!-- Add Media CTA -->
                <a href="add-shopStory.php?id=<?= $id ?>" class="story-card add-card">
                    <div class="add-icon-wrapper"><i class="fas fa-plus"></i></div>
                    <span style="font-weight: 700; font-size: 15px;">Add New Story</span>
                    <span style="font-size: 12px; font-weight: 600; opacity: 0.7; margin-top: 5px;">Photo or Video</span>
                </a>

                <?php 
                    foreach($stories as $st) {
                        // The raw database string matches what we need for the DELETE api!
                        $rawMedia = $st['StoryPhoto']; 
                        
                        $media = $st['StoryPhoto'];
                        $type = $st['StotyType']; 
                        
                        // Handle all variations of localhost database prefix issues for frontend viewing
                        $media = str_replace('https://jibler.app/db/db/', '', $media);
                        $media = str_replace('https://jibler.ma/db/db/', '', $media);
                        $media = str_replace('https://jibler.ma/', '', $media); 
                        
                        // Because the raw url contains everything, we just grab the filename to securely pass via POST
                        $filename = basename($media);
                ?>
                <div class="story-card" id="card-<?= md5($filename) ?>">
                    <button class="del-btn" title="Delete Media" onclick="openDeleteModal('<?= $id ?>', '<?= $filename ?>', 'card-<?= md5($filename) ?>')"><i class="fas fa-trash-alt"></i></button>
                    
                    <div class="media-badge">
                        <?php if($type == 'Photos') { ?>
                            <i class="fas fa-image"></i> Image
                        <?php } else { ?>
                            <i class="fas fa-video"></i> Video
                        <?php } ?>
                    </div>

                    <div class="story-media-box">
                        <?php if($type == 'Photos') { ?>
                            <img src="<?= htmlspecialchars($media) ?>" alt="Story Media" onerror="this.onerror=null; this.src='images/placeholder_image.png'">
                        <?php } else { ?>
                            <video src="<?= htmlspecialchars($media) ?>" controls style="background:#000;"></video>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

            </div>
        </main>
    </div>

    <!-- Modern Delete Modal -->
    <div id="deleteOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(245,246,250,0.8); z-index:9999; backdrop-filter:blur(3px); flex-direction:column; align-items:center; justify-content:center; opacity:0; transition:0.2s;">
        <div style="background:#FFF; padding:40px; border-radius:20px; box-shadow:0 15px 50px rgba(0,0,0,0.15); text-align:center; width:450px; max-width:90%; transform:scale(0.9); transition:0.2s;" id="deleteBox">
            <div style="width:70px; height:70px; background:rgba(225,29,72,0.1); color:var(--accent-red); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:30px; margin:0 auto 20px;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 style="font-size:20px; font-weight:800; color:var(--text-dark); margin-bottom:8px;">Erase Media Forever?</h3>
            <p style="font-size:14px; font-weight:600; color:var(--text-gray); margin-bottom:30px; line-height:1.5;">This action cannot be undone. This media will be permanently deleted from the database and the dashboard.</p>
            
            <div style="display:flex; gap:15px; justify-content:center;">
                <button onclick="closeDeleteModal()" style="padding:14px 24px; border-radius:12px; background:var(--bg-app); border:1px solid var(--border-color); color:var(--text-dark); font-weight:700; cursor:pointer; width:50%; transition:0.2s;">Cancel</button>
                <button id="confirmDeleteBtn" style="padding:14px 24px; border-radius:12px; background:var(--accent-red); border:none; color:#FFF; font-weight:700; cursor:pointer; width:50%; box-shadow:0 5px 15px rgba(225,29,72,0.3); transition:0.2s;">Yes, Delete It</button>
            </div>
        </div>
    </div>

    <!-- Script to Handle Modern API Deletion -->
    <script>
        let targetShopID = null;
        let targetFilename = null;
        let targetCardID = null;

        const overlay = document.getElementById('deleteOverlay');
        const box = document.getElementById('deleteBox');

        function openDeleteModal(shopId, filename, cardId) {
            targetShopID = shopId;
            targetFilename = filename;
            targetCardID = cardId;

            overlay.style.display = 'flex';
            setTimeout(() => {
                overlay.style.opacity = '1';
                box.style.transform = 'scale(1)';
            }, 10);
        }

        function closeDeleteModal() {
            overlay.style.opacity = '0';
            box.style.transform = 'scale(0.9)';
            setTimeout(() => { overlay.style.display = 'none'; }, 200);
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            const btn = this;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            btn.style.opacity = '0.7';

            fetch('DeleteStoryAPI.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ShopID: targetShopID, StoryPhoto: targetFilename })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    // Poof! Fade out the card from the UI
                    const card = document.getElementById(targetCardID);
                    card.style.transform = 'scale(0.8)';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                    closeDeleteModal();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => alert('Network Error.'))
            .finally(() => {
                btn.innerHTML = 'Yes, Delete It';
                btn.style.opacity = '1';
            });
        });
    </script>
</body>
</html>