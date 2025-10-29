# CoreSCM - Supply Chain Management

Modulo standalone per la gestione dei lanci di lavorazione per laboratori esterni (terzisti).

## Requisiti

- PHP 8.2+
- MySQL 8.0+
- Apache con mod_rewrite
- Composer

## Installazione

### 1. Database

Importa lo schema:
```bash
mysql -u username -p database_name < database/schema.sql
```

### 2. Configurazione

Copia e configura `.env`:
```bash
cp .env.example .env
```

Modifica `.env`:
```env
APP_NAME=CoreSCM
APP_ENV=production
APP_DEBUG=false

# Database
DB_HOST=127.0.0.1
DB_NAME=corescm
DB_USER=your_user
DB_PASS=your_password

# API Sync
API_SECRET=your-secret-key-min-32-chars
```

### 3. Dipendenze

```bash
composer install --no-dev --optimize-autoloader
```

## Deployment

### Struttura su Server

CoreSCM funziona **SEMPRE in sottocartella `/scm/`**:

```
/percorso/server/scm/
├── api/
├── app/
│   ├── Controllers/
│   ├── Core/
│   ├── Models/
│   └── views/          ← MINUSCOLO su Linux!
├── config/
├── database/
├── public/
├── storage/
├── vendor/
├── .env
├── .htaccess
└── composer.json
```

### .htaccess Root

**File: `/scm/.htaccess`**
```apache
RewriteEngine On
RewriteBase /scm
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ public/index.php [QSA,L]

Options -Indexes
```

### .htaccess Public

**File: `/scm/public/.htaccess`**
```apache
RewriteEngine On

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Note Importanti

1. **Case Sensitivity**: Su Linux, la cartella deve chiamarsi `views` (minuscolo), non `Views`
2. **DB_HOST**: Su hosting condiviso usa `127.0.0.1` invece di `localhost`
3. **Sottocartella**: L'app funziona SOLO in `/scm/`, non in root
4. **BASE_PATH**: Sempre `/scm` - definito in `public/index.php`

## Struttura URL

Tutti gli URL hanno prefix `/scm/`:

```
/scm/                  → Login
/scm/login             → POST login
/scm/dashboard         → Dashboard laboratorio
/scm/lavora/{id}       → Lavora su lancio
/scm/logout            → Logout
```

## API Sync (per CoreGre)

### Endpoint

**Base URL**: `https://domain.com/scm/api/sync.php`

**Auth**: Header `X-API-Secret: your-secret-key`

### Actions

```bash
# Health check
GET /scm/api/sync.php?action=health

# Get updates
GET /scm/api/sync.php?action=get_updates&since=2025-01-01%2000:00:00

# Push updates
POST /scm/api/sync.php?action=push_updates
Content-Type: application/json
{
  "scm_launches": [...],
  "scm_progress_tracking": [...]
}

# Get stats
GET /scm/api/sync.php?action=get_stats
```

## Troubleshooting

### Errore 403 Forbidden

**Causa**: `.htaccess` nelle subdirectory bloccano l'accesso

**Fix**: Rimuovi `.htaccess` da:
- `app/.htaccess`
- `config/.htaccess`
- `storage/.htaccess`
- `vendor/.htaccess`

Tieni solo root e public.

### Errore 404 - Page Not Found

**Causa**: Routes con doppio `/scm/scm/`

**Fix**: Verifica che:
- Routes in `public/index.php` abbiano prefix `/scm/`
- Views usino `url('/path')` o `$thisurl('/path')` SENZA `/scm/`
- Controller usino `$this->url('/path')` SENZA `/scm/`

### View Not Found

**Causa**: Cartella `Views` maiuscola su Linux

**Fix**: Rinomina `app/Views` → `app/views` (minuscolo)

### Database Connection Failed

**Causa**: `DB_HOST=localhost` su hosting condiviso

**Fix**: Usa `DB_HOST=127.0.0.1` nel `.env`

### Call to undefined method logActivity

**Causa**: Metodo rimosso da BaseController

**Fix**: Già rimosso dal controller. Ricarica `SCMController.php`

## Sviluppo Locale

Per testare in locale (XAMPP):

1. Copia tutto in `C:\xampp\htdocs\scm\`
2. Crea database `corescm`
3. Importa `database/schema.sql`
4. Configura `.env` con `DB_HOST=localhost`
5. Vai su `http://localhost/scm/`

## Credenziali Test

Crea utente test:
```sql
INSERT INTO scm_laboratories (name, email, username, password_hash, is_active)
VALUES (
    'Laboratorio Test',
    'test@lab.it',
    'testlab',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    1
);
```

**Username**: `testlab`
**Password**: `password`

## Files Chiave

| File | Descrizione |
|------|-------------|
| `public/index.php` | Entry point, definisce routes |
| `app/Core/Router.php` | Sistema routing |
| `app/Core/BaseController.php` | Controller base |
| `app/Controllers/SCMController.php` | Controller principale |
| `app/Core/helpers.php` | Funzioni globali `url()` |
| `.htaccess` | Redirect root → public |
| `public/.htaccess` | Rewrite rules |

## Aggiornamenti

Per aggiornare CoreSCM su server:

1. Modifica i file sorgente in `CoreSuite/CoreSCM/`
2. Testa in locale su `localhost/scm/`
3. Carica via FTP solo i file modificati
4. **NON** caricare `.env` (sovrascriverebbe config produzione)
5. **NON** caricare `vendor/` se non hai aggiornato dipendenze

## Supporto

- **PHP Version**: Verifica con `<?php phpinfo(); ?>`
- **Logs**: Controlla `storage/logs/`
- **Debug**: Imposta `APP_DEBUG=true` temporaneamente in `.env`

---

**Versione**: 1.0.0
**Autore**: Emmegiemme
**Data**: 2025-01-29
