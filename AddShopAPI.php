<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

// 1. Capture Coordinates
$ShopName = $_POST["ShopName"];
$ShopPhone = $_POST["ShopPhone"];
$ShopLoginName = $_POST["ShopLoginName"];
$ShopLoginPassword = $_POST["ShopLoginPassword"];
$ShopLatPosition = $_POST["ShopLatPosition"] ?? '';
$CategoryID = $_POST["CategoryID"];
$Type = $_POST["Type"];
$CityID = $_POST["CityID"];

// 2. Image Processing (Logic from original file)
$photo1name = "w-" . rand();
$path_root = "db/db/photo/$photo1name.png";
$actualpath = "https://jibler.ma/$path_root";
$path_dest = "photo/$photo1name.png";

$photo2name = "w-" . rand();
$path_root2 = "db/db/photo/$photo2name.png";
$actualpath2 = "https://jibler.ma/$path_root2";
$path_dest2 = "photo/$photo2name.png";

$Lat = "34.0209"; $Longt = "-6.8416";
if($ShopLatPosition != ""){ 
    $t = explode(",",$ShopLatPosition);
    $Lat = $t[0]; $Longt = $t[1] ?? "-6.8416";
}

session_start();
$AdminID = $_SESSION["AdminID"] ?? "1";

if($Type == "Ourplus") { $Type = "Our"; $BakatID = "3"; }
else if($Type == "Our") { $Type = "Our"; $BakatID = "2"; }
else { $BakatID = "1"; }

// 3. Database Injection
$sql = "INSERT INTO Shops (ShopName,ShopPhone,ShopLogName,ShopPassword,ShopLat,ShopLongt,ShopLogo,ShopCover,CategoryID,Type,AdminID,OwnerPhone,BakatID,CityID) VALUES 
        ('$ShopName','$ShopPhone','$ShopLoginName','$ShopLoginPassword','$Lat','$Longt','$actualpath','$actualpath2','$CategoryID','$Type','$AdminID','$ShopPhone','$BakatID','$CityID')";

if(mysqli_query($con, $sql)) {
    $last_id = $con->insert_id;
    
    // Provision Schedule (Original business logic)
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    foreach($days as $day) {
        mysqli_query($con, "INSERT INTO ShopTimes (ShopID, Day, Times) VALUES ('$last_id', '$day', '00:00-23:59')");
    }

    // Move Uploads
    move_uploaded_file($_FILES["Photo"]["tmp_name"], $path_dest);
    move_uploaded_file($_FILES["Photo2"]["tmp_name"], $path_dest2);

    // 4. ELITE SUCCESS UI RENDER
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Success | Node Provisioned</title>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;400;700;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root { --canvas: #FFFFFF; --brand: #623CEA; --ink: #020617; }
            * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
            body { background: var(--canvas); height: 100vh; display: flex; align-items: center; justify-content: center; overflow: hidden; }
            
            .success-envelope { display: flex; flex-direction: column; align-items: center; text-align: center; gap: 40px; animation: zoomIn 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
            @keyframes zoomIn { from { opacity: 0; transform: scale(0.9) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }

            .check-orb { 
                width: 140px; height: 140px; background: var(--brand); border-radius: 50%; 
                display: flex; align-items: center; justify-content: center; 
                font-size: 60px; color: #FFF; box-shadow: 0 30px 60px rgba(98, 60, 234, 0.3);
                position: relative;
            }
            .check-orb::after { content: ''; position: absolute; inset: -15px; border-radius: 50%; border: 2px solid var(--brand); opacity: 0.2; animation: pulse 2s infinite; }
            @keyframes pulse { 0% { transform: scale(1); opacity: 0.3; } 100% { transform: scale(1.5); opacity: 0; } }

            .msg h1 { font-size: 52px; font-weight: 950; letter-spacing: -3px; line-height: 0.8; margin-bottom: 20px; }
            .msg p { font-size: 18px; font-weight: 800; color: #94A3B8; }

            .card-stub {
                background: #FFF; border-radius: 40px; padding: 30px; 
                box-shadow: 0 40px 80px rgba(0,0,0,0.05); border: 1px solid #F1F5F9;
                display: flex; align-items: center; gap: 20px; width: 400px;
            }
            .stub-img { width: 70px; height: 70px; border-radius: 20px; object-fit: cover; }
            .stub-info { text-align: left; }
            .stub-name { font-size: 20px; font-weight: 950; }
            .stub-meta { font-size: 10px; font-weight: 950; text-transform: uppercase; color: var(--brand); letter-spacing: 2px; }

            .btn-hub { 
                background: var(--ink); color: #FFF; padding: 22px 50px; border-radius: 30px; 
                font-size: 14px; font-weight: 950; text-transform: uppercase; letter-spacing: 2px; 
                text-decoration: none; box-shadow: 0 20px 40px rgba(0,0,0,0.1); transition: 0.4s;
            }
            .btn-hub:hover { transform: translateY(-5px); background: var(--brand); box-shadow: 0 20px 50px rgba(98,60,234,0.3); }
        </style>
    </head>
    <body>
        <div class="success-envelope">
            <div class="check-orb"><i class="fas fa-check"></i></div>
            <div class="msg">
                <h1>Deployment Complete</h1>
                <p>Entity Node initialized within the QOON Network.</p>
            </div>
            
            <div class="card-stub">
                <img src="<?= $path_dest ?>" class="stub-img">
                <div class="stub-info">
                    <div class="stub-meta">ACTIVE NODE #<?= $last_id ?></div>
                    <div class="stub-name"><?= htmlspecialchars($ShopName) ?></div>
                </div>
            </div>

            <a href="shop.php" class="btn-hub">Return to Matrix Hub</a>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "Creation Error: " . mysqli_error($con);
}
mysqli_close($con);
?>
