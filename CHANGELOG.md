# ExamJe Changelog

All notable changes to this project will be documented in this file.

## v2.0.0 (April 2, 2026)

### Rebrand
- Renamed system from EzExam to **ExamJe**
- Updated all views, titles, footers, installer, tutorial, config, and documentation
- New ExamJe logo

### Features
- Professional admin dashboard with stat cards, quick actions, active classrooms, recent results, and score distribution chart
- Teacher tutorial page (`/tutorial.html`) with full guide
- Tutorial link (book icon) in admin top bar
- Dynamic logo upload from Settings page with reset to default
- Version number displayed in all footers dynamically
- Announcement repeat interval options (15s, 30s, 1min, 2min, 5min)
- Announcement duration options (1-60 minutes)
- Overlay crawler (fixed position, no page push, click-through)
- Announcements work in preview mode
- Faster announcement polling (5 seconds)
- Sticky glassmorphism header on homepage
- Hover effects on dashboard cards, quick actions, and table rows

### Fixes
- Fix empty dropdowns in Announcements and Create Question — preload on dashboard init
- Fix homepage content hidden behind fixed header
- Fix footer text readability

---

## v1.4.0 (April 2, 2026)

### Features
- Real-time announcement system — teachers can send scrolling messages to students mid-exam
- Announcements page in admin sidebar with send form, duration selector, and history table
- Student exam page polls for announcements every 15 seconds
- Announcements auto-expire based on set duration (5 min to 2 hours)
- Stop/delete announcements from admin panel
- Replaced static crawler_text field on classrooms with the new real-time system

---

## v1.3.1 (April 2, 2026)

### Fixes
- "Finish" button on last question now opens submit confirmation modal instead of doing nothing

---

## v1.3.0 (April 2, 2026)

### Features
- Question difficulty levels (1-5 stars) — creator sets difficulty, other teachers can filter by level when selecting questions
- Star rating selector in question create/edit modal
- Difficulty stars displayed in question bank table
- Difficulty filter dropdown in question bank
- Difficulty included in CSV import/export

---

## v1.2.1 (April 2, 2026)

### Features
- Question bank CSV import — bulk upload questions via CSV file with auto-category creation
- Question bank CSV export — download all questions as CSV
- CSV template download — pre-formatted template for easy import
- Web-based installer (`public/install.php`) — 5-step wizard for shared hosting (cPanel, StackCP, VPS)
- Build script (`build.sh`) — creates clean distributable zip without secrets

### Branding
- Added "by AzfarMiskam" trademark with link to https://azfarmiskam.site on all footers

---

## v1.2.0 (April 2, 2026)

### Security
- Fixed CAPTCHA bypass vulnerability — server now generates all numbers, client cannot set answers
- Added login rate limiting (5 attempts/minute per IP+email)
- Fixed weak student auth check in exam session data endpoint
- Added student verification to exam take route
- Added guest middleware to login routes, auth middleware to logout
- Removed error detail leaks (stack traces, messages) from API responses
- Added Content-Security-Policy headers with XSS, clickjacking, and frame protection
- Added X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy headers

### Admin Features
- Admin password change (Settings page)
- Real-time notification bell — shows recent exam completions with unread count
- Announcement crawler — scrolling message per classroom displayed to students during exam
- Fixed sidebar collapsed mode — icons and avatar now centered properly

### Student UX
- Timer warnings — yellow banner at 5 min, red banner at 1 min remaining
- Auto-submit on expiry — no confirmation modal, submits immediately with loading overlay
- Server-side expired exam enforcement — auto-grades if student revisits expired session
- Improved results page — animated score ring gauge, pass/fail/expired badge, color-coded stats, inline answer review
- Loading skeleton — shimmer placeholders while exam data loads
- Answer save indicator — "Saving..." / "Saved" feedback on answer selection
- Submit loading overlay — full-screen spinner during exam submission
- Results verification gate — students must enter matric number + email to view results
- Students can recheck results anytime via verification

### UI/Branding
- Added system versioning (`config('app.version')`)
- Replaced emoji logos with actual logo image across all pages
- Responsive improvements for all exam views (phone, tablet, desktop)
- Fixed submit confirmation modal responsiveness

---

## v1.0.0 (December 15, 2025)
- Initial release
- Admin dashboard with classroom, question, category, student, and group management
- Student exam flow: register, instructions, take exam, view results
- Multiple-choice questions with image support and answer shuffling
- Timer countdown with configurable duration
- Anti-cheat measures (copy/paste/right-click disabled)
- Activity logging
- CAPTCHA on admin login
