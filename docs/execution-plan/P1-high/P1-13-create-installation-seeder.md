# [P1-13] Create Default Installation Seeder for New Client

## Objective
Automatically create admin user, roles, default settings, and base configuration during installation.

## Problem Statement
Each new client installation requires manual setup of admin user, roles, and default configuration.

## Expected Outcome
- InstallSeeder created with default data
- Admin user creation with temporary password
- All roles and permissions seeded
- Default academic rules configured

## Scope of Work
1. Create InstallSeeder class
2. Define default roles and permissions
3. Create default admin user
4. Seed default academic rules
5. Seed default fee heads
6. Add installation completion flag

## Files to Modify
- CREATE: `database/seeders/InstallSeeder.php`
- MODIFY: `database/seeders/DatabaseSeeder.php`
- CREATE: `database/seeders/DefaultSettingsSeeder.php`

## Dependencies
P1-11: Implement System Settings Module

## Acceptance Criteria
- [ ] InstallSeeder created with default data
- [ ] Admin user creation with temporary password
- [ ] All roles and permissions seeded
- [ ] Default academic rules configured
- [ ] Default fee heads and structures seeded
- [ ] Installation completion flag set

## Developer Notes
Default admin credentials:
- Email: admin@school.edu
- Password: Generated random password
- Password shown once after installation
- Force password change on first login
