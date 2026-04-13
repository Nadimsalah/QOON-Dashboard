<?php
/**
 * getReelsRecommendationAPI.php
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

error_reporting(E_ALL);
ini_set('display_errors', 0);   // keep errors out of JSON output
ini_set('log_errors', 1);       // log to server error log instead

require_once 'conn.php';   // Uses the existing conn.php in the same folder

// ── Inputs ────────────────────────────────────────────────────────────────
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$limit   = min((int)($_GET['limit']  ?? 10), 50);
$offset  = max((int)($_GET['offset'] ?? 0),  0);
if ($limit < 1) $limit = 10;

// ── URL normalizer ───────────────────────────────────────────────────────
// Strips any legacy domain and rebuilds with qoon.app.
// DB stores paths like:
//   https://jibler.app/db/db/photo/video.mp4
//   https://jibler.app/jibler/partener/Api/photo/video.mp4
//   jibler/partener/Api/photo/video.mp4  (relative)
// All are normalized to: https://qoon.app/{relative_path}
const LEGACY_DOMAINS = [
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

    // If already a correctly-formed qoon.app URL (not in /dash/) — keep it
    if (
        str_starts_with($raw, 'https://qoon.app/') &&
        !str_starts_with($raw, 'https://qoon.app/dash/') &&
        !str_starts_with($raw, 'https://qoon.app/db/')
    ) {
        return $raw;
    }

    // Strip any known legacy domain prefix → get relative path
    foreach (LEGACY_DOMAINS as $prefix) {
        if (str_starts_with($raw, $prefix)) {
            $raw = substr($raw, strlen($prefix));
            break;
        }
    }

    // Trim stray slashes and rebuild
    $relative = ltrim($raw, '/');
    return 'https://qoon.app/' . $relative;
}

// ── Video URL validator ──────────────────────────────────────────────────
// Accepts any URL whose path ends in .mp4 (case-insensitive).
// Query strings (?token=...) are stripped from the path before checking.
function isValidVideoUrl(?string $url): bool {
    if (!$url) return false;

    // Strip query string/fragment — validate path only
    $clean = strtok($url, '?#');
    if (!$clean) return false;

    // Get path portion only (avoids matching domain like https://)
    $path = parse_url($clean, PHP_URL_PATH);
    if (!$path) return false;

    // Must end in .mp4 (case-insensitive)
    if (!preg_match('/\.mp4$/i', $path)) return false;

    // Filename must not be empty, e.g. "/.mp4"
    $filename = pathinfo($path, PATHINFO_FILENAME);
    if (strlen(trim($filename)) < 1) return false;

    // Detect obviously duplicated path segments (e.g. /a/b/c/a/b/c/)
    if (preg_match('#(/[^/]+/[^/]+/).*\1#', $path)) return false;

    return true;
}

// ── Multi-batch retry loop ────────────────────────────────────────────────
// ROOT CAUSE FIX: Reusing a MySQLi prepared statement with get_result()
// across multiple execute() calls causes stale result sets to be returned.
// Solution: issue a fresh mysqli_query() per iteration — ints are safe to
// interpolate directly, eliminating the resource-reuse problem entirely.

$BATCH_SIZE = (int)$limit;  // rows to fetch per DB round-trip
$MAX_LOOPS  = 5;            // hard safety cap

$items         = [];
$currentOffset = (int)$offset;
$loops         = 0;
$debugLog      = [];

// ── STOP CONDITIONS (only 3 allowed) ─────────────────────────────────────
// 1. DB returns 0 rows
// 2. loops >= MAX_LOOPS
// 3. collected items >= limit
// DO NOT stop based on partial batches or invalid-item counts.

for ($loops = 0; $loops < $MAX_LOOPS; $loops++) {

    // Fresh query every iteration — guarantees correct OFFSET
    $sql = "
        SELECT
            P.PostId, P.ShopID, P.PostText,
            P.PostPhoto, P.Video,
            P.PostLikes, P.Postcomments, P.CreatedAtPosts,
            S.ShopName, S.ShopLogo, S.CategoryID, S.CityID,
            CASE WHEN B.BoostsByShopID IS NOT NULL THEN 1 ELSE 0 END AS isBoosted
        FROM Posts P
        LEFT JOIN Shops S ON P.ShopID = S.ShopID
        LEFT JOIN BoostsByShop B
            ON B.ShopID = P.ShopID AND B.BoostStatus = 'Active'
        WHERE P.PostStatus = 'ACTIVE'
          AND P.Video IS NOT NULL
          AND P.Video NOT IN ('', 'NONE', '0')
        ORDER BY isBoosted DESC, P.PostId DESC
        LIMIT {$BATCH_SIZE} OFFSET {$currentOffset}
    ";

    $result = mysqli_query($con, $sql);

    if (!$result) {
        error_log("ReelsAPI loop={$loops} SQL error: " . mysqli_error($con));
        break; // query failed — stop
    }

    $rowsFromDB = mysqli_num_rows($result);

    // Record debug info — including the actual SQL for inspection
    $debugLog[] = [
        'loop'       => $loops,
        'offset'     => $currentOffset,
        'rowsFromDB' => $rowsFromDB,
        'collected'  => count($items),
        'sql'        => $sql,
    ];


    // ── STOP CONDITION 1: DB has no more rows ────────────────────────────
    if ($rowsFromDB === 0) {
        mysqli_free_result($result);
        break;
    }

    // Process rows — validate and collect
    $rejectedLog = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $videoUrl  = normalizeMediaUrl($row['Video']);
        $thumbnail = normalizeMediaUrl($row['PostPhoto']);

        if (!isValidVideoUrl($videoUrl)) {
            $rejectedLog[] = ['raw' => $row['Video'], 'normalized' => $videoUrl];
            continue;
        }

        $items[] = [
            'id'        => (int)$row['PostId'],
            'type'      => 'reel',
            'isBoosted' => (bool)$row['isBoosted'],
            'shop'      => [
                'id'         => (int)($row['ShopID'] ?? 0),
                'name'       => $row['ShopName'] ?? '',
                'logo'       => normalizeMediaUrl($row['ShopLogo'] ?? null),
                'categoryId' => (int)($row['CategoryID'] ?? 0),
                'cityId'     => (int)($row['CityID'] ?? 0),
            ],
            'caption'   => $row['PostText'],
            'videoUrl'  => $videoUrl,
            'thumbnail' => $thumbnail,
            'likes'     => (int)$row['PostLikes'],
            'comments'  => (int)$row['Postcomments'],
            'createdAt' => $row['CreatedAtPosts'],
        ];
    }

    mysqli_free_result($result);

    // Append rejection details to this loop's debug entry
    $debugLog[count($debugLog) - 1]['rejected'] = $rejectedLog;


    // Offset ALWAYS advances by batch size (not by valid count)
    $currentOffset += $BATCH_SIZE;

    // ── STOP CONDITION 3: collected enough valid items ───────────────────
    if (count($items) >= $limit) break;

    // (STOP CONDITION 2 is handled by for-loop ceiling: $loops < $MAX_LOOPS)
}

// ── Total count (approximate — pre-validation filter) ────────────────────
$totalRow = mysqli_fetch_assoc(mysqli_query($con, "
    SELECT COUNT(*) AS c FROM Posts
    WHERE PostStatus = 'ACTIVE'
      AND Video IS NOT NULL
      AND Video NOT IN ('', 'NONE', '0')
"));
$dbTotal = (int)($totalRow['c'] ?? 0);

$hasMore = ($currentOffset < $dbTotal) && (count($items) >= $limit);

echo json_encode([
    'success'  => true,
    'total'    => $dbTotal,
    'returned' => count($items),
    'loops'    => $loops + 1,   // +1 because for-loop index is 0-based
    'limit'    => $limit,
    'offset'   => $offset,
    'hasMore'  => $hasMore,
    'userId'   => $user_id,
    'debug'    => $debugLog,    // remove once confirmed working
    'items'    => $items,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

