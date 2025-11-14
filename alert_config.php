<?php
$pageTitle = 'Configurazione Alert';
require_once 'config.php';
requireLogin();

// Solo admin può configurare gli alert
$currentUser = getCurrentUser();
if ($currentUser['ruolo'] !== 'admin') {
    die('Accesso negato. Solo gli amministratori possono configurare gli alert.');
}

// Gestione form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_config'])) {
        $tipo = $_POST['tipo'] ?? '';
        $attivo = isset($_POST['attivo']) ? 1 : 0;
        $giorni_anticipo = (int)($_POST['giorni_anticipo'] ?? 7);
        $email_destinatario = $_POST['email_destinatario'] ?? '';

        // Verifica se esiste già configurazione per questo tipo
        $stmt = $db->prepare("SELECT id FROM alert_config WHERE tipo = :tipo");
        $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
        $result = $stmt->execute();
        $existing = $result->fetchArray(SQLITE3_ASSOC);

        if ($existing) {
            // UPDATE
            $stmt = $db->prepare("
                UPDATE alert_config SET
                    attivo = :attivo,
                    giorni_anticipo = :giorni_anticipo,
                    email_destinatario = :email_destinatario
                WHERE tipo = :tipo
            ");
        } else {
            // INSERT
            $stmt = $db->prepare("
                INSERT INTO alert_config (tipo, attivo, giorni_anticipo, email_destinatario)
                VALUES (:tipo, :attivo, :giorni_anticipo, :email_destinatario)
            ");
        }

        $stmt->bindValue(':tipo', $tipo, SQLITE3_TEXT);
        $stmt->bindValue(':attivo', $attivo, SQLITE3_INTEGER);
        $stmt->bindValue(':giorni_anticipo', $giorni_anticipo, SQLITE3_INTEGER);
        $stmt->bindValue(':email_destinatario', $email_destinatario, SQLITE3_TEXT);
        $stmt->execute();

        $message = 'Configurazione salvata con successo!';
        logActivity('update_alert_config', 'alert_config', null, "Configurazione alert: $tipo");
    }
}

// Carica configurazioni esistenti
$configs = [];
$result = $db->query("SELECT * FROM alert_config ORDER BY tipo");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $configs[$row['tipo']] = $row;
}

// Tipi di alert disponibili
$tipi_alert = [
    'scadenza_imminente' => 'Scadenza Imminente',
    'scadenza_scaduta' => 'Scadenza Scaduta',
    'progetto_ritardo' => 'Progetto in Ritardo',
    'documento_scadenza' => 'Documento in Scadenza'
];

include 'includes_header.php';
?>

<?php if (isset($message)): ?>
    <div class="card" style="background: #d4edda; border-color: #c3e6cb; color: #155724; margin-bottom: 20px;">
        <strong>✓</strong> <?php echo h($message); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-bell"></i> Configurazione Sistema Alert</h3>
    </div>

    <p style="color: var(--gray-600); margin-bottom: 30px;">
        Configura gli alert automatici per ricevere notifiche via email per scadenze imminenti, progetti in ritardo e altri eventi importanti.
        Gli alert vengono verificati ogni ora dal sistema (richiede configurazione cron).
    </p>

    <?php foreach ($tipi_alert as $tipo_key => $tipo_label): ?>
        <?php
        $config = $configs[$tipo_key] ?? [
            'attivo' => 0,
            'giorni_anticipo' => 7,
            'email_destinatario' => $currentUser['email'] ?? ''
        ];
        ?>

        <form method="POST" style="border: 1px solid var(--gray-200); padding: 25px; border-radius: var(--border-radius); margin-bottom: 20px; background: var(--gray-50);">
            <input type="hidden" name="tipo" value="<?php echo $tipo_key; ?>">

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <div>
                    <h4 style="margin-bottom: 5px; font-size: 1.1em; color: var(--gray-900);">
                        <?php echo h($tipo_label); ?>
                    </h4>
                    <p style="color: var(--gray-600); font-size: 0.9em; margin: 0;">
                        <?php
                        switch($tipo_key) {
                            case 'scadenza_imminente':
                                echo 'Notifica quando una scadenza sta per arrivare';
                                break;
                            case 'scadenza_scaduta':
                                echo 'Notifica quando una scadenza è stata superata';
                                break;
                            case 'progetto_ritardo':
                                echo 'Notifica quando un progetto supera la data di consegna prevista';
                                break;
                            case 'documento_scadenza':
                                echo 'Notifica per documenti con data di scadenza imminente';
                                break;
                        }
                        ?>
                    </p>
                </div>
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                    <input type="checkbox" name="attivo" <?php echo $config['attivo'] ? 'checked' : ''; ?>
                           style="width: 20px; height: 20px;">
                    <span style="font-weight: 600; color: var(--gray-700);">Attivo</span>
                </label>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 20px;">
                <?php if (in_array($tipo_key, ['scadenza_imminente', 'documento_scadenza'])): ?>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 500; color: var(--gray-700);">
                            Giorni di Anticipo
                        </label>
                        <input type="number" name="giorni_anticipo" value="<?php echo $config['giorni_anticipo']; ?>"
                               min="1" max="30" required
                               style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
                        <small style="color: var(--gray-500);">Notifica X giorni prima della scadenza</small>
                    </div>
                <?php else: ?>
                    <div></div>
                <?php endif; ?>

                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 500; color: var(--gray-700);">
                        Email Destinatario
                    </label>
                    <input type="email" name="email_destinatario" value="<?php echo h($config['email_destinatario']); ?>"
                           required
                           style="width: 100%; padding: 10px; border: 1px solid var(--gray-300); border-radius: 6px;">
                    <small style="color: var(--gray-500);">Indirizzo email che riceverà gli alert</small>
                </div>
            </div>

            <button type="submit" name="save_config" class="btn btn-success">
                <i class="fas fa-save"></i> Salva Configurazione
            </button>
        </form>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-info-circle"></i> Configurazione Cron</h3>
    </div>

    <p style="margin-bottom: 15px;">
        Per attivare il sistema di alert automatici, configura un cron job sul server che esegua il file <code>check_alerts.php</code> ogni ora.
    </p>

    <div style="background: var(--gray-900); color: #00ff00; padding: 15px; border-radius: 6px; font-family: 'Courier New', monospace; margin-bottom: 15px;">
        0 * * * * /usr/bin/php <?php echo __DIR__; ?>/check_alerts.php
    </div>

    <p style="color: var(--gray-600); font-size: 0.9em;">
        <strong>Nota:</strong> Questo comando deve essere aggiunto al crontab del server. Contatta l'amministratore di sistema per configurarlo.
    </p>

    <?php
    // Mostra ultima esecuzione per ogni tipo
    $ultima_esecuzione = [];
    foreach ($configs as $tipo => $config) {
        if ($config['ultima_esecuzione']) {
            $ultima_esecuzione[$tipo] = $config['ultima_esecuzione'];
        }
    }
    ?>

    <?php if (!empty($ultima_esecuzione)): ?>
        <h4 style="margin-top: 25px; margin-bottom: 10px; color: var(--gray-800);">Ultime Esecuzioni</h4>
        <table>
            <thead>
                <tr>
                    <th>Tipo Alert</th>
                    <th>Ultima Verifica</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ultima_esecuzione as $tipo => $data): ?>
                    <tr>
                        <td><?php echo h($tipi_alert[$tipo] ?? $tipo); ?></td>
                        <td><?php echo formatDateTime($data, 'd/m/Y H:i'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div style="margin-top: 20px;">
    <a href="notifiche.php" class="btn">
        <i class="fas fa-bell"></i> Vedi Notifiche
    </a>
    <a href="dashboard.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Torna alla Dashboard
    </a>
</div>

<?php include 'includes_footer.php'; ?>
