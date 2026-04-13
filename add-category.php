<?php require "conn.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --bg-master: #F3F4F6;
            --bg-surface: #FFFFFF;
            --border-subtle: #E5E7EB;
            --border-focus: #D1D5DB;
            --text-strong: #111827;
            --text-base: #374151;
            --text-muted: #6B7280;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', -apple-system, sans-serif; }
        body { background: var(--bg-master); color: var(--text-base); display: flex; height: 100vh; overflow: hidden; -webkit-font-smoothing: antialiased; }
        .layout-wrapper { display: flex; width: 100%; height: 100%; }

        main.content-area { flex: 1; overflow-y: auto; display: flex; flex-direction: column; }
        main.content-area::-webkit-scrollbar { width: 6px; }
        main.content-area::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }

        /* Header */
        .header-bar {
            position: sticky; top: 0; z-index: 20;
            background: rgba(255,255,255,0.9); backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 20px 40px;
            display: flex; align-items: center; gap: 16px;
        }
        .back-btn {
            display: inline-flex; align-items: center; justify-content: center;
            width: 34px; height: 34px; border-radius: 8px;
            border: 1px solid var(--border-subtle); background: var(--bg-surface);
            color: var(--text-muted); text-decoration: none;
            box-shadow: var(--shadow-sm); transition: 0.2s; flex-shrink: 0;
        }
        .back-btn:hover { border-color: var(--text-strong); color: var(--text-strong); }
        .header-bar h1 { font-size: 18px; font-weight: 700; color: var(--text-strong); }
        .header-bar p { font-size: 13px; color: var(--text-muted); font-weight: 500; margin-top: 2px; }

        /* Page body */
        .page-body {
            padding: 40px; max-width: 860px; margin: 0 auto; width: 100%;
            display: flex; flex-direction: column; gap: 24px;
        }

        /* Form card */
        .form-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px; box-shadow: var(--shadow-sm); overflow: hidden;
        }
        .form-card-head {
            padding: 18px 24px; border-bottom: 1px solid var(--border-subtle);
            background: #F9FAFB; font-size: 14px; font-weight: 700; color: var(--text-strong);
            display: flex; align-items: center; gap: 8px;
        }
        .form-card-head i { font-size: 13px; color: var(--text-muted); }
        .form-card-body { padding: 28px 24px; display: flex; flex-direction: column; gap: 20px; }

        /* Upload zone */
        .upload-zone {
            border: 2px dashed var(--border-focus); border-radius: 12px;
            background: #F9FAFB; display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            gap: 10px; cursor: pointer; transition: 0.2s;
            position: relative; overflow: hidden;
            min-height: 180px; width: 100%;
        }
        .upload-zone:hover { border-color: var(--text-strong); background: #F3F4F6; }
        .upload-zone input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
        .upload-zone .upload-icon {
            width: 44px; height: 44px; border-radius: 10px;
            background: var(--border-subtle); display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: var(--text-muted);
        }
        .upload-zone .upload-label { font-size: 14px; font-weight: 600; color: var(--text-strong); }
        .upload-zone .upload-hint { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        #imagePreview { display: none; width: 160px; height: 160px; object-fit: cover; border-radius: 12px; border: 1px solid var(--border-subtle); }

        /* Fields */
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .field-row-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; }
        .inp-group { display: flex; flex-direction: column; gap: 6px; }
        .inp-group label { font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .inp-field {
            padding: 10px 14px; border: 1px solid var(--border-subtle); border-radius: 8px;
            font-size: 14px; font-weight: 500; color: var(--text-strong);
            background: var(--bg-surface); outline: none; transition: 0.2s; box-shadow: var(--shadow-sm);
            width: 100%;
        }
        .inp-field:focus { border-color: var(--border-focus); box-shadow: 0 0 0 3px rgba(17,24,39,0.06); }
        select.inp-field { cursor: pointer; }
        select[multiple].inp-field { min-height: 120px; padding: 8px; }
        select[multiple].inp-field option { padding: 6px 8px; border-radius: 4px; }
        select[multiple].inp-field option:checked { background: #111827; color: #fff; }

        /* Footer */
        .form-footer {
            padding: 20px 24px; border-top: 1px solid var(--border-subtle);
            background: #F9FAFB; display: flex; justify-content: flex-end; gap: 12px;
        }
        .btn-cancel {
            padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600;
            border: 1px solid var(--border-subtle); background: var(--bg-surface);
            color: var(--text-muted); cursor: pointer; transition: 0.2s; text-decoration: none;
            display: inline-flex; align-items: center; box-shadow: var(--shadow-sm);
        }
        .btn-cancel:hover { background: #F3F4F6; }
        .btn-submit {
            padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600;
            background: var(--text-strong); color: #FFFFFF; border: none; cursor: pointer;
            transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; box-shadow: var(--shadow-sm);
        }
        .btn-submit:hover { background: #1F2937; box-shadow: var(--shadow-md); }
    </style>
</head>
<body>
    <div class="layout-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="content-area">
            <header class="header-bar">
                <a href="apps.php" class="back-btn"><i class="fas fa-arrow-left"></i></a>
                <div>
                    <h1>Add Category</h1>
                    <p>Create a new app category with translations and zone assignment.</p>
                </div>
            </header>

            <div class="page-body">
                <form method="POST" action="AddCategoryAPI.php" enctype="multipart/form-data">

                    <!-- Image Upload -->
                    <div class="form-card">
                        <div class="form-card-head"><i class="fas fa-image"></i> Category Icon</div>
                        <div class="form-card-body" style="align-items:center;">
                            <div class="upload-zone" id="uploadZone" style="max-width:360px; width:100%;">
                                <input type="file" name="Photo" accept=".png,.jpg,.jpeg" id="imageInput" onchange="previewImage(event)">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <span class="upload-label">Click to upload icon</span>
                                <span class="upload-hint">PNG, JPG — Square format recommended</span>
                            </div>
                            <img id="imagePreview" src="" alt="Preview">
                        </div>
                    </div>

                    <!-- Name & Priority -->
                    <div class="form-card">
                        <div class="form-card-head"><i class="fas fa-language"></i> Category Details</div>
                        <div class="form-card-body">
                            <div class="field-row-3">
                                <div class="inp-group">
                                    <label>Arabic Name</label>
                                    <input type="text" name="Arabic" class="inp-field" placeholder="الاسم بالعربية">
                                </div>
                                <div class="inp-group">
                                    <label>French Name</label>
                                    <input type="text" name="French" class="inp-field" placeholder="Nom en français">
                                </div>
                                <div class="inp-group">
                                    <label>Search Key (English)</label>
                                    <input type="text" name="English" class="inp-field" placeholder="Search keyword">
                                </div>
                            </div>
                            <div class="field-row">
                                <div class="inp-group">
                                    <label>Display Priority</label>
                                    <input type="number" name="Priority" class="inp-field" placeholder="e.g. 1">
                                </div>
                                <div class="inp-group">
                                    <label>Category Type</label>
                                    <select name="Type" class="inp-field">
                                        <option value="Top">Main</option>
                                        <option value="Small">Kenz Madinty</option>
                                    </select>
                                </div>
                            </div>
                            <div class="field-row">
                                <div class="inp-group">
                                    <label>Visibility Tier</label>
                                    <select name="Pro" class="inp-field">
                                        <option value="Normal">Normal</option>
                                        <option value="Pro">Pro</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Zones -->
                    <div class="form-card">
                        <div class="form-card-head"><i class="fas fa-map-marker-alt"></i> Delivery Zone Assignment</div>
                        <div class="form-card-body">
                            <div class="inp-group">
                                <label>Assign to Zones <span style="font-weight:400; text-transform:none; font-size:11px; color:var(--text-muted);">(hold Ctrl / ⌘ to select multiple)</span></label>
                                <select name="DeliveryZoneID[]" class="inp-field" multiple>
                                    <?php
                                        $res = mysqli_query($con, "SELECT * FROM DeliveryZone WHERE Status = 'ACTIVE'");
                                        while($row = mysqli_fetch_assoc($res)):
                                    ?>
                                        <option value="<?= $row['DeliveryZoneID'] ?>"><?= htmlspecialchars($row['CityName']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-footer">
                            <a href="apps.php" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-submit"><i class="fas fa-plus"></i> Add Category</button>
                        </div>
                    </div>

                </form>
            </div>
        </main>
    </div>

    <script>
        function previewImage(e) {
            const file = e.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.getElementById('imagePreview');
                const zone = document.getElementById('uploadZone');
                preview.src = ev.target.result;
                preview.style.display = 'block';
                zone.querySelector('.upload-icon').style.display = 'none';
                zone.querySelector('.upload-label').style.display = 'none';
                zone.querySelector('.upload-hint').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    </script>
</body>
</html>