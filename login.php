<?php
require_once 'config.php';

// Se già loggato, redirect a dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$setup_required = false;

// Verifica se il database ha utenti
try {
    $result = $db->query("SELECT COUNT(*) as count FROM users");
    $row = $result->fetchArray(SQLITE3_ASSOC);
    if ($row['count'] == 0) {
        $setup_required = true;
    }
} catch (Exception $e) {
    $setup_required = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username AND attivo = 1");
        $stmt->bindValue(':username', $username, SQLITE3_TEXT);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['ruolo'] = $user['ruolo'];

            // Aggiorna ultimo accesso
            $stmt = $db->prepare("UPDATE users SET ultimo_accesso = datetime('now') WHERE id = :id");
            $stmt->bindValue(':id', $user['id'], SQLITE3_INTEGER);
            $stmt->execute();

            logActivity('login', 'user', $user['id']);

            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Credenziali non valide';
        }
    } else {
        $error = 'Compila tutti i campi';
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AGstudio CRM</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            padding: 50px;
            width: 100%;
            max-width: 440px;
            border: 1px solid #e9ecef;
        }
        .logo {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo h1 {
            font-size: 2.2em;
            color: #1a2332;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .logo p {
            color: #7f8c8d;
            font-size: 0.95em;
            font-weight: 500;
        }
        .form-group {
            margin-bottom: 22px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
            font-size: 14px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 13px 15px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.2s;
            font-family: inherit;
        }
        input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
        }
        .btn:hover {
            background: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        .error {
            background: #fff5f5;
            border-left: 3px solid #e74c3c;
            padding: 14px 16px;
            margin-bottom: 20px;
            border-radius: 6px;
            color: #c0392b;
            font-size: 14px;
            font-weight: 500;
        }
        .info-box {
            margin-top: 30px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }
        .info-box strong { color: #333; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>AGstudio</h1>
            <p>Sistema di Gestione Clienti e Progetti</p>
        </div>

        <?php if ($setup_required): ?>
            <div class="error">
                <strong>⚠️ Database non inizializzato</strong><br>
                <p style="margin: 10px 0 0;">Prima di effettuare il login, devi inizializzare il database.</p>
                <a href="setup.php" style="color: #c00; font-weight: 600; text-decoration: underline;">
                    👉 Clicca qui per eseguire il setup
                </a>
            </div>
        <?php elseif ($error): ?>
            <div class="error"><?php echo h($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Accedi</button>
        </form>

        <div class="info-box">
            <strong>Demo:</strong> admin / admin123
        </div>
    </div>
</body>
</html>
