# Deployment & Maintenance Notes

Step-by-step checklist for deploying VitalMetrics to a live server and running common maintenance tasks.

## 1. Server Requirements

- PHP 8.2 or higher (with `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml` extensions)
- Composer
- Node.js & NPM (for building Vite assets)
- MySQL 8.0+ or MariaDB

## 2. First-Time Server Setup

1. **Pull the code and install dependencies:**
   ```bash
   git clone https://github.com
   cd vitalMetrics
   composer install --optimize-autoloader --no-dev
   npm install
   npm run build
   ```

2. **Configure `.env`:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Update these key values in `.env`:
   ```env
   APP_NAME=VitalMetrics
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_db_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

3. **Run database setup and link storage:**
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   php artisan storage:link
   ```

4. **Set folder permissions (Linux servers):**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

5. **Cache config, routes, and views:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

## 3. Pushing Updates Later

Whenever you pull new changes from GitHub onto the live server, run:

```bash
git pull origin main
composer install --optimize-autoloader --no-dev
npm install
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 4. Common Troubleshooting

- **Avatars not loading (404):** Run `php artisan storage:link` and check that `APP_URL` in `.env` matches your actual domain.
- **500 Error after `.env` change:** Run `php artisan config:clear` followed by `php artisan config:cache`.
- **Styles or Alpine scripts missing:** Make sure `npm run build` completed without errors and `public/build/` exists on the server.
