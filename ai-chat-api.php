<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($con->connect_error) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit;
}
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');
if (empty($userMessage)) {
    echo json_encode(['error' => 'Empty message']);
    exit;
}

// ─── UTILS ───────────────────────────────────────────────────────────────────
function safeQuery($con, $sql)
{
    try {
        $r = mysqli_query($con, $sql);
        if (!$r)
            return null;
        $row = mysqli_fetch_row($r);
        return $row ? $row[0] : null;
    } catch (\Throwable $e) {
        return null;
    }
}

function utf8ize($d)
{
    if (is_array($d)) {
        foreach ($d as $k => $v)
            $d[$k] = utf8ize($v);
    } else if (is_string($d)) {
        return mb_convert_encoding($d, "UTF-8", "UTF-8");
    }
    return $d;
}

$ctx = [];

// 1. HIGH-LEVEL ANALYTICS
$ctx['total_revenue']     = (float) safeQuery($con, "SELECT IFNULL(SUM(OrderPrice),0) FROM Orders WHERE OrderState IN ('Done','Rated')");
$ctx['total_users']       = (int)   safeQuery($con, "SELECT COUNT(*) FROM Users");
$ctx['total_orders']      = (int)   safeQuery($con, "SELECT COUNT(*) FROM Orders");
$ctx['total_drivers']     = (int)   safeQuery($con, "SELECT COUNT(*) FROM Drivers");
$ctx['total_shops']       = (int)   safeQuery($con, "SELECT COUNT(*) FROM Shops");
$ctx['today_orders']      = (int)   safeQuery($con, "SELECT COUNT(*) FROM Orders WHERE DATE(CreatedAtOrders)=CURDATE()");
$ctx['new_users_week']    = (int)   safeQuery($con, "SELECT COUNT(*) FROM Users WHERE CreatedAt >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$ctx['pending_orders']    = (int)   safeQuery($con, "SELECT COUNT(*) FROM Orders WHERE OrderState='waiting'");
$ctx['active_orders']     = (int)   safeQuery($con, "SELECT COUNT(*) FROM Orders WHERE OrderState='Doing'");

// 2. FINANCIAL HEALTH INDEX
$ctx['driver_debt'] = (float) safeQuery($con, "SELECT IFNULL(SUM(OrderPriceFromShop),0) FROM Orders WHERE PaidForDriver='NotPaid' AND Method='Cash' AND (OrderState='Rated' OR OrderState='Done')");
$ctx['shop_owed']   = (float) safeQuery($con, "SELECT IFNULL(SUM(Balance),0) FROM Shops");

// 3. TRANSACTION STREAM (SAFE PAYLOAD)
$order_stream = [];
try {
    $res = mysqli_query($con, "SELECT OrderDetails FROM Orders WHERE OrderState IN ('Done', 'Rated') ORDER BY OrderID DESC LIMIT 30");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $order_stream[] = $row['OrderDetails'];
        }
    }
} catch (\Throwable $e) {
}
$ctx['order_stream'] = count($order_stream) > 0 ? implode(" | ", $order_stream) : "No transaction history";

// 4. INVENTORY DENSITY
$category_stats = [];
try {
    $res = mysqli_query($con, "SELECT EnglishCategory, (SELECT COUNT(*) FROM Foods WHERE FoodCatID = Categories.CategoriesID) as cnt FROM Categories ORDER BY cnt DESC LIMIT 10");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $category_stats[] = "- {$row['EnglishCategory']}: {$row['cnt']} items";
        }
    }
} catch (\Throwable $e) {
}
$ctx['category_stats'] = count($category_stats) > 0 ? implode("\n", $category_stats) : "None";

// 5. TOP PERFORMERS
$ctx['top_shop'] = "N/A";
try {
    $res = mysqli_query($con, "SELECT DestinationName, COUNT(*) as cnt FROM Orders GROUP BY ShopID ORDER BY cnt DESC LIMIT 1");
    if ($row = mysqli_fetch_assoc($res))
        $ctx['top_shop'] = "{$row['DestinationName']} ({$row['cnt']} orders)";
} catch (\Throwable $e) {
}

// 6. ASSET FEED
$recent_foods = [];
try {
    $res = mysqli_query($con, "SELECT FoodName, FoodPrice FROM Foods ORDER BY FoodID DESC LIMIT 15");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $name = $row['FoodName'] ?: "Unnamed Product";
            $recent_foods[] = "- $name | Price: {$row['FoodPrice']} MAD";
        }
    }
} catch (\Throwable $e) {
}
$ctx['recent_foods_list'] = count($recent_foods) > 0 ? implode("\n", $recent_foods) : "None";

$dbSummary = "
LIVE PLATFORM METRICS:

=== USERS & ECOSYSTEM ===
- Total Registered Users: {$ctx['total_users']}
- New Users (Last 7 Days): {$ctx['new_users_week']}
- Total Drivers: {$ctx['total_drivers']}
- Total Shops: {$ctx['total_shops']}

=== ORDER ACTIVITY ===
- Total Orders (All Time): {$ctx['total_orders']}
- Orders Today: {$ctx['today_orders']}
- Currently Pending: {$ctx['pending_orders']}
- Currently In Transit: {$ctx['active_orders']}
- Top Seller: {$ctx['top_shop']}

=== FINANCIAL ===
- Total Platform Revenue: {$ctx['total_revenue']} MAD
- Unreturned Driver Cash: {$ctx['driver_debt']} MAD
- Shop Balance Owed: {$ctx['shop_owed']} MAD

=== INVENTORY BY CATEGORY ===
{$ctx['category_stats']}

=== RECENT ORDER DETAILS (last 30) ===
{$ctx['order_stream']}

=== RECENT PRODUCTS (last 15) ===
{$ctx['recent_foods_list']}
";

$systemPrompt = "You are QOON Intelligence, the internal AI analyst for the QOON delivery platform. You have read-only access to live operational data.

BEHAVIOR RULES:
- Respond like a senior business analyst: concise, precise, professional.
- Use numbers, percentages, and direct comparisons — no filler words.
- Never use emojis.
- Never share, mention, or reference product images or URLs.
- Format with plain text only. Use bold (**text**) sparingly for key figures.
- If the question is outside your data scope, say so clearly in one sentence.
- Keep responses under 200 words unless explicitly asked for a full report.
- When listing items, use a plain numbered or dashed list — no headers per item.

LIVE DATA SNAPSHOT:
$dbSummary";

// ─── CALL DEEPSEEK API ────────────────────────────────────────────────────────
$history = $input['history'] ?? [];
$messages = [['role' => 'system', 'content' => $systemPrompt]];

$history = array_slice($history, -4);
foreach ($history as $msg) {
    if (isset($msg['role']) && isset($msg['content']) && !empty($msg['content'])) {
        $role = ($msg['role'] === 'ai' || $msg['role'] === 'assistant') ? 'assistant' : 'user';
        $messages[] = ['role' => $role, 'content' => $msg['content']];
    }
}
$messages[] = ['role' => 'user', 'content' => $userMessage];

// UTF8 ENCODE EVERYTHING TO PREVENT JSON_ENCODE FAILURE
$messages = utf8ize($messages);

$payload = [
    'model' => 'deepseek-chat',
    'messages' => $messages,
    'max_tokens' => 1200,
    'temperature' => 0.5,
    'stream' => false
];

$jsonPayload = json_encode($payload);
if (!$jsonPayload) {
    file_put_contents('ai_api_debug.log', "JSON Encode Error: " . json_last_error_msg() . "\nPayload Data: " . print_r($payload, true), FILE_APPEND);
    echo json_encode(['reply' => 'Internal encoding error. Please try a simpler message.']);
    exit;
}

$ch = curl_init('https://api.deepseek.com/chat/completions');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer sk-d25ba3eadc464644a051ea2fe7d83f7a'
    ],
    CURLOPT_POSTFIELDS => $jsonPayload,
    CURLOPT_TIMEOUT => 40,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    $errorLog = "--- ERROR " . date('Y-m-d H:i:s') . " ---\n";
    $errorLog .= "HTTP Status: $httpCode\n";
    $errorLog .= "Response: $response\n\n";
    file_put_contents('ai_api_debug.log', $errorLog, FILE_APPEND);
    echo json_encode(['reply' => "AI service unavailable ($httpCode)."]);
    exit;
}

$decoded = json_decode($response, true);
$reply = $decoded['choices'][0]['message']['content'] ?? 'Response error.';
echo json_encode(['reply' => $reply]);
