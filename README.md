# ExamJe - Multiple Choice Examination Platform

A comprehensive online examination system built with Laravel 12, designed for creating and managing multiple-choice exams with real-time grading and analytics.

## Features

### Admin Features
- **Dashboard**: View analytics, logs, and system overview with real-time notifications
- **Classroom Management**: Create classrooms with unique access codes
  - Set question count per exam
  - Configure timer and auto-submit
  - Add student groups
  - Set result visibility (immediate/email/both/hidden)
  - Add custom instructions for students
  - Announcement crawler (scrolling message displayed to students during exam)
- **Question Bank**: Organize questions by category
  - Support for images in questions
  - Four multiple-choice options (A, B, C, D)
  - Auto-grading with correct answer marking
- **Results**: View student performance and export data
- **Admin Management**: CRUD operations for admin users with password change
- **Activity Logs**: Track all system activities
- **Notifications**: Real-time bell notifications for recent exam completions

### Student Features
- **Easy Access**: Join exams via classroom code
- **Student Registration**: Fill in details before starting
- **Exam Interface**:
  - Timer countdown with warnings at 5 min and 1 min
  - Auto-submit when time expires (no confirmation needed)
  - Skip questions and return later
  - Question navigation with progress tracking
  - Answer save indicator
  - Loading skeleton while questions load
  - Cannot copy or right-click questions
  - Announcement crawler for teacher messages
- **Results**: View scores with visual score gauge, pass/fail badge, and answer review
- **Results Access**: Students can recheck results anytime by verifying their matric number and email

## Technical Specifications

- **Framework**: Laravel 12.42.0
- **PHP**: ^8.2
- **Database**: 
  - Local: SQLite (Laravel Herd)
  - Production: MariaDB (StackCP)
- **Frontend**: Blade, Vite, Vanilla CSS/JS
- **Color Scheme**: Blue theme

## Installation

### From Release Zip (Recommended — no terminal needed)

Works on **cPanel**, **StackCP**, **VPS**, or any PHP hosting. No Node.js or npm required — assets are pre-built.

1. **Download** the latest release zip from [GitHub Releases](https://github.com/azfarmiskam/exam-azfarmiskam/releases)
2. **Extract & upload** all files to your server (via FTP or file manager)
3. **Set document root** to the `public` folder
4. **Visit** `https://yourdomain.com/install.php` in your browser
5. Follow the 5-step wizard (guide, requirements, database, admin account, done)

> **Note:** If the requirements check shows "Composer Dependencies: Not installed", you need SSH access to run `composer install --no-dev` in the project root. Most shared hosts have a terminal in cPanel or StackCP.

### From Git Source (for developers)

Requires PHP, Composer, Node.js, and npm.

```bash
git clone https://github.com/azfarmiskam/exam-azfarmiskam.git
cd exam-azfarmiskam
composer install
npm install && npm run build
php artisan serve
```
Then visit `http://localhost:8000/install.php`

   Or configure manually:
   ```bash
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm run dev
   ```

## Database Schema

### Core Tables
- `users` - Admin accounts
- `classrooms` - Exam classrooms
- `classroom_groups` - Student groups within classrooms
- `categories` - Question categories
- `questions` - Question bank
- `classroom_questions` - Classroom-question relationships
- `students` - Student information
- `exam_sessions` - Active/completed exam sessions
- `student_answers` - Student responses
- `activity_logs` - System activity tracking

## Default Credentials

**Admin Account**
- Email: admin@exam.test
- Password: password

⚠️ **Important**: Change the default password immediately after first login!

## Security Features

- CSRF protection on all forms
- Password hashing with bcrypt
- Activity logging for audit trails
- Login rate limiting (5 attempts per minute per IP+email)
- Strengthened CAPTCHA with random operations (+, -, ×) — server-generated only
- Content Security Policy (CSP) headers
- X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy headers
- Guest middleware on login routes (redirects authenticated users)
- Auth middleware on logout route
- Student identity verification for exam session access
- Results page requires matric number + email verification
- No error detail leaks in API responses
- Prevent copy/paste in exam interface
- Session management
- SQL injection protection via Eloquent ORM
- Server-side expired exam enforcement

## Development Workflow

1. **Start development server**
   ```bash
   npm run dev
   ```

2. **Run tests**
   ```bash
   php artisan test
   ```

3. **Code formatting**
   ```bash
   ./vendor/bin/pint
   ```

## File Structure

```
exam.azfarmiskam/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   └── storage/        # Symlinked storage
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   └── ...
└── storage/
    └── app/public/     # Uploaded files (question images)
```

## Storage Setup

Link storage for file uploads:
```bash
php artisan storage:link
```

## Troubleshooting

### Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Issues
```bash
php artisan migrate:fresh --seed
```

## Support

For issues or questions, please contact the development team.

## License

ExamJe by [AzfarMiskam](https://azfarmiskam.site). All rights reserved.

---

**Version**: 2.2.0  
**Last Updated**: April 2, 2026

For full version history, see [CHANGELOG.md](CHANGELOG.md).
