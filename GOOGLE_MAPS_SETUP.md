# 🗺️ Configurazione Google Maps API

Guida completa per configurare Google Maps API nel progetto MA.GIA DONNA.

---

## 📋 Indice
1. [Creare un Progetto Google Cloud](#1-creare-un-progetto-google-cloud)
2. [Abilitare Google Maps JavaScript API](#2-abilitare-google-maps-javascript-api)
3. [Creare una API Key](#3-creare-una-api-key)
4. [Configurare le Restrizioni](#4-configurare-le-restrizioni)
5. [Inserire la API Key nel Progetto](#5-inserire-la-api-key-nel-progetto)
6. [Testing e Verifica](#6-testing-e-verifica)
7. [Gestione Costi](#7-gestione-costi)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Creare un Progetto Google Cloud

### Passo 1.1: Accedi a Google Cloud Console
- Vai su: https://console.cloud.google.com/
- Accedi con il tuo account Google

### Passo 1.2: Crea un Nuovo Progetto
1. Clicca sul selettore di progetto in alto
2. Clicca su "NEW PROJECT" / "NUOVO PROGETTO"
3. Nome progetto: `MA.GIA DONNA Maps`
4. Clicca su "CREATE" / "CREA"

---

## 2. Abilitare Google Maps JavaScript API

### Passo 2.1: Vai alla Libreria API
1. Nel menu laterale, vai su: **APIs & Services** → **Library**
2. Cerca: `Maps JavaScript API`

### Passo 2.2: Abilita l'API
1. Clicca su **Maps JavaScript API**
2. Clicca su **ENABLE** / **ABILITA**

### API Aggiuntive (Opzionali ma Consigliate)
Per funzionalità avanzate, abilita anche:
- **Places API** (per autocomplete indirizzi)
- **Geocoding API** (per convertire indirizzi in coordinate)
- **Directions API** (per calcolare percorsi)

---

## 3. Creare una API Key

### Passo 3.1: Vai a Credentials
1. Nel menu laterale: **APIs & Services** → **Credentials**
2. Clicca su **+ CREATE CREDENTIALS** / **+ CREA CREDENZIALI**
3. Seleziona **API Key**

### Passo 3.2: Copia la API Key
```
Esempio: AIzaSyB1234567890abcdefghijklmnopqrstuv
```
⚠️ **IMPORTANTE**: Salva questa chiave in un luogo sicuro!

---

## 4. Configurare le Restrizioni

### 🔒 Restrizioni Consigliate per Produzione

#### Passo 4.1: Restrizione per Applicazione
1. Clicca sulla tua API Key per modificarla
2. In **Application restrictions**:
   - Seleziona **HTTP referrers (web sites)**
   - Aggiungi i tuoi domini:
     ```
     https://www.agstudio.digital/*
     https://agstudio.digital/*
     http://localhost/*        (solo per sviluppo)
     ```

#### Passo 4.2: Restrizione API
1. In **API restrictions**:
   - Seleziona **Restrict key**
   - Seleziona solo le API necessarie:
     - ✅ Maps JavaScript API
     - ✅ Places API (se usata)
     - ✅ Geocoding API (se usata)

#### Passo 4.3: Salva
- Clicca su **SAVE** / **SALVA**

---

## 5. Inserire la API Key nel Progetto

### Metodo 1: File di Configurazione (CONSIGLIATO)

#### Passo 5.1: Aggiungi al file .env
```env
GOOGLE_MAPS_API_KEY=AIzaSyB1234567890abcdefghijklmnopqrstuv
```

#### Passo 5.2: Crea un Config File
Crea/modifica `config/services.php`:
```php
return [
    // ... altre configurazioni

    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY', ''),
    ],
];
```

#### Passo 5.3: Usa nel Controller
```php
// LocationController.php
public function index()
{
    $googleMapsApiKey = config('services.google_maps.api_key');

    return view('locations.index', compact('googleMapsApiKey'));
}
```

#### Passo 5.4: Usa nella View
```blade
<script>
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
</script>
```

### Metodo 2: Direttamente nelle Views (Solo per Test)

⚠️ **NON CONSIGLIATO per produzione**

```blade
<script>
    window.onload = function() {
        const script = document.createElement('script');
        script.src = 'https://maps.googleapis.com/maps/api/js?key=TUA_API_KEY&callback=initMap';
        script.async = true;
        script.defer = true;
        document.head.appendChild(script);
    };
</script>
```

---

## 6. Testing e Verifica

### Test 1: Caricamento Mappa
1. Visita: `https://tuodominio.com/locations`
2. Verifica che la mappa si carichi correttamente
3. Controlla la console del browser per errori

### Test 2: Marker e Info Windows
1. Clicca su un marker nella mappa
2. Verifica che l'info window si apra
3. Testa i link "Indicazioni" e "Dettagli"

### Test 3: Responsiveness
1. Testa su mobile
2. Testa su tablet
3. Testa su desktop

### Errori Comuni nella Console

#### Errore: "RefererNotAllowedMapError"
```
Soluzione: Aggiungi il tuo dominio alle HTTP referrer restrictions
```

#### Errore: "ApiNotActivatedMapError"
```
Soluzione: Abilita Maps JavaScript API nel progetto Google Cloud
```

#### Errore: "InvalidKeyMapError"
```
Soluzione: Verifica che la API Key sia corretta e non abbia spazi
```

---

## 7. Gestione Costi

### 💰 Pricing Google Maps (2024)

#### Maps JavaScript API
- **Prime 28.000 caricamenti/mese**: GRATUITI ($200 credito mensile)
- **Oltre 28.000**: $7 per 1000 caricamenti

#### Calcolo Stimato per MA.GIA DONNA

**Scenario Basso Traffico** (< 1000 visite/mese)
```
Caricamenti mappa: ~1.000/mese
Costo: $0 (rientra nei crediti gratuiti)
```

**Scenario Medio Traffico** (5.000 visite/mese)
```
Caricamenti mappa: ~5.000/mese
Costo: $0 (rientra nei crediti gratuiti)
```

**Scenario Alto Traffico** (30.000 visite/mese)
```
Caricamenti mappa: ~30.000/mese
Costo: ~$7 (2.000 caricamenti oltre il limite gratuito)
```

### 🔔 Impostare Alert di Budget

1. Vai su **Billing** → **Budgets & alerts**
2. Crea un nuovo budget:
   - Nome: `Google Maps Budget`
   - Budget amount: $20/mese
   - Alert thresholds: 50%, 90%, 100%
3. Inserisci email per notifiche

---

## 8. Troubleshooting

### Problema: La mappa non si carica

**Checklist:**
- ✅ API Key inserita correttamente?
- ✅ Maps JavaScript API abilitata?
- ✅ Dominio autorizzato nelle restrizioni?
- ✅ Nessun errore nella console del browser?
- ✅ Connessione internet attiva?

### Problema: I marker non appaiono

**Possibili cause:**
1. Coordinate (lat/lng) non valide nel database
2. Sede non ha `visibile_pubblico = true`
3. Sede non ha `attiva = true`
4. JavaScript errors nella console

**Debug:**
```javascript
// Aggiungi nella console del browser
console.log('Locations:', locations);
console.log('Markers:', markers);
```

### Problema: Info window non si apre

**Soluzione:**
```javascript
// Verifica che l'evento click sia registrato
marker.addListener('click', () => {
    console.log('Marker clicked!');
    infoWindow.open(map, marker);
});
```

### Problema: Mappa grigia / vuota

**Cause comuni:**
1. API Key non valida
2. Billing non attivato su Google Cloud
3. API non abilitata
4. Restrizioni troppo stringenti

---

## 📚 Risorse Utili

### Documentazione Ufficiale
- **Google Maps Platform**: https://developers.google.com/maps
- **Maps JavaScript API**: https://developers.google.com/maps/documentation/javascript
- **Pricing Calculator**: https://mapsplatform.google.com/pricing/

### Tutorial e Guide
- **Google Codelabs**: https://codelabs.developers.google.com/?cat=Maps
- **Esempi di Codice**: https://github.com/googlemaps/js-samples

### Supporto
- **Stack Overflow**: Tag `google-maps-api-3`
- **Issue Tracker**: https://issuetracker.google.com/issues?q=componentid:187931

---

## 🎯 Checklist Finale

Prima di andare in produzione, assicurati di:

- [ ] API Key creata e salvata in `.env`
- [ ] Maps JavaScript API abilitata
- [ ] HTTP referrers configurati correttamente
- [ ] API restrictions impostate
- [ ] Budget alerts configurati
- [ ] Billing abilitato su Google Cloud
- [ ] Test completati su tutti i browser
- [ ] Test completati su mobile
- [ ] Console del browser senza errori
- [ ] Tutte le sedi hanno coordinate valide nel database

---

## 📝 Note Importanti

1. **Non committare la API Key su Git**
   - Usa sempre `.env` per le chiavi sensibili
   - Aggiungi `.env` al `.gitignore`

2. **Usa restrizioni in produzione**
   - Mai lasciare una API Key senza restrizioni
   - Monitora l'uso tramite Google Cloud Console

3. **Backup delle credenziali**
   - Salva la API Key in un password manager
   - Documenta chi ha accesso al progetto Google Cloud

---

**Ultimo aggiornamento**: 15 Novembre 2024
**Autore**: Claude Code (AI Assistant)
**Progetto**: MA.GIA DONNA
