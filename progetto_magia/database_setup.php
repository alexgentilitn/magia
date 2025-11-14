<?php
/**
 * Setup Database - Progetto Magia
 *
 * Script per l'inizializzazione del database
 * IMPORTANTE: Eseguire questo file UNA SOLA VOLTA dopo l'installazione
 */

require_once 'config.php';

// Sicurezza: verifica se il setup è già stato eseguito
$check_table = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount();
if ($check_table > 0) {
    die("⚠️ Il database è già stato inizializzato. Se vuoi reinstallarlo, elimina prima le tabelle.");
}

try {
    echo "<h2>🚀 Inizializzazione Database Progetto Magia</h2>";

    // Tabella Utenti
    echo "<p>Creazione tabella <strong>users</strong>...</p>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) UNIQUE NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100),
            role ENUM('admin', 'user') DEFAULT 'user',
            active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            INDEX idx_username (username),
            INDEX idx_email (email)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabella <strong>users</strong> creata<br>";

    // Tabella Progetti
    echo "<p>Creazione tabella <strong>projects</strong>...</p>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS projects (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(200) NOT NULL,
            description TEXT,
            status ENUM('draft', 'active', 'completed', 'archived') DEFAULT 'draft',
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_status (status),
            INDEX idx_created_by (created_by)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabella <strong>projects</strong> creata<br>";

    // Tabella Logs
    echo "<p>Creazione tabella <strong>activity_logs</strong>...</p>";
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS activity_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            action VARCHAR(100) NOT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_user_id (user_id),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    echo "✅ Tabella <strong>activity_logs</strong> creata<br>";

    // Inserimento utente admin di default
    echo "<p>Creazione utente amministratore...</p>";
    $default_password = password_hash('admin123', PASSWORD_BCRYPT);
    $pdo->exec("
        INSERT INTO users (username, email, password, full_name, role)
        VALUES ('admin', 'admin@example.com', '$default_password', 'Amministratore', 'admin')
    ");
    echo "✅ Utente admin creato<br>";

    echo "<hr>";
    echo "<h3>✅ Database inizializzato con successo!</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0;'>";
    echo "<strong>⚠️ Credenziali di accesso predefinite:</strong><br>";
    echo "Username: <code>admin</code><br>";
    echo "Password: <code>admin123</code><br><br>";
    echo "<strong style='color: #d32f2f;'>IMPORTANTE: Cambia la password dopo il primo accesso!</strong>";
    echo "</div>";

    echo "<p><strong>Prossimi passi:</strong></p>";
    echo "<ol>";
    echo "<li>Modifica le credenziali del database in <code>config.php</code></li>";
    echo "<li>Cambia la password dell'admin</li>";
    echo "<li><strong>Elimina o rinomina questo file (database_setup.php) per sicurezza</strong></li>";
    echo "<li>Inizia a utilizzare l'applicazione</li>";
    echo "</ol>";

} catch (PDOException $e) {
    echo "<div style='background: #ffebee; padding: 15px; border-left: 4px solid #f44336;'>";
    echo "<strong>❌ Errore durante l'inizializzazione:</strong><br>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
    error_log("Errore database setup: " . $e->getMessage());
}
