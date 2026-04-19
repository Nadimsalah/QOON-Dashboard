<?php require "conn.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Messaging | QOON Express App</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="fontawesome-kit-5/css/all.css" rel="stylesheet">
    <style>
        :root {
            --bg-app: #F4F7FE;
            --bg-white: #FFFFFF;
            --text-dark: #2B3674;
            --text-gray: #A3AED0;
            --accent-purple: #4318FF;
            --accent-purple-light: #F4F7FE;
            --accent-green: #05CD99;
            --accent-orange: #FFCE20;
            --accent-red: #EE5D50;
            --accent-blue: #3965FF;
            --border-color: #E2E8F0;
            --shadow-card: 0px 18px 40px rgba(112, 144, 176, 0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: var(--bg-app); display: flex; height: 100vh; overflow: hidden; }
        .app-envelope { width: 100%; height: 100%; display: flex; overflow: hidden; }

        .main-panel { flex: 1; padding: 35px 40px; display: flex; flex-direction: column; overflow-y: auto; overflow-x: hidden; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; background: var(--bg-white); padding: 15px 25px; border-radius: 16px; box-shadow: var(--shadow-card); flex-shrink: 0;}
        .breadcrumb { display: flex; align-items: center; gap: 12px; font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .breadcrumb i { color: var(--accent-purple); font-size: 18px; }

        .tab-menu { display: flex; gap: 15px; margin-bottom: 25px; }
        .tab-btn { padding: 14px 24px; border-radius: 12px; font-weight: 700; font-size: 14px; color: var(--text-gray); background: transparent; border: none; cursor: pointer; transition: 0.3s ease; display:flex; align-items:center; gap:8px; border: 2px solid transparent; text-decoration:none;}
        .tab-btn:hover { color: var(--accent-purple); background: var(--accent-purple-light); }
        .tab-btn.active { background: var(--accent-purple); color: #FFF; box-shadow: 0 10px 20px rgba(67, 24, 255, 0.2); border-color: var(--accent-purple); pointer-events:none;}

        .layout-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }

        .glass-card { background: var(--bg-white); border-radius: 20px; padding: 25px; box-shadow: var(--shadow-card); border: 1px solid var(--border-color); }
        .card-header { font-size: 18px; font-weight: 800; color: var(--text-dark); margin-bottom: 25px; display:flex; align-items:center; justify-content: space-between; border-bottom: 1px solid var(--border-color); padding-bottom:15px; }

        .premium-table { width: 100%; border-collapse: collapse; }
        .premium-table th { padding: 15px; text-align: left; font-size: 12px; font-weight: 800; color: var(--text-gray); text-transform: uppercase; border-bottom: 2px solid var(--border-color); }
        .premium-table td { padding: 15px; font-size: 14px; font-weight: 600; color: var(--text-dark); border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .premium-table tr:hover td { background: var(--bg-app); }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
        .form-control { width: 100%; background: var(--bg-app); border: 1px solid var(--border-color); border-radius: 12px; padding: 14px 18px; font-size: 14px; font-weight: 600; color: var(--text-dark); outline:none; transition:0.3s; }
        .form-control:focus { border-color: var(--accent-purple); box-shadow: 0 0 0 4px var(--accent-purple-light); background: #FFF; }
        textarea.form-control { min-height: 120px; resize: vertical; }

        .btn-submit { display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; padding: 16px; background: var(--text-dark); color: #FFF; font-weight: 800; font-size: 15px; border-radius: 14px; border: none; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 20px rgba(43, 54, 116, 0.2); }
        .btn-submit:hover { background: var(--accent-purple); color: #FFF; transform: translateY(-2px); box-shadow: 0 15px 25px rgba(67, 24, 255, 0.3); }

        .inner-tabs { display: flex; gap: 10px; margin-bottom: 20px; background: var(--bg-app); padding: 5px; border-radius: 14px; }
        .inner-tab { flex: 1; text-align: center; padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 700; color: var(--text-gray); cursor: pointer; transition: 0.2s; }
        .inner-tab.active { background: #FFF; color: var(--accent-purple); box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        /* SIDEBAR SUPPORT */
        .sidebar { width: 260px; background: var(--bg-white); display: flex; flex-direction: column; padding: 40px 0; border-right: 1px solid var(--border-color); flex-shrink: 0; }
        .logo-box { display: flex; align-items: center; padding: 0 30px; gap: 12px; margin-bottom: 50px; text-decoration: none; }
        .logo-box img { max-height: 50px; width: auto; object-fit: contain; }
        .nav-list { display: flex; flex-direction: column; gap: 5px; padding: 0 20px; flex: 1; }
        .nav-item { display: flex; align-items: center; gap: 16px; padding: 14px 20px; border-radius: 12px; color: var(--text-gray); text-decoration: none; font-size: 14px; font-weight: 600; transition: all 0.2s ease; }
        .nav-item i, .nav-item img { width: 22px; text-align: center; }
        .nav-item:hover, .nav-item.active { background: var(--accent-purple-light); color: var(--accent-purple); position: relative; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }
        .nav-item.active::before { content: ''; position: absolute; left: -20px; top: 50%; transform: translateY(-50%); height: 60%; width: 4px; background: var(--accent-purple); border-radius: 0 4px 4px 0; }

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
                <div class="breadcrumb">
                    <i class="fas fa-satellite-dish"></i> 
                    <span>Cloud Messaging System / QOON Express App</span>
                </div>
            </header>

            <div class="tab-menu">
                <a href="notifications.php" class="tab-btn"><i class="fas fa-users"></i> QOON</a>
                <a href="notificationsPartner.php" class="tab-btn"><i class="fas fa-store"></i> QOON Seller</a>
                <a href="notificationsDriver.php" class="tab-btn active"><i class="fas fa-motorcycle"></i> QOON Express</a>
            </div>

            <div class="layout-grid">
                
                <!-- Left: History Log -->
                <div class="glass-card">
                    <div class="card-header">
                        <div><i class="fas fa-history" style="color:var(--accent-purple); margin-right:8px;"></i> Broadcast History</div>
                        <span style="font-size:12px; color:var(--text-gray); background:var(--bg-app); padding:4px 10px; border-radius:8px;">Live Auto-Sync</span>
                    </div>
                    
                    <div style="overflow-x:auto;">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Message Body</th>
                                    <th>Dispatched</th>
                                    <th>Target</th>
                                    <th>Author</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = mysqli_query($con,"SELECT NotificationsSentByAdmin.*,Admin.AdminName FROM NotificationsSentByAdmin JOIN Admin ON NotificationsSentByAdmin.AdminID = Admin.AdminID ORDER BY CreatedAtNotificationsSentByAdmin DESC LIMIT 40"); 
                                while($row = mysqli_fetch_assoc($res)){
                                ?>
                                <tr>
                                    <td><span style="background:var(--bg-app); padding:4px 8px; border-radius:6px; font-weight:800; font-size:11px;">#<?php echo $row["NotificationsSentByAdminID"] ?></span></td>
                                    <td style="font-weight:700; color:var(--accent-purple);"><?php echo $row["Title"] ?></td>
                                    <td style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo $row["Bodyy"] ?></td>
                                    <td><i class="far fa-clock" style="color:var(--text-gray);"></i> <?php echo date('M d, H:i', strtotime($row["CreatedAtNotificationsSentByAdmin"])) ?></td>
                                    <td><span style="background:rgba(57, 101, 255, 0.1); color:var(--accent-blue); padding:4px 10px; border-radius:20px; font-size:10px; font-weight:800; text-transform:uppercase;"><?php echo $row["Type"] ?></span></td>
                                    <td><?php echo $row["AdminName"] ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Right: Composer -->
                <div class="glass-card" style="align-self: start;">
                    <div class="card-header">
                        <div><i class="fas fa-paper-plane" style="color:var(--accent-green); margin-right:8px;"></i> New Push Campaign</div>
                    </div>

                    <?php 
                        $OrderNum = 0;
                        $res = mysqli_query($con,"SELECT count(*) as c FROM Drivers");
                        if($row = mysqli_fetch_assoc($res)) { $OrderNum = $row["c"]; }
                    ?>
                    
                    <div class="inner-tabs">
                        <div class="inner-tab active" onclick="switchForm('bulk')">Bulk Broadcast</div>
                        <div class="inner-tab" onclick="switchForm('single')">Single Target ID</div>
                    </div>

                    <div style="background:var(--bg-app); border-radius:12px; padding:15px; margin-bottom:20px; display:flex; align-items:center; gap:15px;">
                        <div style="background:var(--bg-white); width:40px; height:40px; border-radius:10px; display:flex; justify-content:center; align-items:center; font-size:20px; color:var(--accent-green); box-shadow:var(--shadow-card);"><i class="fas fa-motorcycle"></i></div>
                        <div>
                            <div style="font-size:12px; font-weight:700; color:var(--text-gray); text-transform:uppercase;">Total Accessible Audience</div>
                            <div style="font-size:18px; font-weight:800; color:var(--text-dark);"><?= number_format($OrderNum) ?> Driver Fleet</div>
                        </div>
                    </div>

                    <!-- Bulk Form -->
                    <form id="form-bulk" method="POST" action="SendNotfToallDrivers.php" style="display:block;">
                        <div class="form-group">
                            <label>Notification Headline</label>
                            <input type="text" class="form-control" placeholder="Catchy title (Optional)" name="PostTitle">
                        </div>
                        <div class="form-group">
                            <label>Marketing Copy</label>
                            <textarea class="form-control" placeholder="Body of the notification..." name="Message" required></textarea>
                        </div>
                        <button type="submit" class="btn-submit"><i class="fas fa-rocket"></i> Dispatch Bulk Campaign</button>
                    </form>

                    <!-- Single Form -->
                    <form id="form-single" method="POST" action="SendNotfToDriversID.php" style="display:none;">
                        <div class="form-group">
                            <label>Target Driver ID Code</label>
                            <input type="text" class="form-control" placeholder="e.g. 159982" name="DriverID" required>
                        </div>
                        <div class="form-group">
                            <label>Notification Headline</label>
                            <input type="text" class="form-control" placeholder="Catchy title (Optional)" name="PostTitle">
                        </div>
                        <div class="form-group">
                            <label>Marketing Copy</label>
                            <textarea class="form-control" placeholder="Body of the notification..." name="Message" required></textarea>
                        </div>
                        <button type="submit" class="btn-submit" style="background:var(--accent-purple);"><i class="fas fa-paper-plane"></i> Send Direct Message</button>
                    </form>

                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        function switchForm(type) {
            document.querySelectorAll('.inner-tab').forEach(t => t.classList.remove('active'));
            document.getElementById('form-bulk').style.display = 'none';
            document.getElementById('form-single').style.display = 'none';

            if(type === 'bulk') {
                document.querySelectorAll('.inner-tab')[0].classList.add('active');
                document.getElementById('form-bulk').style.display = 'block';
            } else {
                document.querySelectorAll('.inner-tab')[1].classList.add('active');
                document.getElementById('form-single').style.display = 'block';
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
                <div class="ai-bubble">Hello! I am Tamo, your AI assistant for QOON Express. I can help you with broadcast history, campaign creation, or targeting drivers. How can I help?</div>
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
                    body: JSON.stringify({ message: msg, history: driverChatHistory, page_data: { type: 'notifications_driver' } })
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