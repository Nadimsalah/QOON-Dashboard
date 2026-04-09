<?php 
require "conn.php";
$id = $_GET["id"] ?? '';

// Fetch Shop Details for Header Context
$shopName = "Products";
if ($id) {
    if ($stmt = $con->prepare("SELECT ShopName FROM Shops WHERE ShopID = ?")) {
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $resSn = $stmt->get_result();
        if ($rowSn = $resSn->fetch_assoc()) {
            $shopName = htmlspecialchars($rowSn['ShopName']) . " - Products";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Shop Products | QOON</title>
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
            --accent-blue: #007AFF;
            --accent-green: #10B981;
            --accent-red: #EF4444;
            --border-color: #F0F2F6;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --shadow-float: 0 12px 35px rgba(0, 0, 0, 0.05);
            --radius: 16px;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: var(--radius); box-shadow: var(--shadow-card); flex-shrink:0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb a { color: var(--text-gray); text-decoration: none; transition: 0.2s; }
        .breadcrumb a:hover { color: var(--accent-purple); }

        .glass-panel {
            background: var(--bg-white);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow-card);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .panel-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-primary {
            background: var(--accent-purple);
            color: #FFF;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
            box-shadow: 0 4px 15px rgba(98, 60, 234, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(98, 60, 234, 0.4);
            color:#FFF;
        }

        /* Products Grid */
        .prod-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .prod-card {
            background: var(--bg-app);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            transition: 0.3s;
        }

        .prod-card:hover {
            background: #FFF;
            border-color: var(--accent-purple);
            box-shadow: var(--shadow-float);
            transform: translateY(-5px);
        }

        .p-top {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .p-top img {
            width: 70px;
            height: 70px;
            border-radius: 12px;
            object-fit: cover;
            background: #FFF;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .p-info { flex: 1; }
        .p-name { font-size: 16px; font-weight: 800; color: var(--text-dark); line-height: 1.3; margin-bottom: 4px; }
        .p-cat { font-size: 12px; font-weight: 600; color: var(--accent-purple); background: var(--accent-purple-light); padding: 3px 8px; border-radius: 6px; display: inline-block;}
        
        .p-price {
            font-size: 18px;
            font-weight: 800;
            color: var(--accent-green);
        }

        .p-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid var(--border-color);
            padding-top: 15px;
            margin-top: auto;
        }

        .p-date {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-gray);
        }

        .p-actions {
            display: flex;
            gap: 10px;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            text-decoration: none;
            transition: 0.2s;
        }

        .a-edit { background: rgba(0, 122, 255, 0.1); color: var(--accent-blue); }
        .a-edit:hover { background: var(--accent-blue); color: #FFF; }
        
        .a-del { background: rgba(239, 68, 68, 0.1); color: var(--accent-red); }
        .a-del:hover { background: var(--accent-red); color: #FFF; }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--text-gray); font-weight: 600; }
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

            <div class="glass-panel">
                <div class="panel-header">
                    <div class="panel-title"><i class="fas fa-hamburger" style="color: var(--accent-purple);"></i> Product Inventory</div>
                    <a href="add-product.php?id=<?= htmlspecialchars($id) ?>" class="btn-primary">
                        <i class="fas fa-plus"></i> New Product
                    </a>
                </div>

                <?php 
                $resCount = 0;
                if ($stmt = $con->prepare("SELECT * FROM Foods JOIN ShopsCategory ON Foods.FoodCatID = ShopsCategory.CategoryShopID WHERE ShopsCategory.ShopID=? ORDER BY FoodID DESC")) {
                    $stmt->bind_param("s", $id);
                    $stmt->execute();
                    $res = $stmt->get_result();
                    
                    if ($res->num_rows > 0) {
                        echo '<div class="prod-grid">';
                        while ($row = $res->fetch_assoc()) {
                ?>
                            <div class="prod-card">
                                <div class="p-top">
                                    <img src="<?= htmlspecialchars($row["FoodPhoto"]) ?>" onerror="this.src='images/placeholder.png'">
                                    <div class="p-info">
                                        <div class="p-name"><?= htmlspecialchars($row["FoodName"]) ?></div>
                                        <div class="p-cat"><?= htmlspecialchars($row["CategoryName"]) ?></div>
                                    </div>
                                    <div class="p-price"><?= number_format($row["FoodPrice"], 2) ?> <span style="font-size:11px;">MAD</span></div>
                                </div>
                                
                                <div class="p-footer">
                                    <div class="p-date"><i class="far fa-calendar-alt"></i> <?= date('M d, Y', strtotime($row["CreatedAtFoods"])) ?></div>
                                    <div class="p-actions">
                                        <!-- Note: using update-product.php for view/edit as per legacy logic -->
                                        <a href="update-product.php?ProdId=<?= $row["FoodID"] ?>&shopid=<?= htmlspecialchars($id) ?>" class="action-icon a-edit" title="Edit Product">
                                            <i class="fas fa-pencil-alt"></i>
                                        </a>
                                        <a href="DeleteProduct.php?ProdId=<?= $row["FoodID"] ?>&shopid=<?= htmlspecialchars($id) ?>" class="action-icon a-del" title="Delete Product" onclick="return confirm('Are you sure you want to delete this product?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                <?php
                        }
                        echo '</div>';
                    } else {
                        echo '<div class="empty-state">
                                <i class="fas fa-box-open fa-4x" style="opacity:0.2; margin-bottom:15px; display:block;"></i>
                                No products found for this shop. Click "New Product" to build inventory.
                              </div>';
                    }
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>