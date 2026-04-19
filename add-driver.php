<?php
require "conn.php";

// Fetch Countries for dropdown
$countries_res = mysqli_query($con, "SELECT * FROM Countries");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Driver | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-app: #F5F6FA; --bg-white: #FFFFFF;
            --text-dark: #2A3042; --text-gray: #A6A9B6;
            --accent-purple: #623CEA; --accent-purple-light: #F0EDFD;
            --accent-green: #10B981; --accent-blue: #007AFF;
            --shadow-card: 0 8px 30px rgba(0, 0, 0, 0.03);
            --border-color: #F0F2F6;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); height: 100vh; display: flex; overflow: hidden; }

        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        /* Unified Sidebar CSS */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i { font-size: 18px; width: 20px; text-align: center; }
        .nav-item:hover:not(.active) { color: var(--text-dark); background: #F8F9FB; }
        .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

        .main-panel { flex: 1; padding: 35px 50px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }

        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; }
        .back-btn { display: inline-flex; align-items: center; gap: 10px; padding: 10px 18px; border-radius: 12px; background: var(--bg-white); color: var(--text-dark); text-decoration: none; font-weight: 700; font-size: 14px; box-shadow: var(--shadow-card); transition: 0.2s; border: 1px solid var(--border-color); }
        .back-btn:hover { background: #F8F9FB; transform: translateY(-2px); box-shadow: 0 12px 25px rgba(0,0,0,0.05); color: var(--accent-purple); }

        .page-title { display: flex; flex-direction: column; gap: 5px; margin-bottom: 25px;}
        .page-title h1 { font-size: 26px; font-weight: 800; color: var(--text-dark); letter-spacing: -0.5px;}
        .page-title p { font-size: 14px; font-weight: 500; color: var(--text-gray); }

        .form-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 30px; }

        .card { background: var(--bg-white); border-radius: 24px; padding: 35px; box-shadow: var(--shadow-card); border: 1px solid rgba(255,255,255,0.8); }
        .card-header { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .card-header i { color: var(--accent-purple); }

        .input-group { margin-bottom: 20px; display: flex; flex-direction: column; gap: 8px; }
        .input-group.row-split { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .input-group label { font-size: 13px; font-weight: 700; color: var(--text-dark); text-transform: uppercase; letter-spacing: 0.5px; }
        .input-group input, .input-group select { width: 100%; padding: 14px 18px; border-radius: 12px; border: 1px solid var(--border-color); background: #F8F9FA; color: var(--text-dark); font-size: 14px; font-weight: 500; outline: none; transition: 0.2s; font-family: 'Inter', sans-serif; }
        .input-group input:focus, .input-group select:focus { background: #FFF; border-color: var(--accent-purple); box-shadow: 0 0 0 3px var(--accent-purple-light); }

        /* Photo Uploader */
        .photo-upload { display: flex; align-items: center; gap: 20px; margin-bottom: 25px; padding: 20px; background: #F8F9FA; border-radius: 16px; border: 1px dashed #D1D5DF; }
        .photo-preview { width: 70px; height: 70px; border-radius: 50%; background: #EBECEF; display: flex; align-items: center; justify-content: center; overflow: hidden; color: var(--text-gray); font-size: 24px;}
        .photo-preview img { width: 100%; height: 100%; object-fit: cover; display: none; }
        .photo-btn { background: var(--bg-white); border: 1px solid var(--border-color); padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; color: var(--text-dark); cursor: pointer; transition: 0.2s; }
        .photo-btn:hover { background: var(--accent-purple-light); color: var(--accent-purple); border-color: var(--accent-purple-light);}

        /* File Uploaders List */
        .file-list { display: flex; flex-direction: column; gap: 15px; margin-bottom: 25px; }
        .file-item { position: relative; padding: 15px 20px; border-radius: 12px; border: 1px solid var(--border-color); background: #F8F9FA; display: flex; flex-direction: column; transition: 0.2s; overflow: hidden; }
        .file-item:hover { border-color: var(--accent-purple); background: #FFF; box-shadow: 0 5px 15px rgba(98, 60, 234, 0.08); }
        .file-item.attached { border-color: var(--accent-green); background: rgba(16, 185, 129, 0.05); }
        
        .file-content-row { display: flex; justify-content: space-between; align-items: center; width: 100%; position: relative; z-index: 2;}
        .file-info { display: flex; align-items: center; gap: 12px; }
        .file-icon { font-size: 24px; color: var(--text-gray); transition: 0.2s;}
        .file-item.attached .file-icon { color: var(--accent-green); }
        
        .file-text-col { display: flex; flex-direction: column; }
        .file-title { font-weight: 700; color: var(--text-dark); font-size: 14px; transition: 0.2s;}
        .file-subtitle { font-size: 12px; font-weight: 500; color: var(--text-gray); margin-top: 2px; }
        
        .file-input-cover { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; z-index: 5;}

        /* Dynamic Progress Bar */
        .progress-track { width: 100%; height: 4px; background: #EBECEF; border-radius: 2px; margin-top: 12px; overflow: hidden; display: none; position: relative; z-index: 2;}
        .progress-fill { height: 100%; width: 0%; background: var(--accent-purple); border-radius: 2px; transition: width 0.1s linear; }

        .btn-submit { background: linear-gradient(135deg, var(--accent-purple), #4F28D1); color: #FFF; border: none; padding: 18px 24px; border-radius: 16px; font-weight: 800; font-size: 16px; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; box-shadow: 0 10px 25px rgba(98, 60, 234, 0.3); }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(98, 60, 234, 0.4); }
        
        .radio-box { display: flex; align-items: center; gap: 10px; padding: 14px 18px; border: 1px solid var(--accent-purple); background: var(--accent-purple-light); border-radius: 12px; color: var(--accent-purple); font-weight: 700; font-size: 14px; cursor: pointer; }

        /* ── MOBILE RESPONSIVE ──────────────────────────────────────────── */
        @media (max-width: 991px) {
            body { height: auto; overflow-y: auto; }
            .app-envelope { flex-direction: column; height: auto; overflow: visible; }

            /* Hide desktop sidebar rail */
            .sidebar { display: none !important; }

            .main-panel {
                padding: 16px 16px 80px; /* bottom pad for tab bar */
                overflow-y: visible;
                overflow-x: hidden;
            }

            .header { margin-bottom: 16px; }
            .back-btn { font-size: 13px; padding: 9px 14px; }

            .page-title h1 { font-size: 22px; }
            .page-title p  { font-size: 13px; }
            .page-title    { margin-bottom: 18px; }

            /* Form: single column */
            .form-grid { grid-template-columns: 1fr; gap: 16px; }

            /* Cards */
            .card { padding: 20px; border-radius: 18px; }
            .card-header { font-size: 15px; margin-bottom: 18px; }

            /* Row-split inputs: single column on tablet */
            .input-group.row-split { grid-template-columns: 1fr; gap: 0; }

            /* Photo upload: keep side-by-side, just tighter */
            .photo-upload { padding: 14px; gap: 14px; }
            .photo-preview { width: 60px; height: 60px; font-size: 20px; }

            /* File items */
            .file-item { padding: 12px 14px; }
            .file-title { font-size: 13px; }

            /* Submit button */
            .btn-submit { padding: 16px 20px; font-size: 15px; border-radius: 14px; }
        }

        /* ── PHONE ≤ 600px ───────────────────────────────────────────────── */
        @media (max-width: 600px) {
            .main-panel { padding: 12px 12px 80px; }

            /* Photo upload: stack vertically */
            .photo-upload { flex-direction: column; align-items: center; text-align: center; }

            .input-group input,
            .input-group select { padding: 12px 14px; font-size: 14px; }

            .card { padding: 16px; }
            .card-header { font-size: 14px; }
        }

        /* ----- DRIVER AI ASSISTANT (Tamo) ----- */
        .ai-fab {
            position: fixed;
            bottom: 25px; right: 25px;
            width: 62px; height: 62px;
            border-radius: 50%;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 8px 28px rgba(217, 70, 168, 0.35), 0 2px 8px rgba(0,0,0,0.12);
            cursor: pointer;
            z-index: 9999;
            transition: transform 0.3s, box-shadow 0.3s;
            padding: 0;
            border: 2.5px solid #fff;
        }
        .ai-fab:hover { transform: scale(1.08); box-shadow: 0 12px 36px rgba(217, 70, 168, 0.45); }
        .ai-fab img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .ai-fab-dot {
            position: absolute;
            bottom: 2px; right: 2px;
            width: 14px; height: 14px;
            background: #22c55e;
            border: 2.5px solid #fff;
            border-radius: 50%;
            animation: fabPulse 1.8s ease-in-out infinite;
        }
        @keyframes fabPulse {
            0%,100% { box-shadow: 0 0 0 0 rgba(34,197,94,0.7); }
            50%      { box-shadow: 0 0 0 6px rgba(34,197,94,0); }
        }

        /* ── AI CHAT POPUP ── */
        .ai-popup {
            position: fixed;
            bottom: 100px; right: 25px;
            width: 390px; height: 580px;
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.18);
            display: flex; flex-direction: column;
            overflow: hidden;
            z-index: 9998;
            transform: translateY(20px) scale(0.97);
            opacity: 0;
            pointer-events: none;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            border: 1px solid rgba(0,0,0,0.06);
        }
        .ai-popup.open {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: all;
        }

        /* Header */
        .ai-head {
            background: linear-gradient(135deg, #D946A8, #8B5CF6);
            color: #fff;
            padding: 16px 18px;
            display: flex; align-items: center; justify-content: space-between;
            flex-shrink: 0;
        }
        .ai-head-titles { display:flex; flex-direction:column; line-height:1.3; }
        .ai-head-titles span { font-weight:700; font-size:15px; }
        .ai-head-titles small { font-size:11px; opacity:0.85; margin-top:2px; }
        .ai-close {
            cursor:pointer; font-size:18px; opacity:0.8;
            transition:0.2s; width:32px; height:32px;
            display:flex; align-items:center; justify-content:center;
            border-radius:50%; background:rgba(255,255,255,0.15);
        }
        .ai-close:hover { opacity:1; background:rgba(255,255,255,0.25); }

        /* Messages */
        .ai-body {
            flex: 1; padding: 16px;
            overflow-y: auto;
            display: flex; flex-direction: column; gap: 12px;
            background: #F5F6FA;
            scroll-behavior: smooth;
        }
        .ai-body::-webkit-scrollbar { width: 4px; }
        .ai-body::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 4px; }

        .ai-msg { display:flex; max-width:82%; line-height:1.55; font-size:13.5px; }
        .ai-msg.bot  { align-self: flex-start; }
        .ai-msg.user { align-self: flex-end; }
        .ai-bubble {
            padding: 11px 15px; border-radius: 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            word-break: break-word;
        }
        .ai-msg.bot  .ai-bubble { background:#fff; color:#111827; border-bottom-left-radius:4px; border:1px solid #E5E7EB; }
        .ai-msg.user .ai-bubble { background:#D946A8; color:#fff; border-bottom-right-radius:4px; }

        /* Typing */
        .ai-typing {
            font-size:12px; color:#9CA3AF;
            display:none; padding:0 16px 10px;
            background:#F5F6FA; flex-shrink:0;
        }
        .ai-typing span { display:inline-block; animation: typBounce 1.2s infinite; }
        .ai-typing span:nth-child(2) { animation-delay:.2s; }
        .ai-typing span:nth-child(3) { animation-delay:.4s; }
        @keyframes typBounce { 0%,60%,100%{transform:translateY(0)} 30%{transform:translateY(-5px)} }

        /* Input foot */
        .ai-foot {
            padding: 12px 14px;
            background: #fff; border-top: 1px solid #F0F0F0;
            display:flex; gap:10px; align-items:center;
            flex-shrink: 0;
        }
        .ai-input {
            flex: 1; border: 1.5px solid #E5E7EB; border-radius: 22px;
            padding: 10px 16px; font-size:13.5px;
            outline:none; background:#F9FAFB;
            transition:0.2s; font-family:inherit;
            resize: none; line-height: 1.4;
        }
        .ai-input:focus { border-color:#D946A8; background:#fff; box-shadow:0 0 0 3px rgba(217,70,168,0.08); }
        .ai-send {
            width: 40px; height: 40px; border-radius: 50%;
            background:#D946A8; color:white;
            border:none; cursor:pointer;
            display:flex; align-items:center; justify-content:center;
            font-size:15px; transition:0.2s; flex-shrink:0;
        }
        .ai-send:hover { background:#C026D3; transform:scale(1.05); }

        /* Mobile */
        @media (max-width: 600px) {
            .ai-fab  { right: 16px; bottom: 80px; }
            .ai-popup { right: 0; left: 0; bottom: 0; width: 100%; height: 90dvh; border-radius: 24px 24px 0 0; transform: translateY(100%); }
            .ai-popup.open { transform: translateY(0); }
            .ai-foot { padding-bottom: max(12px, env(safe-area-inset-bottom)); }
        }
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <a href="driver.php" class="back-btn"><i class="fas fa-arrow-left"></i> Drivers Directory</a>

            </header>

            <div class="page-title">
                <h1>Register New Driver</h1>
                <p>Fill out the profile details and securely upload the mandated verifying documents.</p>
            </div>

            <form action="AddDriverJiblerAPI.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <!-- Column 1: Personal Details -->
                    <div class="card">
                        <div class="card-header"><i class="fas fa-user-circle"></i> Personal Information</div>
                        
                        <div class="photo-upload">
                            <div class="photo-preview" id="previewArea">
                                <i class="fas fa-camera"></i>
                                <img id="previewImg" src="">
                            </div>
                            <div style="display:flex; flex-direction:column; gap:5px;">
                                <label for="photoInput" class="photo-btn"><i class="fas fa-upload"></i> Upload Profile Photo</label>
                                <span style="font-size:11px; color:var(--text-gray); font-weight:600;">Format: .png, .jpg (Max 2MB)</span>
                            </div>
                            <input type="file" name="PersonalPhoto" id="photoInput" accept=".png, .jpg, .jpeg" style="display:none;" onchange="previewPhoto(this)">
                        </div>

                        <div class="input-group row-split">
                            <div>
                                <label>First Name</label>
                                <input type="text" name="FName" placeholder="E.g. Ahmed" required>
                            </div>
                            <div>
                                <label>Last Name</label>
                                <input type="text" name="LName" placeholder="E.g. Alaoui" required>
                            </div>
                        </div>

                        <div class="input-group">
                            <label>Email Address</label>
                            <input type="email" name="DriverEmail" placeholder="driver@example.com" required>
                        </div>

                        <div class="input-group row-split">
                            <div>
                                <label>Country Code</label>
                                <select name="CountryKey">
                                    <?php while($row = mysqli_fetch_assoc($countries_res)): ?>
                                        <option value="<?= $row['country_code'] ?>"><?= $row['EnglishName'] ?> (<?= $row['country_code'] ?>)</option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div>
                                <label>Phone Number</label>
                                <input type="number" name="DriverPhone" placeholder="123456789" required>
                            </div>
                        </div>

                        <div class="input-group row-split">
                            <div>
                                <label>Age</label>
                                <input type="number" name="AGE" placeholder="Years" required>
                            </div>
                            <div>
                                <label>City Base</label>
                                <input type="text" name="City" placeholder="E.g. Casablanca">
                            </div>
                        </div>
                        
                        <input type="hidden" name="CountryID" value="Morocco">
                        
                        <div class="input-group" style="margin-bottom:0; margin-top:10px;">
                            <label>Default Vehicle Type</label>
                            <label class="radio-box">
                                <input type="radio" name="vehicle-type" checked style="width:auto; margin:0;">
                                <i class="fas fa-motorcycle"></i> Motorbike / Standard Moped
                            </label>
                        </div>
                    </div>

                    <!-- Column 2: Account & Documents -->
                    <div>
                        <div class="card" style="margin-bottom:25px;">
                            <div class="card-header"><i class="fas fa-shield-alt"></i> Authentication</div>
                            <div class="input-group" style="margin-bottom:0;">
                                <label>System Password</label>
                                <input type="password" name="Password" placeholder="Create a secure password..." required>
                            </div>
                        </div>

                        <div class="card" style="margin-bottom:25px;">
                            <div class="card-header"><i class="fas fa-folder-open"></i> Verification Documents</div>
                            <p style="font-size:12px; color:var(--text-gray); font-weight:500; margin-bottom:15px; margin-top:-10px;">Select required legal files to attach. Ensure they are fully legible.</p>

                            <div class="file-list">
                                <div class="file-item" id="file-CIN">
                                    <div class="file-content-row">
                                        <div class="file-info">
                                            <i class="fas fa-id-card file-icon"></i>
                                            <div class="file-text-col">
                                                <span class="file-title">Identity Card (CIN)</span>
                                                <span class="file-subtitle" style="display:none;"></span>
                                            </div>
                                        </div>
                                        <i class="fas fa-paperclip text-gray action-icon"></i>
                                    </div>
                                    <div class="progress-track"><div class="progress-fill"></div></div>
                                    <input type="file" name="CIN" class="file-input-cover" onchange="handleFileUpload(this, 'file-CIN', 'Identity Card (CIN)')">
                                </div>

                                <div class="file-item" id="file-CV">
                                    <div class="file-content-row">
                                        <div class="file-info">
                                            <i class="fas fa-file-alt file-icon"></i>
                                            <div class="file-text-col">
                                                <span class="file-title">Driver Resume (CV)</span>
                                                <span class="file-subtitle" style="display:none;"></span>
                                            </div>
                                        </div>
                                        <i class="fas fa-paperclip text-gray action-icon"></i>
                                    </div>
                                    <div class="progress-track"><div class="progress-fill"></div></div>
                                    <input type="file" name="CV" class="file-input-cover" onchange="handleFileUpload(this, 'file-CV', 'Driver Resume (CV)')">
                                </div>

                                <div class="file-item" id="file-Contract">
                                    <div class="file-content-row">
                                        <div class="file-info">
                                            <i class="fas fa-file-signature file-icon"></i>
                                            <div class="file-text-col">
                                                <span class="file-title">Employment Contract</span>
                                                <span class="file-subtitle" style="display:none;"></span>
                                            </div>
                                        </div>
                                        <i class="fas fa-paperclip text-gray action-icon"></i>
                                    </div>
                                    <div class="progress-track"><div class="progress-fill"></div></div>
                                    <input type="file" name="Contract" class="file-input-cover" onchange="handleFileUpload(this, 'file-Contract', 'Employment Contract')">
                                </div>

                                <div class="file-item" id="file-Cart">
                                    <div class="file-content-row">
                                        <div class="file-info">
                                            <i class="fas fa-car-side file-icon"></i>
                                            <div class="file-text-col">
                                                <span class="file-title">Cart Ownership</span>
                                                <span class="file-subtitle" style="display:none;"></span>
                                            </div>
                                        </div>
                                        <i class="fas fa-paperclip text-gray action-icon"></i>
                                    </div>
                                    <div class="progress-track"><div class="progress-fill"></div></div>
                                    <input type="file" name="Cart-Ownership" class="file-input-cover" onchange="handleFileUpload(this, 'file-Cart', 'Cart Ownership')">
                                </div>

                                <div class="file-item" id="file-Insurance">
                                    <div class="file-content-row">
                                        <div class="file-info">
                                            <i class="fas fa-file-medical-alt file-icon"></i>
                                            <div class="file-text-col">
                                                <span class="file-title">Vehicle Insurance</span>
                                                <span class="file-subtitle" style="display:none;"></span>
                                            </div>
                                        </div>
                                        <i class="fas fa-paperclip text-gray action-icon"></i>
                                    </div>
                                    <div class="progress-track"><div class="progress-fill"></div></div>
                                    <input type="file" name="Insurance" class="file-input-cover" onchange="handleFileUpload(this, 'file-Insurance', 'Vehicle Insurance')">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-user-plus"></i> Submit Registration
                        </button>
                    </div>
                </div>
            </form>
        </main>
    </div>

    <script>
        function previewPhoto(input) {
            const previewArea = document.getElementById('previewArea');
            const img = document.getElementById('previewImg');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.style.display = 'block';
                    previewArea.querySelector('i').style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Dynamic Progress Bar Simulation for Attachments
        function handleFileUpload(input, containerId, originalTitle) {
            const container = document.getElementById(containerId);
            const track = container.querySelector('.progress-track');
            const fill = container.querySelector('.progress-fill');
            const title = container.querySelector('.file-title');
            const subtitle = container.querySelector('.file-subtitle');
            const actionIcon = container.querySelector('.action-icon');
            
            // Reset state
            container.classList.remove('attached');
            actionIcon.className = 'fas fa-spinner fa-spin text-gray action-icon';
            track.style.display = 'block';
            fill.style.width = '0%';
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileSize = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                
                title.textContent = "Processing " + file.name + "...";
                subtitle.style.display = 'none';

                let progress = 0;
                const interval = setInterval(() => {
                    // Random jump between 5 and 20 for dynamic feel
                    progress += Math.random() * 15 + 5; 
                    
                    if (progress >= 100) {
                        progress = 100;
                        clearInterval(interval);
                        
                        setTimeout(() => {
                            // Finished State
                            fill.style.width = '100%';
                            track.style.display = 'none';
                            container.classList.add('attached');
                            
                            title.textContent = file.name;
                            subtitle.textContent = "Verified • " + fileSize;
                            subtitle.style.display = 'block';
                            
                            actionIcon.className = 'fas fa-check-circle action-icon';
                            actionIcon.style.color = 'var(--accent-green)';
                        }, 200);
                    }
                    fill.style.width = progress + '%';
                }, 80);
            } else {
                // Cancelled Selection
                track.style.display = 'none';
                title.textContent = originalTitle;
                subtitle.style.display = 'none';
                actionIcon.className = 'fas fa-paperclip text-gray action-icon';
            }
        }
    </script>
    <!-- DRIVER AI ASSISTANT (Tamo) -->
    <div class="ai-fab" id="aiDriverFab" onclick="toggleDriverAI()" style="position:fixed;">
        <img src="tamo.jpg" alt="Tamo"
             onerror="this.src='https://ui-avatars.com/api/?name=Tamo&background=FFF0F6&color=D946A8&bold=true'">
        <span class="ai-fab-dot"></span>
    </div>
    <div class="ai-popup" id="aiDriverPopup">
        <div class="ai-head">
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="position:relative; width:40px; height:40px;">
                    <img src="tamo.jpg" alt="Tamo" style="width:100%; height:100%; border-radius:12px; object-fit:cover; box-shadow:0 4px 10px rgba(0,0,0,0.1);" onerror="this.src='https://ui-avatars.com/api/?name=Tamo&background=FFF0F6&color=D946A8&bold=true'">
                    <div title="Online 24/24" style="position:absolute; bottom:-2px; right:-2px; width:14px; height:14px; background:#10B981; border:2px solid #fff; border-radius:50%; box-shadow:0 2px 4px rgba(16,185,129,0.4);"></div>
                </div>
                <div class="ai-head-titles">
                    <span>Tamo AI Assistant</span>
                    <small style="display:flex; align-items:center; gap:4px;">
                        <span style="color:#10B981; font-size:8px;">●</span> Online 24/24
                    </small>
                </div>
            </div>
            <i class="fas fa-times ai-close" onclick="toggleDriverAI()"></i>
        </div>
        <div class="ai-body" id="aiDriverBody">
            <div class="ai-msg bot">
                <div class="ai-bubble">Hello! I am Tamo, your AI assistant for QOON Express. I can help you with the registration process or answer questions about driver requirements. How can I help?</div>
            </div>
        </div>
        <div class="ai-typing" id="aiDriverTyping">Analyzing database...</div>
        <div class="ai-foot">
            <input type="text" id="aiDriverInput" class="ai-input" placeholder="Ask Tamo..." onkeypress="if(event.key === 'Enter') sendDriverAIMessage()">
            <button class="ai-send" onclick="sendDriverAIMessage()"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>

    <script>
        let driverChatHistory = [];
        
        function toggleDriverAI() {
            document.getElementById('aiDriverPopup').classList.toggle('open');
            document.getElementById('aiDriverInput').focus();
        }

        async function sendDriverAIMessage() {
            const input = document.getElementById('aiDriverInput');
            const msg = input.value.trim();
            if(!msg) return;

            addDriverAIMsg('user', msg);
            input.value = '';
            
            const typing = document.getElementById('aiDriverTyping');
            typing.style.display = 'block';
            scrollDriverAIBottom();

            try {
                const res = await fetch('ai-user-agent-api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: msg, history: driverChatHistory, page_data: { type: 'add_driver' } })
                });
                const textOutput = await res.text();
                typing.style.display = 'none';
                
                try {
                    const data = JSON.parse(textOutput);
                    if(data.reply) {
                        addDriverAIMsg('bot', data.reply);
                        driverChatHistory.push({ role: 'user', content: msg });
                        driverChatHistory.push({ role: 'ai', content: data.reply });
                    } else {
                        addDriverAIMsg('bot', 'AI connection issue.');
                    }
                } catch (e) {
                    addDriverAIMsg('bot', 'Error processing AI response.');
                }
            } catch(e) {
                typing.style.display = 'none';
                addDriverAIMsg('bot', 'Connection error.');
            }
        }

        function addDriverAIMsg(sender, text) {
            const body = document.getElementById('aiDriverBody');
            const div = document.createElement('div');
            div.className = `ai-msg ${sender}`;
            let formattedText = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            div.innerHTML = `<div class="ai-bubble">${formattedText}</div>`;
            body.appendChild(div);
            scrollDriverAIBottom();
        }

        function scrollDriverAIBottom() {
            const body = document.getElementById('aiDriverBody');
            body.scrollTop = body.scrollHeight;
        }
    </script>
</body>
</html>