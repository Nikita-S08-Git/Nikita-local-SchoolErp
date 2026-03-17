# [P3-06] Implement Scheduled Report Generation

## Objective
Allow users to schedule recurring reports to be generated automatically.

## Problem Statement
Users must manually generate reports each time.

## Expected Outcome
- Schedule report form
- Daily/weekly/monthly options

## Scope of Work
1. Create scheduled_reports table
2. Build schedule form
3. Implement Laravel scheduler
4. Add email delivery

## Files to Modify
- CREATE: `database/migrations/xxxx_create_scheduled_reports_table.php`
- CREATE: `app/Models/ScheduledReport.php`
- CREATE: `app/Console/Commands/GenerateScheduledReports.php`

## Dependencies
None

## Acceptance Criteria
- [ ] Schedule report form
- [ ] Daily/weekly/monthly options
- [ ] Email delivery on generation
- [ ] Schedule management UI
- [ ] Laravel scheduler integration

## Developer Notes
Use Laravel task scheduling:
```php
$schedule->command('reports:generate-scheduled')->daily();
```
