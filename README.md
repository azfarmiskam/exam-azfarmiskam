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

### Web Installer (Recommended for shared hosting)

Works on **cPanel**, **StackCP**, **VPS**, or any PHP hosting.

1. **Upload files** to your server (via FTP, Git, or file manager)
2. **Install dependencies** (via SSH or hosting terminal):
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install && npm run build
   ```
3. **Visit** `https://yourdomain.com/install.php` in your browser
4. Follow the 4-step wizard:
   - **Step 1**: Server requirements check
   - **Step 2**: Database configuration (MySQL/MariaDB/PostgreSQL/SQLite)
   - **Step 3**: Create admin account
   - **Step 4**: Installation complete
5. **Delete** `public/install.php` after installation for security

### Local Development

1. **Clone and install**
   ```bash
   git clone https://github.com/azfarmiskam/exam-azfarmiskam.git
   cd exam-azfarmiskam
   composer install
   npm install
   ```

2. **Run the web installer**
   ```bash
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

**Version**: 2.0.0  
**Last Updated**: April 2, 2026

---

## Version History

### v2.0.0 (April 2, 2026)

#### Rebrand
- Renamed system from EzExam to **ExamJe**
- Updated all views, titles, footers, installer, tutorial, config, and documentation

#### Features
- Teacher tutorial page (`/tutorial.html`) with full guide
- Tutorial link (book icon) in admin top bar
- Dynamic logo upload from Settings page with reset to default
- Version number displayed in all footers dynamically
- Announcement repeat interval options (15s, 30s, 1min, 2min, 5min)
- Announcement duration options (1-60 minutes)
- Overlay crawler (fixed position, no page push, click-through)
- Announcements work in preview mode
- Faster announcement polling (5 seconds)

### v1.4.0 (April 2, 2026)

#### Fixes
- Fix empty dropdowns in Announcements and Create Question — classrooms and categories now preload on dashboard init

#### Features
- Real-time announcement system — teachers can send scrolling messages to students mid-exam
- Announcements page in admin sidebar with send form, duration selector, and history table
- Student exam page polls for announcements every 15 seconds
- Announcements auto-expire based on set duration (5 min to 2 hours)
- Stop/delete announcements from admin panel
- Replaced static crawler_text field on classrooms with the new real-time system

### v1.3.1 (April 2, 2026)

#### Fixes
- "Finish" button on last question now opens submit confirmation modal instead of doing nothing

### v1.3.0 (April 2, 2026)

#### Features
- Question difficulty levels (1-5 stars) — creator sets difficulty, other teachers can filter by level when selecting questions
- Star rating selector in question create/edit modal
- Difficulty stars displayed in question bank table
- Difficulty filter dropdown in question bank
- Difficulty included in CSV import/export

### v1.2.1 (April 2, 2026)

#### Features
- Question bank CSV import — bulk upload questions via CSV file with auto-category creation
- Question bank CSV export — download all questions as CSV
- CSV template download — pre-formatted template for easy import
- Web-based installer (`public/install.php`) — 5-step wizard for shared hosting (cPanel, StackCP, VPS)
- Build script (`build.sh`) — creates clean distributable zip without secrets

#### Branding
- Added "by AzfarMiskam" trademark with link to https://azfarmiskam.site on all footers

### v1.2.0 (April 2, 2026)

#### Security
- Fixed CAPTCHA bypass vulnerability — server now generates all numbers, client cannot set answers
- Added login rate limiting (5 attempts/minute per IP+email)
- Fixed weak student auth check in exam session data endpoint
- Added student verification to exam take route
- Added guest middleware to login routes, auth middleware to logout
- Removed error detail leaks (stack traces, messages) from API responses
- Added Content-Security-Policy headers with XSS, clickjacking, and frame protection
- Added X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy headers

#### Admin Features
- Admin password change (Settings page)
- Real-time notification bell — shows recent exam completions with unread count
- Announcement crawler — scrolling message per classroom displayed to students during exam
- Fixed sidebar collapsed mode — icons and avatar now centered properly

#### Student UX
- Timer warnings — yellow banner at 5 min, red banner at 1 min remaining
- Auto-submit on expiry — no confirmation modal, submits immediately with loading overlay
- Server-side expired exam enforcement — auto-grades if student revisits expired session
- Improved results page — animated score ring gauge, pass/fail/expired badge, color-coded stats, inline answer review
- Loading skeleton — shimmer placeholders while exam data loads
- Answer save indicator — "Saving..." / "Saved" feedback on answer selection
- Submit loading overlay — full-screen spinner during exam submission
- Results verification gate — students must enter matric number + email to view results
- Students can recheck results anytime via verification

#### UI/Branding
- Added system versioning (`config('app.version')`)
- Replaced emoji logos with actual logo image across all pages
- Responsive improvements for all exam views (phone, tablet, desktop)
- Fixed submit confirmation modal responsiveness

### v1.0.0 (December 15, 2025)
- Initial release
- Admin dashboard with classroom, question, category, student, and group management
- Student exam flow: register, instructions, take exam, view results
- Multiple-choice questions with image support and answer shuffling
- Timer countdown with configurable duration
- Anti-cheat measures (copy/paste/right-click disabled)
- Activity logging
- CAPTCHA on admin login
