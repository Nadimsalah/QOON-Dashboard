<?php
/**
 * moderate_post.php
 * Auto-moderates a post using OpenAI GPT-4o-mini.
 * Called via AJAX from content.php
 */
header('Content-Type: application/json; charset=utf-8');

require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$OPENAI_API_KEY = "sk-proj-YOUR_API_KEY_HERE";
$apiUrl   = "https://api.openai.com/v1/chat/completions";
$model    = "gpt-4o-mini";

$isCli = (php_sapi_name() === 'cli');
$postId = isset($_POST['PostId']) ? (int)$_POST['PostId'] : ($isCli && isset($argv[1]) ? (int)$argv[1] : 0);
$apply  = isset($_POST['apply']) ? (bool)$_POST['apply'] : ($isCli ? true : false); // Always apply if CLI
$type   = isset($_POST['Type']) ? $_POST['Type'] : ($isCli && isset($argv[2]) ? $argv[2] : 'post');

if (!$postId) {
    echo json_encode(['success' => false, 'error' => 'Missing ID']);
    exit;
}

// ── Fetch the data ──────────────────────────────────────────────────────────
$postText = '';
$shopName = '';
$hasImage = false;
$hasVideo = false;

$videoUrl = '';

if ($type === 'story') {
    $row = $con->query("SELECT SS.StotyID, SS.StoryPhoto, SS.BunnyV, S.ShopName
                        FROM ShopStory SS JOIN Shops S ON SS.ShopID = S.ShopID
                        WHERE SS.StotyID = $postId LIMIT 1")->fetch_assoc();
    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'Story not found']);
        exit;
    }
    $shopName = $row['ShopName'] ?? '';
    $videoUrl = $row['StoryPhoto'] ?? '';
    // Stories are fundamentally video/image media
    $hasVideo = true; 
} else {
    $row = $con->query("SELECT P.PostId, P.PostText, P.PostPhoto, P.Video, S.ShopName
                        FROM Posts P JOIN Shops S ON P.ShopID = S.ShopID
                        WHERE P.PostId = $postId LIMIT 1")->fetch_assoc();
    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'Post not found']);
        exit;
    }
    $postText = $row['PostText'] ?? '';
    $shopName = $row['ShopName'] ?? '';
    $videoUrl = $row['Video'] ?? '';
    $hasImage = (!empty($row['PostPhoto']) && $row['PostPhoto'] !== 'none') ? true : false;
    $hasVideo = (!empty($videoUrl) && $videoUrl !== 'none') ? true : false;
}

// Normalize videoURL like content.php to ensure Sightengine can fetch it
$videoUrl = trim($videoUrl);
if ($hasVideo && $videoUrl && !in_array(strtolower($videoUrl), ['', 'none', '0', 'null'])) {
    if (str_starts_with($videoUrl, 'https://qoon.app/') && !str_starts_with($videoUrl, 'https://qoon.app/dash/') && !str_starts_with($videoUrl, 'https://qoon.app/db/')) {
        // already perfect
    } else {
        $prefixes = ['https://jibler.app/db/db/', 'http://jibler.app/db/db/', 'https://jibler.app/dash/', 'https://jibler.app/', 'http://jibler.app/', 'https://jibler.ma/db/db/', 'http://jibler.ma/db/db/', 'https://jibler.ma/dash/', 'https://jibler.ma/', 'https://www.jibler.app/', 'https://www.jibler.ma/', 'https://dashboard.jibler.ma/dash/', 'https://qoon.app/dash/', 'https://qoon.app/db/db/', 'http://qoon.app/'];
        foreach ($prefixes as $prefix) {
            if (str_starts_with($videoUrl, $prefix)) {
                $videoUrl = substr($videoUrl, strlen($prefix));
                break;
            }
        }
        $videoUrl = 'https://qoon.app/' . ltrim($videoUrl, '/');
    }
} else {
    $hasVideo = false;
    $videoUrl = '';
}

$imageNote  = $hasImage  ? "Contains product images." : "No image attached.";
$videoNote  = $hasVideo  ? "Contains promotional video." : "No video attached.";

// ── Build the moderation prompt ─────────────────────────────────────────────
$systemPrompt = <<<PROMPT
You are a strict content moderation system for an e-commerce platform.

Your job is to analyze user-generated content (text, image description, or video transcript) and decide if it is allowed to be published.

You must enforce the following rules strictly:

DISALLOWED CONTENT (REJECT):
- Sexual or explicit adult content (nudity, porn, suggestive content)
- Illegal products or services (drugs, weapons, fraud, counterfeit goods)
- Promotion of violence, terrorism, or dangerous activities
- Hate speech, harassment, slurs, or discrimination 
- Explicit political propaganda or political campaigning
- Misleading or scam-like content (fake offers, fraud)
- ANY profanity, swearing, bad words, or offensive insults in ANY language, dialect, or phonetic spelling. You MUST specifically check Arabic and Moroccan Darija (e.g. reject words like "zaml", "kahba", "qahba", "zamel", "9ahba", etc). Do NOT approve any post containing such insults.

LIMITED CONTENT (FLAG FOR REVIEW):
- Mild sexual references (non-explicit)
- Sensitive topics (religion, politics without promotion)
- Aggressive or offensive tone
- Ambiguous products (could be legal or illegal)

ALLOWED CONTENT:
- Normal product listings
- Business promotions
- Neutral or safe content

You must respond ONLY in valid JSON format.

Output schema:
{
  "decision": "APPROVED | REJECTED | PENDING",
  "confidence": 0-100,
  "categories": {
    "sexual": true/false,
    "violence": true/false,
    "illegal": true/false,
    "hate": true/false,
    "political": true/false,
    "scam": true/false
  },
  "reason": "short clear explanation"
}
PROMPT;

$userContent = "Analyze the following content:\n\nTEXT:\n" . ($postText ?: '(No text)') .
               "\n\nIMAGE DESCRIPTION:\n$imageNote" .
               "\n\nVIDEO TRANSCRIPT:\n$videoNote" .
               "\n\nReturn moderation result.";

// ── Execute Moderation ────────────────────────────────────────────────────────
$result = null;

if ($hasVideo && $videoUrl) {
    // ==== SIGHTENGINE VIDEO MODERATION ====
    $SIGHTENGINE_USER   = 'YOUR_API_USER';
    $SIGHTENGINE_SECRET = 'YOUR_API_SECRET';
    
    $params = array(
      'url' => $videoUrl,
      'models' => 'nudity,wad,offensive,scam,gore',
      'api_user' => $SIGHTENGINE_USER,
      'api_secret' => $SIGHTENGINE_SECRET
    );

    $ch = curl_init('https://api.sightengine.com/1.0/video/sync.json?' . http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo json_encode(['success' => false, 'error' => 'Sightengine cURL Error: ' . $err]);
        exit;
    }

    $seData = json_decode($response, true);
    if (!$seData || $seData['status'] !== 'success') {
        // Fallback or failure if API keys are missing or invalid
        $errMessage = $seData['error']['message'] ?? 'Unknown API Error';
        if (strpos($errMessage, 'Missing api_user') !== false || strpos($errMessage, 'Invalid api_user') !== false) {
            echo json_encode(['success' => false, 'error' => 'Sightengine API keys required natively. Please set in moderate_post.php']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Sightengine failed: ' . $errMessage]);
        }
        exit;
    }

    // Evaluate Sightengine frames
    $rejected = false;
    $reason = "Video appears safe.";
    $categories = ["sexual" => false, "violence" => false, "illegal" => false, "hate" => false, "political" => false, "scam" => false];
    $maxConfidence = 0;

    foreach ($seData['data']['frames'] as $frame) {
        $nudity = $frame['nudity']['safe'] ?? 1.0;
        $weapon = $frame['weapon'] ?? 0;
        $drugs  = $frame['drugs'] ?? 0;
        $gore   = $frame['gore']['prob'] ?? 0;
        
        $probUnsafe = max(1 - $nudity, $weapon, $drugs, $gore);
        if ($probUnsafe > $maxConfidence) $maxConfidence = $probUnsafe;

        if ($nudity < 0.4) { $rejected = true; $categories['sexual'] = true; $reason = "Explicit/Nudity detected in video frame."; }
        if ($weapon > 0.6) { $rejected = true; $categories['violence'] = true; $reason = "Weapon detected in video frame."; }
        if ($drugs > 0.6)  { $rejected = true; $categories['illegal'] = true; $reason = "Drugs/Alcohol detected in video frame."; }
        if ($gore > 0.6)   { $rejected = true; $categories['violence'] = true; $reason = "Gore/violence detected in video frame."; }
    }

    $result = [
        "decision" => $rejected ? "REJECTED" : "APPROVED",
        "confidence" => round($maxConfidence * 100),
        "categories" => $categories,
        "reason" => $reason
    ];

} else {
    // ==== OPENAI TEXT/IMAGE MODERATION ====
    $payload = [
        "model"           => $model,
        "response_format" => ["type" => "json_object"],
        "temperature"     => 0.0,
        "messages"        => [
            ["role" => "system", "content" => $systemPrompt],
            ["role" => "user",   "content" => $userContent],
        ],
    ];

    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            "Authorization: Bearer $OPENAI_API_KEY",
            "Content-Type: application/json",
        ],
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_TIMEOUT        => 20,
    ]);

    $raw  = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) {
        echo json_encode(['success' => false, 'error' => 'OpenAI cURL error: ' . $err]);
        exit;
    }

    $aiRes   = json_decode($raw, true);
    $content = $aiRes['choices'][0]['message']['content'] ?? '{}';
    $result  = json_decode($content, true);

    if (!$result || !isset($result['decision'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid AI response', 'raw' => $content]);
        exit;
    }
}

// ── Optionally apply the decision to DB ───────────────────────────────────
$dbStatus = null;
if ($apply) {
    if ($result['decision'] === 'APPROVED') {
        $dbStatus = 'ACTIVE';
    } elseif ($result['decision'] === 'PENDING') {
        $dbStatus = 'PENDING';
    } else {
        $dbStatus = 'REJECTED';
    }

    // Local safety override — extra keyword check
    if ($dbStatus === 'ACTIVE') {
        $keywords = ['gun', 'guns', 'drug', 'drugs', 'weapon', 'porn', 'casino', 'gambling', 'fake', 'counterfeit', 'zamel', 'zaml', 'zhml', 'kahba', 'qahba', '9ahba'];
        foreach ($keywords as $kw) {
            if (stripos($postText, $kw) !== false) {
                $dbStatus = 'PENDING';
                $result['reason'] .= " (Flagged by local keyword filter: '$kw')";
                $result['decision'] = 'PENDING';
                break;
            }
        }
    }
    
    if ($type === 'story') {
        $stmt = $con->prepare("UPDATE ShopStory SET StoryStatus = ?, AiChecked = 1 WHERE StotyID = ?");
    } else {
        $stmt = $con->prepare("UPDATE Posts SET PostStatus = ?, AiChecked = 1 WHERE PostId = ?");
    }
    $stmt->bind_param("si", $dbStatus, $postId);
    $stmt->execute();
}

echo json_encode([
    'success'  => true,
    'postId'   => $postId,
    'decision' => $result['decision'],
    'confidence'=> $result['confidence'] ?? 0,
    'categories'=> $result['categories'] ?? [],
    'reason'   => $result['reason'] ?? '',
    'dbStatus' => $dbStatus,
]);
