# VitalMetrics

VitalMetrics is a web-based health tracking app built with Laravel, Alpine.js, and Tailwind CSS. I built this project to have one place to log daily blood pressure readings, body weight, and workouts, along with an admin panel for managing users and roles.

## Features

### User Side
- **Authentication & Profile:** Login, registration, profile updates, avatar uploads, and checks to block deactivated accounts from logging in.
- **Dashboard:** Shows recent health entries, summaries, and personal activity logs.
- **Health Trackers:** Separate modules for logging Blood Pressure (systolic, diastolic, pulse), Weight (kg or lbs), and Workouts (activity type, duration, distance, calories, speed, steps).
- **Search, Pagination & Views:** Live search, per-page filters (10, 25, 50), and a toggle between table and card views.
- **CSV Export:** Download filtered or full tracker records as CSV files.
- **Light & Dark Mode:** Saves each user's preferred theme in the database.

### Admin Side
- **Admin Dashboard:** Displays user counts, active vs. inactive users, role counts, and recent sign-ups.
- **User Management:** Create and edit users, update avatars, assign roles, and activate or deactivate accounts. Includes safeguards so admins cannot deactivate their own account or other admin accounts.
- **Role & Permission Management:** Create custom roles, assign permissions, toggle active status, and manage deleted roles in a Trash History view (with restore and permanent delete). The built-in Admin role is protected from deletion or deactivation.
- **Settings:** Separate pages for system settings (`/admin/settings`) and the admin's personal theme preference (`/admin/preferences`).

## Tech Stack

- PHP 8.2+ & Laravel
- MySQL
- Blade Templates & Alpine.js
- Tailwind CSS & Flowbite
- Vite & Font Awesome

## Roles & Access

Access is controlled on both the UI and backend routes using role and permission middleware:

- **User:** Can access their own dashboard, profile, settings, and trackers (`/dashboard`, `/bp`, `/weight`, `/workout-tracker`). Blocked from `/admin/*` routes.
- **Admin:** Can access the admin dashboard, user management, role management, system settings, and their own profile/theme preferences.

## Screenshots

**User Dashboard**
 <img width="1919" height="913" alt="user-dashboard" src="https://github.com/user-attachments/assets/e4b78dc0-6f56-4afb-83de-ca8e87c9de11" />
**Workout Tracker**
 <img width="1918" height="904" alt="workout-tracker" src="https://github.com/user-attachments/assets/134866a2-341e-4432-a2b1-f956adcdaf71" />
**Admin Dashboard**
 <img width="1914" height="902" alt="admin-dashboard" src="https://github.com/user-attachments/assets/2ed35ee7-3122-4dbd-a4ae-a16a11655dda" />
**Role Management**
  <img width="1919" height="904" alt="role-management" src="https://github.com/user-attachments/assets/d310d4d0-57ff-498d-b006-6d4146d2146d" />


## Local Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com
   cd vitalMetrics
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Set up the environment file**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Open `.env` and update your database settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=database
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run migrations and seeders**
   This creates the database tables along with the default roles, permissions, and initial users:
   ```bash
   php artisan migrate --seed
   ```

5. **Create the storage symlink**
   Required so uploaded user avatars display properly:
   ```bash
   php artisan storage:link
   ```

6. **Run the app**
   In one terminal:
   ```bash
   php artisan serve
   ```
   In a second terminal:
   ```bash
   npm run dev
   ```

## Deployment Notes

For production servers:

1. Update `.env` with `APP_ENV=production`, `APP_DEBUG=false`, and your live `APP_URL` and database credentials.
2. Install production dependencies and build assets:
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install
   npm run build
   ```
3. Run database migrations, seeders, and link storage:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   ```
4. Cache config, routes, and views:
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. Make sure the `storage/` and `bootstrap/cache/` folders are writable by the server.

## Project Status

- **Phase 1 (Completed):** Authentication, Blood Pressure/Weight/Workout trackers, CSV export, Light/Dark mode, Admin dashboard, User management, Role & Permission management, and route protection.
- **Phase 2 (Planned):** System-wide admin activity logs, Auth and Payment settings tabs, and user notifications.

## Documentation

- [Architecture & Project Notes](docs/ARCHITECTURE.md)
- [Deployment & Maintenance Checklist](docs/DEPLOYMENT.md)

## License

MIT License
