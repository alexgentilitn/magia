
<?php
requireLogin();
$currentUser = getCurrentUser();

// Conta notifiche non lette
$stmt = $db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND letta = 0");
$stmt->bindValue(':user_id', $currentUser['id'], SQLITE3_INTEGER);
$result = $stmt->execute();
$notifiche_non_lette = $result->fetchArray(SQLITE3_ASSOC)['count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Dashboard'; ?> - AGstudio CRM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* === RESET E BASE === */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            /* Palette Professionale */
            --primary: #1a2332;
            --secondary: #2c3e50;
            --accent: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #3498db;

            /* Grigi */
            --gray-50: #f8f9fa;
            --gray-100: #f4f6f9;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #95a5a6;
            --gray-600: #7f8c8d;
            --gray-700: #6c757d;
            --gray-800: #495057;
            --gray-900: #2c3e50;

            /* Sidebar */
            --sidebar-bg: #1a2332;
            --sidebar-text: rgba(255,255,255,0.8);
            --sidebar-hover: rgba(255,255,255,0.1);
            --sidebar-active: #3498db;

            /* Typography */
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-size-base: 15px;

            /* Spacing */
            --sidebar-width: 260px;
            --topbar-height: 70px;
            --border-radius: 8px;
            --box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            --box-shadow-lg: 0 4px 12px rgba(0,0,0,0.1);
        }

        body {
            font-family: var(--font-family);
            font-size: var(--font-size-base);
            background: var(--gray-100);
            color: var(--gray-900);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
        }

        /* === SIDEBAR === */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            color: white;
            overflow-y: auto;
            transition: transform 0.3s;
            z-index: 1000;
            box-shadow: 2px 0 8px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-header h1 {
            font-size: 1.5em;
            margin-bottom: 3px;
            color: white;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .sidebar-header p {
            font-size: 0.8em;
            color: var(--sidebar-text);
            font-weight: 400;
        }

        .sidebar-nav {
            padding: 15px 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 13px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            font-weight: 500;
            font-size: 14px;
        }

        .nav-item:hover {
            background: var(--sidebar-hover);
            color: white;
            border-left-color: var(--sidebar-active);
        }

        .nav-item.active {
            background: rgba(52, 152, 219, 0.15);
            color: white;
            border-left-color: var(--sidebar-active);
        }

        .nav-item i {
            width: 22px;
            margin-right: 12px;
            font-size: 15px;
            text-align: center;
        }

        /* === MAIN CONTENT === */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 25px 30px;
            min-height: 100vh;
        }

        /* === TOPBAR === */
        .topbar {
            background: white;
            padding: 20px 30px;
            margin: -25px -30px 25px -30px;
            box-shadow: var(--box-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--gray-200);
        }

        .topbar-left h2 {
            font-size: 1.6em;
            color: var(--gray-900);
            font-weight: 600;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 15px;
            background: var(--gray-50);
            border-radius: var(--border-radius);
        }

        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .user-info span {
            font-weight: 500;
            color: var(--gray-800);
            font-size: 14px;
        }

        /* === BUTTONS === */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 20px;
            background: var(--accent);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .btn:hover {
            background: #2980b9;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
        }

        .btn-secondary {
            background: var(--gray-500);
        }

        .btn-secondary:hover {
            background: var(--gray-600);
        }

        .btn-success {
            background: var(--success);
        }

        .btn-success:hover {
            background: #229954;
        }

        .btn-danger {
            background: var(--danger);
        }

        .btn-danger:hover {
            background: #c0392b;
        }

        .btn-warning {
            background: var(--warning);
        }

        .btn-warning:hover {
            background: #e67e22;
        }

        /* === CARDS === */
        .card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
            margin-bottom: 20px;
            border: 1px solid var(--gray-200);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--gray-200);
        }

        .card-header h3 {
            font-size: 1.3em;
            color: var(--gray-900);
            font-weight: 600;
        }

        /* === STATS GRID === */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
            border-left: 3px solid var(--accent);
            box-shadow: var(--box-shadow);
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: var(--box-shadow-lg);
            transform: translateY(-2px);
        }

        .stat-card h4 {
            color: var(--gray-600);
            font-size: 0.8em;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-value {
            font-size: 2em;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 5px;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.85em;
            color: var(--gray-500);
        }

        /* === BADGES === */
        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge-in_corso { background: #e3f2fd; color: #1976d2; }
        .badge-pianificazione { background: #fff3e0; color: #f57c00; }
        .badge-completato { background: #e8f5e9; color: #388e3c; }
        .badge-sospeso { background: #fff8e1; color: #f9a825; }
        .badge-annullato { background: #f5f5f5; color: #616161; }

        .badge-alta { background: #ffebee; color: #c62828; }
        .badge-media { background: #fff3e0; color: #f57c00; }
        .badge-bassa { background: #f5f5f5; color: #616161; }

        .badge-in_attesa { background: #fff3e0; color: #f57c00; }
        .badge-pagato { background: #e8f5e9; color: #388e3c; }

        .badge-fattura { background: #ffebee; color: #c62828; }
        .badge-contratto { background: #e3f2fd; color: #1976d2; }
        .badge-preventivo { background: #fff3e0; color: #f57c00; }
        .badge-documento { background: #f5f5f5; color: #616161; }
        .badge-altro { background: #ede7f6; color: #5e35b1; }

        /* === TABLE === */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th {
            background: var(--gray-50);
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: var(--gray-700);
            border-bottom: 2px solid var(--gray-200);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--gray-200);
            color: var(--gray-800);
        }

        table tr:hover {
            background: var(--gray-50);
        }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1>AGstudio</h1>
            <p>Business CRM</p>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="clienti.php" class="nav-item"><i class="fas fa-building"></i> Clienti</a>
            <a href="leads.php" class="nav-item"><i class="fas fa-funnel-dollar"></i> Pipeline Vendite</a>
            <a href="progetti.php" class="nav-item"><i class="fas fa-briefcase"></i> Progetti</a>
            <a href="tasks.php" class="nav-item"><i class="fas fa-tasks"></i> Task</a>
            <a href="scadenze.php" class="nav-item"><i class="fas fa-calendar-alt"></i> Scadenze</a>
            <a href="calendar.php" class="nav-item"><i class="fas fa-calendar"></i> Calendario</a>
            <a href="documenti.php" class="nav-item"><i class="fas fa-folder"></i> Documenti</a>
            <a href="contratti.php" class="nav-item"><i class="fas fa-file-contract"></i> Contratti</a>
            <a href="progetto_spese.php" class="nav-item"><i class="fas fa-receipt"></i> Spese Progetto</a>
            <a href="time_tracking.php" class="nav-item"><i class="fas fa-clock"></i> Time Tracking</a>
            <a href="statistiche.php" class="nav-item"><i class="fas fa-chart-bar"></i> Statistiche</a>
            <a href="notifiche.php" class="nav-item">
                <i class="fas fa-bell"></i> Notifiche
                <?php if ($notifiche_non_lette > 0): ?>
                    <span class="badge badge-alta" style="margin-left: 8px; font-size: 0.75em;">
                        <?php echo $notifiche_non_lette; ?>
                    </span>
                <?php endif; ?>
            </a>
            <?php if ($currentUser['ruolo'] === 'admin'): ?>
                <a href="users_management.php" class="nav-item"><i class="fas fa-users-cog"></i> Gestione Utenti</a>
                <a href="alert_config.php" class="nav-item"><i class="fas fa-bell-on"></i> Configura Alert</a>
                <a href="smtp_settings.php" class="nav-item"><i class="fas fa-envelope"></i> Configurazione Email</a>
                <a href="email_templates.php" class="nav-item"><i class="fas fa-envelope-open-text"></i> Template Email</a>
            <?php endif; ?>
            <a href="impostazioni.php" class="nav-item"><i class="fas fa-cog"></i> Impostazioni</a>
            <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <h2><?php echo $pageTitle ?? 'Dashboard'; ?></h2>
            </div>
            <div class="topbar-right">
                <!-- Notifiche -->
                <a href="notifiche.php" style="position: relative; text-decoration: none; color: var(--gray-700); font-size: 1.3em; padding: 10px;">
                    <i class="fas fa-bell"></i>
                    <?php if ($notifiche_non_lette > 0): ?>
                        <span style="
                            position: absolute;
                            top: 5px;
                            right: 5px;
                            background: var(--danger);
                            color: white;
                            font-size: 0.6em;
                            padding: 2px 6px;
                            border-radius: 10px;
                            font-weight: 600;
                            min-width: 18px;
                            text-align: center;
                        "><?php echo $notifiche_non_lette; ?></span>
                    <?php endif; ?>
                </a>

                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($currentUser['nome'], 0, 1)); ?>
                    </div>
                    <span><?php echo h($currentUser['nome']); ?></span>
                </div>
            </div>
        </div>
