<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== TEST SMTP SETTINGS PAGE ===\n\n";

// Test 1: Config.php
echo "1. Testing config.php inclusion...\n";
try {
    require_once 'config.php';
    echo "   ✓ config.php loaded\n\n";
} catch (Throwable $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n\n";
    exit;
}

// Test 2: Session and Login
echo "2. Testing session and login functions...\n";
try {
    if (!isset($_SESSION['user_id'])) {
        echo "   ! No user logged in (expected in CLI)\n";
        $_SESSION['user_id'] = 1; // Simulate login
        echo "   ✓ Simulated user_id = 1\n";
    }
    $currentUser = getCurrentUser();
    echo "   ✓ getCurrentUser() works\n";
    echo "   User: " . $currentUser['nome'] . " (" . $currentUser['ruolo'] . ")\n\n";
} catch (Throwable $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n\n";
    exit;
}

// Test 3: Database tables
echo "3. Testing database tables...\n";
try {
    $tables_to_check = ['smtp_config', 'email_templates', 'email_queue'];
    foreach ($tables_to_check as $table) {
        $result = $db->query("SELECT COUNT(*) as count FROM $table");
        $row = $result->fetchArray(SQLITE3_ASSOC);
        echo "   ✓ Table '$table' exists (rows: {$row['count']})\n";
    }
    echo "\n";
} catch (Throwable $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n\n";
    exit;
}

// Test 4: Helper functions
echo "4. Testing helper functions...\n";
try {
    $test = h("<script>test</script>");
    echo "   ✓ h() function works\n";

    $test = formatDateTime(date('Y-m-d H:i:s'));
    echo "   ✓ formatDateTime() function works\n";

    $test = formatFileSize(1024);
    echo "   ✓ formatFileSize() function works\n";

    logActivity('test', 'test', null, 'Test activity');
    echo "   ✓ logActivity() function works\n\n";
} catch (Throwable $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n\n";
}

// Test 5: PHPMailer
echo "5. Testing PHPMailer availability...\n";
if (file_exists('PHPMailer/PHPMailer.php')) {
    echo "   ✓ PHPMailer/PHPMailer.php found\n";
    try {
        require_once 'PHPMailer/PHPMailer.php';
        require_once 'PHPMailer/SMTP.php';
        require_once 'PHPMailer/Exception.php';
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        echo "   ✓ PHPMailer instantiated successfully\n\n";
    } catch (Throwable $e) {
        echo "   ✗ ERROR: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "   ✗ PHPMailer not found\n\n";
}

// Test 6: Try to include smtp_settings.php
echo "6. Testing smtp_settings.php inclusion...\n";
try {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    ob_start();
    include 'smtp_settings.php';
    $output = ob_get_clean();
    echo "   ✓ smtp_settings.php included successfully\n";
    echo "   Output length: " . strlen($output) . " bytes\n\n";
} catch (Throwable $e) {
    ob_end_clean();
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n\n";
}

// Test 7: Try to include email_templates.php
echo "7. Testing email_templates.php inclusion...\n";
try {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    ob_start();
    include 'email_templates.php';
    $output = ob_get_clean();
    echo "   ✓ email_templates.php included successfully\n";
    echo "   Output length: " . strlen($output) . " bytes\n\n";
} catch (Throwable $e) {
    ob_end_clean();
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n\n";
}

echo "=== TEST COMPLETED ===\n";
?>
