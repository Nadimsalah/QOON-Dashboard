<?php
/**
 * getFeedRecommendationAPI.php
 * Place in: https://qoon.app/userDriver/UserDriverApi/
 *
 * GET params:
 *   user_id  (int)  – requesting user's ID
 *   limit    (int)  – max items (default 10, max 50)
 *   offset   (int)  – pagination offset (default 0)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once 'conn.php';   // Uses the existing conn.php in the same folder

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
const LEGACY_DOMAINS_FEED = [
    'https://jibler.app/db/db/',
    'http://jibler.app/db/db/',
    'https://jibler.app/dash/',
    'https://jibler.app/',
    'http://jibler.app/',
    'https://jibler.ma/db/db/',
    'http://jibler.ma/db/db/',
    'https://jibler.ma/dash/',
    'https://jibler.ma/',
    'https://www.jibler.app/',
    'https://www.jibler.ma/',
    'https://dashboard.jibler.ma/dash/',
    'https://qoon.app/dash/',
    'https://qoon.app/db/db/',
    'http://qoon.app/',
];

function normalizeMediaUrl(?string $raw): ?string {
    if (!$raw) return null;
    $raw = trim($raw);
    if (in_array($raw, ['', 'NONE', '0', 'none'])) return null;

    // Already a clean qoon.app URL — keep it
    if (
        str_starts_with($raw, 'https://qoon.app/') &&
        !str_starts_with($raw, 'https://qoon.app/dash/') &&
        !str_starts_with($raw, 'https://qoon.app/db/')
    ) {
        return $raw;
    }

    // Strip known legacy domain prefix
    foreach (LEGACY_DOMAINS_FEED as $prefix) {
        if (str_starts_with($raw, $prefix)) {
            $raw = substr($raw, strlen($prefix));
            break;
        }
    }

    return 'https://qoon.app/' . ltrim($raw, '/');
}

// ── Query ─────────────────────────────────────────────────────────────────
$stmt = $con->prepare("
    SELECT
        P.PostId, P.ShopID, P.PostText,
        P.PostPhoto, P.PostPhoto2, P.PostPhoto3, P.Video,
        P.PostLikes, P.Postcomments, P.CreatedAtPosts,
        S.ShopName, S.ShopLogo, S.CategoryID, S.CityID, S.ShopLat, S.ShopLongt, S.Type, S.priority, S.InHome, S.HasStory, S.ShopOpen,
        CASE WHEN B.BoostsByShopID IS NOT NULL THEN 1 ELSE 0 END AS isBoosted
    FROM Posts P
    JOIN Shops S ON P.ShopID = S.ShopID
    LEFT JOIN BoostsByShop B ON B.ShopID = P.ShopID AND B.BoostStatus = 'Active'
    WHERE P.PostStatus = 'ACTIVE'
    GROUP BY P.PostId
    ORDER BY isBoosted DESC, P.PostId DESC
    LIMIT ? OFFSET ?
");

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $con->error]);
    exit;
}

$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$totalRow = $con->query("SELECT COUNT(*) as c FROM Posts WHERE PostStatus = 'ACTIVE'")->fetch_assoc();
$total    = (int)($totalRow['c'] ?? 0);

$itemsCount = 0;
$shops = [];
while ($row = $result->fetch_assoc()) {
    $media = [];
    foreach (['PostPhoto','PostPhoto2','PostPhoto3'] as $col) {
        $url = normalizeMediaUrl($row[$col]);
        if ($url) $media[] = ['type' => 'image', 'url' => $url];
    }
    $vUrl = normalizeMediaUrl($row['Video']);
    if ($vUrl) $media[] = ['type' => 'video', 'url' => $vUrl];

    $distance = calculateDistance($userLat, $userLong, $row['ShopLat'] ?? 0, $row['ShopLongt'] ?? 0);
    
    // Distance filtering: Skip if further than 50km
    if ($userLat != '0' && $userLong != '0' && $distance !== "Null" && (float)$distance > 50) {
        continue;
    }

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
            'CategoryStory' => [] // Renamed from "0"
        ];
    }
    
    // Nested Post (Feed)
    $shops[$shopId]['CategoryStory'][] = [
        'StoryID'    => (string)$row['PostId'], // Renamed from StotyID
        'StoryPhoto' => empty($media) ? null : $media[0]['url'],
        'ShopID'     => $shopId,
        'StoryType'  => 'post', // Renamed from StotyType
        'ProductId'  => (string)$row['PostId'],
        // Extra fields
        'PostText'   => $row['PostText'],
        'PostLikes'  => (int)($row['PostLikes'] ?? 0),
        'Postcomments'=> (int)($row['Postcomments'] ?? 0),
        'CreatedAt'   => date('Y-m-d\TH:i:s\Z', strtotime($row['CreatedAtPosts'])),
        'AllMedia'    => $media
    ];
    
    $itemsCount++;
}

// Convert associative array back to sequential array
$outputArray = array_values($shops);

echo json_encode([
    'status_code' => 200,
    'success'     => true,
    'message'     => 'Success',
    'PageObject'  => [
        'currentpage' => (int)($offset / $limit) + 1,
        'hasNextPage' => ($offset + $limit) < $total
    ],
    'data'        => $outputArray,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Non-blocking trigger to wake up the AI Auto-Moderator seamlessly on the server
$ch = curl_init("http://127.0.0.1/dashx/dash/tick_ai_worker.php");
curl_setopt($ch, CURLOPT_TIMEOUT_MS, 50);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
@curl_exec($ch);
curl_close($ch);
