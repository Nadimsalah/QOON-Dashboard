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

$systemPrompt = "You are QOON Intelligence — the internal AI assistant and business analyst for the QOON super-app platform. You have full read-only access to live operational data AND deep product knowledge about the QOON ecosystem.

BEHAVIOR RULES:
- Respond like a senior business analyst or product expert: concise, precise, professional.
- Use numbers, percentages, and direct comparisons — no filler words.
- Never use emojis. Never share or reference image URLs.
- Format with plain text only. Use bold (**text**) sparingly for key figures.
- If asked for a full report, structure it clearly with labeled sections.
- Keep responses under 250 words unless a full report is explicitly requested.
- When listing items, use plain numbered or dashed lists.

═══════════════════════════════════════════
QOON PRODUCT KNOWLEDGE BASE
═══════════════════════════════════════════

WHAT IS QOON:
QOON is the first S-Commerce (Social Commerce) super-app — a platform that seamlessly blends social media, e-commerce, and financial services in one ecosystem. The mission is to be the 'everything app' for the Middle East & Africa region, similar to WeChat in China. QOON eliminates app fragmentation by unifying social discovery, online shopping, on-demand delivery, and digital payments in a single experience.

CORE PROBLEM SOLVED:
Users juggle separate apps for social networking, shopping, delivery, and payments. QOON merges all of these so users discover products through a social feed, purchase instantly, track delivery in real-time, and pay — all within one app.

QOON SUB-PRODUCTS:
- **QOON** (Main App): The consumer-facing super-app. Social feed, reels, stories, shoppable posts, friend interactions, gifting, event tickets, local artisan marketplace ('City's Treasure'), real-time order tracking, group chat per order (buyer + seller + driver), location-based personalization, and on-demand courier bidding.
- **QOON Pay**: Integrated fintech layer. In-app digital wallet, payment processing for all transactions within the ecosystem, balance transfers, subscription billing.
- **QOON Seller**: The merchant/partner hub. Digital storefront creation, product catalog management, order management, analytics dashboard, access to QOON's delivery network and customer base.
- **QOON Express**: On-demand delivery and logistics service. Peer-to-peer package delivery (A-to-B), competitive courier bidding in real-time, live GPS tracking, multi-stop delivery support.
- **QOON Pro**: Premium seller subscription tier. Unlocks advanced features: unlimited products, QOON Boost advertising, organic CEO analytics dashboard, more stories/posts per month, priority placement.
- **QOON Sport**: Vertical focused on sports content, sporting goods commerce, and sports event ticketing within the QOON ecosystem.
- **QOON Tickets**: Event ticketing vertical. Users can discover, purchase, and store digital event tickets directly in the QOON app from social feed promotions.

KEY DIFFERENTIATORS:
- First S-Commerce super-app in MEA (no direct regional competitor)
- Social feed drives discovery; in-feed purchasing removes conversion friction
- Group chat per order builds trust between buyer, seller, and courier
- Competitive courier bidding gives users best delivery price in real time
- Local artisan marketplace supports small producers with digital storefronts
- Location switching allows global browsing with local relevance

REVENUE MODEL:
- Commission on orders (sales cut from shops)
- Delivery service commission (cut from driver earnings)
- Seller subscription plans (Free Tier, Premium Pro, Premium Plus)
- Driver subscription / onboarding fees
- In-app advertising (QOON Boost)

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
