# 🌿 BRANCH STRUCTURE & WORKFLOW GUIDE

## Branch Hierarchy

```
main (production)
└── Feature (main development)
    └── parth_new (main working branch) ✅
        ├── feature/fix-pending-migrations
        ├── feature/fix-schema-mismatches
        ├── feature/remove-duplicate-files
        ├── feature/fix-relationships
        ├── feature/add-db-indexes
        ├── feature/security-document-access
        ├── feature/fix-n-plus-1-queries
        └── feature/add-missing-services
```

---

## 📋 BRANCH DESCRIPTIONS

### Main Working Branch
| Branch | Purpose | Status |
|--------|---------|--------|
| **parth_new** | Main integration branch for all fixes | ✅ Created |

### Task-Specific Branches

| Branch | Priority | Estimated Time | Description |
|--------|----------|----------------|-------------|
| **feature/fix-pending-migrations** | 🔴 P0-Critical | 2-3 hours | Run 30 pending migrations, verify database |
| **feature/fix-schema-mismatches** | 🔴 P0-Critical | 3-4 hours | Fix attendance/timetable schema conflicts |
| **feature/remove-duplicate-files** | 🔴 P0-Critical | 1 hour | Delete duplicate models and services |
| **feature/fix-relationships** | 🔴 P0-Critical | 2-3 hours | Fix incorrect Eloquent relationships |
| **feature/add-db-indexes** | 🟠 P1-High | 2 hours | Add database indexes for performance |
| **feature/security-document-access** | 🔴 P0-Critical | 6-8 hours | Document access control, policies, logging |
| **feature/fix-n-plus-1-queries** | 🟠 P1-High | 4-6 hours | Fix N+1 query problems |
| **feature/add-missing-services** | 🟠 P1-High | 8-10 hours | Create AttendanceService, FileUploadService, etc. |

---

## 🔄 WORKFLOW

### Step 1: Start Working on a Task

```bash
# Always start from parth_new branch
git checkout parth_new
git pull origin parth_new

# Create/update your task branch
git checkout feature/fix-pending-migrations
```

### Step 2: Make Changes

```bash
# Make your code changes
# Commit frequently with clear messages
git add .
git commit -m "[fix-pending-migrations] Run pending attendance migrations"
```

### Step 3: Push and Create PR

```bash
# Push to remote
git push -u origin feature/fix-pending-migrations

# Create Pull Request on GitHub:
# feature/fix-pending-migrations → parth_new
```

### Step 4: Merge to parth_new

After PR approval:
```bash
# Merge via GitHub UI OR manually:
git checkout parth_new
git pull origin parth_new
git merge feature/fix-pending-migrations
git push origin parth_new
```

### Step 5: Delete Task Branch (Optional)

```bash
# Delete local branch
git branch -d feature/fix-pending-migrations

# Delete remote branch
git push origin --delete feature/fix-pending-migrations
```

---

## 📝 COMMIT MESSAGE FORMAT

Use conventional commits:

```
<type>(<scope>): <subject>

[optional body]
```

### Types:
- `fix` - Bug fix
- `feat` - New feature
- `refactor` - Code refactoring
- `test` - Adding tests
- `docs` - Documentation
- `chore` - Maintenance tasks

### Examples:
```
fix(pending-migrations): Run 30 pending attendance migrations
fix(schema-mismatch): Rename attendance.date to attendance_date
refactor(relationships): Fix Division.academicYear() relationship
feat(security): Add document download policy
perf(database): Add indexes to students.email
```

---

## 🎯 EXECUTION ORDER

### Phase 1: Critical Fixes (Week 1)

```
1. feature/fix-pending-migrations    ← Start here
2. feature/fix-schema-mismatches     ← Depends on #1
3. feature/remove-duplicate-files    ← Can be parallel
4. feature/fix-relationships         ← Depends on #2
```

### Phase 2: Security & Performance (Week 2)

```
5. feature/security-document-access  ← Critical security
6. feature/add-db-indexes            ← Performance
7. feature/fix-n-plus-1-queries      ← Performance
```

### Phase 3: Missing Features (Week 3)

```
8. feature/add-missing-services      ← New services
```

---

## 🔗 PULL REQUEST TEMPLATE

When creating a PR, use this template:

```markdown
## Description
Brief description of changes

## Related Issue
Fixes #[issue-number]

## Type of Change
- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to change)
- [ ] Code refactoring

## Testing
- [ ] I have tested the changes locally
- [ ] I have added/updated tests

## Checklist
- [ ] My code follows the project's coding guidelines
- [ ] I have performed a self-review of my own code
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] My changes generate no new warnings
- [ ] I have updated the documentation accordingly

## Screenshots (if applicable)
Add screenshots here

## Additional Context
Any additional information
```

---

## 🚨 IMPORTANT NOTES

### 1. Never Work Directly on `parth_new`
Always create a task-specific branch from `parth_new`.

### 2. Keep Branches Small
Each branch should focus on ONE specific task. Small branches are easier to review and merge.

### 3. Sync Regularly
Regularly pull `parth_new` into your task branch to avoid conflicts:
```bash
git checkout feature/your-branch
git pull origin parth_new
```

### 4. One Branch at a Time
Complete and merge one branch before starting the next (for dependent tasks).

### 5. Test Before Pushing
Always test your changes locally before pushing.

---

## 📊 BRANCH STATUS TRACKING

| Branch | Status | PR Created | Merged to parth_new | Notes |
|--------|--------|------------|---------------------|-------|
| feature/fix-pending-migrations | ⏳ Pending | ❌ | ❌ | Start here |
| feature/fix-schema-mismatches | ⏳ Pending | ❌ | ❌ | After #1 |
| feature/remove-duplicate-files | ⏳ Pending | ❌ | ❌ | Can be parallel |
| feature/fix-relationships | ⏳ Pending | ❌ | ❌ | After #2 |
| feature/add-db-indexes | ⏳ Pending | ❌ | ❌ | Week 2 |
| feature/security-document-access | ⏳ Pending | ❌ | ❌ | Week 2 |
| feature/fix-n-plus-1-queries | ⏳ Pending | ❌ | ❌ | Week 2 |
| feature/add-missing-services | ⏳ Pending | ❌ | ❌ | Week 3 |

---

## 🛠️ USEFUL GIT COMMANDS

### View Branch Tree
```bash
git log --graph --oneline --all
```

### Check Branch Status
```bash
git status
git branch -a
```

### Compare Branches
```bash
git diff parth_new..feature/your-branch
```

### Rebase on parth_new
```bash
git checkout feature/your-branch
git rebase parth_new
```

### Abort Merge/Rebase
```bash
git merge --abort
git rebase --abort
```

---

## 📞 NEED HELP?

If you encounter issues:
1. Check Git status: `git status`
2. View recent commits: `git log --oneline -5`
3. Check branch differences: `git diff parth_new..feature/your-branch`

---

**Created:** March 24, 2026  
**Last Updated:** March 24, 2026  
**Maintained By:** Development Team
