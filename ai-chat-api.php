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
$ctx['total_revenue'] = (float) safeQuery($con, "SELECT IFNULL(SUM(OrderPrice),0) FROM Orders WHERE OrderState IN ('Done','Rated')");
$ctx['total_users'] = (int) safeQuery($con, "SELECT COUNT(*) FROM Users");
$ctx['total_orders'] = (int) safeQuery($con, "SELECT COUNT(*) FROM Orders");

// 2. FINANCIAL HEALTH INDEX
$ctx['driver_debt'] = (float) safeQuery($con, "SELECT IFNULL(SUM(OrderPriceFromShop),0) FROM Orders WHERE PaidForDriver='NotPaid' AND Method='Cash' AND (OrderState='Rated' OR OrderState='Done')");
$ctx['shop_owed'] = (float) safeQuery($con, "SELECT IFNULL(SUM(Balance),0) FROM Shops");

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
    $res = mysqli_query($con, "SELECT FoodName, FoodPrice, FoodPhoto FROM Foods ORDER BY FoodID DESC LIMIT 15");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $name = $row['FoodName'] ?: "Unnamed Product";
            $recent_foods[] = "- $name | Price: {$row['FoodPrice']} MAD | Image: {$row['FoodPhoto']}";
        }
    }
} catch (\Throwable $e) {
}
$ctx['recent_foods_list'] = count($recent_foods) > 0 ? implode("\n", $recent_foods) : "None";

$dbSummary = "
ELITE QOON SNAPSHOT:
=== PERFORMANCE ===
- Revenue: {$ctx['total_revenue']} MAD
- Volume: {$ctx['total_orders']} Orders
- Top Seller: {$ctx['top_shop']}

=== FINANCES ===
- Driver Debt: {$ctx['driver_debt']} MAD
- Shop Balance: {$ctx['shop_owed']} MAD

=== INVENTORY ===
{$ctx['category_stats']}

=== RECENT ORDERS (PAYLOAD) ===
{$ctx['order_stream']}

=== PRODUCTS ===
{$ctx['recent_foods_list']}
";

$systemPrompt = "You are QOON AI. Analyze the RECENT ORDERS to find Top Products. Use ![Name](URL) for visuals. Group images for carousels.

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
