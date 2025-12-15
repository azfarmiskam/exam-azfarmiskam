---
description: Exam System Implementation Plan
---

# Exam System - Project Implementation Plan

## Project Overview
A comprehensive online exam system with multiple-choice questions, supporting admin management and student exam-taking functionality.

## Technical Stack
- **Framework**: Laravel 12
- **Database**: MariaDB (Production), SQLite (Local Dev)
- **Frontend**: Blade Templates, Vite, Vanilla CSS/JS
- **Local Dev**: Laravel Herd (exam.azfarmiskam.test)
- **Production**: StackCP Shared Hosting (exam.azfarmiskam.site)

## Database Schema

### Users Table (Admins only)
- id
- name
- email
- password
- is_active (boolean)
- created_at, updated_at

### Classrooms Table
- id
- name
- code (unique, for student access)
- description
- instructions (front page text)
- questions_per_exam (integer)
- question_order (enum: sequential, random)
- timer_minutes (nullable)
- auto_submit (boolean)
- result_visibility (enum: immediate, email, both, hidden)
- has_groups (boolean)
- is_active (boolean)
- created_by (admin user_id)
- created_at, updated_at

### Classroom_Groups Table
- id
- classroom_id
- name
- created_at, updated_at

### Categories Table
- id
- name
- description
- created_at, updated_at

### Questions Table
- id
- category_id
- question_text
- image_path (nullable)
- option_a
- option_b
- option_c
- option_d
- correct_answer (enum: a, b, c, d)
- created_by (admin user_id)
- created_at, updated_at

### Classroom_Questions Table (Pivot)
- id
- classroom_id
- question_id
- order (nullable)

### Students Table
- id
- classroom_id
- group_id (nullable)
- name
- email
- phone (nullable)
- matric_number (nullable)
- created_at, updated_at

### Exam_Sessions Table
- id
- student_id
- classroom_id
- started_at
- submitted_at (nullable)
- time_remaining (seconds)
- total_score
- total_questions
- status (enum: in_progress, submitted, expired)

### Student_Answers Table
- id
- exam_session_id
- question_id
- selected_answer (enum: a, b, c, d, nullable)
- is_correct (boolean)
- is_skipped (boolean, default false)
- created_at, updated_at

### Activity_Logs Table
- id
- user_id (nullable)
- action
- description
- ip_address
- created_at

## Implementation Phases

### Phase 1: Setup & Authentication ✓
1. Configure database connections (MariaDB for production)
2. Install Laravel Breeze or custom auth for admin
3. Create migrations for all tables
4. Setup seeders for initial admin user
5. Configure Herd local domain

### Phase 2: Admin Dashboard & Core Features
1. Admin dashboard layout with sidebar navigation
2. Analytics dashboard (total classrooms, students, exams)
3. Activity logs viewer
4. Admin CRUD functionality
5. General settings page

### Phase 3: Question Bank Management
1. Category CRUD
2. Question CRUD with image upload
3. Question listing with filters by category
4. Bulk import questions (optional)

### Phase 4: Classroom Management
1. Classroom CRUD
2. Group management within classrooms
3. Question assignment to classrooms
4. Classroom settings (timer, result visibility, etc.)
5. Generate unique classroom codes

### Phase 5: Student Exam Interface
1. Homepage with code entry
2. Student registration form
3. Exam interface with:
   - Timer countdown
   - Question navigation
   - Skip functionality
   - Answer selection
   - Submit with confirmation
4. Disable right-click and copy
5. Result display based on settings

### Phase 6: Results & Analytics
1. Student results listing per classroom
2. Individual student performance view
3. Export results to CSV/PDF
4. Email results functionality

### Phase 7: UI/UX Polish
1. Blue color scheme implementation
2. Custom modal dialogs
3. Responsive design (mobile & desktop)
4. Loading states and transitions
5. Form validations

### Phase 8: Deployment Preparation
1. Environment configuration for StackCP
2. Database migration scripts
3. File upload configuration
4. .htaccess for shared hosting
5. Deployment documentation

## Color Scheme (Blue Theme)
- Primary: #2563eb (Blue 600)
- Primary Dark: #1e40af (Blue 700)
- Primary Light: #3b82f6 (Blue 500)
- Secondary: #60a5fa (Blue 400)
- Background: #f8fafc (Slate 50)
- Text: #1e293b (Slate 800)
- Border: #cbd5e1 (Slate 300)

## Security Considerations
1. CSRF protection on all forms
2. Rate limiting on exam access
3. Prevent multiple submissions
4. Secure file uploads
5. Admin authentication middleware
6. Activity logging for audit trail

## Next Steps
Start with Phase 1: Database setup and authentication system.
