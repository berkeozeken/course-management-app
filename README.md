# Course Management App (Laravel + Inertia + Vue 3)

A modern **course management system** built with Laravel and Vue 3 (Inertia).  
Roles: **Student / Instructor / Admin**.  
Features: **Course, Section, Lesson CRUD**, **Enrollment**, **Profile Management**, **Role-based Access Control**.

> Demo: (Add your Render URL here once deployed)  
> e.g. `https://course-management-app.onrender.com`

---

## Features

- **Authentication & Roles:** Student / Instructor / Admin (middleware & role-based access)
- **Course Management:** create, edit, delete, publish/unpublish, cover image, price, badges
- **Sections & Lessons:** full CRUD; lesson fields: `title, content, video_url, duration_minutes, is_free, position`
- **Enrollment Flow:** students can enroll/unenroll, start course, navigate previous/next lessons
- **Instructor View:** participant count, first 10 students, modal with full list
- **Profile:** update profile info, change password (optional: delete account)
- **UI:** Inertia + Vue 3 + Tailwind; Navbar with Profile & Logout, My Courses / My Teachings

---

## Tech Stack

- **Backend:** Laravel (PHP 8.3), Eloquent ORM, Migrations
- **Frontend:** Inertia.js, Vue 3, Vite, TailwindCSS
- **Auth:** Laravel Breeze (Inertia stack)
- **Database:** PostgreSQL
- **Production:** Render (Dockerized), PostgreSQL add-on

---

## Getting Started (Local Development)

```bash
git clone https://github.com/berkeozeken/course-management-app.git
cd course-management-app

# Install PHP dependencies
composer install

# Create .env and generate app key
cp .env.example .env
php artisan key:generate

# Configure DB in .env (example for PostgreSQL)
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=courseapp
# DB_USERNAME=courseuser
# DB_PASSWORD=coursepass

# Run migrations
php artisan migrate --seed   # remove --seed if no seeders

# Install frontend packages
npm install
npm run dev   # or npm run build && npm run preview

# Start Laravel
php artisan serve
# http://127.0.0.1:8000
