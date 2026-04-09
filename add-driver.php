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
    </style>
</head>
<body>
    <div class="app-envelope">
        <?php include 'sidebar.php'; ?>

        <main class="main-panel">
            <header class="header">
                <a href="driver.php" class="back-btn"><i class="fas fa-arrow-left"></i> Drivers Directory</a>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="images/avatar-1.png" style="width:36px; height:36px; border-radius:50%;" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=EFEAF8&color=623CEA'">
                    <span style="font-weight:700; color:var(--text-dark); font-size:14px;">Administrator</span>
                </div>
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
</body>
</html>