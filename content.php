<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

// Force browsers to not cache this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Normalizer mapping for URLs (matches feed recommendation logic)
const LEGACY_DOMAINS_FEED = [
    'https://jibler.app/db/db/', 'http://jibler.app/db/db/',
    'https://jibler.app/dash/', 'https://jibler.app/', 'http://jibler.app/',
    'https://jibler.ma/db/db/', 'http://jibler.ma/db/db/',
    'https://jibler.ma/dash/', 'https://jibler.ma/', 'https://www.jibler.app/',
    'https://www.jibler.ma/', 'https://dashboard.jibler.ma/dash/',
    'https://qoon.app/dash/', 'https://qoon.app/db/db/', 'http://qoon.app/',
];

function normalizeMediaUrl($raw) {
    if (!$raw) return null;
    $raw = trim($raw);
    if (in_array(strtolower($raw), ['', 'none', '0'])) return null;

    if (str_starts_with($raw, 'https://qoon.app/') && !str_starts_with($raw, 'https://qoon.app/dash/') && !str_starts_with($raw, 'https://qoon.app/db/')) {
        return $raw;
    }

    foreach (LEGACY_DOMAINS_FEED as $prefix) {
        if (str_starts_with($raw, $prefix)) {
            $raw = substr($raw, strlen($prefix));
            break;
        }
    }
    return 'https://qoon.app/' . ltrim($raw, '/');
}

// Stats calculation is now dynamically computed below after fetching active feed
$totalActive = 0;
$totalPending = 0;
$totalRejected = 0;
$totalAiChecked = 0;
$totalAll = 0;

$sql = "SELECT P.*, COALESCE(S.ShopName, 'Unknown Shop') as ShopName, S.ShopLogo
        FROM Posts P
        LEFT JOIN Shops S ON P.ShopID = S.ShopID
        WHERE (
            (P.PostPhoto  IS NOT NULL AND P.PostPhoto  != '' AND P.PostPhoto  NOT IN ('none','NONE','0','-')) OR
            (P.PostPhoto2 IS NOT NULL AND P.PostPhoto2 != '' AND P.PostPhoto2 NOT IN ('none','NONE','0','-')) OR
            (P.PostPhoto3 IS NOT NULL AND P.PostPhoto3 != '' AND P.PostPhoto3 NOT IN ('none','NONE','0','-')) OR
            (P.Video      IS NOT NULL AND P.Video      != '' AND P.Video      NOT IN ('none','NONE','0','-')) OR
            (P.BunnyV     IS NOT NULL AND P.BunnyV     != '' AND P.BunnyV     NOT IN ('none','NONE','0','-','null'))
        )
        ORDER BY P.PostId DESC LIMIT 500";
$result   = mysqli_query($con, $sql);
$allPosts = [];
while ($r = mysqli_fetch_assoc($result)) {
    if ($r['ShopName'] === 'Unknown Shop' || $r['ShopName'] === 'ام حسن') continue;
    $allPosts[] = $r;
    
    // Tally stats
    $totalAll++;
    $s = strtoupper($r['PostStatus']);
    if ($s === 'ACTIVE') $totalActive++;
    elseif ($s === 'PENDING') $totalPending++;
    elseif ($s === 'REJECTED') $totalRejected++;
    if (!empty($r['AiChecked'])) $totalAiChecked++;
}

// Helper: resolve the best video URL for a post (BunnyV takes priority, fallback to Video)
function resolveVideoUrl($post) {
    $bunny = trim($post['BunnyV'] ?? '');
    if ($bunny && !in_array(strtolower($bunny), ['','none','0','-','null'])) {
        return $bunny;
    }
    return normalizeMediaUrl($post['Video'] ?? null);
}

// Fetch ShopStory videos (Video type with BunnyV CDN URL)
$storyRes   = $con->query("
    SELECT SS.StotyID, SS.StoryPhoto, SS.ShopID, SS.StotyType, SS.StoryStatus, SS.AiChecked,
           SS.BunnyV, SS.BunnyS, COALESCE(S.ShopName,'Unknown Shop') as ShopName, S.ShopLogo
    FROM ShopStory SS
    LEFT JOIN Shops S ON SS.ShopID = S.ShopID
    WHERE SS.BunnyV IS NOT NULL
      AND SS.BunnyV != ''
      AND SS.BunnyV NOT IN ('none','NONE','0','-','null')
      AND SS.StotyType = 'Video'
    ORDER BY SS.StotyID DESC LIMIT 200
");
$allStories = [];
while ($r = $storyRes->fetch_assoc()) {
    if ($r['ShopName'] === 'Unknown Shop' || $r['ShopName'] === 'ام حسن') continue;
    $allStories[] = $r;
    
    // Tally stats
    $totalAll++;
    $s = strtoupper($r['StoryStatus']);
    if ($s === 'ACTIVE') $totalActive++;
    elseif ($s === 'PENDING') $totalPending++;
    elseif ($s === 'REJECTED') $totalRejected++;
    if (!empty($r['AiChecked'])) $totalAiChecked++;
}

// Fetch ShopStory images
$storyImageRes = $con->query("
    SELECT SS.StotyID as PostId, SS.StoryPhoto as PostPhoto, SS.ShopID, SS.StoryStatus as PostStatus, SS.AiChecked,
           COALESCE(S.ShopName,'Unknown Shop') as ShopName, S.ShopLogo
    FROM ShopStory SS
    LEFT JOIN Shops S ON SS.ShopID = S.ShopID
    WHERE SS.StotyType = 'Photos' OR (SS.StotyType != 'Video' AND (SS.BunnyV IS NULL OR SS.BunnyV = '' OR SS.BunnyV = 'none' OR SS.BunnyV = '0'))
    ORDER BY SS.StotyID DESC LIMIT 200
");
$allStoryImages = [];
while ($r = $storyImageRes->fetch_assoc()) {
    if ($r['ShopName'] === 'Unknown Shop' || $r['ShopName'] === 'ام حسن') continue;
    $allStoryImages[] = $r;
    
    if ($s === 'ACTIVE') $totalActive++;
    elseif ($s === 'PENDING') $totalPending++;
    elseif ($s === 'REJECTED') $totalRejected++;
    if (!empty($r['AiChecked'])) $totalAiChecked++;
}

// Fetch Boost Requests
$boostRes = $con->query("
    SELECT B.BoostsByShopID, B.ShopID, B.BoostName, B.BoostPrice, B.BoostTimeDuration, B.BoostPhoto, B.BoostStatus,
           COALESCE(S.ShopName,'Unknown Shop') as ShopName, S.ShopLogo
    FROM BoostsByShop B
    LEFT JOIN Shops S ON B.ShopID = S.ShopID
    ORDER BY B.BoostsByShopID DESC LIMIT 100
");
$allBoosts = [];
while ($r = $boostRes->fetch_assoc()) {
    $allBoosts[] = $r;
    
    // Tally stats (Boosts considered distinct content type)
    $totalAll++;
    $s = strtoupper($r['BoostStatus']);
    if ($s === 'ACTIVE') $totalActive++;
    elseif ($s === 'INREVIEW') $totalPending++;
    elseif ($s === 'REJECTED') $totalRejected++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Content Feed | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-master: #F3F4F6;
            --bg-surface: #FFFFFF;
            --border-subtle: #E5E7EB;
            --text-strong: #111827;
            --text-base: #374151;
            --text-muted: #6B7280;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }

        body {
            background: var(--bg-master);
            color: var(--text-base);
            display: flex;
            height: 100vh;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .layout-wrapper { display: flex; width: 100%; height: 100%; }

        main.content-area {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }
        main.content-area::-webkit-scrollbar { width: 6px; }
        main.content-area::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

        .header-bar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 24px 40px;
        }
        .page-title h1 {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-strong);
            letter-spacing: -0.5px;
        }
        .page-title p {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 4px;
        }

        .page-body {
            padding: 40px;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            flex: 1;
        }

        /* Facebook-style Feed Container */
        .feed-container {
            max-width: 680px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Filter Tabs */
        .view-tabs {
            display: flex; gap: 8px; margin-bottom: 24px;
        }
        .view-tab {
            display: flex; align-items: center; gap: 8px;
            padding: 10px 22px; border-radius: 10px;
            font-size: 14px; font-weight: 700;
            cursor: pointer; border: 1px solid var(--border-subtle);
            background: var(--bg-surface); color: var(--text-muted);
            transition: 0.18s;
        }
        .view-tab:hover { color: var(--text-strong); background: #F9FAFB; }
        .view-tab.active {
            background: var(--text-strong); color: #fff;
            border-color: var(--text-strong);
        }
        .view-tab .tab-count {
            background: rgba(255,255,255,0.2);
            color: inherit; font-size: 11px; font-weight: 800;
            padding: 2px 8px; border-radius: 20px;
        }
        .view-tab:not(.active) .tab-count {
            background: #F3F4F6; color: var(--text-muted);
        }

        /* Reels Grid - YouTube Shorts style */
        .reels-container {
            display: none;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
        }
        .reels-container.visible { display: grid; }

        .reel-card {
            position: relative;
            background: #000;
            border-radius: 16px;
            overflow: hidden;
            aspect-ratio: 9 / 16;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0,0,0,0.25);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .reel-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.35); }

        .reel-card video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .reel-card:hover video { opacity: 0.85; }

        /* Overlay gradient */
        .reel-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.75) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Play button */
        .reel-play {
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 52px; height: 52px;
            background: rgba(255,255,255,0.18);
            backdrop-filter: blur(6px);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-size: 20px;
            pointer-events: none;
            opacity: 0; transition: opacity 0.2s;
        }
        .reel-card:hover .reel-play { opacity: 1; }

        /* Bottom info strip */
        .reel-info {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            padding: 12px 14px;
            pointer-events: none;
        }
        .reel-shop {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 6px;
        }
        .reel-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            object-fit: cover; border: 1.5px solid rgba(255,255,255,0.6);
            flex-shrink: 0;
        }
        .reel-shop-name {
            font-size: 13px; font-weight: 700; color: #fff;
            text-shadow: 0 1px 4px rgba(0,0,0,0.6);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .reel-caption {
            font-size: 12px; color: rgba(255,255,255,0.85);
            line-height: 1.4;
            display: -webkit-box; -webkit-line-clamp: 2;
            -webkit-box-orient: vertical; overflow: hidden;
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .reel-stats {
            display: flex; gap: 10px; margin-top: 6px;
            font-size: 12px; font-weight: 600; color: rgba(255,255,255,0.9);
        }
        /* Status badge on reel */
        .reel-status {
            position: absolute;
            top: 10px; right: 10px;
            font-size: 10px; font-weight: 700;
            padding: 3px 9px; border-radius: 20px;
        }
        /* Reel action buttons (right side like Shorts) */
        .reel-actions {
            position: absolute;
            right: 10px; bottom: 80px;
            display: flex; flex-direction: column; gap: 14px;
            align-items: center;
            pointer-events: all;
        }
        .reel-action-btn {
            display: flex; flex-direction: column; align-items: center;
            gap: 3px; color: #fff; font-size: 10px; font-weight: 600;
            cursor: pointer; text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .reel-action-btn i { font-size: 22px; }

        @media (max-width: 600px) {
            .reels-container { grid-template-columns: repeat(2, 1fr); gap: 8px; }
        }

        /* Post Card */
        .post-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        /* Post Header */
        .post-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }
        .post-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border-subtle);
            background: var(--bg-master);
        }
        .post-meta {
            display: flex;
            flex-direction: column;
        }
        .post-shop-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-strong);
        }
        .post-time {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            margin-top: 2px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Post Content */
        .post-text {
            font-size: 15px;
            color: var(--text-strong);
            line-height: 1.5;
            margin-bottom: 16px;
            white-space: pre-wrap;
        }

        .post-media {
            margin: 0 -20px; /* Bleed out of padding horizontally */
            overflow: hidden;
        }
        .post-media img, .post-media video {
            width: 100%;
            max-height: 600px;
            object-fit: cover;
            display: block;
            background: #000;
        }

        /* Multi-image grid */
        .post-media-grid {
            display: grid;
            gap: 2px;
        }
        .post-media-grid.grid-2 { grid-template-columns: 1fr 1fr; }
        .post-media-grid.grid-3 { grid-template-columns: 1fr 1fr; }
        .post-media-grid.grid-3 img:first-child { grid-row: 1 / 3; }
        .post-media-grid img { height: 100%; min-height: 300px; }

        /* Post Footer / Actions */
        .post-footer {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .post-stats {
            display: flex;
            gap: 16px;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 500;
        }
        .post-stats span {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .post-stats i { cursor: pointer; transition: 0.2s; }
        .post-stats i:hover { color: var(--text-strong); }

        .like-icon { color: #3b82f6; }
        .comment-icon { color: #6b7280; }

        /* Stats Bar */
        .stats-row {
            display: flex; gap: 12px; flex-wrap: wrap;
            margin-top: 20px;
        }
        .stat-card {
            display: flex; align-items: center; gap: 12px;
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 12px;
            padding: 14px 18px;
            flex: 1; min-width: 130px;
            box-shadow: var(--shadow-sm);
        }
        .stat-icon {
            width: 40px; height: 40px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .stat-value { font-size: 22px; font-weight: 800; color: var(--text-strong); line-height: 1; }
        .stat-label { font-size: 12px; font-weight: 500; color: var(--text-muted); margin-top: 3px; }

        /* Bulk AI Button */
        .ai-bulk-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff; border: none; border-radius: 10px;
            padding: 10px 18px; font-size: 14px; font-weight: 700;
            cursor: pointer; box-shadow: 0 4px 14px rgba(99,102,241,0.35);
            transition: 0.2s; white-space: nowrap;
        }
        .ai-bulk-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,0.45); }
        .ai-bulk-btn:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .bulk-badge {
            background: rgba(255,255,255,0.25);
            border-radius: 20px; padding: 2px 10px;
            font-size: 12px; font-weight: 800;
        }

        /* Bulk progress bar */
        #bulkProgress {
            display: none;
            margin: 12px 0 0;
            background: #E5E7EB; border-radius: 10px; height: 8px; overflow: hidden;
        }
        #bulkProgressBar {
            height: 100%; border-radius: 10px; width: 0%;
            background: linear-gradient(90deg, #6366f1, #8b5cf6);
            transition: width 0.3s ease;
        }

        /* AI Moderation */
        .ai-mod-btn {
            display: inline-flex; align-items:center; gap:6px;
            font-size: 13px; font-weight: 600;
            padding: 6px 12px; border-radius: 8px;
            cursor: pointer; border: none; transition: 0.18s;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff; box-shadow: 0 2px 8px rgba(99,102,241,0.3);
        }
        .ai-mod-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(99,102,241,0.4); }
        .ai-mod-btn:disabled { opacity:0.6; cursor:not-allowed; transform:none; }

        /* Badge result that replaces the AI button */
        .mod-badge {
            display: inline-flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 700;
            padding: 5px 12px; border-radius: 8px;
            cursor: pointer;
        }
        .mod-badge.approved { background:#D1FAE5; color:#065F46; border:1px solid #10B981; }
        .mod-badge.rejected { background:#FEE2E2; color:#991B1B; border:1px solid #EF4444; }
        .mod-badge.pending  { background:#FEF3C7; color:#92400E; border:1px solid #F59E0B; }

        /* Moderation Detail Modal */
        .mod-modal-overlay {
            display:none; position:fixed; inset:0; z-index:10000;
            background: rgba(0,0,0,0.55); backdrop-filter:blur(6px);
            align-items:center; justify-content:center;
        }
        .mod-modal {
            background: #fff; border-radius: 20px; width: 100%;
            max-width: 480px; margin: 20px;
            box-shadow: 0 24px 48px rgba(0,0,0,0.2);
            overflow: hidden; font-family: 'Inter', sans-serif;
        }
        .mod-modal-head {
            padding: 22px 24px 18px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
        }
        .mod-modal-head h3 { font-size:18px; font-weight:800; margin-bottom:4px; }
        .mod-modal-head p  { font-size:13px; opacity:0.85; }
        .mod-modal-body { padding: 24px; }
        .mod-decision-row {
            display: flex; align-items:center; gap: 14px;
            padding: 16px; border-radius: 12px;
            margin-bottom: 20px;
        }
        .mod-decision-row.approved { background:#D1FAE5; }
        .mod-decision-row.rejected { background:#FEE2E2; }
        .mod-decision-row.pending  { background:#FEF3C7; }
        .mod-decision-icon { font-size: 28px; }
        .mod-decision-label { font-size:20px; font-weight:800; }
        .mod-confidence { font-size:13px; opacity:0.7; margin-top:2px; }
        .mod-reason { font-size:14px; color:#374151; line-height:1.5; margin-bottom:20px; padding:14px; background:#F9FAFB; border-radius:10px; border:1px solid #E5E7EB; }
        .mod-cats-label { font-size:11px; font-weight:700; color:#9CA3AF; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:10px; }
        .mod-cats { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:20px; }
        .mod-cat-pill {
            font-size:12px; font-weight:600; padding:4px 10px;
            border-radius:20px;
        }
        .mod-cat-pill.flagged { background:#FEE2E2; color:#991B1B; }
        .mod-cat-pill.clean   { background:#F3F4F6; color:#6B7280; }
        .mod-actions { display:flex; gap:10px; }
        .mod-apply-btn {
            flex:1; padding:12px; border-radius:10px; border:none;
            font-size:14px; font-weight:700; cursor:pointer; transition:0.15s;
        }
        .mod-apply-btn.confirm { background:#111827; color:#fff; }
        .mod-apply-btn.confirm:hover { background:#1F2937; }
        .mod-apply-btn.cancel  { background:#F3F4F6; color:#374151; }
        .mod-apply-btn.cancel:hover { background:#E5E7EB; }


        /* Reel thumbnail grid */
        .reels-container {
            display: none;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 12px;
        }
        .reels-container.visible { display: grid; }

        .reel-thumb-card {
            position: relative;
            border-radius: 14px;
            overflow: hidden;
            aspect-ratio: 9 / 16;
            cursor: pointer;
            background: #111;
            box-shadow: 0 4px 16px rgba(0,0,0,0.3);
            transition: transform 0.18s, box-shadow 0.18s;
        }
        .reel-thumb-card:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(0,0,0,0.45); }
        .reel-thumb-card img { width:100%; height:100%; object-fit:cover; display:block; }
        .reel-thumb-nopic { width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:#1a1a1a;font-size:36px;color:#444; }

        .reel-thumb-overlay {
            position:absolute;inset:0;
            background:rgba(0,0,0,0);
            transition:background 0.18s;
            display:flex;align-items:center;justify-content:center;
        }
        .reel-thumb-card:hover .reel-thumb-overlay { background:rgba(0,0,0,0.25); }
        .reel-thumb-play { color:#fff; font-size:28px; opacity:0; transition:opacity 0.18s; }
        .reel-thumb-card:hover .reel-thumb-play { opacity:1; }

        .reel-status {
            position:absolute;top:8px;right:8px;
            font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;
        }
        .reel-thumb-info {
            position:absolute;bottom:0;left:0;right:0;
            padding:8px 10px;
            background:linear-gradient(to top,rgba(0,0,0,0.75),transparent);
            display:flex;align-items:center;gap:6px;
            pointer-events:none;
        }
        .reel-avatar { border-radius:50%;object-fit:cover;flex-shrink:0; }

        /* Modern Table (Boosts) */
        .modern-table-container {
            width: 100%;
            overflow-x: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--border-subtle);
            display: none;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .modern-table-container.visible {
            display: block;
            opacity: 1;
        }
        .modern-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            text-align: left;
            min-width: 800px;
        }
        .modern-table th {
            background: #F9FAFB;
            color: #4B5563;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-subtle);
        }
        .modern-table td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid #F3F4F6;
            color: #111827;
            font-size: 14px;
        }
        .modern-table tr:last-child td { border-bottom: none; }
        .modern-table tbody tr { transition: background 0.15s; }
        .modern-table tbody tr:hover { background: #F9FAFB; }
        
        .mt-shop-info { display: flex; align-items: center; gap: 12px; }
        .mt-shop-logo { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 1px solid var(--border-subtle); }
        .mt-shop-name { font-weight: 600; color: #111827; }
        
        .mt-media-thumb {
            width: 48px; height: 48px; border-radius: 8px; object-fit: cover;
            border: 1px solid rgba(0,0,0,0.1); cursor: pointer; transition: transform 0.2s;
        }
        .mt-media-thumb:hover { transform: scale(1.1); }
        
        .mt-badge {
            display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; gap: 4px; border: 1px solid transparent;
        }
        .mt-actions { display: flex; gap: 8px; align-items: center; justify-content: flex-start; }
        .mt-btn {
            width: 32px; height: 32px; border-radius: 50%; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center; font-size: 14px;
            transition: 0.2s; background: #F3F4F6; color: #4B5563;
        }
        .mt-btn:hover { background: #E5E7EB; transform: scale(1.1); }
        .mt-btn.approve:hover { background: #10B981; color: #fff; }
        .mt-btn.reject:hover { background: #EF4444; color: #fff; }

        /* Video player modal */
        #reelPlayerModal {
            display:none;position:fixed;inset:0;z-index:20000;
            background:rgba(0,0,0,0.85);
            backdrop-filter:blur(20px);
            -webkit-backdrop-filter:blur(20px);
            align-items:flex-start;justify-content:center;
            overflow-y:auto;
        }
        #reelPlayerModal.open { display:flex; }
        .reel-player-wrap {
            width:100%;max-width:400px;
            display:flex;flex-direction:column;
            gap:20px;padding:60px 16px 80px;
            margin:0 auto;
        }
        .reel-player-item {
            position:relative;
            width:100%;
            aspect-ratio:9/16;
            border-radius:20px;
            overflow:hidden;
            background:#000;
            box-shadow:0 16px 48px rgba(0,0,0,0.6);
            flex-shrink:0;
        }
        .reel-player-item video {
            width:100%;height:100%;object-fit:cover;display:block;
        }
        .reel-player-gradient {
            position:absolute;inset:0;
            background:linear-gradient(to top,rgba(0,0,0,0.7) 0%,transparent 55%);
            pointer-events:none;
        }
        .reel-player-info {
            position:absolute;bottom:0;left:0;right:0;padding:16px;
        }
        .reel-player-shop { display:flex;align-items:center;gap:10px;margin-bottom:8px; }
        .reel-player-shopname { font-size:14px;font-weight:700;color:#fff; }
        .reel-player-caption { font-size:13px;color:rgba(255,255,255,0.85);line-height:1.4;margin-bottom:10px; }
        .reel-player-actions { display:flex;gap:10px;flex-wrap:wrap; }
        .reel-player-badge { font-size:11px;font-weight:700;padding:4px 12px;border-radius:20px; }

        /* Close button */
        #reelCloseBtn {
            position:fixed;top:16px;right:16px;z-index:20001;
            width:40px;height:40px;border-radius:50%;
            background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);
            border:none;color:#fff;font-size:20px;cursor:pointer;
            display:none;align-items:center;justify-content:center;
            transition:0.15s;
        }
        #reelCloseBtn:hover { background:rgba(255,255,255,0.3); }
        #reelCloseBtn.open { display:flex; }

        @media (max-width: 991px) {
            .header-bar { padding: 14px 16px; position: static; }
            .page-title h1 { font-size: 20px; }
            .page-body { padding: 12px 12px 80px; }
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }
            main.content-area { overflow-y: visible; }
            
            .feed-container { max-width: 100%; }
            .post-card { border-radius: 8px; border-left: none; border-right: none; padding: 16px; margin: 0 -12px; border-bottom: 1px solid var(--border-subtle); }
            .post-media { margin: 0 -16px; }
        }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">
            <header class="header-bar">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px;">
                    <div class="page-title">
                        <h1>Content Feed</h1>
                        <p>View all posts from across the platform in real-time.</p>
                    </div>
                    <button class="ai-bulk-btn" id="bulkAiBtn" onclick="runBulkAI()">
                        <i class="fas fa-robot"></i>
                        <span>AI Check Unchecked Posts</span>
                        <span class="bulk-badge" id="bulkCount">...</span>
                    </button>
                </div>

                <!-- Stats Row -->
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#EFF6FF; color:#2563EB;"><i class="fas fa-layer-group"></i></div>
                        <div>
                            <div class="stat-value"><?= number_format($totalAll) ?></div>
                            <div class="stat-label">Total Posts</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#F0FDF4; color:#16A34A;"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <div class="stat-value" style="color:#16A34A;"><?= number_format($totalActive) ?></div>
                            <div class="stat-label">Published</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#FFFBEB; color:#D97706;"><i class="fas fa-clock"></i></div>
                        <div>
                            <div class="stat-value" style="color:#D97706;"><?= number_format($totalPending) ?></div>
                            <div class="stat-label">Under Review</div>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon" style="background:#FEF2F2; color:#DC2626;"><i class="fas fa-ban"></i></div>
                        <div>
                            <div class="stat-value" style="color:#DC2626;"><?= number_format($totalRejected) ?></div>
                            <div class="stat-label">Rejected</div>
                        </div>
                    </div>
                    <div class="stat-card" id="statAiChecked">
                        <div class="stat-icon" style="background:#EDE9FE; color:#7C3AED;"><i class="fas fa-robot"></i></div>
                        <div>
                            <div class="stat-value" style="color:#7C3AED;" id="statAiNum"><?= number_format($totalAiChecked) ?></div>
                            <div class="stat-label">AI Checked</div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="page-body">
                <!-- Bulk progress bar -->
                <div id="bulkProgress" style="display:none; margin-bottom:16px; background:#E5E7EB; border-radius:10px; height:8px; overflow:hidden;">
                    <div id="bulkProgressBar" style="height:100%; border-radius:10px; width:0%; background:linear-gradient(90deg,#6366f1,#8b5cf6); transition:width 0.3s ease;"></div>
                </div>

                <!-- View Filter Tabs -->
                <div class="view-tabs">
                    <button class="view-tab active" id="tab-posts" onclick="switchView('posts')">
                        <i class="fas fa-images"></i> Posts
                        <span class="tab-count" id="count-posts">0</span>
                    </button>
                    <button class="view-tab" id="tab-stories" onclick="switchView('stories')">
                        <i class="fas fa-camera-retro"></i> Stories
                        <span class="tab-count" id="count-stories">0</span>
                    </button>
                    <button class="view-tab" id="tab-reels" onclick="switchView('reels')">
                        <i class="fas fa-film"></i> Reels
                        <span class="tab-count" id="count-reels">0</span>
                    </button>
                    <button class="view-tab" id="tab-boosts" onclick="switchView('boosts')" style="color:#D97706;">
                        <i class="fas fa-bullhorn"></i> Boosts
                        <span class="tab-count" id="count-boosts" style="background:#FBBF24;color:#78350F;">0</span>
                    </button>
                </div>

                <!-- Posts Feed -->
                <div class="feed-container" id="view-posts">
                    <?php
                    // Collect all post IDs for JS bulk check
                    $allPostIds = [];
                    ?>
                    <?php if (count($allPosts) > 0): ?>
                        <?php foreach($allPosts as $post): 
                            $allPostIds[] = $post['PostId'];
                            $shopLogo = normalizeMediaUrl($post['ShopLogo']) ?? 'https://ui-avatars.com/api/?name='.urlencode($post['ShopName']).'&background=E5E7EB&color=374151';
                            $timeStr = date('M j, Y, g:i a', strtotime($post['CreatedAtPosts']));
                            
                            $media = [];
                            if ($url = normalizeMediaUrl($post['PostPhoto'])) $media[] = $url;
                            if ($url = normalizeMediaUrl($post['PostPhoto2'])) $media[] = $url;
                            if ($url = normalizeMediaUrl($post['PostPhoto3'])) $media[] = $url;
                            
                            $videoUrl = normalizeMediaUrl($post['Video']);
                            $isReel = !empty($videoUrl);

                            // Status badge
                            $status = strtoupper($post['PostStatus'] ?? 'ACTIVE');
                            $statusStyles = [
                                'ACTIVE'   => 'background:#D1FAE5; color:#065F46; border:1px solid #10B981;',
                                'PENDING'  => 'background:#FEF3C7; color:#92400E; border:1px solid #F59E0B;',
                                'REJECTED' => 'background:#FEE2E2; color:#991B1B; border:1px solid #EF4444;',
                            ];
                            $statusIcons = ['ACTIVE'=>'✅','PENDING'=>'⏳','REJECTED'=>'🚫'];
                            $statusStyle = $statusStyles[$status] ?? 'background:#F3F4F6; color:#374151; border:1px solid #E5E7EB;';
                            $statusIcon  = $statusIcons[$status] ?? '●';
                        ?>
                        <?php if(!$isReel): /* ── PHOTO POST ── */ ?>
                        <div class="post-card" id="post-card-<?= $post['PostId'] ?>" style="<?= $status === 'REJECTED' ? 'opacity:0.6;' : '' ?>">
                            <div class="post-header">
                                <img src="<?= htmlspecialchars($shopLogo) ?>" class="post-avatar" alt="Shop Avatar">
                                <div class="post-meta" style="flex:1;">
                                    <div class="post-shop-name"><?= htmlspecialchars($post['ShopName']) ?></div>
                                    <div class="post-time">
                                        <?= htmlspecialchars($timeStr) ?>
                                        <i class="fas fa-globe-americas" style="font-size:10px; opacity:0.8;"></i>
                                    </div>
                                </div>
                                <!-- Status Badge -->
                                <span style="font-size:11px; font-weight:700; padding:4px 10px; border-radius:20px; <?= $statusStyle ?>">
                                    <?= $statusIcon ?> <?= $status ?>
                                </span>
                            </div>
                            
                            <?php if(!empty($post['PostText']) && $post['PostText'] !== 'none' && $post['PostText'] !== 'NONE'): ?>
                                <div class="post-text"><?= htmlspecialchars($post['PostText']) ?></div>
                            <?php endif; ?>

                            <div class="post-media">
                                <?php if($videoUrl): ?>
                                    <video src="<?= htmlspecialchars($videoUrl) ?>" controls preload="metadata"></video>
                                <?php elseif(count($media) > 0): ?>
                                    <?php if(count($media) == 1): ?>
                                        <img src="<?= htmlspecialchars($media[0]) ?>" alt="Post Image">
                                    <?php elseif(count($media) == 2): ?>
                                        <div class="post-media-grid grid-2">
                                            <img src="<?= htmlspecialchars($media[0]) ?>">
                                            <img src="<?= htmlspecialchars($media[1]) ?>">
                                        </div>
                                    <?php elseif(count($media) >= 3): ?>
                                        <div class="post-media-grid grid-3">
                                            <img src="<?= htmlspecialchars($media[0]) ?>">
                                            <img src="<?= htmlspecialchars($media[1]) ?>">
                                            <img src="<?= htmlspecialchars($media[2]) ?>">
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <div class="post-footer" style="flex-wrap:wrap; gap:10px;">
                                <div class="post-stats">
                                    <span><i class="fas fa-thumbs-up like-icon"></i> <?= (int)$post['PostLikes'] ?></span>
                                    <span style="cursor: pointer;" onclick="openComments('<?= $post['PostId'] ?>')"><i class="fas fa-comment comment-icon" title="View Comments"></i> <?= (int)$post['Postcomments'] ?></span>
                                </div>
                                <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
                                    <!-- AI Moderation Button -->
                                    <span id="mod-zone-<?= $post['PostId'] ?>">
                                        <?php if (!empty($post['AiChecked'])): ?>
                                            <span class="mod-badge approved"><i class="fas fa-check-double"></i> AI Checked</span>
                                        <?php else: ?>
                                            <button class="ai-mod-btn" onclick="runModeration('<?= $post['PostId'] ?>')" id="ai-btn-<?= $post['PostId'] ?>">
                                                <i class="fas fa-robot"></i> AI Check
                                            </button>
                                        <?php endif; ?>
                                    </span>
                                    <!-- Public / Reject toggle -->
                                    <div class="post-stats" style="color: #059669; cursor: pointer; font-weight:600; padding:6px 12px; border-radius:8px; background:#D1FAE5; border:1px solid #10B981; transition:0.2s;" onclick="rejectPost('<?= $post['PostId'] ?>')" onmouseover="this.style.background='#FEE2E2'; this.style.borderColor='#EF4444'; this.style.color='#DC2626'; this.querySelector('i').className='fas fa-ban'; this.querySelector('.status-text').innerText='Reject Post';" onmouseout="this.style.background='#D1FAE5'; this.style.borderColor='#10B981'; this.style.color='#059669'; this.querySelector('i').className='fas fa-check-circle'; this.querySelector('.status-text').innerText='Public';">
                                        <span><i class="fas fa-check-circle"></i> <span class="status-text">Public</span></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: var(--text-muted); padding: 40px; background: #fff; border-radius: 12px;">
                            <i class="fas fa-folder-open" style="font-size: 32px; margin-bottom: 16px; color: #D1D5DB;"></i>
                            <p>No posts with media available yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Stories (Images) Grid -->
                <div class="reels-container" id="view-stories">
                    <?php if (count($allStoryImages) > 0): ?>
                        <?php foreach($allStoryImages as $post): 
                            $shopLogo = normalizeMediaUrl($post['ShopLogo']) ?? 'https://ui-avatars.com/api/?name='.urlencode($post['ShopName']).'&background=E5E7EB&color=374151';
                            
                            $mediaUrl = normalizeMediaUrl($post['PostPhoto']);
                            if (!$mediaUrl) continue;
                            
                            // Status badge
                            $status = strtoupper($post['PostStatus'] ?? 'ACTIVE');
                            $statusStyles = [
                                'ACTIVE'   => 'background:#D1FAE5; color:#065F46;',
                                'PENDING'  => 'background:#FEF3C7; color:#92400E;',
                                'REJECTED' => 'background:#FEE2E2; color:#991B1B;',
                            ];
                            $statusStyle = $statusStyles[$status] ?? 'background:#F3F4F6; color:#374151;';
                        ?>
                        <div class="reel-thumb-card" id="story-card-<?= $post['PostId'] ?>" style="<?= $status === 'REJECTED' ? 'opacity:0.6;' : '' ?>">
                            <img src="<?= htmlspecialchars($mediaUrl) ?>" style="width:100%;height:100%;object-fit:cover; display:block;">
                            
                            <!-- Overlay gradient -->
                            <div style="position:absolute; inset:0; background:linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 40%); pointer-events:none;"></div>
                            
                            <!-- Top Left Status -->
                            <span class="reel-status" style="<?= $statusStyle ?>; font-size:10px; font-weight:700; top:10px; left:10px; right:auto; z-index:15;"><?= $status ?></span>
                            
                            <!-- Center/Top-Right Actions -->
                            <div style="position:absolute; top:10px; right:10px; display:flex; flex-direction:column; gap:8px; z-index:20; pointer-events:auto;">
                                <?php if (empty($post['AiChecked'])): ?>
                                    <button onclick="checkStoryAI('<?= $post['PostId'] ?>', '<?= $post['ShopID'] ?>')" id="ai-story-btn-<?= $post['PostId'] ?>" style="background:rgba(99,102,241,0.95); border:none; color:#fff; border-radius:50%; width:32px; height:32px; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; transition:0.2s;" title="AI Check" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                                        <i class="fas fa-robot"></i>
                                    </button>
                                <?php else: ?>
                                    <div style="background:rgba(16,185,129,0.95); color:#fff; border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 8px rgba(0,0,0,0.3);" title="AI Checked">
                                        <i class="fas fa-check-double" style="font-size:12px;"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <button onclick="toggleStoryStatus('<?= $post['PostId'] ?>', '<?= $post['ShopID'] ?>')" id="status-story-btn-<?= $post['PostId'] ?>" style="background:rgba(0,0,0,0.6); backdrop-filter:blur(4px); border:1px solid rgba(255,255,255,0.2); color:#fff; border-radius:50%; width:32px; height:32px; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.3); display:flex; align-items:center; justify-content:center; transition:0.2s;" data-status="<?= $status === 'REJECTED' ? 'REJECTED' : 'PUBLIC' ?>" title="Toggle Post Status" onmouseover="this.style.transform='scale(1.1)'; this.style.background=this.getAttribute('data-status') === 'REJECTED' ? 'rgba(16,185,129,0.8)' : 'rgba(220,38,38,0.8)'" onmouseout="this.style.transform='scale(1)'; this.style.background='rgba(0,0,0,0.6)'">
                                    <i class="fas <?= $status === 'REJECTED' ? 'fa-undo' : 'fa-ban' ?>" id="status-story-icon-<?= $post['PostId'] ?>"></i>
                                </button>
                            </div>

                            <!-- Bottom Left Info -->
                            <div class="reel-thumb-info" style="justify-content: flex-start; align-items: center;">
                                <img src="<?= htmlspecialchars($shopLogo) ?>" class="reel-avatar" style="width:24px;height:24px;border:2px solid rgba(255,255,255,0.8);" onerror="this.style.display='none'">
                                <span style="font-size:11px;color:#fff;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; text-shadow:0 1px 3px rgba(0,0,0,0.8);"><?= htmlspecialchars($post['ShopName']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="text-align: center; color: var(--text-muted); padding: 40px; background: #fff; border-radius: 12px;">
                            <i class="fas fa-camera-retro" style="font-size: 32px; margin-bottom: 16px; color: #D1D5DB;"></i>
                            <p>No image stories available yet.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Reels Grid: thumbnails only -->
                <div class="reels-container" id="view-reels">
                    <?php
                    $reelsData = [];
                    $hasReels  = false;
                    // 1. POST VIDEOS
                    foreach($allPosts as $post):
                        $videoUrl = resolveVideoUrl($post);
                        if (!$videoUrl) continue;
                        $hasReels = true;
                        $reelId   = 'p' . $post['PostId'];
                        $shopLogo = normalizeMediaUrl($post['ShopLogo']) ?? 'https://ui-avatars.com/api/?name='.urlencode($post['ShopName']).'&background=333&color=fff';
                        $thumb    = !empty($post['BunnyS']) && !in_array($post['BunnyS'],['-','none','0','null']) ? $post['BunnyS'] : null;
                        $status   = strtoupper($post['PostStatus'] ?? 'ACTIVE');
                        $statusStyles = ['ACTIVE'=>'background:#D1FAE5;color:#065F46;','PENDING'=>'background:#FEF3C7;color:#92400E;','REJECTED'=>'background:#FEE2E2;color:#991B1B;'];
                        $reelStyle = $statusStyles[$status] ?? 'background:#F3F4F6;color:#374151;';
                        $caption  = (!empty($post['PostText']) && $post['PostText'] !== 'none' && $post['PostText'] !== 'NONE') ? $post['PostText'] : '';
                        $reelsData[] = json_encode(['id'=>$reelId,'src'=>$videoUrl,'thumb'=>$thumb??'','shop'=>$post['ShopName'],'logo'=>$shopLogo,'caption'=>$caption,'likes'=>(int)$post['PostLikes'],'comments'=>(int)$post['Postcomments'],'type'=>'post','postId'=>$post['PostId'],'status'=>$status,'statusStyle'=>$reelStyle,'aiChecked'=>!empty($post['AiChecked'])]);
                    ?>
                    <div class="reel-thumb-card" onclick="openReelPlayer('<?= $reelId ?>')">
                        <video src="<?= htmlspecialchars($videoUrl) ?>#t=0.1" preload="metadata" style="width:100%;height:100%;object-fit:cover;" muted playsinline loop onmouseover="this.play()" onmouseout="this.pause()"></video>
                        <div class="reel-thumb-overlay"><i class="fas fa-play reel-thumb-play"></i></div>
                        <span class="reel-status" style="<?= $reelStyle ?>"><?= $status ?></span>
                        <div class="reel-thumb-info">
                            <img src="<?= htmlspecialchars($shopLogo) ?>" class="reel-avatar" style="width:20px;height:20px;" onerror="this.style.display='none'">
                            <span style="font-size:10px;color:#fff;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($post['ShopName']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php
                    // 2. STORY VIDEOS
                    foreach($allStories as $story):
                        // BunnyV returns a 403 Forbidden without token auth. Use the raw StoryPhoto video URL.
                        $videoUrl = normalizeMediaUrl($story['StoryPhoto']);
                        $reelId   = 's' . $story['StotyID'];
                        $shopLogo = normalizeMediaUrl($story['ShopLogo']) ?? 'https://ui-avatars.com/api/?name='.urlencode($story['ShopName']).'&background=6366f1&color=fff';
                        $thumb    = !empty($story['BunnyS']) && !in_array($story['BunnyS'],['-','none','0','null']) ? $story['BunnyS'] : normalizeMediaUrl($story['StoryPhoto']);
                        $hasReels = true;
                        $reelsData[] = json_encode(['id'=>$reelId,'src'=>$videoUrl,'thumb'=>$thumb??'','shop'=>$story['ShopName'],'logo'=>$shopLogo,'caption'=>'','likes'=>0,'comments'=>0,'type'=>'story','postId'=>$story['StotyID'],'status'=>'STORY','statusStyle'=>'background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;','aiChecked'=>!empty($story['AiChecked'])]);
                    ?>
                    <div class="reel-thumb-card" onclick="openReelPlayer('<?= $reelId ?>')">
                        <video src="<?= htmlspecialchars($videoUrl) ?>#t=0.1" preload="metadata" style="width:100%;height:100%;object-fit:cover;" muted playsinline loop onmouseover="this.play()" onmouseout="this.pause()"></video>
                        <div class="reel-thumb-overlay"><i class="fas fa-play reel-thumb-play"></i></div>
                        <span class="reel-status" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-size:10px;">📖 Story</span>
                        <div class="reel-thumb-info">
                            <img src="<?= htmlspecialchars($shopLogo) ?>" class="reel-avatar" style="width:20px;height:20px;" onerror="this.style.display='none'">
                            <span style="font-size:10px;color:#fff;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($story['ShopName']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <?php if(!$hasReels): ?>
                        <div style="grid-column:1/-1; text-align:center; color:#9CA3AF; padding:60px 20px; background:#18181b; border-radius:16px;">
                            <i class="fas fa-film" style="font-size:36px; margin-bottom:12px; color:#374151;"></i>
                            <p>No video reels found.</p>
                        </div>
                    <?php endif; ?>
                </div>
                <script>const REELS_DATA = [<?= implode(',', $reelsData ?? []) ?>];</script>
                
                <!-- Boosts Feed (Modern Table) -->
                <div class="modern-table-container" id="view-boosts">
                    <?php if (count($allBoosts) > 0): ?>
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Shop Details</th>
                                <th>Ad Creative</th>
                                <th>Campaign Details</th>
                                <th>Price & Duration</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($allBoosts as $boost): 
                                $mediaUrl = normalizeMediaUrl($boost['BoostPhoto']);
                                $shopLogo = normalizeMediaUrl($boost['ShopLogo']) ?? 'https://ui-avatars.com/api/?name='.urlencode($boost['ShopName']).'&background=6366f1&color=fff';
                                
                                $status = strtoupper($boost['BoostStatus']);
                                $statusStyles = [
                                    'ACTIVE' => 'background:#D1FAE5; color:#065F46;', 
                                    'INREVIEW' => 'background:#FEF3C7; color:#92400E;', 
                                    'REJECTED' => 'background:#FEE2E2; color:#991B1B;'
                                ];
                                $statusStyle = $statusStyles[$status] ?? 'background:#F3F4F6; color:#374151;';
                                $isRejected = $status === 'REJECTED';
                            ?>
                            <tr id="boost-card-<?= $boost['BoostsByShopID'] ?>" style="<?= $isRejected ? 'opacity:0.6;' : '' ?>">
                                <td>
                                    <div class="mt-shop-info">
                                        <img src="<?= htmlspecialchars($shopLogo) ?>" class="mt-shop-logo" onerror="this.src='https://ui-avatars.com/api/?name=Shop'">
                                        <div class="mt-shop-name"><?= htmlspecialchars($boost['ShopName']) ?></div>
                                    </div>
                                </td>
                                <td>
                                    <img src="<?= htmlspecialchars($mediaUrl) ?>" class="mt-media-thumb" onclick="window.open(this.src, '_blank')">
                                </td>
                                <td>
                                    <div style="font-weight:600; color:#111827;"><?= htmlspecialchars($boost['BoostName']) ?></div>
                                    <div style="font-size:12px; color:#6B7280; margin-top:2px;">Requested Boost</div>
                                </td>
                                <td>
                                    <div style="font-weight:700; color:#D97706;"><?= number_format((float)$boost['BoostPrice'], 2) ?> MAD</div>
                                    <div style="font-size:12px; color:#6B7280; margin-top:2px;"><i class="fas fa-clock" style="margin-right:4px;"></i><?= htmlspecialchars($boost['BoostTimeDuration']) ?> Days</div>
                                </td>
                                <td>
                                    <span class="mt-badge reel-status-badge" style="<?= $statusStyle ?>">
                                        <i class="fas <?= $status === 'ACTIVE' ? 'fa-check' : ($status === 'REJECTED' ? 'fa-ban' : 'fa-hourglass-half') ?>"></i> 
                                        <?= $status === 'INREVIEW' ? 'PENDING' : $status ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="mt-actions">
                                        <button onclick="toggleBoostStatus('<?= $boost['BoostsByShopID'] ?>')" id="status-boost-btn-<?= $boost['BoostsByShopID'] ?>" class="mt-btn <?= $isRejected ? 'approve' : 'reject' ?>" data-status="<?= $isRejected ? 'REJECTED' : 'ACTIVE' ?>" title="<?= $isRejected ? 'Restore Boost' : 'Reject Boost' ?>">
                                            <i class="fas <?= $isRejected ? 'fa-undo' : 'fa-ban' ?>" id="status-boost-icon-<?= $boost['BoostsByShopID'] ?>"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <div style="text-align:center; color:#9CA3AF; padding:80px 20px;">
                            <i class="fas fa-bullhorn" style="font-size:42px; margin-bottom:16px; color:#D1D5DB;"></i>
                            <p style="font-size:16px; color:#4B5563;">No boost requests have been submitted yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Inject post IDs from PHP
    const ALL_POST_IDS = <?= json_encode($allPostIds ?? []) ?>;
    let aiCheckedCount = <?= (int)$totalAiChecked ?>;
    
    function incrementAiCheck() {
        aiCheckedCount++;
        const el = document.getElementById('statAiNum');
        if (el) el.innerText = aiCheckedCount;
    }

    // Count posts vs reels
    const postCards   = document.querySelectorAll('#view-posts .post-card');
    const storyCards  = document.querySelectorAll('#view-stories .reel-thumb-card');
    const reelCards   = document.querySelectorAll('#view-reels .reel-thumb-card');
    document.getElementById('count-posts').textContent = postCards.length;
    document.getElementById('count-stories').textContent = storyCards.length;
    document.getElementById('count-reels').textContent = reelCards.length;
    document.getElementById('bulkCount').textContent   = ALL_POST_IDS.length;

    // View switcher
    function switchView(view) {
        const postsEl   = document.getElementById('view-posts');
        const storiesEl = document.getElementById('view-stories');
        const reelsEl   = document.getElementById('view-reels');
        const boostsEl  = document.getElementById('view-boosts');
        
        document.getElementById('tab-posts').classList.toggle('active', view === 'posts');
        document.getElementById('tab-stories').classList.toggle('active', view === 'stories');
        document.getElementById('tab-reels').classList.toggle('active', view === 'reels');
        document.getElementById('tab-boosts').classList.toggle('active', view === 'boosts');

        postsEl.style.display = view === 'posts' ? 'flex' : 'none';
        
        if (view === 'stories') {
            storiesEl.classList.add('visible');
        } else {
            storiesEl.classList.remove('visible');
        }
        
        if (view === 'reels') {
            reelsEl.classList.add('visible');
        } else {
            reelsEl.classList.remove('visible');
        }
        
        if (view === 'boosts') {
            if (boostsEl) boostsEl.classList.add('visible');
        } else {
            if (boostsEl) boostsEl.classList.remove('visible');
        }
    }
    </script>

    <!-- Comments Modal -->
    <div id="commentsModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center; backdrop-filter:blur(4px);">
        <div style="background:#fff; width:100%; max-width:500px; height:80vh; max-height:600px; border-radius:16px; display:flex; flex-direction:column; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); margin: 20px;">
            <!-- Header -->
            <div style="padding:16px 20px; border-bottom:1px solid #E5E7EB; display:flex; align-items:center; justify-content:space-between;">
                <h3 style="font-size:18px; font-weight:700; color:#111827;">Comments</h3>
                <div style="cursor:pointer; width:32px; height:32px; border-radius:50%; background:#F3F4F6; display:flex; align-items:center; justify-content:center; transition:0.2s;" onclick="closeComments()" onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
                    <i class="fas fa-times" style="font-size:16px; color:#6B7280;"></i>
                </div>
            </div>
            <!-- Body -->
            <div id="commentsBody" style="flex:1; overflow-y:auto; padding:20px; background:#fff;">
                Loading comments...
            </div>
        </div>
    </div>

    <!-- AI Moderation Result Modal -->
    <div class="mod-modal-overlay" id="modModal">
        <div class="mod-modal">
            <div class="mod-modal-head">
                <h3><i class="fas fa-robot" style="margin-right:8px;"></i>AI Content Moderation</h3>
                <p>Powered by GPT-4o-mini · Analysis Result</p>
            </div>
            <div class="mod-modal-body">
                <div class="mod-decision-row" id="modDecisionRow">
                    <div class="mod-decision-icon" id="modIcon"></div>
                    <div>
                        <div class="mod-decision-label" id="modDecisionLabel"></div>
                        <div class="mod-confidence" id="modConfidence"></div>
                    </div>
                </div>
                <div class="mod-reason" id="modReason"></div>
                <div class="mod-cats-label">Category Flags</div>
                <div class="mod-cats" id="modCats"></div>
                <div class="mod-actions">
                    <button class="mod-apply-btn confirm" id="modApplyBtn" onclick="applyModDecision()"><i class="fas fa-check" style="margin-right:6px;"></i>Apply Decision to DB</button>
                    <button class="mod-apply-btn cancel" onclick="closeModModal()">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function openComments(postId) {
        document.getElementById('commentsModal').style.display = 'flex';
        document.getElementById('commentsBody').innerHTML = '<div style="text-align:center; color:#6B7280; margin-top:40px;"><i class="fas fa-spinner fa-spin" style="font-size:24px;"></i><p style="margin-top:10px;">Loading comments...</p></div>';
        
        fetch('get_post_comments.php?PostID=' + postId)
            .then(res => res.text())
            .then(html => {
                document.getElementById('commentsBody').innerHTML = html;
            })
            .catch(err => {
                document.getElementById('commentsBody').innerHTML = '<div style="color:red; text-align:center; margin-top:40px;">Failed to load comments.</div>';
            });
    }

    function closeComments() {
        document.getElementById('commentsModal').style.display = 'none';
        document.getElementById('commentsBody').innerHTML = ''; // clear memory
    }
    
    // Close modal when clicking outside
    document.getElementById('commentsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeComments();
        }
    });

    function rejectPost(postId) {
        if(!confirm("Are you sure you want to REJECT this post? It will notify the store and hide the post from the platform.")) return;
        
        let btn = document.querySelector('#post-card-' + postId + ' .post-stats:last-child');
        btn.style.opacity = '0.5';
        btn.style.pointerEvents = 'none';
        
        fetch('ChangePostStatus.php?PostId=' + postId + '&PostStatus=REJECTED')
            .then(res => res.text())
            .then(text => {
                if(text.includes('Done') || text.includes(' Done ')) {
                    let card = document.getElementById('post-card-' + postId);
                    card.style.transition = 'opacity 0.3s';
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                } else {
                    alert("Operation failed: " + text);
                    btn.style.opacity = '1';
                    btn.style.pointerEvents = 'all';
                }
            })
            .catch(err => {
                alert("Network error.");
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'all';
            });
    }

    // ── AI MODERATION ────────────────────────────────────────────
    let currentModPostId = null;
    let currentModDecision = null;

    function runModeration(postId) {
        const btn = document.getElementById('ai-btn-' + postId);
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
        currentModPostId = postId;

        const fd = new FormData();
        fd.append('PostId', postId);

        fetch('moderate_post.php', { method:'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                if (!data.success) {
                    btn.innerHTML = '<i class="fas fa-robot"></i> AI Check';
                    alert('AI Error: ' + (data.error || 'Unknown error'));
                    return;
                }
                
                incrementAiCheck();

                currentModDecision = data;

                // Swap button to badge
                const zone = document.getElementById('mod-zone-' + postId);
                const dec = data.decision.toLowerCase();
                const icons = { approved: '✅', rejected: '🚫', pending: '⚠️' };
                zone.innerHTML = `<span class="mod-badge ${dec}" onclick="showModResult('${postId}')">${icons[dec] || '🤖'} ${data.decision} (${data.confidence}%)</span>`;

                showModResult(postId);
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-robot"></i> AI Check';
                alert('Network error.');
            });
    }

    function showModResult(postId) {
        if (!currentModDecision || currentModDecision.postId != postId) return;
        const d = currentModDecision;
        const dec = d.decision.toLowerCase();

        // Decision row styling
        const row = document.getElementById('modDecisionRow');
        row.className = 'mod-decision-row ' + dec;
        document.getElementById('modIcon').textContent = dec === 'approved' ? '✅' : dec === 'rejected' ? '🚫' : '⚠️';
        document.getElementById('modDecisionLabel').textContent = d.decision;
        document.getElementById('modDecisionLabel').style.color = dec === 'approved' ? '#065F46' : dec === 'rejected' ? '#991B1B' : '#92400E';
        document.getElementById('modConfidence').textContent = 'Confidence: ' + d.confidence + '%';
        document.getElementById('modReason').textContent = d.reason || 'No reason provided.';

        // Category flags
        const cats = d.categories || {};
        const catLabels = { sexual:'Sexual', violence:'Violence', illegal:'Illegal', hate:'Hate Speech', political:'Political', scam:'Scam' };
        let catsHtml = '';
        for (const [k, v] of Object.entries(cats)) {
            catsHtml += `<span class="mod-cat-pill ${v ? 'flagged' : 'clean'}">${v ? '🔴' : '🟢'} ${catLabels[k] || k}</span>`;
        }
        document.getElementById('modCats').innerHTML = catsHtml;

        // Apply button label
        const applyBtn = document.getElementById('modApplyBtn');
        const dbStatus = dec === 'approved' ? 'ACTIVE' : dec === 'pending' ? 'PENDING' : 'REJECTED';
        applyBtn.innerHTML = `<i class="fas fa-check" style="margin-right:6px;"></i>Apply: Set to ${dbStatus}`;
        applyBtn.dataset.postId = postId;

        document.getElementById('modModal').style.display = 'flex';
    }

    function applyModDecision() {
        if (!currentModDecision) return;
        const postId = currentModDecision.postId;
        const btn = document.getElementById('modApplyBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';

        const fd = new FormData();
        fd.append('PostId', postId);
        fd.append('apply', '1');

        fetch('moderate_post.php', { method:'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                closeModModal();
                if (data.success) {
                    // If rejected, remove card
                    if (data.dbStatus === 'REJECTED') {
                        const card = document.getElementById('post-card-' + postId);
                        card.style.transition = 'opacity 0.3s';
                        card.style.opacity = '0';
                        setTimeout(() => card.style.display='none', 300);
                    }
                } else {
                    alert('Failed to apply: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Apply Decision to DB';
                alert('Network error applying decision.');
            });
    }

    function closeModModal() {
        document.getElementById('modModal').style.display = 'none';
    }
    document.getElementById('modModal').addEventListener('click', e => {
        if (e.target === document.getElementById('modModal')) closeModModal();
    });

    // ── BULK AI CHECK ──────────────────────────────────────────────────────────
    async function runBulkAI(isAuto = false) {
        const remaining = ALL_POST_IDS.filter(id => {
            // Only check those whose mod-zone still has the original button (not yet AI-checked)
            const zone = document.getElementById('mod-zone-' + id);
            return zone && zone.querySelector('.ai-mod-btn');
        });

        if (remaining.length === 0) {
            if (!isAuto) alert('All posts in this feed have already been AI checked!');
            return;
        }

        if (!isAuto) {
            if (!confirm(`Run AI moderation on ${remaining.length} unchecked post(s)? This may take a few moments.`)) return;
        }

        const btn = document.getElementById('bulkAiBtn');
        btn.disabled = true;
        btn.querySelector('span:first-of-type').textContent = 'Checking...';

        const progress = document.getElementById('bulkProgress');
        const bar      = document.getElementById('bulkProgressBar');
        progress.style.display = 'block';

        let done = 0;
        for (const postId of remaining) {
            const zone = document.getElementById('mod-zone-' + postId);
            if (!zone || !zone.querySelector('.ai-mod-btn')) {
                done++; continue; // skip already checked
            }

            // Mark as scanning
            const aiBtn = document.getElementById('ai-btn-' + postId);
            if (aiBtn) {
                aiBtn.disabled = true;
                aiBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Scanning...';
            }

            try {
                const fd = new FormData();
                fd.append('PostId', postId);
                fd.append('apply', '1'); // Auto-apply decision to DB

                const res  = await fetch('moderate_post.php', { method:'POST', body: fd });
                const data = await res.json();

                if (data.success) {
                    aiCheckedCount++;
                    document.getElementById('statAiNum').textContent = aiCheckedCount;

                    const dec   = data.decision.toLowerCase();
                    const icons = { approved:'✅', rejected:'🚫', pending:'⚠️' };
                    zone.innerHTML = `<span class="mod-badge ${dec}" onclick="currentModDecision=${JSON.stringify(data).replace(/'/g,"&apos;")};showModResult(${postId})">${icons[dec]||'🤖'} ${data.decision} (${data.confidence}%)</span>`;

                    // Auto-hide rejected cards
                    if (data.dbStatus === 'REJECTED') {
                        const card = document.getElementById('post-card-' + postId);
                        if (card) {
                            card.style.transition = 'opacity 0.4s';
                            card.style.opacity = '0';
                            setTimeout(() => card.style.display = 'none', 400);
                        }
                    }
                } else {
                    if (aiBtn) { aiBtn.disabled = false; aiBtn.innerHTML = '<i class="fas fa-robot"></i> AI Check'; }
                }
            } catch(e) {
                if (aiBtn) { aiBtn.disabled = false; aiBtn.innerHTML = '<i class="fas fa-robot"></i> AI Check'; }
            }

            done++;
            const pct = Math.round((done / remaining.length) * 100);
            bar.style.width = pct + '%';
            document.getElementById('bulkCount').textContent = (remaining.length - done) + ' left';
        }

        // Done
        btn.disabled = false;
        btn.querySelector('span:first-of-type').textContent = 'AI Check Unchecked Posts';
        document.getElementById('bulkCount').textContent = '✓ Done';
        setTimeout(() => {
            progress.style.display = 'none';
            bar.style.width = '0%';
            document.getElementById('bulkCount').textContent = '0';
        }, 3000);
    }
    </script>

    <!-- Reel Full-Screen Player Modal -->
    <button id="reelCloseBtn" onclick="closeReelPlayer()"><i class="fas fa-times"></i></button>
    <div id="reelPlayerModal">
        <div class="reel-player-wrap" id="reelPlayerWrap"></div>
    </div>

    <script>
    let _reelObserver = null;

    function openReelPlayer(startId) {
        if (typeof REELS_DATA === 'undefined' || !REELS_DATA.length) return;
        const modal = document.getElementById('reelPlayerModal');
        const wrap  = document.getElementById('reelPlayerWrap');
        const closeBtn = document.getElementById('reelCloseBtn');

        // Build all reel items
        wrap.innerHTML = '';
        REELS_DATA.forEach((r, idx) => {
            const item = document.createElement('div');
            item.className = 'reel-player-item';
            item.id = 'rpi-' + r.id;
            item.innerHTML = `
                <video id="rpv-${r.id}" src="${r.src}" ${r.thumb ? 'poster="'+r.thumb+'"' : ''}
                    loop playsinline preload="metadata" muted
                    style="width:100%;height:100%;object-fit:contain;background:#000;cursor:pointer;" onclick="toggleRPV('${r.id}')"></video>
                
                <!-- Floating Right Controls -->
                <div style="position:absolute; right:15px; bottom:50%; transform:translateY(50%); display:flex; flex-direction:column; gap:25px; z-index:10;">
                    
                    <!-- Sound Toggle -->
                    <button style="background:transparent;border:none;color:#fff;display:flex;flex-direction:column;align-items:center;gap:6px;cursor:pointer;" 
                            onclick="event.stopPropagation(); toggleMute('${r.id}')" id="mute-btn-${r.id}">
                        <div style="width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.2);transition:0.2s;">
                             <i class="fas fa-volume-mute" style="font-size:20px;" id="mute-icon-${r.id}"></i>
                        </div>
                        <span style="font-size:12px;font-weight:600;text-shadow:0 1px 3px rgba(0,0,0,0.8);" id="mute-text-${r.id}">Sound Off</span>
                    </button>
                    
                    <!-- Status Public/Reject Toggle -->
                    ${r.postId ? `
                    <button style="background:transparent;border:none;color:#fff;display:flex;flex-direction:column;align-items:center;gap:6px;cursor:pointer;" 
                            onclick="event.stopPropagation(); toggleReelStatus('${r.postId}')" id="status-btn-${r.postId}">
                        <div style="width:50px;height:50px;border-radius:50%;background:rgba(16,185,129,0.25);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px);border:1px solid rgba(16,185,129,0.5);transition:0.2s;" id="status-circle-${r.postId}">
                             <i class="fas fa-check" style="font-size:20px;color:#34D399;" id="status-icon-${r.postId}"></i>
                        </div>
                        <span style="font-size:12px;font-weight:600;text-shadow:0 1px 3px rgba(0,0,0,0.8);color:#34D399;" id="status-text-${r.postId}">Public</span>
                    </button>
                    
                    <!-- AI Check Button / Badge -->
                    ${r.postId && !r.aiChecked ? `
                    <button style="background:transparent;border:none;color:#fff;display:flex;flex-direction:column;align-items:center;gap:6px;cursor:pointer;" 
                            onclick="event.stopPropagation(); checkReelAI('${r.postId}', '${r.type}')" id="ai-reel-btn-${r.postId}">
                        <div style="width:50px;height:50px;border-radius:50%;background:linear-gradient(135deg, rgba(99,102,241,0.5), rgba(139,92,246,0.5));display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px);border:1px solid rgba(139,92,246,0.5);transition:0.2s;">
                             <i class="fas fa-robot" style="font-size:20px;color:#fff;"></i>
                        </div>
                        <span style="font-size:12px;font-weight:600;text-shadow:0 1px 3px rgba(0,0,0,0.8);color:#fff;">AI Check</span>
                    </button>
                    ` : r.postId && r.aiChecked ? `
                    <div style="background:transparent;color:#fff;display:flex;flex-direction:column;align-items:center;gap:6px;">
                        <div style="width:50px;height:50px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;backdrop-filter:blur(10px);border:1px solid rgba(255,255,255,0.3);">
                             <i class="fas fa-check-double" style="font-size:20px;color:#fff;"></i>
                        </div>
                        <span style="font-size:12px;font-weight:600;text-shadow:0 1px 3px rgba(0,0,0,0.8);color:#fff;">Checked</span>
                    </div>
                    ` : ''}
                </div>`;
            wrap.appendChild(item);
        });

        modal.classList.add('open');
        closeBtn.classList.add('open');
        document.body.style.overflow = 'hidden'; // prevent page scroll

        // Scroll to start item
        setTimeout(() => {
            const target = document.getElementById('rpi-' + startId);
            if (target) target.scrollIntoView({ behavior: 'instant', block: 'start' });
            setupReelObserver();
        }, 50);
    }

    function setupReelObserver() {
        if (_reelObserver) _reelObserver.disconnect();
        _reelObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const vid = entry.target.querySelector('video');
                if (!vid) return;
                if (entry.isIntersecting && entry.intersectionRatio > 0.7) {
                    vid.play().catch(() => {});
                } else {
                    vid.pause();
                }
            });
        }, { root: document.getElementById('reelPlayerModal'), threshold: 0.7 });

        document.querySelectorAll('.reel-player-item').forEach(item => {
            _reelObserver.observe(item);
        });
    }

    function toggleRPV(id) {
        const v = document.getElementById('rpv-' + id);
        if (!v) return;
        if (v.paused) v.play(); else v.pause();
    }

    function toggleMute(id) {
        const v = document.getElementById('rpv-' + id);
        const icon = document.getElementById('mute-icon-' + id);
        const text = document.getElementById('mute-text-' + id);
        if (!v) return;
        
        v.muted = !v.muted;
        if (v.muted) {
            icon.className = 'fas fa-volume-mute';
            text.innerText = 'Sound Off';
        } else {
            icon.className = 'fas fa-volume-up';
            text.innerText = 'Sound On';
        }
    }

    function toggleReelStatus(postId) {
        const circle = document.getElementById('status-circle-' + postId);
        const icon = document.getElementById('status-icon-' + postId);
        const text = document.getElementById('status-text-' + postId);
        const isPublic = (text.innerText === 'Public');

        // Fire status update to backend without blocking UI
        fetch('update_post_status.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'PostId=' + postId + '&Status=' + (isPublic ? 'REJECTED' : 'ACTIVE')
        }).catch(err => console.error(err));
        
        if (isPublic) {
            text.innerText = 'Rejected';
            text.style.color = '#EF4444';
            icon.className = 'fas fa-ban';
            icon.style.color = '#EF4444';
            circle.style.background = 'rgba(239,68,68,0.25)';
            circle.style.borderColor = 'rgba(239,68,68,0.5)';
        } else {
            text.innerText = 'Public';
            text.style.color = '#34D399';
            icon.className = 'fas fa-check';
            icon.style.color = '#34D399';
            circle.style.background = 'rgba(16,185,129,0.25)';
            circle.style.borderColor = 'rgba(16,185,129,0.5)';
        }
    }

    function checkReelAI(postId, type) {
        const btn = document.getElementById('ai-reel-btn-' + postId);
        if (!btn) return;
        
        // Use existing DOM element logic inside showModResult
        currentModDecision = null;
        
        // Show scanning state in modal
        btn.querySelector('i').className = 'fas fa-spinner fa-spin';
        btn.querySelector('span').innerText = 'Scanning...';

        fetch('moderate_post.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'PostId=' + postId + '&Type=' + type
        })
        .then(r => r.json())
        .then(data => {
            btn.querySelector('i').className = 'fas fa-robot';
            btn.querySelector('span').innerText = 'AI Check';

            if (!data.success) {
                alert('AI Error: ' + (data.error || 'Unknown error'));
                return;
            }
            
            incrementAiCheck();

            // Sync visual status automatically based on AI decision without requiring click flow
            if (data.decision !== 'PENDING') {
                const isCurrentlyPublic = document.getElementById('status-text-' + postId).innerText === 'Public';
                const aiWantsPublic = data.decision === 'APPROVED';
                
                if (isCurrentlyPublic !== aiWantsPublic) {
                     toggleReelStatus(postId); // flip it to match
                }
            }

            // Still show the full AI result popup over the video
            currentModDecision = data;
            showModResult(postId);
        })
        .catch(err => {
            btn.querySelector('i').className = 'fas fa-robot';
            btn.querySelector('span').innerText = 'AI Check';
            alert('Network error.');
        });
    }

    // ── Dedicated Handlers for the New Image Stories Grid ──
    function toggleStoryStatus(postId, shopId) {
        if(!confirm("هل أنت متأكد من تغيير حالة هذه القصة؟ سيتم إخفاؤها من المنصة.")) return;
        
        const btn = document.getElementById('status-story-btn-' + postId);
        const icon = document.getElementById('status-story-icon-' + postId);
        if (!btn) return;
        
        const isCurrentlyRejected = btn.getAttribute('data-status') === 'REJECTED';
        const newStatus = isCurrentlyRejected ? 'ACTIVE' : 'REJECTED';

        fetch('ChangeStoryStatus.php?PostId=' + postId + '&StoryStatus=' + newStatus + '&ShopID=' + shopId)
            .then(res => res.text())
            .then(text => {
                if(text.includes('Done') || text.includes(' Done ')) {
                    // Optimistic UI update on success
                    btn.setAttribute('data-status', newStatus === 'ACTIVE' ? 'PUBLIC' : 'REJECTED');
                    if (newStatus === 'REJECTED') {
                        icon.className = 'fas fa-undo';
                        document.getElementById('story-card-' + postId).style.opacity = '0.6';
                    } else {
                        icon.className = 'fas fa-ban';
                        document.getElementById('story-card-' + postId).style.opacity = '1';
                    }
                } else {
                    alert("فشل العملية: " + text);
                }
            })
            .catch(err => {
                alert("حدث خطأ في الشبكة");
            });
    }

    function checkStoryAI(postId, shopId) {
        const btn = document.getElementById('ai-story-btn-' + postId);
        if (!btn) return;
        
        currentModDecision = null;
        btn.querySelector('i').className = 'fas fa-spinner fa-spin';

        fetch('moderate_post.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'PostId=' + postId + '&Type=story'
        })
        .then(r => r.json())
        .then(data => {
            btn.querySelector('i').className = 'fas fa-robot';

            if (!data.success) {
                alert('AI Error: ' + (data.error || 'Unknown error'));
                return;
            }
            incrementAiCheck();

            // Transform button to badge dynamically
            const parent = btn.parentElement;
            const newBadge = document.createElement('div');
            newBadge.style = "background:rgba(16,185,129,0.95); color:#fff; border-radius:50%; width:32px; height:32px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 8px rgba(0,0,0,0.3);";
            newBadge.title = "AI Checked";
            newBadge.innerHTML = '<i class="fas fa-check-double" style="font-size:12px;"></i>';
            parent.insertBefore(newBadge, btn);
            parent.removeChild(btn);

            if (data.decision !== 'PENDING') {
                const btnStatus = document.getElementById('status-story-btn-' + postId);
                const isCurrentlyRejected = btnStatus.getAttribute('data-status') === 'REJECTED';
                const aiWantsRejected = data.decision === 'REJECTED';
                // Trigger actual backend rejection if AI wants it rejected
                if (isCurrentlyRejected !== aiWantsRejected) toggleStoryStatus(postId, shopId);
            }

            currentModDecision = data;
            showModResult(postId);
        })
        .catch(err => {
            btn.querySelector('i').className = 'fas fa-robot';
            alert('Network error.');
        });
    }

    function toggleBoostStatus(boostsByShopId) {
        if(!confirm("هل أنت متأكد من تغيير حالة هذا الإعلان الممول؟")) return;
        
        const btn = document.getElementById('status-boost-btn-' + boostsByShopId);
        const icon = document.getElementById('status-boost-icon-' + boostsByShopId);
        if (!btn) return;
        
        const isCurrentlyRejected = btn.getAttribute('data-status') === 'REJECTED';
        const newStatus = isCurrentlyRejected ? 'Active' : 'Rejected'; // Assuming 'Active' and 'Rejected' based on PHP script

        // Note: ChangeBoostStatues.php performs a redirect (window.location.href). 
        // Calling it via fetch will likely follow the redirect or return HTML.
        // We'll optimistically update the UI, but it's important to note the backend might redirect.
        fetch('ChangeBoostStatues.php?BoostsByShopID=' + boostsByShopId + '&BoostStatus=' + newStatus)
            .then(res => res.text())
            .then(text => {
                // ChangeBoostStatues.php injects scripts for redirection on success/failure,
                // so we consider any response back a successful trigger for our optimistic UI update here.
                btn.setAttribute('data-status', newStatus === 'Active' ? 'ACTIVE' : 'REJECTED');
                if (newStatus === 'Rejected') {
                    icon.className = 'fas fa-undo';
                    document.getElementById('boost-card-' + boostsByShopId).style.opacity = '0.6';
                    const card = document.getElementById('boost-card-' + boostsByShopId);
                    const badge = card.querySelector('.reel-status-badge');
                    if (badge) {
                        badge.innerHTML = '<i class="fas fa-ban"></i> REJECTED';
                        badge.style.background = '#FEE2E2';
                        badge.style.color = '#991B1B';
                    }
                } else {
                    icon.className = 'fas fa-ban';
                    document.getElementById('boost-card-' + boostsByShopId).style.opacity = '1';
                    const card = document.getElementById('boost-card-' + boostsByShopId);
                    const badge = card.querySelector('.reel-status-badge');
                    if (badge) {
                        badge.innerHTML = '<i class="fas fa-check"></i> ACTIVE';
                        badge.style.background = '#D1FAE5';
                        badge.style.color = '#065F46';
                    }
                }
            })
            .catch(err => {
                console.error(err);
                alert("حدث خطأ في الشبكة");
            });
    }

    function closeReelPlayer() {
        const modal = document.getElementById('reelPlayerModal');
        const wrap  = document.getElementById('reelPlayerWrap');
        const closeBtn = document.getElementById('reelCloseBtn');
        // Pause all
        wrap.querySelectorAll('video').forEach(v => { v.pause(); v.src = ''; });
        if (_reelObserver) { _reelObserver.disconnect(); _reelObserver = null; }
        wrap.innerHTML = '';
        modal.classList.remove('open');
        closeBtn.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Close on Escape key
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeReelPlayer(); });

    function escHtml(str) {
        if (!str) return '';
        return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
    
    // Automatically trigger Bulk AI explicitly on unchecked posts moments after UI loads
    setTimeout(() => {
        runBulkAI(true);
    }, 2500);

    // ── SMART BACKGROUND AI WORKER HEARTBEAT ──────────────────────────────
    // As long as the administrator keeps this dashboard open, the browser acts as the background AI worker!
    // It silently polls the database every 5 seconds for any new mobile uploads and forces the AI check.
    setInterval(() => {
        fetch('tick_ai_worker.php')
          .then(res => res.json())
          .then(data => {
              if (data.success && data.checkedCount > 0) {
                  // A new post was automatically caught and checked by the background AI!
                  // We silently increment the UI stats so the admin sees the AI is working!
                  for (let i = 0; i < data.checkedCount; i++) incrementAiCheck();
              }
          })
          .catch(e => { /* Silently ignore network stutters */ });
    }, 5000);
    </script>
</body>
</html>
