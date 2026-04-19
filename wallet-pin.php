<?php
if(session_status() !== PHP_SESSION_ACTIVE) session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = $_POST['pin'] ?? '';
    if ($pin === '8808') {
        $_SESSION['wallet_auth'] = true;
        header("Location: wallet.php");
        exit;
    } else {
        $error = "Invalid vault PIN code.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Vault Access | QOON</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body {
            background: #111827;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            overflow: hidden;
            position: relative;
        }

        /* Abstract Background Effects */
        .bg-glow-1 {
            position: absolute; width: 600px; height: 600px; background: rgba(124, 58, 237, 0.2);
            filter: blur(100px); border-radius: 50%; top: -200px; left: -100px; z-index: 1;
        }
        .bg-glow-2 {
            position: absolute; width: 500px; height: 500px; background: rgba(5, 150, 105, 0.15);
            filter: blur(100px); border-radius: 50%; bottom: -100px; right: -100px; z-index: 1;
        }

        /* Glassmorphism Auth Box */
        .auth-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 40px;
            width: 100%;
            max-width: 420px;
            z-index: 10;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transform: translateY(30px);
            opacity: 0;
        }

        @keyframes slideUp {
            to { transform: translateY(0); opacity: 1; }
        }

        .auth-icon {
            width: 64px; height: 64px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; color: #fff;
            margin: 0 auto 20px;
        }

        h2 { font-size: 22px; font-weight: 700; margin-bottom: 8px; letter-spacing: -0.5px; }
        p { font-size: 14px; font-weight: 500; color: rgba(255,255,255,0.5); margin-bottom: 30px; }

        .pin-input-group {
            position: relative;
            margin-bottom: 24px;
        }
        .pin-input-group i {
            position: absolute;
            left: 16px; top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            font-size: 14px;
        }
        .pin-input {
            width: 100%;
            background: rgba(0,0,0,0.2);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            padding: 16px 16px 16px 44px;
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            outline: none;
            transition: 0.2s;
            text-align: center;
            letter-spacing: 12px;
        }
        .pin-input:focus {
            border-color: #7C3AED;
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.15);
        }
        .pin-input::placeholder { color: rgba(255,255,255,0.2); letter-spacing: normal; font-weight:400; }

        .btn-submit {
            width: 100%;
            background: #fff;
            color: #111827;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            box-shadow: 0 4px 12px rgba(255,255,255,0.1);
        }
        .btn-submit:hover { opacity: 0.9; transform: translateY(-2px); }

        .error-msg {
            color: #EF4444;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
            background: rgba(239, 68, 68, 0.1);
            padding: 10px;
            border-radius: 8px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 24px;
            color: rgba(255,255,255,0.4);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s;
        }
        .back-link:hover { color: #fff; }

    </style>
</head>
<body>

    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>

    <div class="auth-card">
        <div class="auth-icon"><i class="fas fa-fingerprint"></i></div>
        <h2>Restricted Finance Core</h2>
        <p>Please enter the administrative PIN to access the main wallet dashboard.</p>

        <?php if($error): ?>
            <div class="error-msg"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="pin-input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="pin" class="pin-input" placeholder="••••" autofocus required maxlength="8">
            </div>
            <button type="submit" class="btn-submit">Unlock Wallet</button>
        </form>

        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Return to Dashboard</a>
    </div>

</body>
</html>
