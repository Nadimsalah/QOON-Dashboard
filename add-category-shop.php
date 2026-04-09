<?php 
require "conn.php";
$id = $_GET["id"] ?? '';

// Fetch Shop Details for Header Context (Optional but good for UX)
$shopName = "Categories";
if ($id) {
    if ($stmt = $con->prepare("SELECT ShopName FROM Shops WHERE ShopID = ?")) {
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $resSn = $stmt->get_result();
        if ($rowSn = $resSn->fetch_assoc()) {
            $shopName = htmlspecialchars($rowSn['ShopName']) . " - Categories";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shop Categories | QOON</title>
    <!-- Fonts & Icons -->
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
            --accent-red: #EF4444;
            --border-color: #F0F2F6;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --shadow-float: 0 12px 35px rgba(0, 0, 0, 0.05);
            --radius: 16px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        /* Sidebar Imports */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        /* Module Header */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: var(--radius); box-shadow: var(--shadow-card); flex-shrink:0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        .flex-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 30px;
        }

        .glass-panel {
            background: var(--bg-white);
            border-radius: var(--radius);
            padding: 35px;
            box-shadow: var(--shadow-card);
        }

        .panel-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Form Area */
        .input-group {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .input-group input[type="text"] {
            flex: 1;
            padding: 16px 20px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--bg-app);
            font-size: 15px;
            font-weight: 500;
            color: var(--text-dark);
            outline: none;
            transition: all 0.2s;
        }
        
        .input-group input[type="text"]:focus {
            border-color: var(--accent-purple);
            background: #FFF;
            box-shadow: 0 0 0 4px var(--accent-purple-light);
        }

        .submit-btn {
            background: var(--accent-purple);
            color: #FFF;
            border: none;
            padding: 0 25px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 4px 15px rgba(98, 60, 234, 0.3);
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(98, 60, 234, 0.4);
        }

        /* List Area */
        .cat-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .cat-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px;
            background: var(--bg-app);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            transition: 0.2s;
        }

        .cat-item:hover {
            background: #FFF;
            border-color: var(--accent-purple);
            transform: translateX(4px);
            box-shadow: var(--shadow-float);
        }

        .cat-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cat-name i {
            color: var(--text-gray);
            font-size: 18px;
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--accent-red);
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s;
        }

        .btn-delete:hover {
            background: var(--accent-red);
            color: #FFF;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: var(--bg-app);
            border-radius: 12px;
            color: var(--text-gray);
            font-weight: 600;
            font-size: 14px;
            border: 2px dashed var(--border-color);
        }

    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="shopOnMap.php"><i class="fas fa-store"></i> Shop Management</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);"><?= $shopName ?></span>
                </div>
                <div style="font-size:13px; font-weight:700; color:var(--text-gray); background:var(--bg-app); padding:8px 16px; border-radius:10px;">
                    Shop ID: <?= htmlspecialchars($id) ?>
                </div>
            </header>

            <div class="flex-grid">
                
                <!-- Category Management Block -->
                <div class="glass-panel">
                    <div class="panel-title">
                        <i class="fas fa-layer-group" style="color: var(--accent-purple);"></i> Manage Categories
                    </div>

                    <form action="AddCatShopApi.php" method="POST">
                        <div class="input-group">
                            <input type="text" placeholder="Type a new category name..." name="CategoryName" required>
                            <input type="hidden" name="ShopID" value="<?= htmlspecialchars($id) ?>">
                            <button type="submit" class="submit-btn"><i class="fas fa-plus"></i> ADD</button>
                        </div>
                    </form>

                    <div class="cat-list">
                        <?php 
                        $ShopsCategory = 'ShopsCategory';
                        $resCount = 0;
                        if ($stmt = $con->prepare("SELECT * FROM $ShopsCategory WHERE ShopID=?")) {
                            $stmt->bind_param("s", $id);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = $res->fetch_assoc()) {
                                $resCount++;
                        ?>
                            <div class="cat-item">
                                <div class="cat-name">
                                    <i class="fas fa-folder"></i>
                                    <?= htmlspecialchars($row["CategoryName"]) ?>
                                </div>
                                <a href="DeleteCatShopApi.php?id=<?= $row["CategoryShopID"] ?>&ShopID=<?= urlencode($id) ?>" class="btn-delete">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </div>
                        <?php 
                            }
                        }
                        if ($resCount === 0) {
                            echo '<div class="empty-state"><i class="fas fa-folder-open fa-3x" style="opacity:0.3; margin-bottom:15px; display:block;"></i>This shop has no active categories. Add one above!</div>';
                        }
                        ?>          
                    </div>
                </div>

                <!-- Graphic Showcase -->
                <div class="glass-panel" style="display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, var(--accent-purple), #4A2BBF); color: #FFF; text-align: center;">
                    <i class="fas fa-boxes fa-5x" style="opacity: 0.2; margin-bottom: 30px;"></i>
                    <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 15px;">Organize Products</h2>
                    <p style="font-size: 14px; font-weight: 500; opacity: 0.8; max-width: 80%; line-height: 1.6;">Use categories to intelligently cluster an infinite number of shop products to optimize the end-user navigation experience within the app.</p>
                </div>

            </div>
        </main>
    </div>
</body>
</html>