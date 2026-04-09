<?php 
require "conn.php";
$id = $_GET["id"] ?? '';

// Fetch Shop Details for Header Context
$shopName = "Add Product";
if ($id) {
    if ($stmt = $con->prepare("SELECT ShopName FROM Shops WHERE ShopID = ?")) {
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $resSn = $stmt->get_result();
        if ($rowSn = $resSn->fetch_assoc()) {
            $shopName = htmlspecialchars($rowSn['ShopName']) . " - Add Product";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | QOON</title>
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

        .panel-title { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display:flex; align-items:center; gap:12px; }

        /* Modern Form Elements */
        .form-group { margin-bottom: 24px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; color: var(--text-gray); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .form-control {
            width: 100%;
            padding: 14px 18px;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            background: var(--bg-app);
            font-size: 15px;
            font-weight: 500;
            color: var(--text-dark);
            outline: none;
            transition: 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--accent-purple);
            background: #FFF;
            box-shadow: 0 0 0 4px var(--accent-purple-light);
        }
        
        select.form-control { appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="%23A6A9B6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>'); background-repeat: no-repeat; background-position: right 18px center; }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Image Uploader */
        .img-upload-box {
            border: 2px dashed var(--border-color);
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            background: var(--bg-app);
            transition: 0.3s;
            cursor: pointer;
            position: relative;
            margin-bottom: 24px;
        }
        
        .img-upload-box:hover {
            border-color: var(--accent-purple);
            background: var(--accent-purple-light);
        }
        
        .img-upload-box input[type="file"] {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer;
        }
        
        .upload-icon { font-size: 32px; color: var(--accent-purple); margin-bottom: 15px; }

        .btn-submit {
            background: var(--accent-purple);
            color: #FFF;
            border: none;
            width: 100%;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 4px 15px rgba(98, 60, 234, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(98, 60, 234, 0.4);
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
                    <a href="products.php?id=<?= htmlspecialchars($id) ?>">Products</a>
                    <span>/</span>
                    <span style="color: var(--accent-purple);">Add Product</span>
                </div>
                <div style="font-size:13px; font-weight:700; color:var(--text-gray); background:var(--bg-app); padding:8px 16px; border-radius:10px;">
                    Shop ID: <?= htmlspecialchars($id) ?>
                </div>
            </header>

            <form method="POST" action="AddProductAPI.php" enctype="multipart/form-data" class="flex-grid">
                
                <!-- Left Configuration Panel -->
                <div class="glass-panel">
                    <div class="panel-title"><i class="fas fa-plus-circle" style="color: var(--accent-purple);"></i> Product Details</div>

                    <div class="form-group">
                        <label class="form-label">Product Photo</label>
                        <div class="img-upload-box">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <div style="font-weight:700; color:var(--text-dark); margin-bottom:5px;">Upload High-Res Photo</div>
                            <div style="font-size:13px; color:var(--text-gray);">JPG, PNG or GIF (Max 5MB)</div>
                            <input type="file" name="Photo" accept=".png, .jpg, .jpeg" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="ProdName" placeholder="e.g. Double Cheeseburger" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" style="height: 120px; resize:none;" name="Description" placeholder="Provide a tantalizing description of the product..."></textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label">Regular Price (MAD)</label>
                            <input type="text" class="form-control" name="Price" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Offer Price (MAD)</label>
                            <input type="text" class="form-control" name="OfferPrice" placeholder="0.00 (Optional)">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 35px;">
                        <label class="form-label">Category</label>
                        <select class="form-control" name="CategoryID" required>
                            <option value="" disabled selected>-- Select an active category --</option>
                            <?php
                            if ($stmt = $con->prepare("SELECT * FROM ShopsCategory WHERE ShopID = ?")) {
                                $stmt->bind_param("s", $id);
                                $stmt->execute();
                                $resCat = $stmt->get_result();
                                while ($rowCat = $resCat->fetch_assoc()) {
                                    echo '<option value="' . $rowCat["CategoryShopID"] . '">' . htmlspecialchars($rowCat["CategoryName"]) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <input type="hidden" name="ShopID" value="<?= htmlspecialchars($id) ?>">
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-check-circle"></i> Create Product
                    </button>
                </div>

                <!-- Right Visual Context Panel -->
                <div class="glass-panel" style="display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, var(--accent-purple), #4A2BBF); color: #FFF; text-align: center;">
                    <i class="fas fa-box-open fa-5x" style="opacity: 0.2; margin-bottom: 30px;"></i>
                    <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 15px;">Build Your Catalog</h2>
                    <p style="font-size: 14px; font-weight: 500; opacity: 0.8; max-width: 80%; line-height: 1.6;">High-quality imagery and detailed descriptions significantly increase conversation rates for end-users ordering via the app.</p>
                </div>

            </form>
        </main>
    </div>
</body>
</html>