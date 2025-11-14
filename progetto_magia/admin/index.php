<?php
/**
 * Progetto Magia Donna - Area Admin
 *
 * Dashboard amministrativa
 */

require_once '../config.php';

// Verifica autenticazione
if (!is_logged_in()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .dashboard-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .dashboard-card h3 {
            color: #667eea;
            margin-bottom: 15px;
        }
        .dashboard-card .number {
            font-size: 3em;
            font-weight: bold;
            color: #764ba2;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: white;
        }
        .nav-menu {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-menu a {
            margin-right: 20px;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }
        .nav-menu a:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Dashboard Amministrativa</h1>
            <p>Benvenuto, <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin'; ?></p>
        </div>

        <div class="nav-menu">
            <a href="index.php">🏠 Dashboard</a>
            <a href="projects.php">📁 Progetti</a>
            <a href="users.php">👥 Utenti</a>
            <a href="settings.php">⚙️ Impostazioni</a>
            <a href="logout.php" style="float: right; color: #f44336;">🚪 Logout</a>
        </div>

        <div class="dashboard">
            <div class="dashboard-card">
                <h3>📊 Progetti Totali</h3>
                <div class="number">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) FROM projects");
                        echo $stmt->fetchColumn();
                    } catch (PDOException $e) {
                        echo '0';
                    }
                    ?>
                </div>
            </div>

            <div class="dashboard-card">
                <h3>✅ Progetti Attivi</h3>
                <div class="number">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) FROM projects WHERE status = 'active'");
                        echo $stmt->fetchColumn();
                    } catch (PDOException $e) {
                        echo '0';
                    }
                    ?>
                </div>
            </div>

            <div class="dashboard-card">
                <h3>👥 Utenti</h3>
                <div class="number">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                        echo $stmt->fetchColumn();
                    } catch (PDOException $e) {
                        echo '0';
                    }
                    ?>
                </div>
            </div>

            <div class="dashboard-card">
                <h3>📝 Attività Oggi</h3>
                <div class="number">
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT COUNT(*) FROM activity_logs WHERE DATE(created_at) = CURDATE()");
                        echo $stmt->fetchColumn();
                    } catch (PDOException $e) {
                        echo '0';
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="card mt-2">
            <h3>📋 Ultime Attività</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Utente</th>
                        <th>Azione</th>
                        <th>Descrizione</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $pdo->query("
                            SELECT al.*, u.full_name, u.username
                            FROM activity_logs al
                            LEFT JOIN users u ON al.user_id = u.id
                            ORDER BY al.created_at DESC
                            LIMIT 10
                        ");
                        $logs = $stmt->fetchAll();

                        if (count($logs) > 0) {
                            foreach ($logs as $log) {
                                echo "<tr>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($log['created_at'])) . "</td>";
                                echo "<td>" . htmlspecialchars($log['full_name'] ?? $log['username'] ?? 'N/A') . "</td>";
                                echo "<td>" . htmlspecialchars($log['action']) . "</td>";
                                echo "<td>" . htmlspecialchars($log['description']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' style='text-align: center;'>Nessuna attività registrata</td></tr>";
                        }
                    } catch (PDOException $e) {
                        echo "<tr><td colspan='4' style='text-align: center;'>Errore nel caricamento delle attività</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>
