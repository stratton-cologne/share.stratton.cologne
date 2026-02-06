## Stratton Share

WeTransfer-ähnlicher File-Sharing Dienst für share.stratton.cologne, umgesetzt mit Laravel 12, Vue 3, Vite, TypeScript und Tailwind CSS.

### Features

- Datei-Upload mit Fortschritt
- Share-Link generieren
- Download-Ansicht für Empfänger
- Optionales Ablaufdatum & Download-Limit

### Lokale Entwicklung

1. Backend-Abhängigkeiten installieren

- `composer install`

2. Frontend-Abhängigkeiten installieren

- `npm install`

3. Migrationen ausführen

- `php artisan migrate`

4. Dev-Server starten

- `php artisan serve` (Laravel Backend auf http://localhost:8000)
- `npm run dev` (Vite HMR auf Port 5173)

**Wichtig:** Die App öffnen unter **http://localhost:8000** (nicht Port 5173!)

### Build

- `npm run build`
  Die Assets werden in `public/build` abgelegt (Vite).

### Wichtige Pfade

- SPA Shell: [resources/views/app.blade.php](resources/views/app.blade.php)
- Vue Einstieg: [resources/js/app.ts](resources/js/app.ts)
- API Routes: [routes/api.php](routes/api.php)
- Upload/Download Controller: [app/Http/Controllers/SharedFileController.php](app/Http/Controllers/SharedFileController.php)

### Konfiguration

- `APP_URL` ist auf https://share.stratton.cologne gesetzt.
- Datenbank: MariaDB (`DB_CONNECTION=mariadb` in .env).
- Dateigrößenlimit in `SharedFileController::store()` (aktuell 1 GB, abhängig von PHP-Upload-Limits).
