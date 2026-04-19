<?php
/**
 * getHybridContentAPI.php
 * Unified Feed + Reels API for Instagram-like experience
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once 'conn.php';

// ── Inputs ────────────────────────────────────────────────────────────────
$user_id  = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$userLat  = isset($_GET['userLocationLant']) ? $_GET['userLocationLant'] : (isset($_GET['userLat']) ? $_GET['userLat'] : '0');
$userLong = isset($_GET['userLocationLong']) ? $_GET['userLocationLong'] : (isset($_GET['userLong']) ? $_GET['userLong'] : '0');
$limit    = min((int)($_GET['limit']  ?? 10), 50);
$offset   = max((int)($_GET['offset'] ?? 0),  0);
if ($limit < 1) $limit = 10;

function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    if (!$lat1 || !$lon1 || !$lat2 || !$lon2) return "Null";
    $theta = (float)$lon1 - (float)$lon2;
    $dist = sin(deg2rad((float)$lat1)) * sin(deg2rad((float)$lat2)) +  cos(deg2rad((float)$lat1)) * cos(deg2rad((float)$lat2)) * cos(deg2rad($theta));
    if ($dist > 1) $dist = 1; else if ($dist < -1) $dist = -1;
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $km = $miles * 1.609344;
    return strval(round($km, 2));
}

// ── URL normalizer ───────────────────────────────────────────────────────
const LEGACY_DOMAINS = [
    'https://jibler.app/db/db/', 'http://jibler.app/db/db/', 'https://jibler.app/dash/', 'https://jibler.app/', 'http://jibler.app/',
    'https://jibler.ma/db/db/', 'http://jibler.ma/db/db/', 'https://jibler.ma/dash/', 'https://jibler.ma/', 'https://www.jibler.app/',
    'https://www.jibler.ma/', 'https://dashboard.jibler.ma/dash/', 'https://qoon.app/dash/', 'https://qoon.app/db/db/', 'http://qoon.app/'
];

function normalizeMediaUrl(?string $raw): ?string {
    if (!$raw) return null;
    $raw = trim($raw);
    if (in_array($raw, ['', 'NONE', '0', 'none'])) return null;
    if (str_starts_with($raw, 'https://qoon.app/') && !str_starts_with($raw, 'https://qoon.app/dash/') && !str_starts_with($raw, 'https://qoon.app/db/')) return $raw;
    foreach (LEGACY_DOMAINS as $prefix) {
        if (str_starts_with($raw, $prefix)) { $raw = substr($raw, strlen($prefix)); break; }
    }
    return 'https://qoon.app/' . ltrim($raw, '/');
}

// ── Overfetch Logic ──────────────────────────────────────────────────────
$BATCH_SIZE = (int)$limit;
$MAX_LOOPS  = 10;
$itemsCount = 0;
$shops      = [];
$currentOffset = (int)$offset;
$loops      = 0;

for ($loops = 0; $loops < $MAX_LOOPS; $loops++) {
    // 1. Fetch Hybrid batch
    $sql = "
        SELECT
            P.PostId, P.ShopID, P.PostText, P.CreatedAtPosts,
            P.PostPhoto, P.PostPhoto2, P.PostPhoto3, P.Video,
            CAST(P.PostLikes AS UNSIGNED) as PostLikesNumeric, 
            CAST(P.Postcomments AS UNSIGNED) as PostCommentsNumeric,
            S.ShopName, S.ShopLogo, S.CategoryID, S.CityID, S.ShopLat, S.ShopLongt, 
            S.Type, S.priority, S.InHome, S.HasStory, S.ShopOpen,
            CASE WHEN P.Video IS NOT NULL AND P.Video NOT IN ('', 'NONE', '0') THEN 'reel' ELSE 'post' END AS ComputedStoryType,
            CASE WHEN B.BoostsByShopID IS NOT NULL THEN 1 ELSE 0 END AS isBoosted
        FROM Posts P
        JOIN Shops S ON P.ShopID = S.ShopID
        LEFT JOIN BoostsByShop B ON B.ShopID = P.ShopID AND B.BoostStatus = 'Active'
        WHERE P.PostStatus = 'ACTIVE'
        GROUP BY P.PostId
        -- Ranking System Integration: Boosted First -> High Engagement -> Most Recent -> Fallback Index
        ORDER BY isBoosted DESC, PostLikesNumeric DESC, P.CreatedAtPosts DESC, P.PostId DESC
        LIMIT {$BATCH_SIZE} OFFSET {$currentOffset}
    ";

    $result = mysqli_query($con, $sql);
    if (!$result) {
        http_response_code(500); echo json_encode(['success' => false, 'error' => mysqli_error($con)]); exit;
    }

    $rowsFromDB = mysqli_num_rows($result);
    if ($rowsFromDB === 0) { mysqli_free_result($result); break; }

    while ($row = mysqli_fetch_assoc($result)) {
        // Evaluate Media Standardly
        $media = [];
        foreach (['PostPhoto','PostPhoto2','PostPhoto3'] as $col) {
            $url = normalizeMediaUrl($row[$col]);
            if ($url) $media[] = ['type' => 'image', 'url' => $url];
        }
        $vUrl = normalizeMediaUrl($row['Video']);
        if ($vUrl) $media[] = ['type' => 'video', 'url' => $vUrl];

        $distance = calculateDistance($userLat, $userLong, $row['ShopLat'] ?? 0, $row['ShopLongt'] ?? 0);
        
        // Exclude completely remote shops if valid bounds exist
        if ($userLat != '0' && $userLong != '0' && $distance !== "Null" && (float)$distance > 50) continue;

        $shopId = (string)($row['ShopID'] ?? '');
        if (!isset($shops[$shopId])) {
            $shops[$shopId] = [
                'ShopID'     => $shopId,
                'ShopName'   => (string)($row['ShopName'] ?? ''),
                'ShopLat'    => (string)($row['ShopLat'] ?? ''),
                'ShopLongt'  => (string)($row['ShopLongt'] ?? ''),
                'ShopOpen'   => (string)($row['ShopOpen'] ?? 'open'),
                'ShopLogo'   => normalizeMediaUrl($row['ShopLogo'] ?? null) ?? '',
                'CategoryID' => (string)($row['CategoryID'] ?? ''),
                'Type'       => (string)($row['Type'] ?? ''),
                'priority'   => (string)($row['priority'] ?? ''),
                'InHome'     => (string)($row['InHome'] ?? ''),
                'HasStory'   => (strtoupper((string)($row['HasStory'] ?? '')) === 'YES' || $row['HasStory'] == '1'),
                'HasFeed'    => true,
                'LastUpdated'=> gmdate('Y-m-d\TH:i:s\Z'),
                'distance'   => $distance === "Null" ? 0 : (float)$distance,
                'CategoryStory' => [] 
            ];
        }

        // Output properly depending on computed type
        $shops[$shopId]['CategoryStory'][] = [
            'StoryID'    => (string)$row['PostId'],
            'StoryPhoto' => empty($media) ? null : ($row['ComputedStoryType'] === 'reel' ? normalizeMediaUrl($row['PostPhoto']) : $media[0]['url']),
            'ShopID'     => $shopId,
            'StoryType'  => $row['ComputedStoryType'],
            'ProductId'  => (string)$row['PostId'],
            'PostText'   => $row['PostText'],
            'PostLikes'  => (int)($row['PostLikesNumeric'] ?? 0),
            'Postcomments'=> (int)($row['PostCommentsNumeric'] ?? 0),
            'CreatedAt'  => date('Y-m-d\TH:i:s\Z', strtotime($row['CreatedAtPosts'])),
            'AllMedia'   => $media
        ];
        
        $itemsCount++;
    }

    mysqli_free_result($result);
    $currentOffset += $BATCH_SIZE;
    if ($itemsCount >= $limit) break;
}

$outputArray = array_values($shops);

$totalRow = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(*) AS c FROM Posts WHERE PostStatus = 'ACTIVE'"));
$dbTotal = (int)($totalRow['c'] ?? 0);
$hasMore = ($currentOffset < $dbTotal) && ($itemsCount >= $limit);

echo json_encode([
    'status_code' => 200,
    'success'     => true,
    'message'     => 'Success',
    'PageObject'  => [
        'currentpage' => (int)($offset / $limit) + 1,
        'hasNextPage' => $hasMore
    ],
    'data'        => $outputArray,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
