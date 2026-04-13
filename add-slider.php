<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Slide | QOON</title>
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
            box-shadow: var(--shadow-sm); transition: 0.2s;
        }
        .back-btn:hover { border-color: var(--text-strong); color: var(--text-strong); }
        .header-bar h1 { font-size: 18px; font-weight: 700; color: var(--text-strong); }
        .header-bar p { font-size: 13px; color: var(--text-muted); font-weight: 500; margin-top: 2px; }

        /* Page body */
        .page-body {
            padding: 40px; max-width: 860px; margin: 0 auto; width: 100%;
            display: flex; flex-direction: column; gap: 24px;
        }

        /* Card */
        .form-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-subtle);
            border-radius: 16px; box-shadow: var(--shadow-sm);
            overflow: hidden;
        }
        .form-card-head {
            padding: 18px 24px; border-bottom: 1px solid var(--border-subtle);
            background: #F9FAFB;
            font-size: 14px; font-weight: 700; color: var(--text-strong);
            display: flex; align-items: center; gap: 8px;
        }
        .form-card-head i { font-size: 13px; color: var(--text-muted); }
        .form-card-body { padding: 28px 24px; display: flex; flex-direction: column; gap: 20px; }

        /* Image Upload Area */
        .upload-zone {
            border: 2px dashed var(--border-focus);
            border-radius: 12px; background: #F9FAFB;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 12px; cursor: pointer; transition: 0.2s;
            position: relative; overflow: hidden;
            min-height: 200px; width: 100%;
        }
        .upload-zone:hover { border-color: var(--text-strong); background: #F3F4F6; }
        .upload-zone input[type="file"] {
            position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
        }
        .upload-zone .upload-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: var(--border-subtle); display: flex; align-items: center; justify-content: center;
            font-size: 20px; color: var(--text-muted);
        }
        .upload-zone .upload-label { font-size: 14px; font-weight: 600; color: var(--text-strong); }
        .upload-zone .upload-hint { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        #imagePreview {
            display: none; width: 100%; max-height: 300px;
            object-fit: contain; border-radius: 8px;
        }

        /* Fields */
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .inp-group { display: flex; flex-direction: column; gap: 6px; }
        .inp-group label { font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .inp-field {
            padding: 10px 14px; border: 1px solid var(--border-subtle);
            border-radius: 8px; font-size: 14px; font-weight: 500;
            color: var(--text-strong); background: var(--bg-surface);
            outline: none; transition: 0.2s; box-shadow: var(--shadow-sm);
        }
        .inp-field:focus { border-color: var(--border-focus); box-shadow: 0 0 0 3px rgba(17,24,39,0.06); }
        select.inp-field { cursor: pointer; }

        /* Submit */
        .form-footer { padding: 20px 24px; border-top: 1px solid var(--border-subtle); background: #F9FAFB; display: flex; justify-content: flex-end; gap: 12px; }
        .btn-cancel {
            padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600;
            border: 1px solid var(--border-subtle); background: var(--bg-surface);
            color: var(--text-muted); cursor: pointer; transition: 0.2s; text-decoration: none;
            display: inline-flex; align-items: center; box-shadow: var(--shadow-sm);
        }
        .btn-cancel:hover { background: #F3F4F6; }
        .btn-submit {
            padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600;
            background: var(--text-strong); color: #FFFFFF;
            border: none; cursor: pointer; transition: 0.2s;
            display: inline-flex; align-items: center; gap: 8px; box-shadow: var(--shadow-sm);
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
                    <h1>Add Slide</h1>
                    <p>Upload a new banner slide to the app's home carousel.</p>
                </div>
            </header>

            <div class="page-body">
                <form method="POST" action="AddSliderAPI.php" enctype="multipart/form-data">

                    <!-- Image Upload -->
                    <div class="form-card">
                        <div class="form-card-head"><i class="fas fa-image"></i> Slide Image</div>
                        <div class="form-card-body">
                            <div class="upload-zone" id="uploadZone">
                                <input type="file" name="Photo" accept=".png,.jpg,.jpeg" id="imageInput" onchange="previewImage(event)">
                                <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <span class="upload-label">Click to upload slide image</span>
                                <span class="upload-hint">PNG, JPG – Recommended ratio 16:9</span>
                            </div>
                            <img id="imagePreview" src="" alt="Preview">
                        </div>
                    </div>

                    <!-- Slide Details -->
                    <div class="form-card">
                        <div class="form-card-head"><i class="fas fa-sliders-h"></i> Slide Configuration</div>
                        <div class="form-card-body">
                            <div class="field-row">
                                <div class="inp-group">
                                    <label>Position Label</label>
                                    <input type="text" name="position" class="inp-field" placeholder="e.g. Home Top Banner">
                                </div>
                                <div class="inp-group">
                                    <label>Priority Order</label>
                                    <input type="text" name="Priority" class="inp-field" placeholder="e.g. 1">
                                </div>
                            </div>
                            <div class="field-row">
                                <div class="inp-group">
                                    <label>Linked Product ID</label>
                                    <input type="text" name="ProductID" class="inp-field" placeholder="Optional product link">
                                </div>
                                <div class="inp-group">
                                    <label>External Link URL</label>
                                    <input type="text" name="OpenNow" class="inp-field" placeholder="https://...">
                                </div>
                            </div>
                            <div class="field-row">
                                <div class="inp-group">
                                    <label>Is Default Photo?</label>
                                    <select name="SelectType" class="inp-field">
                                        <option value="Yes">Yes — Default photo</option>
                                        <option value="No">No — Not default photo</option>
                                    </select>
                                </div>
                                <div class="inp-group">
                                    <label>Open Action Type</label>
                                    <select name="OpenType" class="inp-field">
                                        <option value="NO">None</option>
                                        <option value="LINK">External Link</option>
                                        <option value="Product">Product Page</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-footer">
                            <a href="apps.php" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-submit"><i class="fas fa-plus"></i> Add Slide</button>
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