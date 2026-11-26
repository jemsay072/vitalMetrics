# Project Notes & Architecture

Quick reference for how VitalMetrics is structured so it is easy to jump back into for Phase 2 or maintenance.

## Folder & Code Structure

- **Controllers (`app/Http/Controllers/`):**
  - `Admin/` — Handles admin dashboard, user management (`UserController`), roles (`RoleController`), and system settings (`AdminSettingsController`).
  - `User/` — Handles user dashboard, trackers (`BloodPressureController`, `WeightTrackerController`, `WorkoutTrackerController`), and personal preferences (`UserSettingsController`).
- **Views (`resources/views/`):**
  - `admin/` — Admin pages (`dashboard`, `user`, `role`, `settings`).
  - `page/` — User-facing pages (`dashboard`, `bp`, `weight`, `workout`, `settings`).
  - `components/` — Reusable UI blocks (`table/*`, `modal`, `page-header`, `alert`, `search-filter`, `pagination`).
- **Frontend Logic:**
  - Server-rendered Blade templates paired with Alpine.js for modals, live search (`searchFilter`), view toggles (table vs. grid), and dark mode state.

## Database & Key Tables

- `users` — Stores user credentials, `role_id`, and account `status` (`active` or `inactive`).
- `user_profiles` — One-to-one with `users` for personal details and `avatar` storage path (`storage/app/public/...`).
- `user_preferences` — Stores per-user settings (`theme`, `weight_unit`, `date_format`, `dashboard_landing`, notification flags).
- `roles` & `permissions` — Many-to-many via `role_permission`. `roles` uses soft deletes (`deleted_at`) for the Trash History feature.
- `blood_pressures`, `weights`, `workouts` — Health tracker records tied to `user_id`.

## Route & Permission Flow

1. **Authentication & Status Check:** Inactive users (`status === 'inactive'`) are blocked during login.
2. **Role Middleware (`admin` vs `user`):**
   - `/admin/*` routes require `['auth', 'admin']`. Regular users hitting these URLs get a `403` response.
   - Tracker routes require `['auth', 'verified', 'user']`.
   - `/profile` is shared under `['auth', 'verified']` so both admins and users can update their own profile.
3. **Permission Middleware (`hasPermission:<key>`):**
   - Checks the logged-in user's assigned role permissions before allowing access to specific modules (`dashboard`, `trackers`, `export`, `users`, `roles`, `settings`, `profile`).
4. **Built-in Safeguards:**
   - The core `Admin` role cannot be deactivated, trashed, or force-deleted.
   - Admins cannot deactivate their own logged-in account or other Admin accounts.

## Settings Split

- **`/settings` (User) & `/admin/preferences` (Admin):** Both use `UserSettingsController` and `page/settings/index.blade.php` to save personal preferences (like Light/Dark mode).
- **`/admin/settings`:** Uses `AdminSettingsController` for global system settings.

## Phase 2 Notes (Upcoming)

- Add a system-wide `Activity Logs` page in the admin sidebar to monitor all user actions.
- Complete the `Auth` and `Payment` tabs under `/admin/settings`.
- Wire up active alerts for the user `Notifications` tab.
