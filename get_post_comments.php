<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$postID = $_GET['PostID'] ?? '';
if(!$postID) {
    echo "<div style='padding:20px; text-align:center;'>Invalid Post ID</div>";
    exit;
}

// Comments table may have UserID or ShopID
$sql = "SELECT C.*, 
               U.name as UserName, U.UserPhoto, 
               S.ShopName, S.ShopLogo 
        FROM Comments C 
        LEFT JOIN Users U ON C.UserID = U.UserID AND C.UserID != '0' AND C.UserID != ''
        LEFT JOIN Shops S ON C.ShopID = S.ShopID AND C.ShopID != '0' AND C.ShopID != ''
        WHERE C.PostID = '$postID' 
        ORDER BY C.CommentID ASC"; // oldest first

$res = mysqli_query($con, $sql);

if(mysqli_num_rows($res) == 0): ?>
    <div style="text-align: center; color: #6B7280; padding: 30px;">
        <i class="fas fa-comment-slash" style="font-size: 24px; margin-bottom: 8px; color: #D1D5DB;"></i>
        <p>No comments yet.</p>
    </div>
<?php else: ?>
    <div style="display:flex; flex-direction:column; gap:16px;">
    <?php while($row = mysqli_fetch_assoc($res)): 
        $isShop = empty($row['UserName']) && !empty($row['ShopID']) && $row['ShopID'] != '0';
        $authorName = !empty($row['UserName']) ? $row['UserName'] : (!empty($row['ShopName']) ? $row['ShopName'] : 'Unknown User');
        
        $authorPhoto = !empty($row['UserPhoto']) ? $row['UserPhoto'] : (!empty($row['ShopLogo']) ? $row['ShopLogo'] : null);
        // Provide fallback avatar
        if(!$authorPhoto || $authorPhoto == 'none' || $authorPhoto == 'NONE') {
            $authorPhoto = 'https://ui-avatars.com/api/?name='.urlencode($authorName?$authorName:'Unknown').'&background=E5E7EB&color=374151';
        } else if (!str_starts_with($authorPhoto, 'http')) {
            $authorPhoto = 'https://qoon.app/' . ltrim($authorPhoto, '/');
        }
        
        $timeStr = date('M j, g:i a', strtotime($row['CreatedAtComments']));
    ?>
        <div style="display: flex; gap: 12px;">
            <img src="<?= htmlspecialchars($authorPhoto) ?>" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; background: #F3F4F6;" onerror="this.src='https://ui-avatars.com/api/?name=User&background=E5E7EB'">
            <div style="flex: 1;">
                <div style="background: #F3F4F6; padding: 10px 14px; border-radius: 16px; border-top-left-radius: 4px; display: inline-block; max-width: 100%;">
                    <div style="font-size: 13px; font-weight: 700; color: #111827; margin-bottom: 2px;">
                        <?= htmlspecialchars($authorName ? $authorName : 'Unknown User') ?>
                        <?php if($isShop): ?>
                            <i class="fas fa-store" style="font-size: 9px; color: #3b82f6; margin-left: 4px;" title="Store"></i>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 14px; color: #374151; line-height: 1.4; word-break: break-word;">
                        <?= htmlspecialchars($row['CommentText']) ?>
                    </div>
                </div>
                <div style="font-size: 11px; color: #9CA3AF; margin-top: 4px; padding-left: 4px;">
                    <?= $timeStr ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
<?php endif; ?>
