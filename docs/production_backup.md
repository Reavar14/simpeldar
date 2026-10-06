# Production Backup Strategy

Application: SIMPELDAR (CodeIgniter 3)  
Target Environment: Production  
Last Updated: 2026-09-27

---

## 1. Database Backup

### Automated Daily Backup (Recommended)

Create a scheduled task / cron job to run daily at 02:00 (low traffic):

```bash
mysqldump --single-transaction --routines --triggers \
  -u simpeldar_backup -p'PASSWORD' \
  darah > /backups/db/darah_$(date +%Y%m%d_%H%M%S).sql
```

**Windows (Task Scheduler) equivalent:**

```powershell
# backup_db.ps1
$date = Get-Date -Format "yyyyMMdd_HHmmss"
$dest = "D:\backups\db\darah_$date.sql"
mysqldump --single-transaction --routines --triggers -u simpeldar_backup -p"PASSWORD" darah > $dest
# Compress
Compress-Archive -Path $dest -DestinationPath "$dest.zip" -Force
Remove-Item $dest
```

### Retention Policy

| Frequency | Retention | Storage |
|-----------|-----------|---------|
| Hourly | 24 hours | Local disk |
| Daily | 30 days | Local + offsite |
| Weekly | 12 weeks | Offsite |
| Monthly | 12 months | Cold storage (annual) |

### Pre-Deployment Backup (Mandatory)

Before ANY deployment or migration:

```bash
mysqldump --single-transaction -u root -p darah > /backups/db/darah_PREDEPLOY_$(date +%Y%m%d).sql
```

---

## 2. Application Backup

### Full Application Code

```bash
# From deployment root
tar -czf /backups/app/simpeldar_app_$(date +%Y%m%d).tar.gz \
  application/ \
  assets/ \
  system/ \
  index.php \
  --exclude="application/logs/*" \
  --exclude="application/cache/*"
```

**Windows:**

```powershell
$date = Get-Date -Format "yyyyMMdd"
Compress-Archive -Path "D:\laragon\www\simpeldar_codeigniter3\application",
  "D:\laragon\www\simpeldar_codeigniter3\assets",
  "D:\laragon\www\simpeldar_codeigniter3\system",
  "D:\laragon\www\simpeldar_codeigniter3\index.php" `
  -DestinationPath "D:\backups\app\simpeldar_app_$date.zip" -Force
```

---

## 3. Configuration Backup

Critical configuration files must be backed up separately (they contain environment-specific settings):

| File | Contents |
|------|----------|
| `application/config/config.php` | Base URL, encryption key, cookie, CSRF settings |
| `application/config/database.php` | DB credentials |
| `application/config/autoload.php` | Libraries, helpers loaded |
| `application/config/hooks.php` | CSRF hash header hook |
| `application/config/routes.php` | URL routing |

```bash
# Config-only backup (before any change)
tar -czf /backups/config/simpeldar_config_$(date +%Y%m%d).tar.gz \
  application/config/
```

**Version control**: These files should be in Git, with production values managed via environment-specific config or deployment-time injection.

---

## 4. Uploads / Assets Backup

```bash
# User uploads and generated files
tar -czf /backups/uploads/simpeldar_uploads_$(date +%Y%m%d).tar.gz \
  application/uploads/ \
  application/cache/sessions/ \
  assets/logo/ \
  assets/img/
```

**Note**: Sessions are ephemeral — backup only for forensic purposes, not restore.

---

## 5. Restore Procedure

### 5.1 Database Restore

```bash
# 1. Stop application (maintenance mode)
# 2. Restore database
mysql -u root -p darah < /backups/db/darah_YYYYMMDD.sql

# 3. Verify row counts
mysql -u root -p -e "SELECT COUNT(*) FROM darah.pesan_darah;"

# 4. Restart application
```

### 5.2 Application Restore

```bash
# 1. Backup current state (safety)
cp -r /var/www/simpeldar /var/www/simpeldar.broken

# 2. Extract backup
cd /var/www
rm -rf simpeldar
tar -xzf /backups/app/simpeldar_app_YYYYMMDD.tar.gz

# 3. Restore configuration
cp /backups/config/application/config/*.php simpeldar/application/config/

# 4. Set permissions
chown -R www-data:www-data simpeldar
chmod -R 755 simpeldar
chmod 700 simpeldar/application/cache/sessions

# 5. Test
curl -I https://domain/
```

### 5.3 Config-Only Rollback

If a config change breaks the app:

```bash
cp /backups/config/config.php application/config/config.php
cp /backups/config/database.php application/config/database.php
# Clear opcache if enabled
systemctl reload php-fpm  # or apache2
```

---

## 6. Backup Verification

**Monthly drill** — verify backups are restorable:

1. Restore DB backup to a test database
2. Run `SELECT COUNT(*)` on key tables
3. Compare with production counts
4. Restore app to a test server
5. Verify login + one transaction completes

**Automated integrity check:**

```bash
# Verify gzip integrity
gzip -t /backups/app/*.tar.gz && echo "OK" || echo "CORRUPT"

# Verify SQL dump
head -5 /backups/db/*.sql  # should show MySQL dump header
tail -3 /backups/db/*.sql  # should show "-- Dump completed"
```

---

## 7. Emergency Contacts

| Role | Responsibility | Contact |
|------|---------------|---------|
| System Admin | Server, backups | TBD |
| DBA | Database | TBD |
| App Developer | Code, deployment | TBD |
| RS Kanker Dharmais | Business owner | TBD |

---

## 8. Pre-Deployment Checklist

- [ ] Full database backup completed
- [ ] Application code backed up
- [ ] Config files backed up
- [ ] Encryption key documented (secure location)
- [ ] DB user credentials documented (secure location)
- [ ] Rollback plan tested
- [ ] Maintenance page ready
