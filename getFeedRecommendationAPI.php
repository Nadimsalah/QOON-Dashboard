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
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$limit   = min((int)($_GET['limit']  ?? 10), 50);
$offset  = max((int)($_GET['offset'] ?? 0),  0);
if ($limit < 1) $limit = 10;

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
        S.ShopName, S.ShopLogo, S.CategoryID, S.CityID,
        CASE WHEN B.BoostsByShopID IS NOT NULL THEN 1 ELSE 0 END AS isBoosted
    FROM Posts P
    JOIN Shops S ON P.ShopID = S.ShopID
    LEFT JOIN BoostsByShop B ON B.ShopID = P.ShopID AND B.BoostStatus = 'Active'
    WHERE P.PostStatus = 'ACTIVE'
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

$items = [];
while ($row = $result->fetch_assoc()) {
    $media = [];
    foreach (['PostPhoto','PostPhoto2','PostPhoto3'] as $col) {
        $url = normalizeMediaUrl($row[$col]);
        if ($url) $media[] = ['type' => 'image', 'url' => $url];
    }
    $vUrl = normalizeMediaUrl($row['Video']);
    if ($vUrl) $media[] = ['type' => 'video', 'url' => $vUrl];

    $items[] = [
        'id'        => (int)$row['PostId'],
        'type'      => 'post',
        'isBoosted' => (bool)$row['isBoosted'],
        'shop'      => [
            'id'        => (int)$row['ShopID'],
            'name'      => $row['ShopName'],
            'logo'      => normalizeMediaUrl($row['ShopLogo']),
            'categoryId'=> (int)$row['CategoryID'],
            'cityId'    => (int)$row['CityID'],
        ],
        'text'      => $row['PostText'],
        'media'     => $media,
        'likes'     => (int)$row['PostLikes'],
        'comments'  => (int)$row['Postcomments'],
        'createdAt' => $row['CreatedAtPosts'],
    ];
}

echo json_encode([
    'success' => true,
    'total'   => $total,
    'limit'   => $limit,
    'offset'  => $offset,
    'hasMore' => ($offset + $limit) < $total,
    'userId'  => $user_id,
    'items'   => $items,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
