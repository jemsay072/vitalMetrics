# VitalMetrics

A personal health tracking application built with Laravel.

VitalMetrics helps users monitor and manage their daily health metrics
such as blood pressure, weight, and workouts in one place.

## ✨ Features

- 🔐 User authentication
- 👤 User and Admin roles
- ❤️ Blood Pressure Tracker
- ⚖️ Weight Tracker
- 🏃 Workout Tracker
- 🔎 Search and filtering
- 📄 Pagination
- 📊 Table and Grid/List views
- 📥 Data export
- 🧩 Reusable Blade components
- 📱 Responsive UI

## 🛠️ Tech Stack

- **Laravel**
- **PHP**
- **MySQL**
- **Blade**
- **Alpine.js**
- **Tailwind CSS**
- **Vite**
- **Flowbite**

## 📂 Trackers

### Blood Pressure

Record and monitor:

- Systolic
- Diastolic
- Pulse
- Reading time
- Blood pressure classification
- Notes

### Weight

Record:

- Weight
- Measurement time
- Notes

### Workout

Record:

- Activity type
- Duration
- Distance
- Calories burned
- Speed
- Steps

## 🧩 Reusable Components

VitalMetrics uses reusable Blade components to keep the UI consistent and easier to maintain.

Examples:

- Alert
- Navigation
- Page Header
- Search & Filter
- Pagination
- Table
- Export
- Form components

## 🔑 Roles

### User

Users can:

- Manage their own health records
- View their trackers
- Search and filter records
- Export their data

### Admin

Administrators can:

- Access the admin dashboard
- Manage users
- Assign roles
- Manage system settings

## 🚀 Installation

Clone the repository:

```bash
git clone https://github.com/jemsay072/vitalMetrics.git
cd vitalmetrics

```
## Install dependencies:
```bash
composer install
npm install

```

## Create the environment file:
```bash
cp .env.example .env
```

## Configure your database in .env, then run:
```bash
php artisan migrate --seed
```

## Start the development server:
```bash
php artisan serve
```

## In other Terminal:
```bash
npm run dev
```

## 🎯 Project Purpose

VitalMetrics is a personal learning project focused on building a
structured Laravel application while practicing:

- Laravel architecture
- Authentication and authorization
- Database relationships
- CRUD operations
- Reusable Blade components
- Alpine.js interactions
- Tailwind CSS
- Search and filtering
- Pagination
- Data export
- Scalable and maintainable code organization

## 📌 Project Status
🚧 Currently in development

More features and improvements will be added as development continues.

## License
