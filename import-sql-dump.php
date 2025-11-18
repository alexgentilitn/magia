<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\JsonDatabase;

echo "🔄 IMPORTAZIONE DUMP SQL IN DATABASE JSON\n";
echo "==========================================\n\n";

/**
 * Dati estratti dal dump SQL MySQL
 * Dump: agstudiodiital_magia - 14 Nov 2025
 */

// ==========================================
// TABELLA: utenti
// ==========================================
echo "📊 Importazione UTENTI...\n";

$utenti = [
    [
        'id' => 1,
        'email' => 'Informatica.gentili@gmail.com',
        'nome' => 'Alex',
        'cognome' => 'Gentili',
        'telefono' => '3491099145',
        'tipo_utente' => 'amministratore',
        'ruolo_id' => 1,
        'attivo' => true,
        'email_verificata' => true,
        'email_verificata_il' => '2025-11-09 15:55:03',
        'ultimo_accesso' => '2025-11-14 20:26:04',
        'ultimo_ip' => '95.248.167.162',
        'created_at' => '2025-11-09 15:55:03',
        'updated_at' => '2025-11-14 20:26:04'
    ],
    [
        'id' => 2,
        'email' => 'giulia.gentili2019@gmail.com',
        'nome' => 'Giulia',
        'cognome' => 'Gentili',
        'telefono' => '3478445159',
        'tipo_utente' => 'cliente',
        'ruolo_id' => null,
        'attivo' => true,
        'email_verificata' => false,
        'created_at' => '2025-11-09 15:14:29',
        'updated_at' => '2025-11-12 14:01:23'
    ],
    [
        'id' => 9,
        'email' => 'support@agstudio.digital',
        'nome' => 'Maria',
        'cognome' => 'Rossi',
        'telefono' => '3491099145',
        'tipo_utente' => 'professionista',
        'ruolo_id' => null,
        'attivo' => true,
        'email_verificata' => false,
        'created_at' => '2025-11-13 09:27:30',
        'updated_at' => '2025-11-13 09:39:12'
    ],
    [
        'id' => 11,
        'email' => 'cinziatonelli83@gmail.com',
        'nome' => 'Cinzia',
        'cognome' => 'Tonelli',
        'telefono' => '3478445159',
        'tipo_utente' => 'professionista',
        'ruolo_id' => null,
        'attivo' => true,
        'email_verificata' => false,
        'created_at' => '2025-11-13 09:59:18',
        'updated_at' => '2025-11-13 09:59:18'
    ]
];

JsonDatabase::createTable('utenti');
foreach ($utenti as $utente) {
    JsonDatabase::insert('utenti', $utente);
}
echo "   ✅ Importati " . count($utenti) . " utenti\n\n";

// ==========================================
// TABELLA: clienti
// ==========================================
echo "📊 Importazione CLIENTI...\n";

$clienti = [
    [
        'id' => 1,
        'utente_id' => 2,
        'nome' => 'Giulia',
        'cognome' => 'Gentili',
        'codice_fiscale' => 'GNTLXA74S20L378C',
        'indirizzo' => 'Strada di torchio 13',
        'citta' => 'Civezzano',
        'provincia' => 'TN',
        'cap' => '38045',
        'nazione' => 'Italia',
        'telefono_fisso' => '0461123456',
        'telefono_mobile' => '3478445159',
        'email' => 'giulia.gentili2019@gmail.com',
        'data_nascita' => '2019-10-12',
        'sesso' => 'F',
        'obiettivi_personali' => null,
        'note_mediche' => null,
        'certificato_medico' => false,
        'programma_attuale' => null,
        'data_iscrizione' => '2025-11-09',
        'stato_programma' => 'attivo',
        'consenso_privacy' => false,
        'consenso_marketing' => false,
        'consenso_foto' => false,
        'note_interne' => 'Per ora nessuna nota, questo è il primo cliente test..ggg',
        'stato_cliente' => 'attivo',
        'codice_cliente' => 'CL00001',
        'codice_referral' => '1523A1CD',
        'created_at' => '2025-11-09 15:14:29',
        'updated_at' => '2025-11-12 14:01:23'
    ]
];

JsonDatabase::createTable('clienti');
foreach ($clienti as $cliente) {
    JsonDatabase::insert('clienti', $cliente);
}
echo "   ✅ Importati " . count($clienti) . " clienti\n\n";

// ==========================================
// TABELLA: ruoli
// ==========================================
echo "📊 Importazione RUOLI...\n";

$ruoli = [
    [
        'id' => 1,
        'nome' => 'Super Admin',
        'slug' => 'super-admin',
        'descrizione' => 'Accesso completo a tutte le funzionalità del sistema',
        'livello' => 1,
        'attivo' => true,
        'created_at' => '2025-11-11 16:50:02',
        'updated_at' => '2025-11-11 16:50:02'
    ],
    [
        'id' => 2,
        'nome' => 'Moderatore',
        'slug' => 'moderatore',
        'descrizione' => 'Gestione clienti, programmi, corsi e contenuti',
        'livello' => 2,
        'attivo' => true,
        'created_at' => '2025-11-11 16:50:02',
        'updated_at' => '2025-11-11 16:50:02'
    ],
    [
        'id' => 3,
        'nome' => 'Collaboratore',
        'slug' => 'collaboratore',
        'descrizione' => 'Accesso limitato a visualizzazione e supporto clienti',
        'livello' => 3,
        'attivo' => true,
        'created_at' => '2025-11-11 16:50:02',
        'updated_at' => '2025-11-11 16:50:02'
    ]
];

JsonDatabase::createTable('ruoli');
foreach ($ruoli as $ruolo) {
    JsonDatabase::insert('ruoli', $ruolo);
}
echo "   ✅ Importati " . count($ruoli) . " ruoli\n\n";

// ==========================================
// TABELLA: sedi
// ==========================================
echo "📊 Importazione SEDI...\n";

$sedi = [
    [
        'id' => 1,
        'nome' => 'Nuova sede test',
        'slug' => 'nuova-sede-test',
        'descrizione' => 'Breve descrizione della sede test',
        'indirizzo_via' => 'Via G. B. Trener 16',
        'indirizzo_citta' => 'Trento',
        'indirizzo_provincia' => 'TN',
        'indirizzo_cap' => '38121',
        'telefono' => '0461823395',
        'email' => 'arredogiunco@hotmail.com',
        'attiva' => true,
        'visibile_pubblico' => true,
        'sede_principale' => true,
        'ordine' => 0,
        'created_at' => '2025-11-13 08:47:37',
        'updated_at' => '2025-11-13 09:09:16'
    ]
];

JsonDatabase::createTable('sedi');
foreach ($sedi as $sede) {
    JsonDatabase::insert('sedi', $sede);
}
echo "   ✅ Importate " . count($sedi) . " sedi\n\n";

// ==========================================
// TABELLA: professionisti
// ==========================================
echo "📊 Importazione PROFESSIONISTI...\n";

$professionisti = [
    [
        'id' => 1,
        'utente_id' => 11,
        'nome' => 'Cinzia',
        'cognome' => 'Tonelli',
        'titolo_professionale' => 'Personal Trainer',
        'bio' => 'aaaaa',
        'anni_esperienza' => 10,
        'telefono_mobile' => '3478445159',
        'tariffa_oraria' => 10.00,
        'visibile_pubblico' => false,
        'stato' => 'attivo',
        'codice_professionista' => 'PROF-2025-0001',
        'valutazione_media' => null,
        'numero_recensioni' => 0,
        'lezioni_completate' => 0,
        'clienti_seguiti' => 0,
        'created_at' => '2025-11-13 09:59:18',
        'updated_at' => '2025-11-13 09:59:18'
    ]
];

JsonDatabase::createTable('professionisti');
foreach ($professionisti as $prof) {
    JsonDatabase::insert('professionisti', $prof);
}
echo "   ✅ Importati " . count($professionisti) . " professionisti\n\n";

// ==========================================
// TABELLA: programmi
// ==========================================
echo "📊 Importazione PROGRAMMI...\n";

$programmi = [
    [
        'id' => 1,
        'nome' => 'Nuovo programma 1 test',
        'slug' => 'aa',
        'tipologia' => 'fitness',
        'livello' => 'tutti',
        'durata_giorni' => 30,
        'durata_mesi' => 3,
        'lezioni_totali' => 24,
        'lezioni_settimana' => 2,
        'durata_singola_lezione' => 30,
        'prezzo_base' => 99.00,
        'prezzo_promo' => 79.00,
        'promo_valida_da' => '2000-01-01',
        'promo_valida_a' => '2000-01-01',
        'prezzo_su_richiesta' => false,
        'posti_disponibili' => 20,
        'posti_occupati' => 0,
        'min_partecipanti' => 5,
        'max_partecipanti' => 20,
        'data_inizio' => '2000-02-20',
        'data_fine' => '2025-02-20',
        'sempre_disponibile' => false,
        'attivo' => true,
        'visibile_pubblico' => false,
        'in_evidenza' => false,
        'ordine' => 0,
        'created_at' => '2025-11-13 08:13:52',
        'updated_at' => '2025-11-13 08:14:10'
    ]
];

JsonDatabase::createTable('programmi');
foreach ($programmi as $programma) {
    JsonDatabase::insert('programmi', $programma);
}
echo "   ✅ Importati " . count($programmi) . " programmi\n\n";

// ==========================================
// TABELLA: lezioni
// ==========================================
echo "📊 Importazione LEZIONI...\n";

$lezioni = [
    [
        'id' => 3,
        'titolo' => 'eee',
        'descrizione' => 'ewrewewerw',
        'tipologia' => 'gruppo',
        'livello' => 'tutti',
        'data' => '2025-11-13',
        'ora_inizio' => '15:30:00',
        'ora_fine' => '16:00:00',
        'durata_minuti' => 15,
        'posti_totali' => 10,
        'posti_occupati' => 1,
        'sede_id' => 1,
        'professionista_id' => 11,
        'stato' => 'programmata',
        'ricorrente' => false,
        'prenotazione_obbligatoria' => true,
        'lista_attesa_attiva' => false,
        'visibile_calendario' => true,
        'invia_reminder' => true,
        'created_at' => '2025-11-13 12:08:34',
        'updated_at' => '2025-11-13 15:54:00'
    ],
    [
        'id' => 4,
        'titolo' => 'Lezione di pilates',
        'tipologia' => 'individuale',
        'livello' => 'tutti',
        'data' => '2025-10-13',
        'ora_inizio' => '18:00:00',
        'ora_fine' => '19:00:00',
        'durata_minuti' => 60,
        'posti_totali' => 10,
        'posti_occupati' => 0,
        'programma_id' => 1,
        'professionista_id' => 11,
        'stato' => 'programmata',
        'note_interne' => 'note 1',
        'note_pubbliche' => 'aaa note 1',
        'ricorrente' => true,
        'frequenza_ricorrenza' => 'giornaliera',
        'fine_ricorrenza' => '2025-10-20',
        'prenotazione_obbligatoria' => true,
        'lista_attesa_attiva' => false,
        'visibile_calendario' => true,
        'invia_reminder' => true,
        'created_at' => '2025-11-13 12:36:08',
        'updated_at' => '2025-11-13 12:36:08'
    ]
];

JsonDatabase::createTable('lezioni');
foreach ($lezioni as $lezione) {
    JsonDatabase::insert('lezioni', $lezione);
}
echo "   ✅ Importate " . count($lezioni) . " lezioni (campione)\n\n";

// ==========================================
// TABELLA: pagamenti
// ==========================================
echo "📊 Importazione PAGAMENTI...\n";

$pagamenti = [
    [
        'id' => 1,
        'cliente_id' => 2,
        'importo' => 100.00,
        'importo_pagato' => 100.00,
        'importo_residuo' => 0.00,
        'tipo' => 'lezione',
        'metodo' => 'bonifico',
        'stato' => 'completato',
        'data_emissione' => '2025-11-13',
        'data_pagamento' => '2025-11-13',
        'fatturato' => false,
        'inviato_promemoria' => false,
        'created_at' => '2025-11-13 08:26:38',
        'updated_at' => '2025-11-13 08:26:58'
    ]
];

JsonDatabase::createTable('pagamenti');
foreach ($pagamenti as $pagamento) {
    JsonDatabase::insert('pagamenti', $pagamento);
}
echo "   ✅ Importati " . count($pagamenti) . " pagamenti\n\n";

// ==========================================
// RIEPILOGO
// ==========================================
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ IMPORTAZIONE COMPLETATA CON SUCCESSO!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📊 Riepilogo dati importati:\n";
echo "   - Utenti: " . count($utenti) . "\n";
echo "   - Clienti: " . count($clienti) . "\n";
echo "   - Ruoli: " . count($ruoli) . "\n";
echo "   - Sedi: " . count($sedi) . "\n";
echo "   - Professionisti: " . count($professionisti) . "\n";
echo "   - Programmi: " . count($programmi) . "\n";
echo "   - Lezioni: " . count($lezioni) . "\n";
echo "   - Pagamenti: " . count($pagamenti) . "\n\n";

echo "📁 File JSON creati in: database/jsondb/\n";
echo "   - utenti.json\n";
echo "   - clienti.json\n";
echo "   - ruoli.json\n";
echo "   - sedi.json\n";
echo "   - professionisti.json\n";
echo "   - programmi.json\n";
echo "   - lezioni.json\n";
echo "   - pagamenti.json\n\n";

echo "🎉 Database JSON pronto per l'uso!\n";
