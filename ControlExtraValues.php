<?php 
require "conn.php";
$id = $_GET["id"] ?? ''; // This is ExtraCategoryID
$prodid = $_GET["prodid"] ?? ''; // This is ProductID passed through

// Fetch Category Details for Header Context
$catName = "Extra Values";
if ($id) {
    if ($stmt = $con->prepare("SELECT ExtraCategoryName FROM ExtraCategory WHERE ExtraCategoryID = ?")) {
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $resSn = $stmt->get_result();
        if ($rowSn = $resSn->fetch_assoc()) {
            $catName = htmlspecialchars($rowSn['ExtraCategoryName']) . " - Values";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Extra Configs | QOON</title>
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

        .flex-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; }
        .glass-panel { background: var(--bg-white); border-radius: var(--radius); padding: 35px; box-shadow: var(--shadow-card); }
        .panel-title { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display: flex; align-items: center; gap: 12px; }

        .input-group { display: flex; gap: 15px; margin-bottom: 30px; }
        .input-group input[type="text"], .input-group input[type="number"] { padding: 16px 20px; border-radius: 12px; border: 1px solid var(--border-color); background: var(--bg-app); font-size: 15px; font-weight: 500; outline: none; transition: all 0.2s; width: 100%; }
        .input-group input:focus { border-color: var(--accent-blue); background: #FFF; box-shadow: 0 0 0 4px rgba(0, 122, 255, 0.1); }
        
        .name-box { flex: 2; }
        .price-box { flex: 1; position: relative; }
        .price-box::after { content: "MAD"; font-size: 12px; font-weight: 700; color: var(--text-gray); position: absolute; right: 15px; top: 18px; pointer-events: none; }
        .price-box input { padding-right: 50px; }

        .submit-btn { background: var(--accent-blue); color: #FFF; border: none; padding: 0 25px; border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 15px rgba(0, 122, 255, 0.3); }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0, 122, 255, 0.4); }

        .cat-list { display: flex; flex-direction: column; gap: 12px; }
        .cat-item { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: var(--bg-app); border-radius: 12px; border: 1px solid var(--border-color); transition: 0.2s; }
        .cat-item:hover { background: #FFF; border-color: var(--accent-blue); box-shadow: var(--shadow-float); }
        
        .item-info { display: flex; align-items: center; gap: 15px; }
        .item-icon { width: 36px; height: 36px; background: rgba(0, 122, 255, 0.1); color: var(--accent-blue); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 14px; }
        
        .item-text { display: flex; flex-direction: column; }
        .cat-name { font-size: 15px; font-weight: 700; color: var(--text-dark); }
        .cat-price { font-size: 13px; font-weight: 800; color: var(--accent-green); }

        .btn-delete { background: rgba(239, 68, 68, 0.1); color: var(--accent-red); border: none; padding: 8px 16px; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; text-decoration: none; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-delete:hover { background: var(--accent-red); color: #FFF; }

        .empty-state { text-align: center; padding: 40px 20px; background: var(--bg-app); border-radius: 12px; color: var(--text-gray); font-weight: 600; font-size: 14px; border: 2px dashed var(--border-color); }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <div class="breadcrumb">
                    <a href="controlExtra.php?prodid=<?= htmlspecialchars($prodid) ?>"><i class="fas fa-arrow-left"></i> Back to Group</a>
                    <span>/</span>
                    <span style="color: var(--accent-blue);"><?= $catName ?></span>
                </div>
                <div style="font-size:13px; font-weight:700; color:var(--text-gray); background:var(--bg-app); padding:8px 16px; border-radius:10px;">
                    Category ID: <?= htmlspecialchars($id) ?>
                </div>
            </header>

            <div class="flex-grid">
                
                <div class="glass-panel">
                    <div class="panel-title">
                        <i class="fas fa-plus-square" style="color: var(--accent-blue);"></i> Add Configurable Value
                    </div>

                    <form action="AddExtraInSideCategotyApi.php" method="POST">
                        <div class="input-group">
                            <div class="name-box">
                                <input type="text" placeholder="Value Name (e.g., Large, Ketchup)" name="Name" required>
                            </div>
                            <div class="price-box">
                                <input type="number" step="0.01" placeholder="Price" name="Price" required>
                            </div>
                            <input type="hidden" name="ExtraCategoryID" value="<?= htmlspecialchars($id) ?>">
                            <button type="submit" class="submit-btn"><i class="fas fa-plus"></i> ADD</button>
                        </div>
                    </form>

                    <div class="cat-list">
                        <?php 
                        $resCount = 0;
                        if ($stmt = $con->prepare("SELECT * FROM ExtraInSideCategoty WHERE ExtraCategoryID=?")) {
                            $stmt->bind_param("s", $id);
                            $stmt->execute();
                            $res = $stmt->get_result();
                            while ($row = $res->fetch_assoc()) {
                                $resCount++;
                        ?>
                            <div class="cat-item">
                                <div class="item-info">
                                    <div class="item-icon"><i class="fas fa-tag"></i></div>
                                    <div class="item-text">
                                        <div class="cat-name"><?= htmlspecialchars($row["Name"]) ?></div>
                                        <div class="cat-price">+<?= number_format($row["Price"], 2) ?> MAD</div>
                                    </div>
                                </div>
                                
                                <a href="DeleteExtraValueApi.php?id=<?= $row["ExtraInSideCategotyID"] ?>&prodid=<?= urlencode($id) ?>" class="btn-delete">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </a>
                            </div>
                        <?php 
                            }
                        }
                        if ($resCount === 0) {
                            echo '<div class="empty-state"><i class="fas fa-tags fa-3x" style="opacity:0.3; margin-bottom:15px; display:block;"></i>No values added yet. Add a modifier like "Large" with its price above.</div>';
                        }
                        ?>          
                    </div>
                </div>

                <div class="glass-panel" style="display: flex; flex-direction: column; justify-content: center; align-items: center; background: linear-gradient(135deg, var(--accent-blue), #0056D2); color: #FFF; text-align: center;">
                    <i class="fas fa-sliders-h fa-5x" style="opacity: 0.2; margin-bottom: 30px;"></i>
                    <h2 style="font-size: 24px; font-weight: 800; margin-bottom: 15px;">Value Modifiers</h2>
                    <p style="font-size: 14px; font-weight: 500; opacity: 0.8; max-width: 80%; line-height: 1.6;">These are the actual selectable items within the group. Enter the exact name of the addon and the exact margin price to add to the base product cost if selected by the user.</p>
                </div>

            </div>
        </main>
    </div>
</body>
</html>