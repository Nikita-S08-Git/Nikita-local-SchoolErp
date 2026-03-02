# [P3-09] Implement Backup and Restore Feature

## Objective
Build database backup and restore utility for data recovery.

## Problem Statement
Data recovery requires manual database backup. Built-in backup would simplify recovery.

## Expected Outcome
- Backup database button in admin
- Download backup file

## Scope of Work
1. Create backup command
2. Build backup UI
3. Implement restore functionality
4. Add scheduled backups

## Files to Modify
- CREATE: `app/Console/Commands/BackupDatabase.php`
- CREATE: `app/Http/Controllers/Web/BackupController.php`
- CREATE: `resources/views/settings/backup.blade.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Backup database button in admin
- [ ] Download backup file
- [ ] Upload and restore backup
- [ ] Scheduled backups option
- [ ] Backup encryption option

## Developer Notes
Use mysqldump for MySQL:
```php
exec('mysqldump --user='.$user.' --password='.$pass.' '.$db);
```
