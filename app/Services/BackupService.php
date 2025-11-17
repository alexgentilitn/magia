<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupService
{
    /**
     * Directory dove salvare i backup
     */
    const BACKUP_DIR = 'backups';

    /**
     * Numero massimo di backup da mantenere
     */
    const MAX_BACKUPS = 30;

    /**
     * Esegue un backup completo del database
     *
     * @param string|null $description Descrizione opzionale del backup
     * @return array ['success' => bool, 'message' => string, 'filename' => string|null]
     */
    public function createBackup(?string $description = null): array
    {
        try {
            // Verifica disponibilità mysqldump
            if (!$this->isMysqldumpAvailable()) {
                return [
                    'success' => false,
                    'message' => 'mysqldump non è disponibile su questo server. Contatta l\'amministratore di sistema.',
                    'filename' => null
                ];
            }

            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            $filepath = storage_path('app/' . self::BACKUP_DIR . '/' . $filename);

            // Configurazione database
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $port = config('database.connections.mysql.port', 3306);

            // Comando mysqldump
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($database),
                escapeshellarg($filepath)
            );

            // Esegui il backup
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                Log::error('Errore backup database', [
                    'output' => $output,
                    'return_code' => $returnCode
                ]);
                return [
                    'success' => false,
                    'message' => 'Errore durante il backup: ' . implode("\n", $output),
                    'filename' => null
                ];
            }

            // Verifica che il file sia stato creato e non sia vuoto
            if (!file_exists($filepath) || filesize($filepath) === 0) {
                return [
                    'success' => false,
                    'message' => 'Il file di backup è vuoto o non è stato creato',
                    'filename' => null
                ];
            }

            // Salva metadati del backup
            $this->saveBackupMetadata($filename, $description);

            // Pulisci i backup vecchi
            $this->cleanOldBackups();

            $size = $this->formatBytes(filesize($filepath));

            Log::info('Backup database creato con successo', [
                'filename' => $filename,
                'size' => $size
            ]);

            return [
                'success' => true,
                'message' => "Backup creato con successo: {$filename} ({$size})",
                'filename' => $filename
            ];

        } catch (\Exception $e) {
            Log::error('Eccezione durante backup database', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage(),
                'filename' => null
            ];
        }
    }

    /**
     * Ottiene la lista di tutti i backup disponibili
     *
     * @return array
     */
    public function listBackups(): array
    {
        $backupPath = storage_path('app/' . self::BACKUP_DIR);

        if (!is_dir($backupPath)) {
            return [];
        }

        $files = glob($backupPath . '/backup_*.sql');
        $backups = [];

        foreach ($files as $file) {
            $filename = basename($file);
            $metadata = $this->getBackupMetadata($filename);

            $backups[] = [
                'filename' => $filename,
                'size' => filesize($file),
                'size_human' => $this->formatBytes(filesize($file)),
                'created_at' => filemtime($file),
                'created_at_human' => Carbon::createFromTimestamp(filemtime($file))->format('d/m/Y H:i:s'),
                'description' => $metadata['description'] ?? null,
                'age_days' => Carbon::createFromTimestamp(filemtime($file))->diffInDays(now())
            ];
        }

        // Ordina per data più recente
        usort($backups, function($a, $b) {
            return $b['created_at'] - $a['created_at'];
        });

        return $backups;
    }

    /**
     * Elimina un backup specifico
     *
     * @param string $filename
     * @return bool
     */
    public function deleteBackup(string $filename): bool
    {
        try {
            $filepath = storage_path('app/' . self::BACKUP_DIR . '/' . $filename);
            $metadataFile = storage_path('app/' . self::BACKUP_DIR . '/.metadata_' . $filename . '.json');

            if (file_exists($filepath)) {
                unlink($filepath);
            }

            if (file_exists($metadataFile)) {
                unlink($metadataFile);
            }

            Log::info('Backup eliminato', ['filename' => $filename]);

            return true;
        } catch (\Exception $e) {
            Log::error('Errore eliminazione backup', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Download di un backup
     *
     * @param string $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|null
     */
    public function downloadBackup(string $filename)
    {
        $filepath = storage_path('app/' . self::BACKUP_DIR . '/' . $filename);

        if (!file_exists($filepath)) {
            return null;
        }

        return response()->download($filepath);
    }

    /**
     * Pulisce i backup più vecchi mantenendo solo gli ultimi MAX_BACKUPS
     *
     * @return int Numero di backup eliminati
     */
    public function cleanOldBackups(): int
    {
        $backups = $this->listBackups();
        $deleted = 0;

        if (count($backups) > self::MAX_BACKUPS) {
            $toDelete = array_slice($backups, self::MAX_BACKUPS);

            foreach ($toDelete as $backup) {
                if ($this->deleteBackup($backup['filename'])) {
                    $deleted++;
                }
            }

            Log::info('Backup vecchi eliminati', ['count' => $deleted]);
        }

        return $deleted;
    }

    /**
     * Ottiene statistiche sui backup
     *
     * @return array
     */
    public function getBackupStats(): array
    {
        $backups = $this->listBackups();
        $totalSize = 0;

        foreach ($backups as $backup) {
            $totalSize += $backup['size'];
        }

        return [
            'total_backups' => count($backups),
            'total_size' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize),
            'oldest_backup' => !empty($backups) ? end($backups)['created_at_human'] : null,
            'newest_backup' => !empty($backups) ? $backups[0]['created_at_human'] : null,
        ];
    }

    /**
     * Salva i metadati di un backup
     *
     * @param string $filename
     * @param string|null $description
     * @return void
     */
    private function saveBackupMetadata(string $filename, ?string $description): void
    {
        $metadataFile = storage_path('app/' . self::BACKUP_DIR . '/.metadata_' . $filename . '.json');

        $metadata = [
            'filename' => $filename,
            'description' => $description,
            'created_at' => now()->toDateTimeString(),
            'created_by' => auth()->user()->nome ?? 'System'
        ];

        file_put_contents($metadataFile, json_encode($metadata, JSON_PRETTY_PRINT));
    }

    /**
     * Ottiene i metadati di un backup
     *
     * @param string $filename
     * @return array
     */
    private function getBackupMetadata(string $filename): array
    {
        $metadataFile = storage_path('app/' . self::BACKUP_DIR . '/.metadata_' . $filename . '.json');

        if (!file_exists($metadataFile)) {
            return [];
        }

        $content = file_get_contents($metadataFile);
        return json_decode($content, true) ?? [];
    }

    /**
     * Formatta i byte in formato leggibile
     *
     * @param int $bytes
     * @return string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return round($bytes / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    /**
     * Verifica se mysqldump è disponibile
     *
     * @return bool
     */
    public function isMysqldumpAvailable(): bool
    {
        exec('which mysqldump', $output, $returnCode);
        return $returnCode === 0;
    }
}
