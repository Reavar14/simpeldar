# SIMPELDAR Release v1.0

**Application:** SIMPELDAR  
**Version:** 1.0  
**Date:** 2026-09-27  
**Status:** Production Ready

---

## 1. System Overview

SIMPELDAR is a blood request management system for hospital transfusion services. Migrated from legacy native PHP (JasperReports, JavaBridge, remote DB) to CodeIgniter 3 with local MySQL database.

### Key Capabilities
- Blood request workflow (create → process → ready → deliver)
- Rawat inap (inpatient) monitoring
- Multi-level authorization (4 roles)
- Reporting & billing monitoring
- Print-ready forms (Bon, Form Darah, Hasil Pemeriksaan, Laporan)

---

## 2. Requirements

| Component | Version |
|-----------|---------|
| PHP | 8.1+ (tested 8.2), compatible 7.4+ |
| MySQL / MariaDB | 8.0 / 10.5+ |
| Web Server | Apache 2.4+ or Nginx 1.18+ |
| PHP Extensions | `mysqli`, `json`, `mbstring`, `session`, `ctype`, `filter`, `xml`, `gd` |
| Browser | Modern (ES5+, CSS Grid/Flexbox) |

---

## 3. Installation Steps

```bash
# 1. Clone / copy to web root
cp -r simpeldar_codeigniter3 /var/www/html/simpeldar

# 2. Set permissions
chown -R www-data:www-data /var/www/html/simpeldar
chmod -R 755 /var/www/html/simpeldar
chmod -R 775 /var/www/html/simpeldar/application/cache
chmod -R 775 /var/www/html/simpeldar/application/logs
chmod -R 775 /var/www/html/simpeldar/application/cache/sessions
chmod -R 775 /var/www/html/simpeldar/assets

# 3. Configure virtual host (Apache example)
<VirtualHost *:443>
    ServerName simpeldar.example.com
    DocumentRoot /var/www/html/simpeldar
    
    <Directory /var/www/html/simpeldar>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # HTTPS (Let's Encrypt recommended)
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/simpeldar.example.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/simpeldar.example.com/privkey.pem
</VirtualHost>

# 4. Enable mod_rewrite (Apache)
a2enmod rewrite ssl headers
systemctl reload apache2

# 5. Import database
mysql -u simpeldar_app -p darah < darah_schema.sql

# 6. Copy and edit configs
cp application/config/database.php.example application/config/database.php
# Edit: hostname, username, password, database

# 7. Set production environment
# Apache: SetEnv CI_ENV production
# Nginx: fastcgi_param CI_ENV production;
# Or edit index.php line 56: define('ENVIRONMENT', 'production');

# 8. Test
curl -I https://simpeldar.example.com/index.php/auth
```

---

## 4. Database Setup

### Schema
Import provided `darah_schema.sql` (includes tables: `pesan_darah`, `cek_billing`, `kantong_luar`, `kelengkapan`, `pasien2`, `usrmst`, `ruangan`, `referensi`, `variabel`, `jenis_darah`, `dokter`, `perawat`, `analis`, `status_terima`, `numbset`, `dsphis`).

### Production User (recommended)
```sql
CREATE USER 'simpeldar_app'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT SELECT, INSERT, UPDATE, DELETE ON darah.* TO 'simpeldar_app'@'localhost';
FLUSH PRIVILEGES;
```

### Required Indexes (applied in UI-11B)
```sql
CREATE INDEX idx_pd_tgl_minta_status ON darah.pesan_darah (tgl_minta, status);
CREATE INDEX idx_pd_tgl_minta_ruangan ON darah.pesan_darah (tgl_minta, ruangan);
CREATE INDEX idx_pd_tgl_minta_mr ON darah.pesan_darah (tgl_minta, mr);
CREATE INDEX idx_cb_req_status ON darah.cek_billing (no_permintaan, status);
```

### Test Users (change passwords in production)
| Username | Level | Default Pass | Role |
|----------|-------|--------------|------|
| hanif | 1 (Admin) | 123 | Full access |
| rawatinap | 2 (Rawat Inap) | rawatinap | View inpatient |
| user | 3 (Petugas) | user | View/print |
| dokter | 4 (Dokter) | dokter | View/print |

---

## 5. Configuration Changes

### `application/config/config.php`
| Setting | Dev Value | Prod Value | Action |
|---------|-----------|------------|--------|
| `base_url` | `http://localhost/simpeldar_codeigniter3/` | `https://your-domain.com/` | **MUST EDIT** |
| `cookie_secure` | `FALSE` | `TRUE` | **MUST EDIT** (HTTPS) |
| `cookie_httponly` | `TRUE` | `TRUE` | ✅ OK |
| `cookie_samesite` | `Lax` | `Lax` | ✅ OK |
| `log_threshold` | `0` | `1` (errors only) | **RECOMMENDED** |
| `encryption_key` | (present) | (keep) | ✅ OK |
| `csrf_protection` | `TRUE` | `TRUE` | ✅ OK |
| `csrf_regenerate` | `TRUE` | `TRUE` | ✅ OK |
| `sess_save_path` | `APPPATH . 'cache/sessions'` | same | Ensure writable |
| `sess_samesite` | `Lax` | `Lax` | ✅ OK |

### `application/config/database.php`
| Setting | Dev Value | Prod Value | Action |
|---------|-----------|------------|--------|
| `hostname` | `localhost` | production host | **MUST EDIT** |
| `username` | `root` | `simpeldar_app` | **MUST EDIT** |
| `password` | `''` | `STRONG_PASSWORD` | **MUST EDIT** |
| `database` | `darah` | `darah` | ✅ OK |
| `db_debug` | `ENVIRONMENT !== 'production'` | auto (FALSE in prod) | ✅ OK |
| `stricton` | `FALSE` | `TRUE` (recommended) | **RECOMMENDED** |

### `index.php`
```php
// Line 56: Set via server env var (preferred)
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');

// For manual override only:
define('ENVIRONMENT', 'production');
```

---

## 6. Folder Permissions

| Path | Owner | Permissions | Notes |
|------|-------|-------------|-------|
| `application/cache/` | www-data | 775 | Writable |
| `application/cache/sessions/` | www-data | 775 | Session files |
| `application/logs/` | www-data | 775 | Log files |
| `assets/` | www-data | 755 | Static assets |
| All `.php` files | www-data | 644 | Read-only |

---

## 7. Backup Procedure

### Daily (Automated via cron)
```bash
#!/bin/bash
# /etc/cron.daily/simpeldar-backup
DB_USER="simpeldar_app"
DB_PASS="STRONG_PASSWORD"
DB_NAME="darah"
BACKUP_DIR="/var/backups/simpeldar"
DATE=$(date +%F_%H-%M)

mkdir -p $BACKUP_DIR

# Database dump
mysqldump -u$DB_USER -p$DB_PASS --single-transaction --routines --triggers $DB_NAME | gzip > $BACKUP_DIR/darah_$DATE.sql.gz

# Application code (exclude cache/logs/vendor)
tar -czf $BACKUP_DIR/app_$DATE.tar.gz \
  --exclude='application/cache/*' \
  --exclude='application/logs/*' \
  --exclude='assets/admin' \
  --exclude='assets/fonts' \
  --exclude='assets/sweetalert' \
  --exclude='assets/datepicker' \
  --exclude='assets/datetimepicker' \
  --exclude='assets/img' \
  /var/www/html/simpeldar

# Retention: 30 days
find $BACKUP_DIR -type f -mtime +30 -delete
```

### Manual Backup
```bash
mysqldump -u simpeldar_app -p darah > darah_backup_$(date +%F).sql
tar -czf simpeldar_app_$(date +%F).tar.gz /var/www/html/simpeldar --exclude='*/cache/*' --exclude='*/logs/*'
```

---

## 8. Restore Procedure

```bash
# 1. Stop web server
systemctl stop apache2

# 2. Restore database
gunzip -c /var/backups/simpeldar/darah_2026-09-27_02-00.sql.gz | mysql -u simpeldar_app -p darah

# 3. Restore application
tar -xzf /var/backups/simpeldar/app_2026-09-27_02-00.tar.gz -C /

# 4. Fix permissions
chown -R www-data:www-data /var/www/html/simpeldar
chmod -R 775 /var/www/html/simpeldar/application/cache
chmod -R 775 /var/www/html/simpeldar/application/logs
chmod -R 775 /var/www/html/simpeldar/application/cache/sessions

# 5. Start web server
systemctl start apache2

# 6. Verify
curl -I https://simpeldar.example.com/index.php/auth
```

---

## 9. Security Checklist

| Item | Status | Notes |
|------|--------|-------|
| HTTPS enforced | ⚠️ Configure vhost | HSTS header recommended |
| `cookie_secure` = TRUE | ⚠️ Edit config.php | Required for HTTPS |
| `cookie_httponly` = TRUE | ✅ | JS cannot read cookies |
| `cookie_samesite` = Lax | ✅ | CSRF mitigation |
| CSRF protection enabled | ✅ | `csrf_regenerate` = TRUE |
| Dedicated DB user | ⚠️ Create `simpeldar_app` | No root in prod |
| DB `stricton` = TRUE | ⚠️ Recommended | Prevents silent truncation |
| Error display off | ✅ | `ENVIRONMENT=production` |
| Log errors only | ⚠️ Set `log_threshold=1` | Prevent log bloat |
| Session path writable | ⚠️ Verify `cache/sessions/` | 775 + www-data |
| Encryption key set | ✅ | 32-byte hex in config |
| XSS filtering | ✅ | CI3 input class + `htmlspecialchars` |
| SQL injection prevention | ✅ | Query Builder + bound params |
| Unused assets removed | ⚠️ Run cleanup (opt) | See ASSET_CLEANUP_v1.md |
| Default passwords changed | ⚠️ MANDATORY | All `usrmst` passwords |
| `expose_php` = Off | ⚠️ php.ini | Hide PHP version |
| `display_errors` = Off | ✅ | Via ENVIRONMENT |

---

## 10. Post-Deploy Verification

```bash
# 1. Auth
curl -s -o /dev/null -w "%{http_code}" https://domain/index.php/auth
# Expected: 200

# 2. Login (L1)
curl -c cookies.txt -X POST -d "uname=hanif&pass=NEW_PASSWORD&csrf_token=TOKEN" https://domain/index.php/auth/login

# 3. Dashboard
curl -b cookies.txt -s -o /dev/null -w "%{http_code}" https://domain/index.php/dashboard
# Expected: 200

# 4. AJAX endpoints (sample)
curl -b cookies.txt -X POST -d "draw=1&start=0&length=5&csrf_token=TOKEN" https://domain/index.php/billing/ajax_list
# Expected: JSON with "draw":1

# 5. L2 redirect
curl -c cookies2.txt -X POST -d "uname=rawatinap&pass=NEW_PASSWORD&csrf_token=TOKEN" https://domain/index.php/auth/login
curl -b cookies2.txt -s -o /dev/null -w "%{http_code}" https://domain/index.php/dashboard
# Expected: 307 → /darah/view_dokter

# 6. L3/L4 403
curl -b cookies3.txt -X POST -d "uname=user&pass=NEW_PASSWORD&csrf_token=TOKEN" https://domain/index.php/auth/login
curl -b cookies3.txt -s -o /dev/null -w "%{http_code}" https://domain/index.php/dashboard
# Expected: 403
```

---

## 11. Known Limitations

1. **Plaintext passwords** in `usrmst.PASSWORD` — migrate to `password_hash` in next phase
2. **No password reset** flow
3. **No audit logging** for data modifications
4. **Legacy login page** uses `assets/login_style/` (not new layout)
5. **Billing/Riwayat** default to 30-day window (configurable in controller)

---

## 12. Version History

| Version | Date | Notes |
|---------|------|-------|
| 1.0 | 2026-09-27 | Initial production release |

---

*Generated by Phase UI-12 release preparation*