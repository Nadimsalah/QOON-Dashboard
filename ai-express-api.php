<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
try {
$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";
$dbname = "qoon_Qoon";
try {
    $con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($con->connect_error) {
        echo json_encode(['error' => 'DB Connect Error: ' . $con->connect_error]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['error' => 'DB Exception: ' . $e->getMessage()]);
    exit;
}
mysqli_set_charset($con, "utf8mb4");
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');
if (empty($userMessage)) {
    echo json_encode(['error' => 'Empty message']);
    exit;
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

// AGGREGATE STATISTICS - Full database
$totalUsers    = (int)(mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM Users"))[0] ?? 0);
$totalOrders   = (int)(mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM Orders"))[0] ?? 0);
$totalRevenue  = (float)(mysqli_fetch_row(mysqli_query($con, "SELECT IFNULL(SUM(OrderPrice),0) FROM Orders WHERE OrderState IN ('Done','Rated')"))[0] ?? 0);
$androidUsers  = (int)(mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM Users WHERE UserType='ANDROID'"))[0] ?? 0);
$iosUsers      = (int)(mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM Users WHERE UserType!='ANDROID' AND UserType!='' "))[0] ?? 0);
$newUsersWeek  = (int)(mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM Users WHERE CreatedAtUser >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))[0] ?? 0);
$pendingOrders = (int)(mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM Orders WHERE OrderState='waiting'"))[0] ?? 0);
$todayOrders   = (int)(mysqli_fetch_row(mysqli_query($con, "SELECT COUNT(*) FROM Orders WHERE DATE(CreatedAtOrders)=CURDATE()"))[0] ?? 0);

// USER SNAPSHOT with full details
$usersData = [];
$query = "
    SELECT 
        u.UserID, u.name, u.Email, u.Balance, u.CreatedAtUser, u.UserType,
        (SELECT COUNT(*) FROM Orders o WHERE o.UserID = u.UserID) as OrderCount,
        (SELECT IFNULL(SUM(OrderPrice),0) FROM Orders o WHERE o.UserID = u.UserID AND o.OrderState IN ('Done','Rated')) as TotalSpent
    FROM Users u 
    ORDER BY u.UserID DESC 
    LIMIT 600
";
$res = mysqli_query($con, $query);
if (!$res) {
    throw new Exception(mysqli_error($con));
}
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $id = $row['UserID'];
        $n = str_replace(',', '', $row['name'] ?? '');
        $em = $row['Email'] ?? '';
        $bal = (float)($row['Balance'] ?? 0);
        $type = $row['UserType'] ?? 'Unknown';
        $ord = (int)($row['OrderCount'] ?? 0);
        $spent = (float)($row['TotalSpent'] ?? 0);
        $date = date('Y-m-d', strtotime($row['CreatedAtUser']));
        $usersData[] = "[$id] N:$n | E:$em | OS:$type | Bal:{$bal} MAD | Orders:$ord | Spent:{$spent} MAD | Reg:$date";
    }
}
$realCount = count($usersData);
$usersContext = implode("\n", $usersData);

// RECENT ORDERS SNAPSHOT
$ordersData = [];
$queryOrders = "
    SELECT 
        o.OrderID, o.UserID, o.ShopID, o.DestinationName, o.OrderState, o.OrderPrice, o.CreatedAtOrders, o.OrderType
    FROM Orders o 
    ORDER BY o.CreatedAtOrders DESC 
    LIMIT 200
";
$resOrders = mysqli_query($con, $queryOrders);
if ($resOrders) {
    while ($row = mysqli_fetch_assoc($resOrders)) {
        $oid = $row['OrderID'];
        $uid = $row['UserID'];
        $sid = $row['ShopID'] ?? 'Unknown';
        $dest = str_replace(',', '', $row['DestinationName'] ?? '');
        $state = $row['OrderState'] ?? 'Unknown';
        $price = (float)($row['OrderPrice'] ?? 0);
        $otype = $row['OrderType'] ?? 'Normal';
        $odate = date('Y-m-d H:i', strtotime($row['CreatedAtOrders']));
        $ordersData[] = "[$oid] User:$uid | Shop:$sid | Dest:$dest | State:$state | Price:{$price} MAD | Type:$otype | Date:$odate";
    }
}
$ordersCount = count($ordersData);
$ordersContext = implode("\n", $ordersData);

// RECENT SHOPS SNAPSHOT
$shopsData = [];
$queryShops = "SELECT ShopID, ShopName, ShopPhone, Type, Balance, CityID, Email, Status FROM Shops LIMIT 300";
$resShops = mysqli_query($con, $queryShops);
if ($resShops) {
    while ($row = mysqli_fetch_assoc($resShops)) {
        $sid = $row['ShopID'];
        $sn = str_replace(',', '', $row['ShopName'] ?? '');
        $sph = $row['ShopPhone'] ?? '';
        $smail = $row['Email'] ?? '';
        $st = $row['Status'] ?? '';
        $shopsData[] = "[$sid] Name:$sn | Phone:$sph | Email:$smail | Status:$st";
    }
}
$shopsCount = count($shopsData);
$shopsContext = implode("\n", $shopsData);

// RECENT DRIVERS SNAPSHOT
$driversData = [];
$queryDrivers = "SELECT DriverID, FName, LName, DriverPhone, DriverState, Online FROM Drivers LIMIT 300";
$resDrivers = mysqli_query($con, $queryDrivers);
if ($resDrivers) {
    while ($row = mysqli_fetch_assoc($resDrivers)) {
        $did = $row['DriverID'];
        $df = str_replace(',', '', $row['FName'] ?? '');
        $dl = str_replace(',', '', $row['LName'] ?? '');
        $dph = $row['DriverPhone'] ?? '';
        $dst = $row['DriverState'] ?? '';
        $don = (int)($row['Online'] ?? 0);
        $dnm = trim("$df $dl");
        $driversData[] = "[$did] Name:$dnm | Phone:$dph | State:$dst | Online:$don";
    }
}
$driversCount = count($driversData);
$driversContext = implode("\n", $driversData);

$systemPrompt = "You are 'Chemsy', the virtual AI assistant for QOON Express. Your specialization is fleets, couriers, and delivery flow. You are an internal AI analyst with full read-only access to QOON\'s live database.

RULES:
- Be incredibly concise, direct, and highly professional.
- Present data using clear conversational text and neat bullet points. DO NOT output HTML tables.
- Focus specifically on your specialization when answering.
- Always detect the language of the user\'s message and respond in THAT EXACT LANGUAGE. (e.g., if Arabic, respond in fluent business Arabic).
- NEVER use dollar sign ($). ALWAYS use MAD as the currency for all monetary values.
- NEVER invent or estimate data. Only report what is in the provided context.
- Use the PLATFORM STATS below for platform-wide summaries and totals.

=== PLATFORM STATS (LIVE - FULL DATABASE) ===
- Total Registered Users: $totalUsers
- Android Users: $androidUsers
- iOS Users: $iosUsers
- New Users (Last 7 Days): $newUsersWeek
- Total Orders (All Time): $totalOrders
- Orders Today: $todayOrders
- Pending Orders: $pendingOrders
- Total Revenue (Done+Rated): $totalRevenue MAD

=== USER SNAPSHOT ($realCount users) — (ID | Name | Email | OS | Balance | Orders | Spent | Reg Date) ===
$usersContext

=== RECENT ORDERS SNAPSHOT ($ordersCount recent orders) — (ID | UserID | ShopID | Dest | State | Price | Type | Date) ===
$ordersContext

=== SHOPS/VENDORS SNAPSHOT ($shopsCount shops) — (ID | Name | Phone | Email | Status) ===
$shopsContext

=== DRIVERS SNAPSHOT ($driversCount drivers) — (ID | Name | Phone | State | Online) ===
$driversContext
";

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
$messages = utf8ize($messages);

$payload = [
    'model' => 'deepseek-chat',
    'messages' => $messages,
    'max_tokens' => 1500,
    'temperature' => 0.3,
    'stream' => false
];

$jsonPayload = json_encode($payload);
if (!$jsonPayload) {
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
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo json_encode(['reply' => "QOON AI service unavailable ($httpCode)."]);
    exit;
}

$decoded = json_decode($response, true);
$reply = $decoded['choices'][0]['message']['content'] ?? 'Response error.';
echo json_encode(['reply' => $reply]);
} catch (\Throwable $t) {
    http_response_code(200);
    echo json_encode(['error' => 'Fatal Error: ' . $t->getMessage() . ' on line ' . $t->getLine()]);
}
