<?php
$pageTitle = 'Test SMTP';
require_once 'config.php';
requireLogin();

$currentUser = getCurrentUser();

include 'includes_header.php';
?>

<div class="card">
    <div class="card-header">
        <h3>Test SMTP Page</h3>
    </div>

    <p>Se vedi questa pagina, il sistema funziona!</p>

    <p>Utente: <?php echo h($currentUser['nome']); ?> (<?php echo h($currentUser['ruolo']); ?>)</p>

    <?php
    // Test query smtp_config
    $result = $db->query("SELECT COUNT(*) as count FROM smtp_config");
    $row = $result->fetchArray(SQLITE3_ASSOC);
    echo "<p>Tabella smtp_config: {$row['count']} record</p>";

    // Test query email_templates
    $result = $db->query("SELECT COUNT(*) as count FROM email_templates");
    $row = $result->fetchArray(SQLITE3_ASSOC);
    echo "<p>Tabella email_templates: {$row['count']} record</p>";

    // Test PHPMailer
    if (file_exists('PHPMailer/PHPMailer.php')) {
        echo "<p>✓ PHPMailer trovato</p>";
    } else {
        echo "<p>✗ PHPMailer NON trovato</p>";
    }
    ?>
</div>

<?php include 'includes_footer.php'; ?>
