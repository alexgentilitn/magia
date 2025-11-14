<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;

/**
 * Model: Impostazione
 * Gestisce le configurazioni di sistema (SMTP, generale, ecc.)
 */
class Impostazione extends Model
{
    use HasFactory;

    protected $table = 'impostazioni';

    protected $fillable = [
        'chiave',
        'valore',
        'gruppo',
        'tipo',
        'descrizione',
        'criptata',
    ];

    protected $casts = [
        'criptata' => 'boolean',
    ];

    /**
     * Ottieni il valore di un'impostazione
     * Supporta cache per performance
     */
    public static function get(string $chiave, $default = null)
    {
        return Cache::remember("impostazione_{$chiave}", 3600, function () use ($chiave, $default) {
            $impostazione = static::where('chiave', $chiave)->first();

            if (!$impostazione) {
                return $default;
            }

            // Se è criptata, decripta
            if ($impostazione->criptata && $impostazione->valore) {
                try {
                    return Crypt::decryptString($impostazione->valore);
                } catch (\Exception $e) {
                    return $default;
                }
            }

            return $impostazione->valore;
        });
    }

    /**
     * Imposta il valore di un'impostazione
     * Cripta automaticamente se necessario
     */
    public static function set(string $chiave, $valore): bool
    {
        $impostazione = static::where('chiave', $chiave)->first();

        if (!$impostazione) {
            return false;
        }

        // Se deve essere criptata, cripta
        if ($impostazione->criptata && $valore) {
            $valore = Crypt::encryptString($valore);
        }

        $impostazione->valore = $valore;
        $saved = $impostazione->save();

        // Pulisci cache
        Cache::forget("impostazione_{$chiave}");

        return $saved;
    }

    /**
     * Ottieni tutte le impostazioni di un gruppo
     */
    public static function getGruppo(string $gruppo): array
    {
        $impostazioni = static::where('gruppo', $gruppo)->get();
        $result = [];

        foreach ($impostazioni as $impostazione) {
            $valore = $impostazione->valore;

            // Se è criptata, decripta
            if ($impostazione->criptata && $valore) {
                try {
                    $valore = Crypt::decryptString($valore);
                } catch (\Exception $e) {
                    $valore = null;
                }
            }

            $result[$impostazione->chiave] = [
                'valore' => $valore,
                'tipo' => $impostazione->tipo,
                'descrizione' => $impostazione->descrizione,
                'criptata' => $impostazione->criptata,
            ];
        }

        return $result;
    }

    /**
     * Aggiorna multiple impostazioni di un gruppo
     */
    public static function updateGruppo(string $gruppo, array $dati): bool
    {
        $success = true;

        foreach ($dati as $chiave => $valore) {
            $impostazione = static::where('chiave', $chiave)
                ->where('gruppo', $gruppo)
                ->first();

            if ($impostazione) {
                // Se deve essere criptata, cripta
                if ($impostazione->criptata && $valore) {
                    $valore = Crypt::encryptString($valore);
                }

                $impostazione->valore = $valore;
                if (!$impostazione->save()) {
                    $success = false;
                }

                // Pulisci cache
                Cache::forget("impostazione_{$chiave}");
            }
        }

        return $success;
    }

    /**
     * Ottieni configurazione SMTP completa
     */
    public static function getSmtpConfig(): array
    {
        return [
            'host' => static::get('smtp_host', 'smtp.gmail.com'),
            'port' => static::get('smtp_porta', '587'),
            'username' => static::get('smtp_username', ''),
            'password' => static::get('smtp_password', ''),
            'encryption' => static::get('smtp_encryption', 'tls'),
            'from_address' => static::get('mail_from_address', 'noreply@magiadonna.it'),
            'from_name' => static::get('mail_from_name', 'MA.GIA DONNA'),
        ];
    }

    /**
     * Applica configurazione SMTP a Laravel Mail
     */
    public static function applySmtpConfig(): void
    {
        $config = static::getSmtpConfig();

        config([
            'mail.mailers.smtp.host' => $config['host'],
            'mail.mailers.smtp.port' => $config['port'],
            'mail.mailers.smtp.username' => $config['username'],
            'mail.mailers.smtp.password' => $config['password'],
            'mail.mailers.smtp.encryption' => $config['encryption'],
            'mail.from.address' => $config['from_address'],
            'mail.from.name' => $config['from_name'],
        ]);
    }
}
