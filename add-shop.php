<?php
require "conn.php";
mysqli_set_charset($con, "utf8mb4");

$cats_query   = mysqli_query($con, "SELECT * FROM Categories ORDER BY EnglishCategory ASC");
$cities_query = mysqli_query($con, "SELECT DeliveryZoneID, CityName FROM DeliveryZone ORDER BY CityName ASC");
$cats   = [];
$cities = [];
while($r = mysqli_fetch_assoc($cats_query))   $cats[]   = $r;
while($r = mysqli_fetch_assoc($cities_query)) $cities[] = $r;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Shop | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-master:  #F3F4F6;
            --bg-surface: #FFFFFF;
            --border:     #E5E7EB;
            --border-md:  #D1D5DB;
            --text-strong:#111827;
            --text-base:  #374151;
            --text-muted: #6B7280;
            --green-text: #059669; --green-bg: #ECFDF5;
            --shadow-sm:  0 1px 2px rgba(0,0,0,0.05);
            --shadow-md:  0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-xl:  0 20px 40px -10px rgba(0,0,0,0.08);
        }

        * { margin:0; padding:0; box-sizing:border-box; font-family:'Inter',-apple-system,sans-serif; -webkit-font-smoothing:antialiased; }
        body { background:var(--bg-master); color:var(--text-base); display:flex; height:100vh; overflow:hidden; }
        .layout-wrapper { display:flex; width:100%; height:100%; }

        main.content-area { flex:1; overflow-y:auto; display:flex; flex-direction:column; }
        main.content-area::-webkit-scrollbar { width:6px; }
        main.content-area::-webkit-scrollbar-thumb { background:rgba(0,0,0,0.1); border-radius:10px; }

        /* Header */
        .header-bar {
            position:sticky; top:0; z-index:20;
            background:rgba(255,255,255,0.9); backdrop-filter:blur(16px);
            border-bottom:1px solid var(--border);
            padding:20px 40px;
            display:flex; align-items:center; gap:16px;
        }
        .back-btn {
            display:inline-flex; align-items:center; justify-content:center;
            width:34px; height:34px; border-radius:8px;
            border:1px solid var(--border); background:var(--bg-surface);
            color:var(--text-muted); text-decoration:none;
            box-shadow:var(--shadow-sm); transition:0.2s; flex-shrink:0;
        }
        .back-btn:hover { border-color:var(--text-strong); color:var(--text-strong); }
        .header-bar h1 { font-size:18px; font-weight:700; color:var(--text-strong); }
        .header-bar p  { font-size:13px; color:var(--text-muted); font-weight:500; margin-top:2px; }

        /* Page body */
        .page-body {
            padding:40px;
            display:grid; grid-template-columns:1fr 340px; gap:28px;
            align-items:start; max-width:1100px; margin:0 auto; width:100%;
        }

        /* Stepper */
        .stepper { display:flex; gap:6px; margin-bottom:8px; }
        .step-bar { flex:1; height:3px; border-radius:10px; background:var(--border); transition:0.4s; }
        .step-bar.done { background:var(--text-strong); }

        /* Form card */
        .form-card { background:var(--bg-surface); border:1px solid var(--border); border-radius:16px; box-shadow:var(--shadow-sm); overflow:hidden; }
        .form-card-head { padding:18px 24px; border-bottom:1px solid var(--border); background:#F9FAFB; }
        .form-card-head h2 { font-size:15px; font-weight:700; color:var(--text-strong); display:flex; align-items:center; gap:8px; }
        .form-card-head h2 i { font-size:13px; color:var(--text-muted); }
        .form-card-head p { font-size:12px; color:var(--text-muted); font-weight:500; margin-top:3px; }

        /* Phases */
        .form-phase { display:none; padding:28px 24px; flex-direction:column; gap:20px; animation:slideUp 0.3s ease; }
        .form-phase.active { display:flex; }
        @keyframes slideUp { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        /* Fields */
        .field-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
        .inp-group { display:flex; flex-direction:column; gap:6px; }
        .inp-group label { font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .inp-field { padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-size:14px; font-weight:500; color:var(--text-strong); background:var(--bg-surface); outline:none; transition:0.2s; box-shadow:var(--shadow-sm); width:100%; }
        .inp-field:focus { border-color:var(--border-md); box-shadow:0 0 0 3px rgba(17,24,39,0.06); }
        select.inp-field { cursor:pointer; }

        /* Upload areas */
        .upload-row { display:flex; gap:16px; }

        .upload-square {
            width:120px; height:120px; flex-shrink:0;
            border:2px dashed var(--border-md); border-radius:12px;
            background:#F9FAFB; position:relative; cursor:pointer; overflow:hidden;
            display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;
            transition:0.2s;
        }
        .upload-square:hover { border-color:var(--text-strong); background:#F3F4F6; }
        .upload-square .up-icon { font-size:20px; color:var(--text-muted); }
        .upload-square .up-hint { font-size:10px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .upload-square img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:none; }
        .upload-square input { position:absolute; inset:0; opacity:0; cursor:pointer; }

        .upload-banner {
            flex:1; height:120px;
            border:2px dashed var(--border-md); border-radius:12px;
            background:#F9FAFB; position:relative; cursor:pointer; overflow:hidden;
            display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;
            transition:0.2s;
        }
        .upload-banner:hover { border-color:var(--text-strong); background:#F3F4F6; }
        .upload-banner .up-icon { font-size:20px; color:var(--text-muted); }
        .upload-banner .up-hint { font-size:10px; font-weight:600; color:var(--text-muted); text-transform:uppercase; }
        .upload-banner img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; display:none; }
        .upload-banner input { position:absolute; inset:0; opacity:0; cursor:pointer; }

        /* Actions */
        .phase-actions { display:flex; gap:10px; padding:16px 24px; border-top:1px solid var(--border); background:#F9FAFB; }
        .btn-next { flex:1; display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 20px; border-radius:8px; background:var(--text-strong); color:#fff; font-size:14px; font-weight:600; border:none; cursor:pointer; transition:0.2s; box-shadow:var(--shadow-sm); }
        .btn-next:hover { background:#1F2937; box-shadow:var(--shadow-md); }
        .btn-back { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 18px; border-radius:8px; border:1px solid var(--border); background:var(--bg-surface); color:var(--text-muted); font-size:14px; font-weight:600; cursor:pointer; transition:0.2s; }
        .btn-back:hover { background:#F3F4F6; }

        /* Live Preview Card (right column) */
        .preview-panel {
            position:sticky; top:40px;
            background:var(--bg-surface); border:1px solid var(--border);
            border-radius:16px; box-shadow:var(--shadow-sm); overflow:hidden;
        }
        .preview-head { padding:14px 20px; border-bottom:1px solid var(--border); background:#F9FAFB; font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }
        .preview-body { padding:24px; }

        /* The 3D preview card (matches shop cards) */
        .preview-card {
            background:#FFFFFF; border-radius:20px; padding:24px;
            box-shadow:0 4px 6px -1px rgba(0,0,0,0.05), 0 16px 32px -8px rgba(0,0,0,0.08);
            border:1px solid var(--border);
            display:flex; flex-direction:column; gap:18px;
            transform-style:preserve-3d;
            transition:transform 0.4s cubic-bezier(0.16,1,0.3,1);
        }
        .pc-head { display:flex; align-items:center; gap:14px; }
        .pc-logo { width:56px; height:56px; border-radius:14px; object-fit:cover; border:1px solid var(--border); background:#F3F4F6; flex-shrink:0; }
        .pc-id   { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted); }
        .pc-name { font-size:17px; font-weight:700; color:var(--text-strong); letter-spacing:-0.3px; }
        .pc-pills { display:flex; gap:8px; }
        .pc-pill { flex:1; background:#F9FAFB; border:1px solid var(--border); border-radius:10px; padding:10px; text-align:center; }
        .pc-pill-val { font-size:13px; font-weight:700; color:var(--green-text); }
        .pc-pill-lbl { font-size:9px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.5px; }

        /* Step indicators in header */
        .step-indicator { display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; color:var(--text-muted); }
        .step-dot { width:22px; height:22px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; background:var(--border); color:var(--text-muted); transition:0.2s; }
        .step-dot.active { background:var(--text-strong); color:#fff; }
        .step-dot.done   { background:var(--green-text); color:#fff; }
        .step-line { width:20px; height:2px; border-radius:2px; background:var(--border); transition:0.2s; }
        .step-line.done { background:var(--green-text); }

        /* ── TABLET ≤ 960px ──────────────────────────────────────────────── */
        @media (max-width: 960px) {
            body { height: auto; overflow-y: auto; }
            .layout-wrapper { flex-direction: column; height: auto; overflow: visible; }

            /* Hide desktop sidebar */
            .sb-container { display: none !important; }

            main.content-area { overflow-y: visible; }

            /* Header: wrap step indicators below title */
            .header-bar {
                flex-wrap: wrap;
                padding: 14px 16px;
                gap: 10px;
            }
            .step-indicator { width: 100%; justify-content: flex-end; }

            /* Page body: single column */
            .page-body { grid-template-columns: 1fr; padding: 16px 16px 80px; gap: 16px; }
            .preview-panel { display: none; }

            /* field-row: single column on tablet */
            .field-row { grid-template-columns: 1fr; gap: 14px; }

            .form-card-head { padding: 14px 18px; }
            .phase-actions { padding: 14px 18px; }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .header-bar { padding: 12px 14px; }
            .header-bar h1 { font-size: 16px; }

            /* Upload: logo sq + banner stack vertically */
            .upload-row { flex-direction: column; }
            .upload-square { width: 100%; height: 100px; }
            .upload-banner  { height: 100px; }

            .page-body { padding: 12px 12px 80px; }
        }

    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">
            <header class="header-bar">
                <a href="shop.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <div style="flex:1;">
                    <h1>Add Shop</h1>
                    <p>Register a new store on the QOON vendor network.</p>
                </div>
                <!-- Step indicators -->
                <div class="step-indicator" id="stepIndicator">
                    <div class="step-dot active" id="sd1">1</div>
                    <div class="step-line" id="sl1"></div>
                    <div class="step-dot" id="sd2">2</div>
                    <div class="step-line" id="sl2"></div>
                    <div class="step-dot" id="sd3">3</div>
                </div>
            </header>

            <div class="page-body">

                <!-- Form -->
                <div>
                    <!-- Stepper Bar -->
                    <div class="stepper" style="margin-bottom:16px;">
                        <div class="step-bar done" id="sb1"></div>
                        <div class="step-bar" id="sb2"></div>
                        <div class="step-bar" id="sb3"></div>
                    </div>

                    <div class="form-card">

                        <form action="AddShopAPI.php" method="POST" enctype="multipart/form-data" id="shopForm">

                            <!-- PHASE 1: Identity -->
                            <div class="form-phase active" id="p1">
                                <div class="form-card-head">
                                    <h2><i class="fas fa-store"></i> Brand Identity</h2>
                                    <p>Upload a logo, banner and set the shop name.</p>
                                </div>
                                <div style="padding:28px 24px; display:flex; flex-direction:column; gap:20px;">
                                    <div class="upload-row">
                                        <div class="upload-square" onclick="document.getElementById('f_logo').click()">
                                            <input type="file" id="f_logo" name="Photo" accept=".png,.jpg,.jpeg" onchange="previewFile(this,'prev_logo')">
                                            <img id="prev_logo" alt="">
                                            <i class="fas fa-camera up-icon"></i>
                                            <span class="up-hint">Logo</span>
                                        </div>
                                        <div class="upload-banner" onclick="document.getElementById('f_banner').click()">
                                            <input type="file" id="f_banner" name="Photo2" accept=".png,.jpg,.jpeg" onchange="previewFile(this,'prev_banner')">
                                            <img id="prev_banner" alt="">
                                            <i class="fas fa-image up-icon"></i>
                                            <span class="up-hint">Banner image</span>
                                        </div>
                                    </div>
                                    <div class="inp-group">
                                        <label>Shop Name</label>
                                        <input type="text" name="ShopName" class="inp-field" placeholder="e.g. Fresh Market" required oninput="updatePreviewName(this.value)">
                                    </div>
                                </div>
                                <div class="phase-actions">
                                    <button type="button" class="btn-next" onclick="goStep(2)">Next <i class="fas fa-arrow-right"></i></button>
                                </div>
                            </div>

                            <!-- PHASE 2: Market Details -->
                            <div class="form-phase" id="p2">
                                <div class="form-card-head">
                                    <h2><i class="fas fa-map-marker-alt"></i> Market & Classification</h2>
                                    <p>Assign a city, category and service tier.</p>
                                </div>
                                <div style="padding:28px 24px; display:flex; flex-direction:column; gap:20px;">
                                    <div class="field-row">
                                        <div class="inp-group">
                                            <label>City / Zone</label>
                                            <select name="CityID" class="inp-field" required>
                                                <?php foreach($cities as $c): ?>
                                                    <option value="<?= $c['DeliveryZoneID'] ?>"><?= htmlspecialchars($c['CityName']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="inp-group">
                                            <label>Category</label>
                                            <select name="CategoryID" class="inp-field" required>
                                                <?php foreach($cats as $cat): ?>
                                                    <option value="<?= $cat['CategoryId'] ?>"><?= htmlspecialchars($cat['EnglishCategory']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="inp-group">
                                        <label>Service Tier</label>
                                        <select name="Type" class="inp-field">
                                            <option value="Ourplus">Premium Elite</option>
                                            <option value="Our">Standard Plus</option>
                                            <option value="Other">Standard Core</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="phase-actions">
                                    <button type="button" class="btn-back" onclick="goStep(1)"><i class="fas fa-arrow-left"></i> Back</button>
                                    <button type="button" class="btn-next" onclick="goStep(3)">Next <i class="fas fa-arrow-right"></i></button>
                                </div>
                            </div>

                            <!-- PHASE 3: Access Credentials -->
                            <div class="form-phase" id="p3">
                                <div class="form-card-head">
                                    <h2><i class="fas fa-lock"></i> Access Credentials</h2>
                                    <p>Set the phone number and login details for the shop owner.</p>
                                </div>
                                <div style="padding:28px 24px; display:flex; flex-direction:column; gap:20px;">
                                    <div class="inp-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="ShopPhone" class="inp-field" placeholder="+212 6 00 000 000" required>
                                    </div>
                                    <div class="field-row">
                                        <div class="inp-group">
                                            <label>Login Username</label>
                                            <input type="text" name="ShopLoginName" class="inp-field" placeholder="e.g. freshmarket" required>
                                        </div>
                                        <div class="inp-group">
                                            <label>Login Password</label>
                                            <input type="password" name="ShopLoginPassword" class="inp-field" placeholder="••••••••" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="phase-actions">
                                    <button type="button" class="btn-back" onclick="goStep(2)"><i class="fas fa-arrow-left"></i> Back</button>
                                    <button type="submit" class="btn-next"><i class="fas fa-check"></i> Create Shop</button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Live Preview Panel -->
                <div class="preview-panel">
                    <div class="preview-head"><i class="fas fa-eye" style="margin-right:6px;"></i> Live Preview</div>
                    <div class="preview-body">
                        <div class="preview-card" id="previewCard">
                            <div class="pc-head">
                                <img id="previewLogo" class="pc-logo"
                                     src="https://ui-avatars.com/api/?name=Shop&background=F3F4F6&color=111827&bold=true&size=128" alt="">
                                <div>
                                    <div class="pc-id">New Entity</div>
                                    <div class="pc-name" id="previewName">Shop Name</div>
                                </div>
                            </div>
                            <div class="pc-pills">
                                <div class="pc-pill">
                                    <div class="pc-pill-val">Active</div>
                                    <div class="pc-pill-lbl">Status</div>
                                </div>
                                <div class="pc-pill">
                                    <div class="pc-pill-val"><?= date('Y') ?></div>
                                    <div class="pc-pill-lbl">Est.</div>
                                </div>
                            </div>
                        </div>
                        <p style="font-size:11px;color:var(--text-muted);font-weight:500;text-align:center;margin-top:16px;">Preview updates as you fill in the form.</p>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        let currentStep = 1;

        function goStep(s) {
            // Hide all phases
            document.querySelectorAll('.form-phase').forEach(el => el.classList.remove('active'));
            document.getElementById('p' + s).classList.add('active');
            currentStep = s;

            // Update step bars
            ['sb1','sb2','sb3'].forEach((id, i) => {
                document.getElementById(id).classList.toggle('done', i + 1 <= s);
            });

            // Update step dots
            ['sd1','sd2','sd3'].forEach((id, i) => {
                const el = document.getElementById(id);
                el.classList.remove('active','done');
                if(i + 1 === s)      el.classList.add('active');
                else if(i + 1 < s)   el.classList.add('done');
            });

            // Update step lines
            ['sl1','sl2'].forEach((id, i) => {
                document.getElementById(id).classList.toggle('done', i + 1 < s);
            });
        }

        function previewFile(input, previewId) {
            if(!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = e => {
                const img = document.getElementById(previewId);
                img.src = e.target.result;
                img.style.display = 'block';
                // Hide upload UI elements
                const zone = img.parentElement;
                zone.querySelectorAll('.up-icon, .up-hint').forEach(el => el.style.display = 'none');
                // Update preview card logo if it's the logo file
                if(previewId === 'prev_logo') {
                    document.getElementById('previewLogo').src = e.target.result;
                }
            };
            reader.readAsDataURL(input.files[0]);
        }

        function updatePreviewName(val) {
            document.getElementById('previewName').textContent = val || 'Shop Name';
        }

        // 3D tilt on preview card
        const card = document.getElementById('previewCard');
        document.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const cx = rect.left + rect.width  / 2;
            const cy = rect.top  + rect.height / 2;
            const dx = (e.clientX - cx) / 25;
            const dy = (e.clientY - cy) / 25;
            card.style.transform = `perspective(800px) rotateY(${dx}deg) rotateX(${-dy}deg)`;
        });
    </script>
</body>
</html>