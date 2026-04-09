<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$action = $_GET['action'] ?? '';

if ($action == 'sliders') {
    sleep(1); // Artificial delay to ensure shimmer is visible
    echo '<a class="thumb-box add-new" href="add-slider.php"> <i class="fas fa-plus"></i> Add Slide </a>';
    $resSl = mysqli_query($con, "SELECT * FROM Sliders");
    while ($row = mysqli_fetch_assoc($resSl)) {
        echo '
        <div class="thumb-box shimmer">
            <a class="trash-btn" href="DeleteSlider.php?HomeSlidesID=' . $row['SliderID'] . '" onclick="return confirm(\'Delete slide?\');"><i class="fas fa-trash"></i></a>
            <img src="' . $row['SliderPhoto'] . '" onerror="this.onerror=null; this.src=\'images/logo.png\'; this.parentElement.classList.remove(\'shimmer\'); this.classList.add(\'img-loaded\');" onload="this.parentElement.classList.remove(\'shimmer\'); this.classList.add(\'img-loaded\');">
        </div>';
    }
}

if ($action == 'categories') {
    sleep(1);
    echo '<a class="thumb-box add-new" href="add-category.php"> <i class="fas fa-plus"></i> Add Category </a>';
    $resCat = mysqli_query($con, "SELECT * FROM Categories ORDER BY priority DESC");
    while ($row = mysqli_fetch_assoc($resCat)) {
        echo '
        <div class="thumb-box shimmer" style="padding:15px; position:relative;">
            <a class="trash-btn" href="DeleteCategory.php?CategoryId=' . $row['CategoryId'] . '" onclick="return confirm(\'Delete category completely?\');" style="z-index:5;"><i class="fas fa-trash"></i></a>
            <a href="updateCategory.php?CategoryId=' . $row['CategoryId'] . '" style="display:block; width:100%;">
                <img src="' . htmlspecialchars($row['Photo']) . '" onerror="this.onerror=null; this.src=\'images/logo.png\'; this.parentElement.parentElement.classList.remove(\'shimmer\'); this.classList.add(\'img-loaded\');" onload="this.parentElement.parentElement.classList.remove(\'shimmer\'); this.classList.add(\'img-loaded\');">
            </a>
        </div>';
    }
}

if ($action == 'partners') {
    sleep(1);
    echo '<a class="thumb-box add-new" href="add-slider-Partner.php"> <i class="fas fa-plus"></i> Add Partner Slide </a>';
    $resSlP = mysqli_query($con, "SELECT * FROM SliderPartner");
    while ($row = mysqli_fetch_assoc($resSlP)) {
        echo '
        <div class="thumb-box shimmer">
            <a class="trash-btn" href="DeleteSliderPartner.php?SliderPartnerID=' . $row['SliderPartnerID'] . '" onclick="return confirm(\'Delete slide?\');"><i class="fas fa-trash"></i></a>
            <img src="' . $row['SliderPhoto'] . '" onerror="this.onerror=null; this.src=\'images/logo.png\'; this.parentElement.classList.remove(\'shimmer\'); this.classList.add(\'img-loaded\');" onload="this.parentElement.classList.remove(\'shimmer\'); this.classList.add(\'img-loaded\');">
        </div>';
    }
}

if ($action == 'posts') {
    sleep(1);
    $resPsts = mysqli_query($con, "SELECT * FROM Posts JOIN Shops ON Posts.ShopID = Shops.ShopID ORDER BY PostId DESC LIMIT 30");
    while ($row = mysqli_fetch_assoc($resPsts)) {
        $statusColor = $row['PostStatus'] == 'ACTIVE' ? 'var(--accent-green)' : 'var(--accent-red)';
        echo '
        <div class="social-card">
            <div class="social-header">
                <img src="' . $row['ShopLogo'] . '" onerror="this.src=\'images/placeholder.png\'">
                <div class="social-meta">
                    <h5><a href="shop-profile.php?id=' . $row['ShopID'] . '" style="color:inherit; text-decoration:none;">' . $row['ShopName'] . '</a></h5>
                    <p>' . $row['CreatedAtPosts'] . ' &nbsp;•&nbsp; <span id="stat_' . $row['PostId'] . '" style="color:' . $statusColor . '; font-weight:800;">' . $row['PostStatus'] . '</span></p>
                </div>
                <div style="background:var(--bg-app); padding:8px 12px; border-radius:8px; font-weight:700; font-size:13px;"><i class="fas fa-heart text-danger"></i> ' . $row['PostLikes'] . ' &nbsp;&nbsp; <i class="fas fa-comment text-primary"></i> ' . $row['Postcomments'] . '</div>
            </div>
            <div class="social-body">' . nl2br(htmlspecialchars($row['PostText'])) . '</div>
            <div class="social-media">';
                if ($row['PostPhoto'] && $row['PostPhoto'] != 'NONE' && $row['PostPhoto'] != '0') {
                    echo "<div class='shimmer' style='border-radius:16px; overflow:hidden; min-height:300px;'><img src='{$row['PostPhoto']}' style='opacity:0; transition:opacity 0.4s; border-radius:16px; width:100%;' onload=\"this.parentElement.classList.remove('shimmer'); this.parentElement.style.minHeight='auto'; this.classList.add('img-loaded');\" onerror=\"this.onerror=null; this.src='images/placeholder.png'; this.parentElement.classList.remove('shimmer'); this.classList.add('img-loaded');\"></div>";
                }
                if ($row['PostPhoto2'] && $row['PostPhoto2'] != 'NONE' && $row['PostPhoto2'] != '0') {
                    echo "<div class='shimmer' style='border-radius:16px; overflow:hidden; min-height:300px;'><img src='{$row['PostPhoto2']}' style='opacity:0; transition:opacity 0.4s; border-radius:16px; width:100%;' onload=\"this.parentElement.classList.remove('shimmer'); this.parentElement.style.minHeight='auto'; this.classList.add('img-loaded');\" onerror=\"this.onerror=null; this.src='images/placeholder.png'; this.parentElement.classList.remove('shimmer'); this.classList.add('img-loaded');\"></div>";
                }
                if ($row['PostPhoto3'] && $row['PostPhoto3'] != 'NONE' && $row['PostPhoto3'] != '0') {
                    echo "<div class='shimmer' style='border-radius:16px; overflow:hidden; min-height:300px;'><img src='{$row['PostPhoto3']}' style='opacity:0; transition:opacity 0.4s; border-radius:16px; width:100%;' onload=\"this.parentElement.classList.remove('shimmer'); this.parentElement.style.minHeight='auto'; this.classList.add('img-loaded');\" onerror=\"this.onerror=null; this.src='images/placeholder.png'; this.parentElement.classList.remove('shimmer'); this.classList.add('img-loaded');\"></div>";
                }
                if ($row['Video'] && $row['Video'] != 'NONE' && $row['Video'] != '0') {
                    echo "<div style='border-radius:16px; overflow:hidden;'><video src='{$row['Video']}' controls style='width:100%; border-radius:16px; min-height:300px; background:#000;'></video></div>";
                }
        echo '
            </div>
            <div class="social-actions">
                <button class="pill-btn pill-green" onclick="modAction(\'ChangePostStatus.php?PostStatus=ACTIVE&PostId=' . $row['PostId'] . '\', \'btn_stat_' . $row['PostId'] . '\', \'ACTIVE\', \'stat_' . $row['PostId'] . '\')"><i class="fas fa-check"></i> Approve</button>
                <button class="pill-btn pill-red" onclick="modAction(\'ChangePostStatus.php?PostStatus=NOTACTIVE&PostId=' . $row['PostId'] . '\', \'btn_stat_' . $row['PostId'] . '\', \'NOTACTIVE\', \'stat_' . $row['PostId'] . '\')"><i class="fas fa-ban"></i> Suspend</button>
                <button class="pill-btn pill-red" onclick="modAction(\'ChangePostStatus.php?PostStatus=DELETED&PostId=' . $row['PostId'] . '\', \'btn_stat_' . $row['PostId'] . '\', \'DELETED\', \'stat_' . $row['PostId'] . '\')"><i class="fas fa-trash"></i> Delete</button>
            </div>
        </div>';
    }
}

if ($action == 'stories') {
    sleep(1);
    
    echo '<style>
    .tinder-stack { position:relative; width: 100%; max-width:400px; margin: 0 auto; height: 600px; perspective: 1000px; }
    .tinder-card { position:absolute; top:0; left:0; width:100%; height: 550px; background: #fff; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow:hidden; display:flex; flex-direction:column; transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275), opacity 0.5s; z-index: 1; border: 1px solid var(--border-color); }
    .tinder-img { flex:1; width:100%; object-fit:contain; background:#000; height: 100%; }
    .tinder-badge { position:absolute; top:20px; left:20px; background: rgba(245, 158, 11, 0.9); color: #FFF; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; z-index:10; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); text-transform:uppercase; letter-spacing:1px; }
    .tinder-meta { position:absolute; bottom:80px; left:0; width:100%; padding:30px 20px; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); color:#fff; pointer-events:none; }
    .tinder-meta h3 { font-size: 22px; font-weight:800; margin-bottom:5px; text-shadow: 0 2px 5px rgba(0,0,0,0.5); display:flex; align-items:center; gap:10px; }
    .tinder-meta h3 img { width: 30px; height: 30px; border-radius: 50%; border: 2px solid #FFF; }
    .tinder-meta p { font-size:13px; font-weight:600; opacity:0.9; }
    .tinder-actions { position:absolute; bottom:0; left:0; width:100%; height:80px; background:#fff; display:flex; justify-content:center; align-items:center; gap:40px; border-top: 1px solid var(--border-color); }
    .btn-tinder { width:55px; height:55px; border-radius:50%; border:none; display:flex; justify-content:center; align-items:center; font-size:24px; cursor:pointer; box-shadow: 0 5px 25px rgba(0,0,0,0.08); transition:0.2s; background:#fff; outline:none; }
    .btn-reject { color: #FF4B4B; } .btn-reject:hover { transform:scale(1.15); box-shadow: 0 8px 25px rgba(255, 75, 75, 0.3); background:#FFF0F0; }
    .btn-approve { color: #10B981; } .btn-approve:hover { transform:scale(1.15); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3); background:#E6FFF4; }
    
    .swipe-left { transform: translateX(-150%) rotate(-30deg) translateY(50px); opacity:0 !important; pointer-events:none; }
    .swipe-right { transform: translateX(150%) rotate(30deg) translateY(50px); opacity:0 !important; pointer-events:none; }
    </style>';

    echo '<div class="tinder-stack" id="stories-stack">';

    // Only select stories NOTACTIVE (Under Review)
    $resStry = mysqli_query($con, "SELECT * FROM ShopStory JOIN Shops ON ShopStory.ShopID = Shops.ShopID WHERE StoryStatus != 'ACTIVE' ORDER BY StotyID ASC LIMIT 20");
    $count = 0;
    
    $cardsData = [];
    while ($row = mysqli_fetch_assoc($resStry)) { $cardsData[] = $row; }
    $cardsData = array_reverse($cardsData); // Reverse so first in queue is top of z-index

    $z = count($cardsData);
    
    if($z == 0) {
        echo '<div style="text-align:center; padding:150px 20px; color:var(--text-gray);">
                <i class="fas fa-check-circle" style="font-size:70px; color:var(--accent-green); margin-bottom:20px; opacity:0.5;"></i>
                <h3 style="font-weight:800; color:var(--text-dark);">Inbox Zero!</h3>
                <p style="font-weight:600; font-size:14px;">All Fleet Stories have been reviewed and processed.</p>
              </div>';
    }

    foreach($cardsData as $row) {
        $stryId = $row['StotyID'];
        echo '
        <div class="tinder-card" id="card_' . $stryId . '" style="z-index: ' . $z-- . ';">
            <div class="tinder-badge"><i class="fas fa-eye"></i> Under Review</div>';
            
            if ($row['StotyType'] == 'Photos') {
                echo '<img src="' . $row['StoryPhoto'] . '" class="tinder-img" onerror="this.src=\'images/placeholder.png\'">';
            } else {
                echo '<video src="' . $row['StoryPhoto'] . '" class="tinder-img" autoplay loop muted></video>';
            }
            
        echo '
            <div class="tinder-meta">
                <h3><img src="' . $row['ShopLogo'] . '" onerror="this.src=\'images/placeholder.png\'"> ' . $row['ShopName'] . '</h3>
                <p>Story Tag ID: #' . $stryId . ' &nbsp;|&nbsp; Waiting for Approval</p>
            </div>
            <div class="tinder-actions">
                <button class="btn-tinder btn-reject" onclick="handleSwipe(\'card_' . $stryId . '\', \'DELETED\', ' . $stryId . ', ' . $row['ShopID'] . ')" title="Reject & Delete"><i class="fas fa-times"></i></button>
                <button class="btn-tinder btn-approve" onclick="handleSwipe(\'card_' . $stryId . '\', \'ACTIVE\', ' . $stryId . ', ' . $row['ShopID'] . ')" title="Approve Story"><i class="fas fa-heart"></i></button>
            </div>
        </div>';
    }

    echo '</div>'; // End stack

    echo "
    <script>
    function handleSwipe(cardId, actionStatus, postId, shopId) {
        let card = document.getElementById(cardId);
        if(!card) return;
        
        // CSS Animation Trigger
        if(actionStatus === 'ACTIVE') {
            card.classList.add('swipe-right');
        } else {
            card.classList.add('swipe-left');
        }
        
        // Cleanup DOM after animation
        setTimeout(() => { card.remove(); }, 600);
        
        // Fire Background Override AJAX
        $.ajax({
            url: 'ChangeStoryStatus.php?StoryStatus=' + actionStatus + '&PostId=' + postId + '&ShopID=' + shopId,
            type: 'POST',
            data: { SellerEmail: 'SYS_OVERRIDE' }
        });
    }
    </script>";
}

if ($action == 'boosts') {
    sleep(1);
    $resBsts = mysqli_query($con, "SELECT * FROM BoostsByShop JOIN Shops ON BoostsByShop.ShopID = Shops.ShopID ORDER BY BoostsByShop.BoostsByShopID DESC LIMIT 30");
    while ($row = mysqli_fetch_assoc($resBsts)) {
        $statusBg = $row['BoostStatus'] == 'Active' ? 'rgba(16,185,129,0.1)' : 'rgba(239,68,68,0.1)';
        $statusCol = $row['BoostStatus'] == 'Active' ? 'var(--accent-green)' : 'var(--accent-red)';
        echo '
        <tr>
            <td>
                <div style="display:flex; align-items:center; gap:10px;">
                    <img src="' . $row['ShopLogo'] . '" style="width:30px; height:30px; border-radius:8px;">
                    <span style="font-weight:800; font-size:13px;">' . $row['ShopName'] . '</span>
                </div>
            </td>
            <td><span style="font-size:12px; font-weight:800; color:var(--text-gray);">' . $row['BoostName'] . '</span></td>
            <td>';
            
            if ($row['BoostPhoto'] && $row['BoostPhoto'] != 'NONE') {
                $bfile = strtolower($row['BoostPhoto']);
                if (strpos($bfile, '.mp4') !== false || strpos($bfile, '.mov') !== false) {
                    echo '<video src="'.$row['BoostPhoto'].'" style="width:70px; height:70px; border-radius:10px; object-fit:cover; border:1px solid var(--border-color); cursor:pointer;" controls muted></video>';
                } else {
                    echo '<a href="'.$row['BoostPhoto'].'" target="_blank">
                              <img src="'.$row['BoostPhoto'].'" style="width:70px; height:70px; border-radius:10px; object-fit:cover; border:1px solid var(--border-color); transition:0.3s;" onerror="this.src=\'images/placeholder.png\'">
                          </a>';
                }
            } else {
                echo '<span style="font-size:11px; font-weight:700; color:var(--text-gray); background:#f1f1f1; padding:5px 8px; border-radius:6px;">No Media</span>';
            }
            
            echo '</td>
            <td>' . $row['BoostCity'] . '</td>
            <td>' . $row['BoostTimeDuration'] . ' Days</td>
            <td style="font-weight:800; color:var(--accent-green)">' . $row['BoostPrice'] . ' MAD</td>
            <td><span style="background:' . $statusBg . '; color:' . $statusCol . '; padding:5px 10px; border-radius:6px; font-size:11px; font-weight:800; text-transform:uppercase;">' . $row['BoostStatus'] . '</span></td>
            <td>
                <div style="display:flex; gap:8px;">
                    <a href="ChangeBoostStatues.php?BoostStatus=Active&BoostsByShopID=' . $row['BoostsByShopID'] . '" style="width:30px; height:30px; border-radius:6px; display:flex; justify-content:center; align-items:center; background:var(--accent-green); color:#FFF; text-decoration:none;"><i class="fas fa-check"></i></a>
                    <a href="ChangeBoostStatues.php?BoostStatus=NotActive&BoostsByShopID=' . $row['BoostsByShopID'] . '" style="width:30px; height:30px; border-radius:6px; display:flex; justify-content:center; align-items:center; background:var(--accent-red); color:#FFF; text-decoration:none;"><i class="fas fa-times"></i></a>
                </div>
            </td>
        </tr>';
    }
}
?>
