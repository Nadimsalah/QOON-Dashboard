<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

// 🔴🔴🔴 YOUR OPENAI API KEY HERE 🔴🔴🔴
$OPENAI_API_KEY = "sk-proj-YOUR_API_KEY_HERE"; 

$dbhost = "145.223.33.118";
$dbuser = "qoon_Qoon";
$dbpass = ";)xo6b(RE}K%";
$dbname = "qoon_Qoon";

$con = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($con->connect_error) {
    echo json_encode(['reply' => 'Database connection failed.']);
    exit;
}
$con->set_charset("utf8mb4");

$input = json_decode(file_get_contents('php://input'), true);
$userMessage = trim($input['message'] ?? '');
if (empty($userMessage)) {
    echo json_encode(['reply' => 'Empty message received.']);
    exit;
}

// Ensure the OpenAI key is ready
if ($OPENAI_API_KEY === "sk-OPENAI_KEY_HERE") {
    // 🔥 FALLBACK TO DEEPSEEK JUST SO IT WON'T CRASH UNTIL YOU ADD YOUR GPT KEY
    $OPENAI_API_KEY = "sk-d25ba3eadc464644a051ea2fe7d83f7a";
    $apiUrl = "https://api.deepseek.com/chat/completions";
    $modelName = "deepseek-chat";
} else {
    $apiUrl = "https://api.openai.com/v1/chat/completions";
    $modelName = "gpt-4o-mini"; // Standard ChatGPT model for data tasks
}

// 1. Text-to-SQL: Generate the query
function generateSQL($question, $schema, $apiKey, $apiUrl, $modelName, $history = []) {
    $prompt = "You are a MySQL query generator for QOON.
Your ONLY job is to convert the user question into a SQL query.

Database Schema:
$schema

Rules:
- ONLY return valid JSON in the format: { \"sql\": \"your query here\" }
- DO NOT explain anything.
- DO NOT ask questions.
- DO NOT add conversational text.
- ONLY generate SELECT queries.
- No DELETE, UPDATE, DROP, ALTER.
- Always add LIMIT 100.
- If counting, use COUNT(*).
- DO NOT invent columns.
- Use exact column names (case sensitive).
- Use table names exactly (case sensitive). Example: 'Users' not 'users'.
- CONTEXT CONTINUITY: If the question lacks a specific subject (e.g. 'how many active?'), YOU MUST read the Conversation History and use the EXACT SAME table/subject as the previous query.
- If the question is completely ambiguous, make a reasonable assumption and still generate SQL.

Examples:
Q: how many users
A: { \"sql\": \"SELECT COUNT(*) FROM Users LIMIT 100\" }

Q: list users with phone
A: { \"sql\": \"SELECT UserID, name, PhoneNumber FROM Users LIMIT 100\" }

Q: users with high balance
A: { \"sql\": \"SELECT UserID, name, Balance FROM Users ORDER BY Balance DESC LIMIT 100\" }
";

    $messagesArr = [
        ["role" => "system", "content" => $prompt]
    ];
    
    // Inject recent conversational context to resolve pronouns / references
    if (!empty($history)) {
        $recentHistory = array_slice($history, -4);
        foreach ($recentHistory as $msg) {
            if (isset($msg['role']) && isset($msg['text']) && !empty($msg['text'])) {
                $role = ($msg['role'] === 'ai' || $msg['role'] === 'assistant') ? 'assistant' : 'user';
                $messagesArr[] = ["role" => $role, "content" => $msg['text']];
            }
        }
    }

    $messagesArr[] = ["role" => "user", "content" => $question];

    $data = [
        "model" => $modelName,
        "response_format" => ["type" => "json_object"],
        "messages" => $messagesArr,
        "temperature" => 0.0
    ];

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_SSL_VERIFYPEER => false // Removed strict SSL verification for local dev functionality
    ]);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);
    
    $rawContent = $response['choices'][0]['message']['content'] ?? '{}';
    $decodedData = json_decode($rawContent, true);
    $sql = $decodedData['sql'] ?? 'NOT_SQL';
    
    // Safety fallback: Clean up markdown if the AI ever bypasses JSON instructions
    $sql = trim($sql);
    $sql = str_replace(["```sql", "```"], "", $sql);

    // ensure LIMIT exists to prevent database freezing
    if ($sql !== 'NOT_SQL' && stripos($sql, 'limit') === false) {
        $sql .= " LIMIT 100";
    }

    return trim($sql);
}

// 2. Validate the SQL strictly
function isSafeQuery($sql) {
    if ($sql === 'NOT_SQL') return false;
    
    $sqlLower = strtolower($sql);
    $blocked = ['delete ', 'update ', 'drop ', 'insert ', 'alter ', 'truncate ', 'replace ', '--', ';'];
    foreach ($blocked as $word) {
        if (strpos($sqlLower, $word) !== false) return false;
    }
    
    // Must start with SELECT
    if (strpos(trim($sqlLower), 'select') !== 0) return false;
    return true;
}

// 3. Format the result using AI
function formatResultWithAI($question, $dbData, $apiKey, $apiUrl, $modelName, $history = []) {
    $dataStr = json_encode($dbData);
    $prompt = "
You are QOON Intelligence, the platform's AI Admin Assistant.
We ran a secure DB query based on the user's question and conversation history to get this raw JSON result.
Your job is to translate this raw JSON into a human-friendly response that makes sense in the context of the conversation.

Raw Database JSON:
$dataStr

Rules:
- Match the user's exact language (Arabic -> Arabic, English -> English).
- Be conversational but precise. Understand the context of the user's pronouns (like 'they', 'them', 'it', 'active ones') based on conversation history.
- NEVER mention 'SQL', 'database', 'JSON', or 'schemas'. Just give the final answer naturally.
- ALL currency on this platform is exclusively Moroccan Dirham. ALWAYS use ' MAD' (not '$' or 'USD') when formatting money or balances.
- If the dataset is empty ([]), say that no records match their request.
- Present data naturally using conversational text and simple bullet points. DO NOT output HTML tables or Markdown tables unless explicitly requested.
";

    $messagesArr = [
        ["role" => "system", "content" => $prompt]
    ];
    
    // Inject recent conversational context so it understands pronouns in its answer
    if (!empty($history)) {
        $recentHistory = array_slice($history, -6); // Take a bit more context if helpful
        foreach ($recentHistory as $msg) {
            if (isset($msg['role']) && isset($msg['text']) && !empty($msg['text'])) {
                $role = ($msg['role'] === 'ai' || $msg['role'] === 'assistant') ? 'assistant' : 'user';
                $messagesArr[] = ["role" => $role, "content" => $msg['text']];
            }
        }
    }

    $messagesArr[] = ["role" => "user", "content" => $question];

    $data = [
        "model" => $modelName,
        "messages" => $messagesArr,
        "temperature" => 0.4
    ];

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer $apiKey",
            "Content-Type: application/json"
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);
    return $response['choices'][0]['message']['content'] ?? 'Error formatting response.';
}

// ─── EXECUTION FLOW ─────────────────────────────────────────────────────────

$schema = "
Table: Users
Columns: UserID, name, PhoneNumber, Email, BirthDate, Gender, AccountType, AccountState, CreatedAtUser, Balance, UserOrdersNum, UserType, CityID, CountryID

Table: Orders
Columns: OrderID, UserID, ShopID, DestinationName, OrderState, OrderPrice, OrderPriceFromShop, OrderPriceForOur, CreatedAtOrders, OrderType, PaidForDriver, ShopAccept

Table: Drivers
Columns: DriverID, FName, LName, DriverPhone, CountryID, City, DriverRate, DriverState, DriverOrdersNum, charge, CreatedAtDrivers, TotolEarnMoney, Online

Table: Shops
Columns: ShopID, ShopName, ShopRate, ShopPhone, Type, Balance, CreatedAtShops, CityID, Email, Status
";


// Extract history from the request payload
$history = $input['history'] ?? [];

// Step 1: Generate SQL (Passed with context history)
$sql = generateSQL($userMessage, $schema, $OPENAI_API_KEY, $apiUrl, $modelName, $history);

// Step 2: Validate SQL Security
if (!$sql || !isSafeQuery($sql)) {
    // If it's a conversational prompt and not a DB query, handle via simple fallback
    $friendlyPrompt = "You are QOON AI. The user said something that doesn't rely entirely on a DB query. Respond politely, understand the context from the conversation history, and ask how you can help with data analytics.";
    
    $messagesArr = [
        ["role" => "system", "content" => $friendlyPrompt]
    ];
    if (!empty($history)) {
        $recentHistory = array_slice($history, -4);
        foreach ($recentHistory as $msg) {
            if (isset($msg['role']) && isset($msg['text']) && !empty($msg['text'])) {
                $role = ($msg['role'] === 'ai' || $msg['role'] === 'assistant') ? 'assistant' : 'user';
                $messagesArr[] = ["role" => $role, "content" => $msg['text']];
            }
        }
    }
    $messagesArr[] = ["role" => "user", "content" => $userMessage];

    $payload = [
        "model" => $modelName,
        "messages" => $messagesArr
    ];
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ["Authorization: Bearer $OPENAI_API_KEY", "Content-Type: application/json"],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_SSL_VERIFYPEER => false
    ]);
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);
    echo json_encode(['reply' => $res['choices'][0]['message']['content'] ?? 'I can only assist with platform data queries.']);
    exit;
}

// Step 3: Run the SECURE Query
$dbResult = [];
try {
    $res = mysqli_query($con, $sql);
    if ($res) {
        if ($res instanceof mysqli_result) {
            while ($row = mysqli_fetch_assoc($res)) {
                $dbResult[] = $row;
            }
        }
    } else {
        $error = mysqli_error($con);
        echo json_encode(['reply' => "Database error executing query: $error\n\nGenerated SQl:\n$sql"]);
        exit;
    }
} catch (Throwable $e) {
    echo json_encode(['reply' => 'System error: ' . $e->getMessage() . "\n\nGenerated SQL:\n$sql"]);
    exit;
}

// Step 4: AI formats raw JSON into a human reply
$humanReply = formatResultWithAI($userMessage, $dbResult, $OPENAI_API_KEY, $apiUrl, $modelName, $history);

// Return Final Output
echo json_encode(['reply' => $humanReply]);
