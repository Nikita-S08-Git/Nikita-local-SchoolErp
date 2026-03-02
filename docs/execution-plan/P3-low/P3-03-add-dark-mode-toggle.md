# [P3-03] Add Dark Mode Toggle

## Objective
Implement theme switcher for user preference between light and dark modes.

## Problem Statement
Users prefer dark mode for reduced eye strain. Theme preference should be user-configurable.

## Expected Outcome
- Dark mode CSS variables defined
- Theme toggle button in navbar

## Scope of Work
1. Define dark mode CSS
2. Create toggle button
3. Save user preference
4. Apply theme dynamically

## Files to Modify
- MODIFY: `resources/views/layouts/app.blade.php`
- CREATE: `resources/css/dark-mode.css`

## Dependencies
P2-13: Implement Branding Configuration

## Acceptance Criteria
- [ ] Dark mode CSS variables defined
- [ ] Theme toggle button in navbar
- [ ] Preference saved per user
- [ ] All views support dark mode
- [ ] Smooth transition between themes

## Developer Notes
Use CSS variables for theming:
```css
:root { --bg-color: #fff; }
[data-theme="dark"] { --bg-color: #1a1a1a; }
```
