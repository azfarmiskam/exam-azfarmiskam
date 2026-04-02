<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - ExamJe</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/css/toast.css'])
</head>
<body>
    <div class="dashboard-layout">
        <!-- Mobile Menu Button -->
        <button class="mobile-menu-btn" id="mobileMenuBtn">
            ☰
        </button>

        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                    <img src="{{ $logoUrl }}" alt="ExamJe" style="height: 40px; width: auto; object-fit: contain;">
                </a>
                <button class="sidebar-toggle" id="sidebarToggle" title="Toggle Sidebar">
                    <span id="toggleIcon">◀</span>
                </button>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="sidebar-nav">
                <!-- Main Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link active" data-page="dashboard">
                                <span class="nav-icon">📊</span>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Management Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="classrooms">
                                <span class="nav-icon">🏫</span>
                                <span class="nav-text">Classrooms</span>
                                <span class="nav-badge">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="questions">
                                <span class="nav-icon">❓</span>
                                <span class="nav-text">Question Bank</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="categories">
                                <span class="nav-icon">📁</span>
                                <span class="nav-text">Categories</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="groups">
                                <span class="nav-icon">👥</span>
                                <span class="nav-text">Groups</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="students">
                                <span class="nav-icon">🎓</span>
                                <span class="nav-text">Students</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="announcements">
                                <span class="nav-icon">📢</span>
                                <span class="nav-text">Announcements</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Reports Section -->
                <div class="nav-section">
                    <div class="nav-section-title">Reports</div>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="results">
                                <span class="nav-icon">📈</span>
                                <span class="nav-text">Exam Results</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="analytics">
                                <span class="nav-icon">📉</span>
                                <span class="nav-text">Analytics</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="activity">
                                <span class="nav-icon">📋</span>
                                <span class="nav-text">Activity Logs</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- System Section -->
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <ul class="nav-menu">
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="admins">
                                <span class="nav-icon">👤</span>
                                <span class="nav-text">Admin Users</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link" data-page="settings">
                                <span class="nav-icon">⚙️</span>
                                <span class="nav-text">Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ Auth::user()->name }}</div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <h1 class="page-title" id="pageTitle">Dashboard</h1>
                <div class="top-bar-actions">
                    <div class="notif-wrapper" id="notifWrapper">
                        <button class="action-btn" title="Notifications" onclick="toggleNotifications()">
                            <span>🔔</span>
                            <span class="badge" id="notifBadge" style="display: none;">0</span>
                        </button>
                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-header">
                                <span style="font-weight: 700; font-size: 0.9375rem;">Notifications</span>
                                <button class="notif-mark-read" onclick="markAllRead()">Mark all read</button>
                            </div>
                            <div class="notif-list" id="notifList">
                                <div class="notif-empty">Loading...</div>
                            </div>
                        </div>
                    </div>
                    <a href="/tutorial.html" target="_blank" class="action-btn" title="Tutorial" style="text-decoration: none;">
                        <span>📖</span>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            </header>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Dashboard Content -->
                <div class="spa-content active" id="page-dashboard">
                    <div class="dashboard-grid">
                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon">🏫</div>
                            </div>
                            <div class="stat-value" id="totalClassrooms">0</div>
                            <div class="stat-label">Total Classrooms</div>
                            <div class="stat-change positive">↑ 0 this month</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon">❓</div>
                            </div>
                            <div class="stat-value" id="totalQuestions">0</div>
                            <div class="stat-label">Question Bank</div>
                            <div class="stat-change positive">↑ 0 this month</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon">👥</div>
                            </div>
                            <div class="stat-value" id="totalStudents">0</div>
                            <div class="stat-label">Total Students</div>
                            <div class="stat-change positive">↑ 0 this month</div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-header">
                                <div class="stat-icon">📝</div>
                            </div>
                            <div class="stat-value" id="totalExams">0</div>
                            <div class="stat-label">Exams Taken</div>
                            <div class="stat-change positive">↑ 0 this week</div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h3>Welcome to ExamJe Admin Dashboard!</h3>
                            <p>Get started by creating your first classroom or adding questions to the question bank.</p>
                        </div>
                    </div>
                </div>

                <!-- Classrooms Content -->
                <div class="spa-content" id="page-classrooms">
                    <!-- Header with Add Button -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div>
                            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Classrooms</h2>
                            <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">Manage your exam classrooms and settings</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateModal()" style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>➕</span>
                            <span>Create Classroom</span>
                        </button>
                    </div>

                    <!-- Classrooms Table -->
                    <div class="card">
                        <div class="card-body" style="padding: 0;">
                            <div style="overflow-x: auto;">
                                <table class="data-table" id="classroomsTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Questions</th>
                                            <th>Students</th>
                                            <th>Timer</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="classroomsTableBody">
                                        <tr>
                                            <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                                <div style="font-size: 3rem; margin-bottom: 1rem;">📚</div>
                                                <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No classrooms yet</div>
                                                <div style="font-size: 0.875rem;">Create your first classroom to get started</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Content -->
                <div class="spa-content" id="page-questions">
                    <!-- Header with Add Button -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div>
                            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Question Bank</h2>
                            <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">Manage exam questions</p>
                        </div>
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            <a href="/admin/api/questions-export" class="btn btn-secondary" style="display: flex; align-items: center; gap: 0.5rem; width: auto;">
                                <span>📥</span>
                                <span>Export CSV</span>
                            </a>
                            <button class="btn btn-secondary" onclick="openImportModal()" style="display: flex; align-items: center; gap: 0.5rem; width: auto;">
                                <span>📤</span>
                                <span>Import CSV</span>
                            </button>
                            <button class="btn btn-primary" onclick="openCreateQuestionModal()" style="display: flex; align-items: center; gap: 0.5rem; width: auto;">
                                <span>➕</span>
                                <span>Add Question</span>
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem;">
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Category</label>
                                    <select id="questionCategoryFilter" class="form-control" onchange="filterQuestions()">
                                        <option value="">All Categories</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Difficulty</label>
                                    <select id="questionDifficultyFilter" class="form-control" onchange="filterQuestions()">
                                        <option value="">All Levels</option>
                                        <option value="1">&#9733; Very Easy</option>
                                        <option value="2">&#9733;&#9733; Easy</option>
                                        <option value="3">&#9733;&#9733;&#9733; Medium</option>
                                        <option value="4">&#9733;&#9733;&#9733;&#9733; Hard</option>
                                        <option value="5">&#9733;&#9733;&#9733;&#9733;&#9733; Very Hard</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Search</label>
                                    <input type="text" id="questionSearch" class="form-control" placeholder="Search questions..." onkeyup="filterQuestions()">
                                </div>
                                <div style="display: flex; align-items: flex-end;">
                                    <button class="btn btn-secondary" onclick="clearFilters()">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Questions Table -->
                    <div class="card">
                        <div class="card-body" style="padding: 0;">
                            <div style="overflow-x: auto;">
                                <table class="data-table" id="questionsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 35%;">Question</th>
                                            <th>Category</th>
                                            <th>Difficulty</th>
                                            <th>Answer</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="questionsTableBody">
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                                <div style="font-size: 3rem; margin-bottom: 1rem;">❓</div>
                                                <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No questions yet</div>
                                                <div style="font-size: 0.875rem;">Add your first question to get started</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Content -->
                <div class="spa-content" id="page-categories">
                    <!-- Header with Add Button -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div>
                            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Categories</h2>
                            <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">Organize questions by category</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateCategoryModal()" style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>➕</span>
                            <span>Create Category</span>
                        </button>
                    </div>

                    <!-- Categories Table -->
                    <div class="card">
                        <div class="card-body" style="padding: 0;">
                            <div style="overflow-x: auto;">
                                <table class="data-table" id="categoriesTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Description</th>
                                            <th>Questions</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="categoriesTableBody">
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                                <div style="font-size: 3rem; margin-bottom: 1rem;">📁</div>
                                                <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No categories yet</div>
                                                <div style="font-size: 0.875rem;">Create your first category to organize questions</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Students Content -->
                <div class="spa-content" id="page-students">
                    <!-- Header -->
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Exam Participants</h2>
                        <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">View students who have registered for exams (students self-register when taking exams)</p>
                    </div>

                    <!-- Filters -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem;">
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Classroom</label>
                                    <select id="studentClassroomFilter" class="form-control" onchange="filterStudents()">
                                        <option value="">All Classrooms</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Group</label>
                                    <select id="studentGroupFilter" class="form-control" onchange="filterStudents()">
                                        <option value="">All Groups</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Search</label>
                                    <input type="text" id="studentSearch" class="form-control" placeholder="Name, email, matric..." onkeyup="filterStudents()">
                                </div>
                                <div style="display: flex; align-items: flex-end;">
                                    <button class="btn btn-secondary" onclick="clearStudentFilters()">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="card">
                        <div class="card-body" style="padding: 0;">
                            <div style="overflow-x: auto;">
                                <table class="data-table" id="studentsTable">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Matric Number</th>
                                            <th>Classroom</th>
                                            <th>Group</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentsTableBody">
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                                <div style="font-size: 3rem; margin-bottom: 1rem;">🎓</div>
                                                <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No exam participants yet</div>
                                                <div style="font-size: 0.875rem;">Students will appear here after they register for exams</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Groups Content -->
                <div class="spa-content" id="page-groups">
                    <!-- Header with Add Button -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div>
                            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Groups</h2>
                            <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">Manage classroom groups</p>
                        </div>
                        <button class="btn btn-primary" onclick="openCreateGroupModal()" style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>➕</span>
                            <span>Create Group</span>
                        </button>
                    </div>

                    <!-- Filter -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem;">
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Classroom</label>
                                    <select id="groupClassroomFilter" class="form-control" onchange="filterGroups()">
                                        <option value="">All Classrooms</option>
                                    </select>
                                </div>
                                <div style="display: flex; align-items: flex-end;">
                                    <button class="btn btn-secondary" onclick="clearGroupFilters()">Clear</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Groups Table -->
                    <div class="card">
                        <div class="card-body" style="padding: 0;">
                            <div style="overflow-x: auto;">
                                <table class="data-table" id="groupsTable">
                                    <thead>
                                        <tr>
                                            <th>Group Name</th>
                                            <th>Classroom</th>
                                            <th>Students</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="groupsTableBody">
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                                                <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                                                <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No groups yet</div>
                                                <div style="font-size: 0.875rem;">Create your first group to organize students</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Content -->
                <div class="spa-content" id="page-results">
                    <!-- Header -->
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Exam Results</h2>
                        <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">View and analyze student exam performance</p>
                    </div>

                    <!-- Filters -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem;">
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Classroom</label>
                                    <select id="resultsClassroomFilter" class="form-control" onchange="filterResults()">
                                        <option value="">All Classrooms</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Status</label>
                                    <select id="resultsStatusFilter" class="form-control" onchange="filterResults()">
                                        <option value="">All Status</option>
                                        <option value="completed">Completed</option>
                                        <option value="in_progress">In Progress</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Search Student</label>
                                    <input type="text" id="resultsSearchInput" class="form-control" placeholder="Search by name or matric..." onkeyup="filterResults()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Matric No.</th>
                                            <th>Classroom</th>
                                            <th>Score</th>
                                            <th>Questions</th>
                                            <th>Status</th>
                                            <th>Completed At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="resultsTableBody">
                                        <tr>
                                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                                Loading results...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Content -->
                <div class="spa-content" id="page-analytics">
                    <!-- Header -->
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Analytics</h2>
                        <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">Performance insights and statistics</p>
                    </div>

                    <!-- Stats Cards -->
                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                        <div class="card">
                            <div class="card-body">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <div style="font-size: 0.875rem; color: var(--text-secondary);">Total Exams</div>
                                    <span style="font-size: 1.5rem;">📊</span>
                                </div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);" id="analyticsTotalExams">0</div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <div style="font-size: 0.875rem; color: var(--text-secondary);">Completed</div>
                                    <span style="font-size: 1.5rem;">✅</span>
                                </div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--success);" id="analyticsCompleted">0</div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <div style="font-size: 0.875rem; color: var(--text-secondary);">Average Score</div>
                                    <span style="font-size: 1.5rem;">📈</span>
                                </div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--warning);" id="analyticsAvgScore">0%</div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.5rem;">
                                    <div style="font-size: 0.875rem; color: var(--text-secondary);">Pass Rate</div>
                                    <span style="font-size: 1.5rem;">🎯</span>
                                </div>
                                <div style="font-size: 2rem; font-weight: 700; color: var(--success);" id="analyticsPassRate">0%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance by Classroom -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.125rem; font-weight: 700;">Performance by Classroom</h3>
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Classroom</th>
                                            <th>Total Students</th>
                                            <th>Completed</th>
                                            <th>Average Score</th>
                                            <th>Pass Rate</th>
                                            <th>Highest Score</th>
                                            <th>Lowest Score</th>
                                        </tr>
                                    </thead>
                                    <tbody id="analyticsClassroomTable">
                                        <tr>
                                            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                                Loading analytics...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="card">
                        <div class="card-body">
                            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.125rem; font-weight: 700;">Recent Exam Completions</h3>
                            <div id="analyticsRecentActivity">
                                <p style="text-align: center; padding: 2rem; color: var(--text-secondary);">Loading recent activity...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Content -->
                <div class="spa-content" id="page-activity">
                    <!-- Header -->
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Activity Logs</h2>
                        <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">Recent system activities and events</p>
                    </div>

                    <!-- Filter -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Activity Type</label>
                                    <select id="activityTypeFilter" class="form-control" onchange="filterActivities()">
                                        <option value="">All Activities</option>
                                        <option value="exam_started">Exam Started</option>
                                        <option value="exam_completed">Exam Completed</option>
                                        <option value="student_registered">Student Registered</option>
                                        <option value="classroom_created">Classroom Created</option>
                                        <option value="question_added">Question Added</option>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Search</label>
                                    <input type="text" id="activitySearchInput" class="form-control" placeholder="Search activities..." onkeyup="filterActivities()">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Timeline -->
                    <div class="card">
                        <div class="card-body">
                            <div id="activityTimeline">
                                <p style="text-align: center; padding: 2rem; color: var(--text-secondary);">Loading activities...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Announcements Content -->
                <div class="spa-content" id="page-announcements">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div>
                            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Announcements</h2>
                            <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">Send real-time messages to students during exams</p>
                        </div>
                    </div>

                    <!-- Send Announcement Form -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <h3 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 700;">Send Announcement</h3>
                            <form id="announcementForm" onsubmit="sendAnnouncement(event)">
                                <div class="form-group">
                                    <label class="form-label">Exam Room</label>
                                    <select name="classroom_id" id="announcementClassroom" class="form-control" required>
                                        <option value="">Select exam room</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Message</label>
                                    <input type="text" name="message" class="form-control" placeholder="Type your announcement message..." maxlength="500" required>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 1rem; align-items: flex-end;">
                                    <div class="form-group" style="margin: 0;">
                                        <label class="form-label">Duration</label>
                                        <select name="duration" class="form-control">
                                            <option value="1">1 minute</option>
                                            <option value="2">2 minutes</option>
                                            <option value="3">3 minutes</option>
                                            <option value="4">4 minutes</option>
                                            <option value="5" selected>5 minutes</option>
                                            <option value="10">10 minutes</option>
                                            <option value="15">15 minutes</option>
                                            <option value="20">20 minutes</option>
                                            <option value="30">30 minutes</option>
                                            <option value="40">40 minutes</option>
                                            <option value="50">50 minutes</option>
                                            <option value="60">60 minutes</option>
                                        </select>
                                    </div>
                                    <div class="form-group" style="margin: 0;">
                                        <label class="form-label">Repeat Interval</label>
                                        <select name="repeat_interval" class="form-control">
                                            <option value="0">No repeat (show once)</option>
                                            <option value="15">Every 15 seconds</option>
                                            <option value="30" selected>Every 30 seconds</option>
                                            <option value="60">Every 1 minute</option>
                                            <option value="120">Every 2 minutes</option>
                                            <option value="300">Every 5 minutes</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary" style="width: auto; white-space: nowrap;" id="sendAnnouncementBtn">
                                        Send
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Active Announcements -->
                    <div class="card">
                        <div class="card-body">
                            <h3 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 700;">Announcement History</h3>
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Exam Room</th>
                                            <th>Message</th>
                                            <th>Status</th>
                                            <th>Expires</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="announcementsTableBody">
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                                Loading...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Admins Content -->
                <div class="spa-content" id="page-admins">
                    <!-- Header -->
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <div>
                            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Admin Users</h2>
                            <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">Manage system administrators</p>
                        </div>
                        <button class="btn btn-primary" onclick="openAdminModal()" style="display: flex; align-items: center; gap: 0.5rem;">
                            <span>➕</span>
                            <span>Add Admin</span>
                        </button>
                    </div>

                    <!-- Admins Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-container">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="adminsTableBody">
                                        <tr>
                                            <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                                                Loading admins...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="spa-content" id="page-settings">
                    <!-- Header -->
                    <div style="margin-bottom: 1.5rem;">
                        <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">Settings</h2>
                        <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary); font-size: 0.875rem;">Configure system settings</p>
                    </div>

                    <!-- General Settings -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.125rem; font-weight: 700;">General Settings</h3>
                            <form id="settingsForm">
                                <div class="form-group">
                                    <label class="form-label">System Name</label>
                                    <input type="text" class="form-control" id="systemName" value="ExamJe System" placeholder="Enter system name">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">System Email</label>
                                    <input type="email" class="form-control" id="systemEmail" value="admin@examje.com" placeholder="Enter system email">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Timezone</label>
                                    <select class="form-control" id="timezone">
                                        <option value="Asia/Kuala_Lumpur" selected>Asia/Kuala Lumpur (UTC+8)</option>
                                        <option value="Asia/Singapore">Asia/Singapore (UTC+8)</option>
                                        <option value="Asia/Jakarta">Asia/Jakarta (UTC+7)</option>
                                        <option value="UTC">UTC</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Exam Settings -->
                    <div class="card" style="margin-bottom: 1.5rem;">
                        <div class="card-body">
                            <h3 style="margin: 0 0 1.5rem 0; font-size: 1.125rem; font-weight: 700;">Exam Settings</h3>
                            <div class="form-group">
                                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="checkbox" id="allowLateSubmission" checked>
                                    <span>Allow late submission (after timer expires)</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="checkbox" id="showScoreImmediately" checked>
                                    <span>Show score immediately after submission</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="form-label" style="display: flex; align-items: center; gap: 0.5rem;">
                                    <input type="checkbox" id="allowReviewAnswers" checked>
                                    <span>Allow students to review answers</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div style="display: flex; justify-content: flex-end; gap: 1rem;">
                        <button type="button" class="btn btn-secondary" onclick="resetSettings()">Reset</button>
                        <button type="button" class="btn btn-primary" onclick="saveSettings()">Save Settings</button>
                    </div>

                    <!-- System Logo -->
                    <div class="card" style="margin-top: 1.5rem;">
                        <div class="card-body">
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.125rem; font-weight: 700;">System Logo</h3>
                            <p style="margin: 0 0 1.25rem 0; color: var(--text-secondary); font-size: 0.875rem;">Upload your own logo. It will replace the default logo across all pages.</p>
                            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
                                <div style="background: var(--bg-light); border-radius: 8px; padding: 1rem; display: flex; align-items: center; justify-content: center; min-width: 120px;">
                                    <img src="{{ $logoUrl }}" alt="Current Logo" id="logoPreview" style="height: 60px; width: auto; object-fit: contain;">
                                </div>
                                <div>
                                    <p style="font-size: 0.75rem; color: var(--text-light); margin-bottom: 0.5rem;">PNG, JPG, SVG, or WebP. Max 2MB.</p>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <label class="btn btn-primary" style="width: auto; cursor: pointer; font-size: 0.8125rem; padding: 0.5rem 1rem;">
                                            Upload Logo
                                            <input type="file" id="logoFile" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="display: none;" onchange="uploadLogo()">
                                        </label>
                                        <button type="button" class="btn btn-secondary" style="width: auto; font-size: 0.8125rem; padding: 0.5rem 1rem;" onclick="resetLogo()">Reset to Default</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="card" style="margin-top: 1.5rem;">
                        <div class="card-body">
                            <h3 style="margin: 0 0 0.5rem 0; font-size: 1.125rem; font-weight: 700;">Change Password</h3>
                            <p style="margin: 0 0 1.5rem 0; color: var(--text-secondary); font-size: 0.875rem;">Update your account password. You will stay logged in after changing.</p>
                            <form id="changePasswordForm" onsubmit="changePassword(event)">
                                <div class="form-group">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="currentPassword" required placeholder="Enter current password">
                                    <span class="form-error" id="currentPasswordError" style="display: none;"></span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="newPassword" required placeholder="Enter new password" minlength="8">
                                    <small style="color: var(--text-secondary); font-size: 0.75rem;">Minimum 8 characters</small>
                                    <span class="form-error" id="newPasswordError" style="display: none;"></span>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirmPassword" required placeholder="Confirm new password">
                                    <span class="form-error" id="confirmPasswordError" style="display: none;"></span>
                                </div>
                                <div style="display: flex; justify-content: flex-end;">
                                    <button type="submit" class="btn btn-primary" id="changePasswordBtn">Change Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Notifications Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Modals -->
    <!-- Create/Edit Classroom Modal -->
    <div class="modal" id="classroomModal" style="display: none;">
        <div class="modal-overlay" onclick="closeClassroomModal()"></div>
        <div class="modal-content" style="max-width: 800px;">
            <div class="modal-header">
                <h3 id="modalTitle">Create Classroom</h3>
                <button class="modal-close" onclick="closeClassroomModal()">×</button>
            </div>
            <form id="classroomForm" onsubmit="saveClassroom(event)">
                <div class="modal-body">
                    <div class="form-grid">
                        <!-- Left Column -->
                        <div class="form-column">
                            <div class="form-group">
                                <label class="form-label">Classroom Name *</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g., Mathematics 101">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Questions Per Exam *</label>
                                <input type="number" name="questions_per_exam" class="form-control" required min="1" value="10">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Timer (Minutes)</label>
                                <input type="number" name="timer_minutes" class="form-control" min="1" placeholder="Leave empty for no timer">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Optional description"></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="form-column">
                            <div class="form-group">
                                <label class="form-label">Instructions</label>
                                <textarea name="instructions" class="form-control" rows="3" placeholder="Exam instructions for students"></textarea>
                            </div>


                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="show_results_immediately" checked>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Show results immediately</span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="show_correct_answers">
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Show correct answers</span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="allow_review" checked>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Allow review</span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="shuffle_questions">
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Shuffle Questions (Random Order)</span>
                                </label>
                            </div>

                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="is_active" checked>
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Active</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeClassroomModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Create Classroom</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal" style="display: none;">
        <div class="modal-overlay" onclick="closeDeleteModal()"></div>
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3>Delete Classroom</h3>
                <button class="modal-close" onclick="closeDeleteModal()">×</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this classroom? This action cannot be undone.</p>
                <p style="color: var(--danger); font-weight: 600;" id="deleteClassroomName"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn" style="background: var(--danger); color: white;" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div class="modal" id="categoryModal" style="display: none;">
        <div class="modal-overlay" onclick="closeCategoryModal()"></div>
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 id="categoryModalTitle">Create Category</h3>
                <button class="modal-close" onclick="closeCategoryModal()">×</button>
            </div>
            <form id="categoryForm" onsubmit="saveCategory(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Category Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g., Mathematics">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Optional description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeCategoryModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="categorySaveBtn">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Question Modal -->
    <div class="modal" id="questionModal" style="display: none;">
        <div class="modal-overlay" onclick="closeQuestionModal()"></div>
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h3 id="questionModalTitle">Add Question</h3>
                <button class="modal-close" onclick="closeQuestionModal()">×</button>
            </div>
            <form id="questionForm" onsubmit="saveQuestion(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Category *</label>
                        <select name="category_id" class="form-control" required id="questionCategorySelect">
                            <option value="">Select category</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Question *</label>
                        <textarea name="question_text" class="form-control" rows="3" required placeholder="Enter your question"></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Option A *</label>
                            <input type="text" name="option_a" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Option B *</label>
                            <input type="text" name="option_b" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Option C *</label>
                            <input type="text" name="option_c" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Option D *</label>
                            <input type="text" name="option_d" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="shuffle_answers">
                            <span class="checkbox-custom"></span>
                            <span class="checkbox-text">Shuffle Answers (Hide ABCD labels)</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Correct Answer *</label>
                        <select name="correct_answer" class="form-control" required>
                            <option value="">Select correct answer</option>
                            <option value="a">A</option>
                            <option value="b">B</option>
                            <option value="c">C</option>
                            <option value="d">D</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Difficulty Level</label>
                        <div class="star-rating" id="difficultyRating">
                            <input type="hidden" name="difficulty" id="difficultyInput" value="3">
                            <span class="star active" data-value="1">&#9733;</span>
                            <span class="star active" data-value="2">&#9733;</span>
                            <span class="star active" data-value="3">&#9733;</span>
                            <span class="star" data-value="4">&#9733;</span>
                            <span class="star" data-value="5">&#9733;</span>
                            <span class="star-label" id="difficultyLabel">Medium</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeQuestionModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="questionSaveBtn">Add Question</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Student Modal -->
    <div class="modal" id="studentModal" style="display: none;">
        <div class="modal-overlay" onclick="closeStudentModal()"></div>
        <div class="modal-content" style="max-width: 600px;">
            <div class="modal-header">
                <h3 id="studentModalTitle">Add Student</h3>
                <button class="modal-close" onclick="closeStudentModal()">×</button>
            </div>
            <form id="studentForm" onsubmit="saveStudent(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Classroom *</label>
                        <select name="classroom_id" class="form-control" required id="studentClassroomSelect" onchange="loadGroupsForStudent(this.value)">
                            <option value="">Select classroom</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Group</label>
                        <select name="group_id" class="form-control" id="studentGroupSelect">
                            <option value="">No group</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="Student name">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" required placeholder="student@example.com">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" class="form-control" placeholder="Optional">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Matric Number</label>
                        <input type="text" name="matric_number" class="form-control" placeholder="Optional">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeStudentModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="studentSaveBtn">Add Student</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Group Modal -->
    <div class="modal" id="groupModal" style="display: none;">
        <div class="modal-overlay" onclick="closeGroupModal()"></div>
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 id="groupModalTitle">Create Group</h3>
                <button class="modal-close" onclick="closeGroupModal()">×</button>
            </div>
            <form id="groupForm" onsubmit="saveGroup(event)">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Classroom *</label>
                        <select name="classroom_id" class="form-control" required id="groupClassroomSelect">
                            <option value="">Select classroom</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Group Name *</label>
                        <input type="text" name="name" class="form-control" required placeholder="e.g., Group A">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeGroupModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="groupSaveBtn">Create Group</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom Confirm Modal -->
    <div class="modal" id="confirmModal" style="display: none;">
        <div class="modal-overlay" onclick="closeConfirmModal(false)"></div>
        <div class="modal-container" style="max-width: 450px;">
            <div class="modal-header">
                <h3 id="confirmTitle">Confirm Action</h3>
            </div>
            <div class="modal-body">
                <p id="confirmMessage" style="font-size: 0.9375rem; line-height: 1.6; color: var(--text-primary);"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeConfirmModal(false)">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmBtn" onclick="closeConfirmModal(true)">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Custom Alert Modal -->
    <div class="modal" id="alertModal" style="display: none;">
        <div class="modal-overlay" onclick="closeAlertModal()"></div>
        <div class="modal-container" style="max-width: 450px;">
            <div class="modal-header">
                <h3 id="alertTitle">Notice</h3>
            </div>
            <div class="modal-body">
                <p id="alertMessage" style="font-size: 0.9375rem; line-height: 1.6; color: var(--text-primary);"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="closeAlertModal()" style="width: 100%;">OK</button>
            </div>
        </div>
    </div>

    <!-- Custom Prompt Modal -->
    <div class="modal" id="promptModal" style="display: none;">
        <div class="modal-overlay" onclick="closePromptModal(null)"></div>
        <div class="modal-container" style="max-width: 450px;">
            <div class="modal-header">
                <h3 id="promptTitle">Input Required</h3>
            </div>
            <div class="modal-body">
                <p id="promptMessage" style="font-size: 0.9375rem; line-height: 1.6; color: var(--text-primary); margin-bottom: 1rem;"></p>
                <input type="text" id="promptInput" class="form-control" placeholder="Enter value">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closePromptModal(null)">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="closePromptModal(document.getElementById('promptInput').value)">OK</button>
            </div>
        </div>
    </div>

    <!-- Exam Details Modal -->
    <div class="modal" id="examDetailsModal" style="display: none;">
        <div class="modal-overlay" onclick="closeExamDetailsModal()"></div>
        <div class="modal-content" style="max-width: 900px; max-height: 90vh; overflow-y: auto;">
            <div class="modal-header">
                <h3>Exam Result Details</h3>
                <button type="button" class="modal-close" onclick="closeExamDetailsModal()">×</button>
            </div>
            <div class="modal-body" id="examDetailsContent">
                <!-- Content will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="closeExamDetailsModal()">Close</button>
            </div>
        </div>
        </div>
    </div>

    <!-- Import Questions Modal -->
    <div class="modal" id="importModal" style="display: none;">
        <div class="modal-overlay" onclick="closeImportModal()"></div>
        <div class="modal-content" style="max-width: 520px;">
            <div class="modal-header">
                <h3>Import Questions from CSV</h3>
                <button type="button" class="modal-close" onclick="closeImportModal()">×</button>
            </div>
            <div class="modal-body">
                <div style="background: var(--bg-light); border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                    <p style="font-size: 0.8125rem; color: var(--text-secondary); margin-bottom: 0.75rem;">
                        Upload a CSV file with these columns:
                    </p>
                    <div style="font-family: monospace; font-size: 0.75rem; background: #1e293b; color: #e2e8f0; padding: 0.625rem 0.875rem; border-radius: 6px; overflow-x: auto; white-space: nowrap;">
                        question_text, option_a, option_b, option_c, option_d, correct_answer, category, shuffle_answers
                    </div>
                    <p style="font-size: 0.75rem; color: var(--text-light); margin-top: 0.5rem;">
                        <strong>correct_answer</strong>: a, b, c, or d &nbsp;|&nbsp;
                        <strong>category</strong>: auto-created if new &nbsp;|&nbsp;
                        <strong>shuffle_answers</strong>: true/false (optional)
                    </p>
                    <a href="/admin/api/questions-template" style="display: inline-flex; align-items: center; gap: 0.375rem; font-size: 0.75rem; color: var(--primary); font-weight: 600; margin-top: 0.5rem; text-decoration: none;">
                        📄 Download CSV Template
                    </a>
                </div>

                <form id="importForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="form-label">CSV File</label>
                        <input type="file" name="file" id="importFile" class="form-control" accept=".csv,.txt" required>
                    </div>
                </form>

                <div id="importResult" style="display: none; margin-top: 1rem;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeImportModal()">Cancel</button>
                <button type="button" class="btn btn-primary" id="importBtn" onclick="submitImport()">Import</button>
            </div>
        </div>
    </div>

    <!-- Admin User Modal -->
    <div class="modal" id="adminModal" style="display: none;">
        <div class="modal-overlay" onclick="closeAdminModal()"></div>
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h3 id="adminModalTitle">Add Admin User</h3>
                <button type="button" class="modal-close" onclick="closeAdminModal()">×</button>
            </div>
            <div class="modal-body">
                <form id="adminForm">
                    <input type="hidden" id="adminId">
                    <div class="form-group">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="adminName" required placeholder="Enter admin name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="adminEmail" required placeholder="Enter email address">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" id="adminPassword" placeholder="Enter password (leave blank to keep current)">
                        <small style="color: var(--text-secondary); font-size: 0.75rem;">Minimum 8 characters</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAdminModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAdmin()">Save</button>
            </div>
        </div>
    </div>

    <!-- Classroom Details Modal -->
    <div class="modal" id="classroomDetailsModal" style="display: none;">
        <div class="modal-overlay" onclick="closeClassroomDetailsModal()"></div>
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h3 id="classroomDetailsTitle">Classroom Details</h3>
                <button class="modal-close" onclick="closeClassroomDetailsModal()">×</button>
            </div>
            <div class="modal-body">
                <div id="classroomDetailsContent"></div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1.5rem;">
                    <button class="btn btn-primary" onclick="openQuestionAssignment()" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span>📝</span>
                        <span>Manage Questions</span>
                    </button>
                    <button class="btn btn-primary" onclick="manageClassroomGroups()" style="display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <span>👥</span>
                        <span>Manage Groups</span>
                    </button>
                </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="previewExam()" style="margin-right: auto;">
                    <span>👁️ Preview Exam</span>
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeClassroomDetailsModal()">Close</button>
            </div>
        </div>
    </div>

    <!-- Question Assignment Modal -->
    <div class="modal" id="questionAssignmentModal" style="display: none;">
        <div class="modal-overlay" onclick="closeQuestionAssignmentModal()"></div>
        <div class="modal-content" style="max-width: 900px;">
            <div class="modal-header">
                <h3>Assign Questions to <span id="assignClassroomName"></span></h3>
                <button class="modal-close" onclick="closeQuestionAssignmentModal()">×</button>
            </div>
            <div class="modal-body">
                <!-- Filter -->
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Filter by Category</label>
                    <select id="assignCategoryFilter" class="form-control" onchange="filterAssignableQuestions()">
                        <option value="">All Categories</option>
                    </select>
                </div>

                <!-- Available Questions -->
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="font-size: 0.9375rem; font-weight: 600; margin-bottom: 0.75rem;">Available Questions</h4>
                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                        <table class="data-table" id="availableQuestionsTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Select</th>
                                    <th>Question</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody id="availableQuestionsBody">
                                <!-- Questions will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Assigned Questions -->
                <div>
                    <h4 style="font-size: 0.9375rem; font-weight: 600; margin-bottom: 0.75rem;">
                        Assigned Questions (<span id="assignedCount">0</span>)
                    </h4>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 0.5rem;">
                        <table class="data-table" id="assignedQuestionsTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">Remove</th>
                                    <th>Question</th>
                                    <th>Category</th>
                                </tr>
                            </thead>
                            <tbody id="assignedQuestionsBody">
                                <!-- Assigned questions will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeQuestionAssignmentModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveQuestionAssignments()">Save Changes</button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const toggleIcon = document.getElementById('toggleIcon');

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            toggleIcon.textContent = sidebar.classList.contains('collapsed') ? '▶' : '◀';
            
            // Adjust footer position
            const footer = document.querySelector('body > div[style*="position: fixed"]');
            if (footer) {
                footer.style.left = sidebar.classList.contains('collapsed') ? '80px' : '250px';
            }
            
            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });

        // Restore sidebar state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            toggleIcon.textContent = '▶';
        }

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openMobileMenu() {
            sidebar.classList.add('mobile-open');
            sidebarOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeMobileMenu() {
            sidebar.classList.remove('mobile-open');
            sidebarOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        mobileMenuBtn.addEventListener('click', openMobileMenu);
        sidebarOverlay.addEventListener('click', closeMobileMenu);

        // SPA Navigation
        const navLinks = document.querySelectorAll('.nav-link[data-page]');
        const pageTitle = document.getElementById('pageTitle');
        const spaContents = document.querySelectorAll('.spa-content');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                const page = link.getAttribute('data-page');
                
                // Update active nav link
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                
                // Update page title
                const pageTitles = {
                    'dashboard': 'Dashboard',
                    'classrooms': 'Classrooms',
                    'questions': 'Question Bank',
                    'categories': 'Categories',
                    'groups': 'Groups',
                    'students': 'Exam Participants',
                    'results': 'Exam Results',
                    'analytics': 'Analytics',
                    'activity': 'Activity Logs',
                    'announcements': 'Announcements',
                    'admins': 'Admin Users',
                    'settings': 'Settings'
                };
                pageTitle.textContent = pageTitles[page] || 'Dashboard';
                
                // Show/hide content
                spaContents.forEach(content => {
                    content.classList.remove('active');
                });
                
                const targetContent = document.getElementById(`page-${page}`);
                if (targetContent) {
                    targetContent.classList.add('active');
                }
                
                // Save current page to localStorage
                localStorage.setItem('currentPage', page);
                
                // Load data for the page
                loadPageData(page);
                
                // Close mobile menu after navigation
                closeMobileMenu();
            });
        });

        // Load data for specific page
        function loadPageData(page) {
            switch(page) {
                case 'dashboard':
                    if (typeof loadDashboardStats === 'function') loadDashboardStats();
                    break;
                case 'classrooms':
                    if (typeof loadClassrooms === 'function') loadClassrooms();
                    break;
                case 'questions':
                    if (typeof loadQuestions === 'function') loadQuestions();
                    break;
                case 'categories':
                    if (typeof loadCategories === 'function') loadCategories();
                    break;
                case 'groups':
                    if (typeof loadGroups === 'function') loadGroups();
                    break;
                case 'students':
                    if (typeof loadStudents === 'function') loadStudents();
                    break;
                case 'results':
                    if (typeof loadResults === 'function') loadResults();
                    break;
                case 'analytics':
                    if (typeof loadAnalytics === 'function') loadAnalytics();
                    break;
                case 'activity':
                    if (typeof loadActivities === 'function') loadActivities();
                    break;
            }
        }

        // Restore current page and load its data
        const currentPage = localStorage.getItem('currentPage');
        if (currentPage) {
            const targetLink = document.querySelector(`.nav-link[data-page="${currentPage}"]`);
            if (targetLink) {
                targetLink.click();
                // Load data after a short delay to ensure page is shown
                setTimeout(() => loadPageData(currentPage), 100);
            }
        } else {
            // Load dashboard data by default
            loadPageData('dashboard');
        }

        // ==========================================
        // CUSTOM MODAL SYSTEM
        // ==========================================
        
        // Custom Confirm Dialog
        let confirmResolve = null;
        
        function customConfirm(message, title = 'Confirm Action') {
            return new Promise((resolve) => {
                confirmResolve = resolve;
                document.getElementById('confirmTitle').textContent = title;
                document.getElementById('confirmMessage').textContent = message;
                document.getElementById('confirmModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        }
        
        function closeConfirmModal(result) {
            document.getElementById('confirmModal').style.display = 'none';
            document.body.style.overflow = '';
            if (confirmResolve) {
                confirmResolve(result);
                confirmResolve = null;
            }
        }
        
        // Custom Alert Dialog
        let alertResolve = null;
        
        function customAlert(message, title = 'Notice') {
            return new Promise((resolve) => {
                alertResolve = resolve;
                document.getElementById('alertTitle').textContent = title;
                document.getElementById('alertMessage').textContent = message;
                document.getElementById('alertModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            });
        }
        
        function closeAlertModal() {
            document.getElementById('alertModal').style.display = 'none';
            document.body.style.overflow = '';
            if (alertResolve) {
                alertResolve();
                alertResolve = null;
            }
        }
        
        // Custom Prompt Dialog
        let promptResolve = null;
        
        function customPrompt(message, title = 'Input Required', defaultValue = '') {
            return new Promise((resolve) => {
                promptResolve = resolve;
                document.getElementById('promptTitle').textContent = title;
                document.getElementById('promptMessage').textContent = message;
                document.getElementById('promptInput').value = defaultValue;
                document.getElementById('promptModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
                document.getElementById('promptInput').focus();
            });
        }
        
        function closePromptModal(result) {
            document.getElementById('promptModal').style.display = 'none';
            document.body.style.overflow = '';
            if (promptResolve) {
                promptResolve(result);
                promptResolve = null;
            }
        }

        // ==========================================
        // CLASSROOM MANAGEMENT
        // ==========================================
        
        let classrooms = [];
        let editingClassroomId = null;
        let deletingClassroomId = null;

        // Load classrooms
        async function loadClassrooms() {
            try {
                const response = await fetch('/admin/api/classrooms', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                classrooms = data.classrooms;
                renderClassrooms();
                updateClassroomBadge();
            } catch (error) {
                console.error('Error loading classrooms:', error);
                showNotification('Error loading classrooms', 'error');
            }
        }

        // Render classrooms table
        function renderClassrooms() {
            const tbody = document.getElementById('classroomsTableBody');
            
            if (classrooms.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📚</div>
                            <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No classrooms yet</div>
                            <div style="font-size: 0.875rem;">Create your first classroom to get started</div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = classrooms.map(classroom => `
                <tr>
                    <td style="font-weight: 600;">${classroom.name}</td>
                    <td><code style="background: var(--bg-light); padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600;">${classroom.code}</code></td>
                    <td>${classroom.questions_count || 0}</td>
                    <td>${classroom.students_count || 0}</td>
                    <td>${classroom.timer_minutes ? classroom.timer_minutes + ' min' : 'No limit'}</td>
                    <td>
                        <span class="badge ${classroom.is_active ? 'badge-success' : 'badge-secondary'}" 
                              style="cursor: pointer;" 
                              onclick="toggleStatus(${classroom.id})">
                            ${classroom.is_active ? '✓ Active' : '✗ Inactive'}
                        </span>
                    </td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn-icon" onclick="viewClassroom(${classroom.id})" title="View Details">👁️</button>
                            <button class="btn-icon" onclick="editClassroom(${classroom.id})" title="Edit">✏️</button>
                            <button class="btn-icon" onclick="deleteClassroom(${classroom.id})" title="Delete" style="color: var(--danger);">🗑️</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Open create modal
        function openCreateModal() {
            editingClassroomId = null;
            document.getElementById('modalTitle').textContent = 'Create Classroom';
            document.getElementById('saveBtn').textContent = 'Create Classroom';
            document.getElementById('classroomForm').reset();
            document.getElementById('classroomModal').style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        }

        // Edit classroom
        function editClassroom(id) {
            const classroom = classrooms.find(c => c.id === id);
            if (!classroom) return;

            editingClassroomId = id;
            document.getElementById('modalTitle').textContent = 'Edit Classroom';
            document.getElementById('saveBtn').textContent = 'Update Classroom';
            
            const form = document.getElementById('classroomForm');
            form.name.value = classroom.name;
            form.description.value = classroom.description || '';
            form.questions_per_exam.value = classroom.questions_per_exam;
            form.timer_minutes.value = classroom.timer_minutes || '';
            form.instructions.value = classroom.instructions || '';
            form.show_results_immediately.checked = classroom.show_results_immediately;
            form.show_correct_answers.checked = classroom.show_correct_answers;
            form.allow_review.checked = classroom.allow_review;
            form.shuffle_questions.checked = classroom.shuffle_questions;
            form.is_active.checked = classroom.is_active;
            
            document.getElementById('classroomModal').style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        }

        // View classroom details
        let currentClassroomId = null;
        
        function viewClassroom(id) {
            console.log('viewClassroom called with id:', id);
            console.log('Available classrooms:', classrooms);
            
            const classroom = classrooms.find(c => c.id === id);
            if (!classroom) {
                console.error('Classroom not found:', id);
                showNotification('Classroom not found', 'error');
                return;
            }

            console.log('Found classroom:', classroom);
            currentClassroomId = id;
            
            const details = `
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                    <div style="padding: 1rem; background: var(--bg-light); border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Classroom Code</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary); font-family: monospace;">${classroom.code}</div>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-light); border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Status</div>
                        <div style="font-size: 1.125rem; font-weight: 600; color: ${classroom.is_active ? 'var(--success)' : 'var(--text-secondary)'};">
                            ${classroom.is_active ? '✓ Active' : '✗ Inactive'}
                        </div>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-light); border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Questions</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">${classroom.questions_count || 0}</div>
                    </div>
                    <div style="padding: 1rem; background: var(--bg-light); border-radius: 8px;">
                        <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.25rem;">Students</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary);">${classroom.students_count || 0}</div>
                    </div>
                </div>

                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Settings</div>
                    <div style="display: grid; gap: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: var(--bg-light); border-radius: 6px;">
                            <span style="font-size: 0.875rem; color: var(--text-secondary);">Questions per exam:</span>
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">${classroom.questions_per_exam}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: var(--bg-light); border-radius: 6px;">
                            <span style="font-size: 0.875rem; color: var(--text-secondary);">Timer:</span>
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">${classroom.timer_minutes ? classroom.timer_minutes + ' minutes' : 'No limit'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: var(--bg-light); border-radius: 6px;">
                            <span style="font-size: 0.875rem; color: var(--text-secondary);">Show results immediately:</span>
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">${classroom.show_results_immediately ? 'Yes' : 'No'}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; padding: 0.5rem; background: var(--bg-light); border-radius: 6px;">
                            <span style="font-size: 0.875rem; color: var(--text-secondary);">Show correct answers:</span>
                            <span style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">${classroom.show_correct_answers ? 'Yes' : 'No'}</span>
                        </div>
                    </div>
                </div>

                ${classroom.description ? `
                    <div style="margin-bottom: 1rem;">
                        <div style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Description</div>
                        <div style="padding: 0.75rem; background: var(--bg-light); border-radius: 6px; font-size: 0.875rem; color: var(--text-secondary);">${classroom.description}</div>
                    </div>
                ` : ''}
            `;

            console.log('Setting modal content...');
            console.log('Details HTML length:', details.length);
            
            const titleElement = document.getElementById('classroomDetailsTitle');
            const contentElement = document.getElementById('classroomDetailsContent');
            const modalElement = document.getElementById('classroomDetailsModal');
            
            console.log('Title element:', titleElement);
            console.log('Content element:', contentElement);
            console.log('Modal element:', modalElement);
            
            if (!contentElement) {
                console.error('classroomDetailsContent element not found!');
                return;
            }
            
            titleElement.textContent = classroom.name;
            contentElement.innerHTML = details;
            
            console.log('Content element after setting innerHTML:', contentElement);
            console.log('Content element children:', contentElement.children.length);
            
            modalElement.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            console.log('Modal should be visible now');
        }

        function closeClassroomDetailsModal() {
            document.getElementById('classroomDetailsModal').style.display = 'none';
            document.body.style.overflow = '';
            currentClassroomId = null;
        }

        // Question Assignment
        let availableQuestions = [];
        let assignedQuestions = [];
        let filteredAvailableQuestions = [];

        async function openQuestionAssignment() {
            if (!currentClassroomId) return;
            
            const classroom = classrooms.find(c => c.id === currentClassroomId);
            if (!classroom) return;

            document.getElementById('assignClassroomName').textContent = classroom.name;
            
            // Load all questions and categories
            await loadQuestionsForAssignment();
            await loadCategoriesForFilter();
            
            document.getElementById('questionAssignmentModal').style.display = 'flex';
        }

        async function loadQuestionsForAssignment() {
            try {
                // Load all questions
                const questionsResponse = await fetch('/admin/api/questions');
                const questionsData = await questionsResponse.json();
                availableQuestions = questionsData.questions || [];

                // Load assigned questions for this classroom
                const assignedResponse = await fetch(`/admin/api/classrooms/${currentClassroomId}/questions`);
                const assignedData = await assignedResponse.json();
                assignedQuestions = assignedData.questions || [];

                // Filter out already assigned questions from available
                const assignedIds = assignedQuestions.map(q => q.id);
                filteredAvailableQuestions = availableQuestions.filter(q => !assignedIds.includes(q.id));

                renderQuestionAssignment();
            } catch (error) {
                console.error('Error loading questions:', error);
                showNotification('Error loading questions', 'error');
            }
        }

        async function loadCategoriesForFilter() {
            try {
                const response = await fetch('/admin/api/categories');
                const data = await response.json();
                const categories = data.categories || [];
                
                const select = document.getElementById('assignCategoryFilter');
                select.innerHTML = '<option value="">All Categories</option>' +
                    categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }

        function filterAssignableQuestions() {
            const categoryId = document.getElementById('assignCategoryFilter').value;
            
            const assignedIds = assignedQuestions.map(q => q.id);
            filteredAvailableQuestions = availableQuestions.filter(q => {
                const notAssigned = !assignedIds.includes(q.id);
                const matchesCategory = !categoryId || q.category_id == categoryId;
                return notAssigned && matchesCategory;
            });
            
            renderQuestionAssignment();
        }

        function renderQuestionAssignment() {
            // Render available questions
            const availableBody = document.getElementById('availableQuestionsBody');
            if (filteredAvailableQuestions.length === 0) {
                availableBody.innerHTML = `
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            No available questions
                        </td>
                    </tr>
                `;
            } else {
                availableBody.innerHTML = filteredAvailableQuestions.map(q => `
                    <tr>
                        <td style="text-align: center;">
                            <button class="btn-icon" onclick="assignQuestion(${q.id})" title="Add">➕</button>
                        </td>
                        <td style="font-size: 0.875rem;">${q.question_text.substring(0, 80)}${q.question_text.length > 80 ? '...' : ''}</td>
                        <td><span class="badge badge-secondary">${q.category ? q.category.name : 'No category'}</span></td>
                    </tr>
                `).join('');
            }

            // Render assigned questions
            const assignedBody = document.getElementById('assignedQuestionsBody');
            document.getElementById('assignedCount').textContent = assignedQuestions.length;
            
            if (assignedQuestions.length === 0) {
                assignedBody.innerHTML = `
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            No questions assigned yet
                        </td>
                    </tr>
                `;
            } else {
                assignedBody.innerHTML = assignedQuestions.map(q => `
                    <tr>
                        <td style="text-align: center;">
                            <button class="btn-icon" onclick="unassignQuestion(${q.id})" title="Remove" style="color: var(--danger);">➖</button>
                        </td>
                        <td style="font-size: 0.875rem;">${q.question_text.substring(0, 80)}${q.question_text.length > 80 ? '...' : ''}</td>
                        <td><span class="badge badge-secondary">${q.category ? q.category.name : 'No category'}</span></td>
                    </tr>
                `).join('');
            }
        }

        function assignQuestion(questionId) {
            const question = filteredAvailableQuestions.find(q => q.id === questionId);
            if (!question) return;
            
            assignedQuestions.push(question);
            filterAssignableQuestions();
        }

        function unassignQuestion(questionId) {
            assignedQuestions = assignedQuestions.filter(q => q.id !== questionId);
            filterAssignableQuestions();
        }

        async function saveQuestionAssignments() {
            try {
                const questionIds = assignedQuestions.map(q => q.id);
                
                const response = await fetch(`/admin/api/classrooms/${currentClassroomId}/questions`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ question_ids: questionIds })
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    closeQuestionAssignmentModal();
                    loadClassrooms(); // Reload to update counts
                } else {
                    showNotification(result.message || 'Error saving assignments', 'error');
                }
            } catch (error) {
                console.error('Error saving assignments:', error);
                showNotification('Error saving assignments', 'error');
            }
        }

        function closeQuestionAssignmentModal() {
            document.getElementById('questionAssignmentModal').style.display = 'none';
            document.body.style.overflow = '';
        }

        // Manage Classroom Groups
        function manageClassroomGroups() {
            if (!currentClassroomId) return;
            
            const classroom = classrooms.find(c => c.id === currentClassroomId);
            if (!classroom) return;

            // Close classroom details modal
            closeClassroomDetailsModal();
            
            // Navigate to groups page
            const groupsLink = document.querySelector('.nav-link[data-page="groups"]');
            if (groupsLink) {
                groupsLink.click();
                
                // Filter groups by this classroom
                setTimeout(() => {
                    const classroomFilter = document.getElementById('groupClassroomFilter');
                    if (classroomFilter) {
                        classroomFilter.value = currentClassroomId;
                        filterGroups();
                    }
                }, 100);
            }
            
            showNotification(`Showing groups for ${classroom.name}`, 'info');
        }

        // Preview Exam (for admin testing)
        function previewExam() {
            if (!currentClassroomId) return;
            
            const classroom = classrooms.find(c => c.id === currentClassroomId);
            if (!classroom) return;

            // Open exam preview in new tab
            const previewUrl = `/admin/classrooms/${currentClassroomId}/preview`;
            window.open(previewUrl, '_blank');
            
            showNotification(`Opening preview for ${classroom.name}`, 'info');
        }

        // Save classroom
        async function saveClassroom(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const data = {
                name: formData.get('name'),
                description: formData.get('description'),
                questions_per_exam: parseInt(formData.get('questions_per_exam')),
                timer_minutes: formData.get('timer_minutes') ? parseInt(formData.get('timer_minutes')) : null,
                instructions: formData.get('instructions'),
                show_results_immediately: formData.get('show_results_immediately') === 'on',
                show_correct_answers: formData.get('show_correct_answers') === 'on',
                allow_review: formData.get('allow_review') === 'on',
                shuffle_questions: formData.get('shuffle_questions') === 'on',
                is_active: formData.get('is_active') === 'on',
            };

            try {
                const url = editingClassroomId 
                    ? `/admin/api/classrooms/${editingClassroomId}`
                    : '/admin/api/classrooms';
                
                const method = editingClassroomId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                // Check if response is OK
                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server response:', text);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    closeClassroomModal();
                    loadClassrooms();
                } else {
                    const errorMsg = result.message || 'Error saving classroom';
                    showNotification(errorMsg, 'error');
                }
            } catch (error) {
                console.error('Error saving classroom:', error);
                showNotification('Error: ' + error.message, 'error');
            }
        }

        // Delete classroom
        function deleteClassroom(id) {
            const classroom = classrooms.find(c => c.id === id);
            if (!classroom) return;

            deletingClassroomId = id;
            document.getElementById('deleteClassroomName').textContent = classroom.name;
            document.getElementById('deleteModal').style.display = 'flex';
            document.body.style.overflow = 'hidden'; // Prevent background scroll
        }

        // Confirm delete
        async function confirmDelete() {
            try {
                const response = await fetch(`/admin/api/classrooms/${deletingClassroomId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    closeDeleteModal();
                    loadClassrooms();
                } else {
                    showNotification('Error deleting classroom', 'error');
                }
            } catch (error) {
                console.error('Error deleting classroom:', error);
                showNotification('Error deleting classroom', 'error');
            }
        }

        // Toggle status
        async function toggleStatus(id) {
            try {
                const response = await fetch(`/admin/api/classrooms/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    loadClassrooms();
                } else {
                    showNotification('Error updating status', 'error');
                }
            } catch (error) {
                console.error('Error toggling status:', error);
                showNotification('Error updating status', 'error');
            }
        }

        // Close modals
        function closeClassroomModal() {
            document.getElementById('classroomModal').style.display = 'none';
            editingClassroomId = null;
            document.body.style.overflow = ''; // Restore background scroll
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            deletingClassroomId = null;
            document.body.style.overflow = ''; // Restore background scroll
        }

        // Update classroom badge
        function updateClassroomBadge() {
            const badge = document.querySelector('.nav-link[data-page="classrooms"] .nav-badge');
            if (badge) {
                badge.textContent = classrooms.length;
            }
        }

        // ==========================================
        // CATEGORY MANAGEMENT
        // ==========================================
        
        let categories = [];
        let editingCategoryId = null;

        // Load categories
        async function loadCategories() {
            try {
                const response = await fetch('/admin/api/categories', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                categories = data.categories;
                renderCategories();
            } catch (error) {
                console.error('Error loading categories:', error);
                showNotification('Error loading categories', 'error');
            }
        }

        // Render categories table
        function renderCategories() {
            const tbody = document.getElementById('categoriesTableBody');
            
            if (categories.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📁</div>
                            <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No categories yet</div>
                            <div style="font-size: 0.875rem;">Create your first category to organize questions</div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = categories.map(category => `
                <tr>
                    <td style="font-weight: 600;">${category.name}</td>
                    <td style="color: var(--text-secondary); font-size: 0.875rem;">${category.description || '-'}</td>
                    <td><span class="badge badge-secondary">${category.questions_count || 0} questions</span></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn-icon" onclick="editCategory(${category.id})" title="Edit">✏️</button>
                            <button class="btn-icon" onclick="deleteCategory(${category.id})" title="Delete" style="color: var(--danger);">🗑️</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Open create modal
        function openCreateCategoryModal() {
            editingCategoryId = null;
            document.getElementById('categoryModalTitle').textContent = 'Create Category';
            document.getElementById('categorySaveBtn').textContent = 'Create Category';
            document.getElementById('categoryForm').reset();
            document.getElementById('categoryModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Edit category
        function editCategory(id) {
            const category = categories.find(c => c.id === id);
            if (!category) return;

            editingCategoryId = id;
            document.getElementById('categoryModalTitle').textContent = 'Edit Category';
            document.getElementById('categorySaveBtn').textContent = 'Update Category';
            
            const form = document.getElementById('categoryForm');
            form.name.value = category.name;
            form.description.value = category.description || '';
            
            document.getElementById('categoryModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Save category
        async function saveCategory(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const data = {
                name: formData.get('name'),
                description: formData.get('description'),
            };

            try {
                const url = editingCategoryId 
                    ? `/admin/api/categories/${editingCategoryId}`
                    : '/admin/api/categories';
                
                const method = editingCategoryId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Server response:', text);
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    closeCategoryModal();
                    loadCategories();
                } else {
                    const errorMsg = result.message || 'Error saving category';
                    showNotification(errorMsg, 'error');
                }
            } catch (error) {
                console.error('Error saving category:', error);
                showNotification('Error: ' + error.message, 'error');
            }
        }

        // Delete category
        async function deleteCategory(id) {
            const category = categories.find(c => c.id === id);
            if (!category) return;

            if (!confirm(`Delete category "${category.name}"? This action cannot be undone.`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/api/categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    loadCategories();
                } else {
                    showNotification(result.message || 'Error deleting category', 'error');
                }
            } catch (error) {
                console.error('Error deleting category:', error);
                showNotification('Error deleting category', 'error');
            }
        }

        // Close modal
        function closeCategoryModal() {
            document.getElementById('categoryModal').style.display = 'none';
            editingCategoryId = null;
            document.body.style.overflow = '';
        }

        // Load categories when navigating to categories page
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const page = link.getAttribute('data-page');
                if (page === 'categories') {
                    loadCategories();
                }
                if (page === 'questions') {
                    loadQuestions();
                    populateCategorySelects();
                }
            });
        });

        // ==========================================
        // QUESTION MANAGEMENT
        // ==========================================
        
        let questions = [];
        let allQuestions = [];
        let editingQuestionId = null;

        // Load questions
        // ==========================================
        // IMPORT / EXPORT
        // ==========================================
        function openImportModal() {
            document.getElementById('importForm').reset();
            document.getElementById('importResult').style.display = 'none';
            document.getElementById('importBtn').disabled = false;
            document.getElementById('importBtn').textContent = 'Import';
            document.getElementById('importModal').style.display = 'flex';
        }

        function closeImportModal() {
            document.getElementById('importModal').style.display = 'none';
        }

        async function submitImport() {
            const fileInput = document.getElementById('importFile');
            if (!fileInput.files.length) {
                alert('Please select a CSV file.');
                return;
            }

            const btn = document.getElementById('importBtn');
            btn.disabled = true;
            btn.textContent = 'Importing...';

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);

            try {
                const response = await fetch('/admin/api/questions-import', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();
                const resultDiv = document.getElementById('importResult');
                resultDiv.style.display = 'block';

                if (result.success) {
                    let html = `<div style="background: #d1fae5; color: #065f46; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem;">
                        <strong>${result.message}</strong>
                    </div>`;

                    if (result.errors && result.errors.length > 0) {
                        html += `<div style="background: #fef3c7; color: #92400e; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.75rem; margin-top: 0.5rem; max-height: 120px; overflow-y: auto;">
                            <strong>Skipped rows:</strong><br>${result.errors.join('<br>')}
                        </div>`;
                    }

                    resultDiv.innerHTML = html;
                    btn.textContent = 'Done';

                    // Reload questions and categories
                    loadQuestions();
                    loadCategories();
                    showNotification(result.message, 'success');
                } else {
                    resultDiv.innerHTML = `<div style="background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem;">${result.message}</div>`;
                    btn.disabled = false;
                    btn.textContent = 'Import';
                }
            } catch (error) {
                console.error('Import error:', error);
                document.getElementById('importResult').style.display = 'block';
                document.getElementById('importResult').innerHTML = `<div style="background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.8125rem;">Import failed. Please check your file format.</div>`;
                btn.disabled = false;
                btn.textContent = 'Import';
            }
        }

        async function loadQuestions() {
            try {
                const response = await fetch('/admin/api/questions', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                const data = await response.json();
                allQuestions = data.questions;
                questions = allQuestions;
                renderQuestions();
            } catch (error) {
                console.error('Error loading questions:', error);
                showNotification('Error loading questions', 'error');
            }
        }

        // Render questions table
        function renderQuestions() {
            const tbody = document.getElementById('questionsTableBody');
            
            if (questions.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">❓</div>
                            <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No questions yet</div>
                            <div style="font-size: 0.875rem;">Add your first question to get started</div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = questions.map(q => `
                <tr>
                    <td style="font-size: 0.875rem;">${q.question_text.substring(0, 100)}${q.question_text.length > 100 ? '...' : ''}</td>
                    <td><span class="badge badge-secondary">${q.category ? q.category.name : 'No category'}</span></td>
                    <td>${renderStars(q.difficulty || 3)}</td>
                    <td><span style="font-weight: 700; color: var(--primary);">${q.correct_answer.toUpperCase()}</span></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn-icon" onclick="editQuestion(${q.id})" title="Edit">✏️</button>
                            <button class="btn-icon" onclick="deleteQuestion(${q.id})" title="Delete" style="color: var(--danger);">🗑️</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Populate category selects
        function populateCategorySelects() {
            const selects = [
                document.getElementById('questionCategorySelect'),
                document.getElementById('questionCategoryFilter')
            ];

            selects.forEach(select => {
                if (select && select.id === 'questionCategorySelect') {
                    select.innerHTML = '<option value="">Select category</option>' +
                        categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                } else if (select) {
                    select.innerHTML = '<option value="">All Categories</option>' +
                        categories.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
                }
            });
        }

        // Filter questions
        function filterQuestions() {
            const categoryId = document.getElementById('questionCategoryFilter').value;
            const difficulty = document.getElementById('questionDifficultyFilter').value;
            const search = document.getElementById('questionSearch').value.toLowerCase();

            questions = allQuestions.filter(q => {
                const matchesCategory = !categoryId || q.category_id == categoryId;
                const matchesDifficulty = !difficulty || (q.difficulty || 3) == difficulty;
                const matchesSearch = !search || q.question_text.toLowerCase().includes(search);
                return matchesCategory && matchesDifficulty && matchesSearch;
            });

            renderQuestions();
        }

        // Clear filters
        function clearFilters() {
            document.getElementById('questionCategoryFilter').value = '';
            document.getElementById('questionDifficultyFilter').value = '';
            document.getElementById('questionSearch').value = '';
            questions = allQuestions;
            renderQuestions();
        }

        // Open create modal
        // Star rating helpers
        const difficultyLabels = { 1: 'Very Easy', 2: 'Easy', 3: 'Medium', 4: 'Hard', 5: 'Very Hard' };

        function setDifficulty(value) {
            document.getElementById('difficultyInput').value = value;
            document.getElementById('difficultyLabel').textContent = difficultyLabels[value] || 'Medium';
            document.querySelectorAll('#difficultyRating .star').forEach(star => {
                star.classList.toggle('active', parseInt(star.dataset.value) <= value);
            });
        }

        function renderStars(level) {
            let html = '<span class="star-display">';
            for (let i = 1; i <= 5; i++) {
                html += i <= level ? '&#9733;' : '<span class="empty">&#9733;</span>';
            }
            html += '</span>';
            return html;
        }

        // Attach star click handlers
        document.querySelectorAll('#difficultyRating .star').forEach(star => {
            star.addEventListener('click', () => setDifficulty(parseInt(star.dataset.value)));
        });

        function openCreateQuestionModal() {
            editingQuestionId = null;
            document.getElementById('questionModalTitle').textContent = 'Add Question';
            document.getElementById('questionSaveBtn').textContent = 'Add Question';
            document.getElementById('questionForm').reset();
            setDifficulty(3);
            document.getElementById('questionModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Edit question
        function editQuestion(id) {
            const question = allQuestions.find(q => q.id === id);
            if (!question) return;

            editingQuestionId = id;
            document.getElementById('questionModalTitle').textContent = 'Edit Question';
            document.getElementById('questionSaveBtn').textContent = 'Update Question';

            const form = document.getElementById('questionForm');
            form.category_id.value = question.category_id;
            form.question_text.value = question.question_text;
            form.option_a.value = question.option_a;
            form.option_b.value = question.option_b;
            form.option_c.value = question.option_c;
            form.option_d.value = question.option_d;
            form.correct_answer.value = question.correct_answer;
            form.shuffle_answers.checked = question.shuffle_answers || false;
            setDifficulty(question.difficulty || 3);

            document.getElementById('questionModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Save question
        async function saveQuestion(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const data = {
                category_id: parseInt(formData.get('category_id')),
                question_text: formData.get('question_text'),
                option_a: formData.get('option_a'),
                option_b: formData.get('option_b'),
                option_c: formData.get('option_c'),
                option_d: formData.get('option_d'),
                correct_answer: formData.get('correct_answer'),
                difficulty: parseInt(formData.get('difficulty')) || 3,
                shuffle_answers: form.shuffle_answers.checked,
            };

            try {
                const url = editingQuestionId 
                    ? `/admin/api/questions/${editingQuestionId}`
                    : '/admin/api/questions';
                
                const method = editingQuestionId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    closeQuestionModal();
                    loadQuestions();
                } else {
                    showNotification(result.message || 'Error saving question', 'error');
                }
            } catch (error) {
                console.error('Error saving question:', error);
                showNotification('Error: ' + error.message, 'error');
            }
        }

        // Delete question
        async function deleteQuestion(id) {
            if (!confirm('Delete this question? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/admin/api/questions/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    loadQuestions();
                } else {
                    showNotification(result.message || 'Error deleting question', 'error');
                }
            } catch (error) {
                console.error('Error deleting question:', error);
                showNotification('Error deleting question', 'error');
            }
        }

        // Close modal
        function closeQuestionModal() {
            document.getElementById('questionModal').style.display = 'none';
            editingQuestionId = null;
            document.body.style.overflow = '';
        }

        // ==========================================
        // STUDENT MANAGEMENT
        // ==========================================
        
        let students = [];
        let allStudents = [];
        let editingStudentId = null;
        let studentGroups = [];

        // Load students
        async function loadStudents() {
            try {
                const response = await fetch('/admin/api/students');
                const data = await response.json();
                allStudents = data.students;
                students = allStudents;
                renderStudents();
                populateStudentFilters();
            } catch (error) {
                console.error('Error loading students:', error);
                showNotification('Error loading students', 'error');
            }
        }

        // Render students table
        function renderStudents() {
            const tbody = document.getElementById('studentsTableBody');
            
            if (students.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                            <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No students yet</div>
                            <div style="font-size: 0.875rem;">Add your first student to get started</div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = students.map(s => `
                <tr>
                    <td style="font-weight: 600;">${s.name}</td>
                    <td style="font-size: 0.875rem;">${s.email}</td>
                    <td style="font-size: 0.875rem;">${s.matric_number || '-'}</td>
                    <td><span class="badge badge-secondary">${s.classroom ? s.classroom.name : 'No classroom'}</span></td>
                    <td><span class="badge badge-secondary">${s.group ? s.group.name : 'No group'}</span></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn-icon" onclick="editStudent(${s.id})" title="Edit">✏️</button>
                            <button class="btn-icon" onclick="deleteStudent(${s.id})" title="Delete" style="color: var(--danger);">🗑️</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Populate student filters
        function populateStudentFilters() {
            const classroomFilter = document.getElementById('studentClassroomFilter');
            const classroomSelect = document.getElementById('studentClassroomSelect');
            
            if (classroomFilter) {
                classroomFilter.innerHTML = '<option value="">All Classrooms</option>' +
                    classrooms.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            }
            
            if (classroomSelect) {
                classroomSelect.innerHTML = '<option value="">Select classroom</option>' +
                    classrooms.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            }
        }

        // Load groups for selected classroom
        async function loadGroupsForStudent(classroomId) {
            const groupSelect = document.getElementById('studentGroupSelect');
            
            if (!classroomId) {
                groupSelect.innerHTML = '<option value="">No group</option>';
                return;
            }

            try {
                const response = await fetch(`/admin/api/classrooms/${classroomId}/groups`);
                const data = await response.json();
                studentGroups = data.groups || [];
                
                groupSelect.innerHTML = '<option value="">No group</option>' +
                    studentGroups.map(g => `<option value="${g.id}">${g.name}</option>`).join('');
            } catch (error) {
                console.error('Error loading groups:', error);
            }
        }

        // Filter students
        function filterStudents() {
            const classroomId = document.getElementById('studentClassroomFilter').value;
            const groupId = document.getElementById('studentGroupFilter').value;
            const search = document.getElementById('studentSearch').value.toLowerCase();

            students = allStudents.filter(s => {
                const matchesClassroom = !classroomId || s.classroom_id == classroomId;
                const matchesGroup = !groupId || s.group_id == groupId;
                const matchesSearch = !search || 
                    s.name.toLowerCase().includes(search) ||
                    s.email.toLowerCase().includes(search) ||
                    (s.matric_number && s.matric_number.toLowerCase().includes(search));
                return matchesClassroom && matchesGroup && matchesSearch;
            });

            renderStudents();
            
            // Update group filter based on classroom
            if (classroomId) {
                loadGroupsForFilter(classroomId);
            } else {
                document.getElementById('studentGroupFilter').innerHTML = '<option value="">All Groups</option>';
            }
        }

        // Load groups for filter
        async function loadGroupsForFilter(classroomId) {
            const groupFilter = document.getElementById('studentGroupFilter');
            
            try {
                const response = await fetch(`/admin/api/classrooms/${classroomId}/groups`);
                const data = await response.json();
                const groups = data.groups || [];
                
                groupFilter.innerHTML = '<option value="">All Groups</option>' +
                    groups.map(g => `<option value="${g.id}">${g.name}</option>`).join('');
            } catch (error) {
                console.error('Error loading groups:', error);
            }
        }

        // Clear filters
        function clearStudentFilters() {
            document.getElementById('studentClassroomFilter').value = '';
            document.getElementById('studentGroupFilter').value = '';
            document.getElementById('studentSearch').value = '';
            students = allStudents;
            renderStudents();
        }

        // Open create modal
        function openCreateStudentModal() {
            editingStudentId = null;
            document.getElementById('studentModalTitle').textContent = 'Add Student';
            document.getElementById('studentSaveBtn').textContent = 'Add Student';
            document.getElementById('studentForm').reset();
            document.getElementById('studentModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Edit student
        function editStudent(id) {
            const student = allStudents.find(s => s.id === id);
            if (!student) return;

            editingStudentId = id;
            document.getElementById('studentModalTitle').textContent = 'Edit Student';
            document.getElementById('studentSaveBtn').textContent = 'Update Student';
            
            const form = document.getElementById('studentForm');
            form.classroom_id.value = student.classroom_id;
            loadGroupsForStudent(student.classroom_id);
            setTimeout(() => {
                form.group_id.value = student.group_id || '';
            }, 100);
            form.name.value = student.name;
            form.email.value = student.email;
            form.phone.value = student.phone || '';
            form.matric_number.value = student.matric_number || '';
            
            document.getElementById('studentModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Save student
        async function saveStudent(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const data = {
                classroom_id: parseInt(formData.get('classroom_id')),
                group_id: formData.get('group_id') ? parseInt(formData.get('group_id')) : null,
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone') || null,
                matric_number: formData.get('matric_number') || null,
            };

            try {
                const url = editingStudentId 
                    ? `/admin/api/students/${editingStudentId}`
                    : '/admin/api/students';
                
                const method = editingStudentId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    closeStudentModal();
                    loadStudents();
                } else {
                    showNotification(result.message || 'Error saving student', 'error');
                }
            } catch (error) {
                console.error('Error saving student:', error);
                showNotification('Error: ' + error.message, 'error');
            }
        }

        // Delete student
        async function deleteStudent(id) {
            if (!confirm('Delete this student? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/admin/api/students/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    loadStudents();
                } else {
                    showNotification(result.message || 'Error deleting student', 'error');
                }
            } catch (error) {
                console.error('Error deleting student:', error);
                showNotification('Error deleting student', 'error');
            }
        }

        // Close modal
        function closeStudentModal() {
            document.getElementById('studentModal').style.display = 'none';
            editingStudentId = null;
            document.body.style.overflow = '';
        }

        // Load students when navigating to students page
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const page = link.getAttribute('data-page');
                if (page === 'students') {
                    loadStudents();
                }
            });
        });

        // ==========================================
        // GROUP MANAGEMENT
        // ==========================================
        
        let groups = [];
        let allGroups = [];
        let editingGroupId = null;

        // Load all groups
        async function loadAllGroups() {
            try {
                allGroups = [];
                for (const classroom of classrooms) {
                    const response = await fetch(`/admin/api/classrooms/${classroom.id}/groups`);
                    const data = await response.json();
                    const classroomGroups = (data.groups || []).map(g => ({
                        ...g,
                        classroom: classroom
                    }));
                    allGroups = allGroups.concat(classroomGroups);
                }
                groups = allGroups;
                renderGroups();
                populateGroupFilters();
            } catch (error) {
                console.error('Error loading groups:', error);
                showNotification('Error loading groups', 'error');
            }
        }

        // Render groups table
        function renderGroups() {
            const tbody = document.getElementById('groupsTableBody');
            
            if (groups.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                            <div style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem;">No groups yet</div>
                            <div style="font-size: 0.875rem;">Create your first group to organize students</div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = groups.map(g => `
                <tr>
                    <td style="font-weight: 600;">${g.name}</td>
                    <td><span class="badge badge-secondary">${g.classroom ? g.classroom.name : 'Unknown'}</span></td>
                    <td><span class="badge badge-secondary">${g.students_count || 0} students</span></td>
                    <td>
                        <div style="display: flex; gap: 0.5rem;">
                            <button class="btn-icon" onclick="editGroup(${g.id}, ${g.classroom_id})" title="Edit">✏️</button>
                            <button class="btn-icon" onclick="deleteGroup(${g.id}, ${g.classroom_id})" title="Delete" style="color: var(--danger);">🗑️</button>
                        </div>
                    </td>
                </tr>
            `).join('');
        }

        // Populate group filters
        function populateGroupFilters() {
            const classroomFilter = document.getElementById('groupClassroomFilter');
            const classroomSelect = document.getElementById('groupClassroomSelect');
            
            if (classroomFilter) {
                classroomFilter.innerHTML = '<option value="">All Classrooms</option>' +
                    classrooms.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            }
            
            if (classroomSelect) {
                classroomSelect.innerHTML = '<option value="">Select classroom</option>' +
                    classrooms.map(c => `<option value="${c.id}">${c.name}</option>`).join('');
            }
        }

        // Filter groups
        function filterGroups() {
            const classroomId = document.getElementById('groupClassroomFilter').value;

            groups = allGroups.filter(g => {
                return !classroomId || g.classroom_id == classroomId;
            });

            renderGroups();
        }

        // Clear filters
        function clearGroupFilters() {
            document.getElementById('groupClassroomFilter').value = '';
            groups = allGroups;
            renderGroups();
        }

        // Open create modal
        function openCreateGroupModal() {
            editingGroupId = null;
            document.getElementById('groupModalTitle').textContent = 'Create Group';
            document.getElementById('groupSaveBtn').textContent = 'Create Group';
            document.getElementById('groupForm').reset();
            document.getElementById('groupModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        // Edit group
        async function editGroup(id, classroomId) {
            try {
                const response = await fetch(`/admin/api/classrooms/${classroomId}/groups`);
                const data = await response.json();
                const group = (data.groups || []).find(g => g.id === id);
                
                if (!group) return;

                editingGroupId = id;
                editingGroupClassroomId = classroomId;
                document.getElementById('groupModalTitle').textContent = 'Edit Group';
                document.getElementById('groupSaveBtn').textContent = 'Update Group';
                
                const form = document.getElementById('groupForm');
                form.classroom_id.value = classroomId;
                form.name.value = group.name;
                
                document.getElementById('groupModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Error loading group:', error);
                showNotification('Error loading group', 'error');
            }
        }

        // Save group
        async function saveGroup(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const classroomId = formData.get('classroom_id');
            const data = {
                name: formData.get('name'),
            };

            try {
                const url = editingGroupId 
                    ? `/admin/api/classrooms/${editingGroupClassroomId}/groups/${editingGroupId}`
                    : `/admin/api/classrooms/${classroomId}/groups`;
                
                const method = editingGroupId ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    closeGroupModal();
                    loadAllGroups();
                } else {
                    showNotification(result.message || 'Error saving group', 'error');
                }
            } catch (error) {
                console.error('Error saving group:', error);
                showNotification('Error: ' + error.message, 'error');
            }
        }

        // Delete group
        async function deleteGroup(id, classroomId) {
            if (!confirm('Delete this group? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await fetch(`/admin/api/classrooms/${classroomId}/groups/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const result = await response.json();

                if (result.success) {
                    showNotification(result.message, 'success');
                    loadAllGroups();
                } else {
                    showNotification(result.message || 'Error deleting group', 'error');
                }
            } catch (error) {
                console.error('Error deleting group:', error);
                showNotification('Error deleting group', 'error');
            }
        }

        // Close modal
        function closeGroupModal() {
            document.getElementById('groupModal').style.display = 'none';
            editingGroupId = null;
            document.body.style.overflow = '';
        }

        // Load groups when navigating to groups page
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const page = link.getAttribute('data-page');
                if (page === 'groups') {
                    loadAllGroups();
                }
            });
        });

        // Show notification
        function showNotification(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            
            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            // Icon based on type
            const icons = {
                success: '✓',
                error: '✕',
                warning: '⚠',
                info: 'ℹ'
            };
            
            // Titles based on type
            const titles = {
                success: 'Success',
                error: 'Error',
                warning: 'Warning',
                info: 'Info'
            };
            
            toast.innerHTML = `
                <div class="toast-icon">${icons[type] || icons.info}</div>
                <div class="toast-content">
                    <div class="toast-title">${titles[type] || titles.info}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">×</button>
            `;
            
            container.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Load classrooms when navigating to classrooms page
        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const page = link.getAttribute('data-page');
                if (page === 'classrooms') {
                    loadClassrooms();
                }
            });
        });

        // Load dashboard stats (placeholder - will be replaced with API calls)
        async function loadDashboardStats() {
            try {
                // Fetch all data in parallel
                const [classroomsRes, questionsRes, studentsRes, sessionsRes] = await Promise.all([
                    fetch('/admin/api/classrooms'),
                    fetch('/admin/api/questions'),
                    fetch('/admin/api/students'),
                    fetch('/admin/api/exam-sessions')
                ]);

                const classrooms = await classroomsRes.json();
                const questions = await questionsRes.json();
                const students = await studentsRes.json();
                const sessions = await sessionsRes.json();

                // Update stat cards
                document.getElementById('totalClassrooms').textContent = classrooms.classrooms?.length || 0;
                document.getElementById('totalQuestions').textContent = questions.questions?.length || 0;
                document.getElementById('totalStudents').textContent = students.students?.length || 0;
                document.getElementById('totalExams').textContent = sessions.sessions?.length || 0;
            } catch (error) {
                console.error('Error loading dashboard stats:', error);
                // Keep showing 0 if there's an error
                document.getElementById('totalClassrooms').textContent = '0';
                document.getElementById('totalQuestions').textContent = '0';
                document.getElementById('totalStudents').textContent = '0';
                document.getElementById('totalExams').textContent = '0';
            }
        }

        // ==========================================
        // RESULTS MANAGEMENT
        // ==========================================
        
        let allResults = [];
        let filteredResults = [];

        async function loadResults() {
            try {
                const response = await fetch('/admin/api/exam-sessions');
                const data = await response.json();
                
                allResults = data.sessions || [];
                filteredResults = [...allResults];
                
                // Populate classroom filter
                const classroomFilter = document.getElementById('resultsClassroomFilter');
                const classrooms = [...new Set(allResults.map(r => r.classroom))];
                classroomFilter.innerHTML = '<option value="">All Classrooms</option>';
                classrooms.forEach(classroom => {
                    if (classroom) {
                        classroomFilter.innerHTML += `<option value="${classroom.id}">${classroom.name}</option>`;
                    }
                });
                
                renderResults();
            } catch (error) {
                console.error('Error loading results:', error);
                document.getElementById('resultsTableBody').innerHTML = `
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--error);">
                            Error loading results. Please try again.
                        </td>
                    </tr>
                `;
            }
        }

        function filterResults() {
            const classroomFilter = document.getElementById('resultsClassroomFilter').value;
            const statusFilter = document.getElementById('resultsStatusFilter').value;
            const searchInput = document.getElementById('resultsSearchInput').value.toLowerCase();

            filteredResults = allResults.filter(result => {
                const matchesClassroom = !classroomFilter || result.classroom?.id == classroomFilter;
                const matchesStatus = !statusFilter || result.status === statusFilter;
                const matchesSearch = !searchInput || 
                    result.student?.name.toLowerCase().includes(searchInput) ||
                    result.student?.matric_number.toLowerCase().includes(searchInput);

                return matchesClassroom && matchesStatus && matchesSearch;
            });

            renderResults();
        }

        function renderResults() {
            const tbody = document.getElementById('resultsTableBody');
            
            if (filteredResults.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                            No results found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = filteredResults.map(result => {
                const statusColors = {
                    'completed': 'success',
                    'in_progress': 'warning',
                    'expired': 'error'
                };
                
                const status = result.status || 'in_progress';
                const statusColor = statusColors[status] || 'secondary';
                const score = result.score || 0;
                const scoreColor = score >= 70 ? 'success' : score >= 50 ? 'warning' : 'error';
                
                return `
                    <tr>
                        <td>${result.student?.name || 'N/A'}</td>
                        <td>${result.student?.matric_number || 'N/A'}</td>
                        <td>${result.classroom?.name || 'N/A'}</td>
                        <td>
                            <span class="badge badge-${scoreColor}">
                                ${result.score !== null && result.score !== undefined ? result.score + '%' : 'N/A'}
                            </span>
                        </td>
                        <td>${result.correct_answers || 0} / ${result.total_questions || 0}</td>
                        <td>
                            <span class="badge badge-${statusColor}">
                                ${status.replace('_', ' ')}
                            </span>
                        </td>
                        <td>${result.completed_at ? new Date(result.completed_at).toLocaleString() : 'In Progress'}</td>
                        <td>
                            ${status === 'completed' ? 
                                `<button class="btn btn-sm btn-primary" onclick="viewResultDetails(${result.id})">
                                    View Details
                                </button>` : 
                                '<span style="color: var(--text-secondary); font-size: 0.75rem;">Not completed</span>'
                            }
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // Exam Details Modal Functions
        function openExamDetailsModal(htmlContent) {
            document.getElementById('examDetailsContent').innerHTML = htmlContent;
            document.getElementById('examDetailsModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeExamDetailsModal() {
            document.getElementById('examDetailsModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        async function viewResultDetails(sessionId) {
            try {
                console.log('Fetching details for session:', sessionId);
                const response = await fetch(`/admin/api/exam-sessions/${sessionId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Session data:', data);
                
                if (!data || !data.student || !data.classroom) {
                    throw new Error('Invalid session data received');
                }
                
                // Create detailed view HTML
                const detailsHtml = `
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="margin: 0 0 0.5rem 0; font-size: 1.25rem;">Student: ${data.student.name}</h4>
                        <p style="margin: 0; color: var(--text-secondary);">Matric: ${data.student.matric_number}</p>
                        <p style="margin: 0.25rem 0 0 0; color: var(--text-secondary);">Classroom: ${data.classroom.name}</p>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="padding: 1rem; background: var(--bg-light); border-radius: 8px; text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Score</div>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--primary);">${data.score}%</div>
                        </div>
                        <div style="padding: 1rem; background: var(--bg-light); border-radius: 8px; text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Correct Answers</div>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--success);">${data.correct_answers}</div>
                        </div>
                        <div style="padding: 1rem; background: var(--bg-light); border-radius: 8px; text-align: center;">
                            <div style="font-size: 0.75rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Total Questions</div>
                            <div style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">${data.total_questions}</div>
                        </div>
                    </div>
                    
                    <div style="padding: 1rem; background: var(--bg-light); border-radius: 8px; margin-bottom: 1.5rem;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                            <div>
                                <strong>Started:</strong> ${new Date(data.started_at).toLocaleString()}
                            </div>
                            <div>
                                <strong>Completed:</strong> ${new Date(data.completed_at).toLocaleString()}
                            </div>
                        </div>
                    </div>
                    
                    ${data.classroom.show_correct_answers && data.answers && data.answers.length > 0 ? `
                        <h5 style="margin: 0 0 1rem 0; font-size: 1.125rem; font-weight: 700;">Answer Details</h5>
                        <div style="max-height: 400px; overflow-y: auto;">
                            ${data.answers.map((answer, index) => `
                                <div style="padding: 1rem; margin-bottom: 0.75rem; background: var(--bg-light); border-radius: 8px; border-left: 4px solid ${answer.is_correct ? 'var(--success)' : 'var(--error)'};">
                                    <div style="font-weight: 600; margin-bottom: 0.5rem; color: var(--text-primary);">
                                        Question ${index + 1}
                                    </div>
                                    <div style="margin-bottom: 0.5rem; color: var(--text-primary);">
                                        ${answer.question.question_text}
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.875rem;">
                                        <div>
                                            <span style="color: var(--text-secondary);">Student Answer:</span>
                                            <strong style="color: ${answer.is_correct ? 'var(--success)' : 'var(--error)'}; margin-left: 0.25rem;">${answer.answer}</strong>
                                        </div>
                                        <div>
                                            <span style="color: var(--text-secondary);">Correct Answer:</span>
                                            <strong style="color: var(--success); margin-left: 0.25rem;">${answer.question.correct_answer}</strong>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : '<p style="text-align: center; padding: 2rem; color: var(--text-secondary); background: var(--bg-light); border-radius: 8px;">Answer details are hidden for this exam.</p>'}
                `;
                
                openExamDetailsModal(detailsHtml);
                
            } catch (error) {
                console.error('Error loading result details:', error);
                console.error('Error details:', error.message);
                alert(`Error loading result details: ${error.message}\n\nPlease try again or contact support.`);
            }
        }

        // ==========================================
        // ANALYTICS MANAGEMENT
        // ==========================================
        
        async function loadAnalytics() {
            try {
                // Load statistics
                const statsResponse = await fetch('/admin/api/exam-sessions/statistics');
                const stats = await statsResponse.json();
                
                document.getElementById('analyticsTotalExams').textContent = stats.total_sessions;
                document.getElementById('analyticsCompleted').textContent = stats.completed_sessions;
                document.getElementById('analyticsAvgScore').textContent = stats.average_score + '%';
                document.getElementById('analyticsPassRate').textContent = stats.pass_rate + '%';
                
                // Load classroom performance
                const sessionsResponse = await fetch('/admin/api/exam-sessions');
                const sessionsData = await sessionsResponse.json();
                
                // Group by classroom
                const classroomStats = {};
                sessionsData.sessions.forEach(session => {
                    if (!session.classroom) return;
                    
                    const classroomId = session.classroom.id;
                    if (!classroomStats[classroomId]) {
                        classroomStats[classroomId] = {
                            name: session.classroom.name,
                            total: 0,
                            completed: 0,
                            scores: [],
                            passed: 0
                        };
                    }
                    
                    classroomStats[classroomId].total++;
                    if (session.status === 'completed') {
                        classroomStats[classroomId].completed++;
                        if (session.score !== null) {
                            classroomStats[classroomId].scores.push(session.score);
                            if (session.score >= 50) {
                                classroomStats[classroomId].passed++;
                            }
                        }
                    }
                });
                
                // Render classroom table
                const tbody = document.getElementById('analyticsClassroomTable');
                const classroomRows = Object.values(classroomStats).map(classroom => {
                    const avgScore = classroom.scores.length > 0 
                        ? (classroom.scores.reduce((a, b) => a + b, 0) / classroom.scores.length).toFixed(2)
                        : 'N/A';
                    const passRate = classroom.scores.length > 0
                        ? ((classroom.passed / classroom.scores.length) * 100).toFixed(2)
                        : 'N/A';
                    const highest = classroom.scores.length > 0 ? Math.max(...classroom.scores) : 'N/A';
                    const lowest = classroom.scores.length > 0 ? Math.min(...classroom.scores) : 'N/A';
                    
                    return `
                        <tr>
                            <td>${classroom.name}</td>
                            <td>${classroom.total}</td>
                            <td>${classroom.completed}</td>
                            <td><span class="badge badge-${avgScore >= 70 ? 'success' : avgScore >= 50 ? 'warning' : 'error'}">${avgScore}${avgScore !== 'N/A' ? '%' : ''}</span></td>
                            <td><span class="badge badge-success">${passRate}${passRate !== 'N/A' ? '%' : ''}</span></td>
                            <td>${highest}${highest !== 'N/A' ? '%' : ''}</td>
                            <td>${lowest}${lowest !== 'N/A' ? '%' : ''}</td>
                        </tr>
                    `;
                }).join('');
                
                tbody.innerHTML = classroomRows || '<tr><td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">No data available</td></tr>';
                
                // Load recent completions
                const recentCompletions = sessionsData.sessions
                    .filter(s => s.status === 'completed')
                    .sort((a, b) => new Date(b.completed_at) - new Date(a.completed_at))
                    .slice(0, 10);
                
                const activityHtml = recentCompletions.map(session => `
                    <div style="padding: 1rem; border-left: 3px solid var(--success); background: var(--bg-light); margin-bottom: 0.75rem; border-radius: 4px;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div>
                                <div style="font-weight: 600; margin-bottom: 0.25rem;">${session.student?.name || 'Unknown'} completed ${session.classroom?.name || 'exam'}</div>
                                <div style="font-size: 0.875rem; color: var(--text-secondary);">
                                    Score: <strong>${session.score}%</strong> | ${new Date(session.completed_at).toLocaleString()}
                                </div>
                            </div>
                            <span class="badge badge-${session.score >= 70 ? 'success' : session.score >= 50 ? 'warning' : 'error'}">${session.score}%</span>
                        </div>
                    </div>
                `).join('');
                
                document.getElementById('analyticsRecentActivity').innerHTML = activityHtml || '<p style="text-align: center; padding: 2rem; color: var(--text-secondary);">No recent activity</p>';
                
            } catch (error) {
                console.error('Error loading analytics:', error);
            }
        }

        // ==========================================
        // ACTIVITY LOGS MANAGEMENT
        // ==========================================
        
        let allActivities = [];
        let filteredActivities = [];

        async function loadActivities() {
            try {
                // Load exam sessions as activities
                const response = await fetch('/admin/api/exam-sessions');
                const data = await response.json();
                
                // Convert sessions to activities
                allActivities = data.sessions.map(session => {
                    let type, description, icon;
                    
                    if (session.status === 'completed') {
                        type = 'exam_completed';
                        description = `${session.student?.name} completed ${session.classroom?.name} with ${session.score}%`;
                        icon = '✅';
                    } else if (session.status === 'in_progress') {
                        type = 'exam_started';
                        description = `${session.student?.name} started ${session.classroom?.name}`;
                        icon = '📝';
                    } else {
                        type = 'exam_expired';
                        description = `${session.student?.name}'s exam expired for ${session.classroom?.name}`;
                        icon = '⏰';
                    }
                    
                    return {
                        type,
                        description,
                        icon,
                        timestamp: session.completed_at || session.started_at || session.created_at,
                        data: session
                    };
                }).sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));
                
                filteredActivities = [...allActivities];
                renderActivities();
            } catch (error) {
                console.error('Error loading activities:', error);
                document.getElementById('activityTimeline').innerHTML = '<p style="text-align: center; padding: 2rem; color: var(--error);">Error loading activities</p>';
            }
        }

        function filterActivities() {
            const typeFilter = document.getElementById('activityTypeFilter').value;
            const searchInput = document.getElementById('activitySearchInput').value.toLowerCase();

            filteredActivities = allActivities.filter(activity => {
                const matchesType = !typeFilter || activity.type === typeFilter;
                const matchesSearch = !searchInput || activity.description.toLowerCase().includes(searchInput);
                return matchesType && matchesSearch;
            });

            renderActivities();
        }

        function renderActivities() {
            const timeline = document.getElementById('activityTimeline');
            
            if (filteredActivities.length === 0) {
                timeline.innerHTML = '<p style="text-align: center; padding: 2rem; color: var(--text-secondary);">No activities found</p>';
                return;
            }

            const activitiesHtml = filteredActivities.map(activity => `
                <div style="display: flex; gap: 1rem; padding: 1rem 0; border-bottom: 1px solid var(--border-color);">
                    <div style="flex-shrink: 0; width: 40px; height: 40px; border-radius: 50%; background: var(--bg-light); display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        ${activity.icon}
                    </div>
                    <div style="flex: 1;">
                        <div style="font-weight: 500; margin-bottom: 0.25rem;">${activity.description}</div>
                        <div style="font-size: 0.875rem; color: var(--text-secondary);">${new Date(activity.timestamp).toLocaleString()}</div>
                    </div>
                </div>
            `).join('');

            timeline.innerHTML = activitiesHtml;
        }

        // Update loadPageData to include analytics and activity
        const originalLoadPageData = loadPageData;
        loadPageData = function(page) {
            originalLoadPageData(page);
            if (page === 'analytics') {
                loadAnalytics();
            } else if (page === 'activity') {
                loadActivities();
            }
        };

        // ==========================================
        // ADMIN USERS MANAGEMENT
        // ==========================================
        
        let allAdmins = [];

        async function loadAdmins() {
            try {
                const response = await fetch('/admin/api/users');
                const data = await response.json();
                allAdmins = data.users || [];
                renderAdmins();
            } catch (error) {
                console.error('Error loading admins:', error);
                document.getElementById('adminsTableBody').innerHTML = `
                    <tr><td colspan="4" style="text-align: center; padding: 2rem; color: var(--error);">Error loading admins</td></tr>
                `;
            }
        }

        function renderAdmins() {
            const tbody = document.getElementById('adminsTableBody');
            
            if (allAdmins.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-secondary);">No admins found</td></tr>';
                return;
            }

            tbody.innerHTML = allAdmins.map(admin => `
                <tr>
                    <td>${admin.name}</td>
                    <td>${admin.email}</td>
                    <td>${new Date(admin.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="editAdmin(${admin.id})" style="margin-right: 0.5rem;">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteAdmin(${admin.id})">Delete</button>
                    </td>
                </tr>
            `).join('');
        }

        function openAdminModal(adminId = null) {
            document.getElementById('adminModalTitle').textContent = adminId ? 'Edit Admin User' : 'Add Admin User';
            document.getElementById('adminId').value = adminId || '';
            document.getElementById('adminName').value = '';
            document.getElementById('adminEmail').value = '';
            document.getElementById('adminPassword').value = '';
            
            if (adminId) {
                const admin = allAdmins.find(a => a.id === adminId);
                if (admin) {
                    document.getElementById('adminName').value = admin.name;
                    document.getElementById('adminEmail').value = admin.email;
                }
            }
            
            document.getElementById('adminModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeAdminModal() {
            document.getElementById('adminModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function editAdmin(id) {
            openAdminModal(id);
        }

        async function saveAdmin() {
            const id = document.getElementById('adminId').value;
            const name = document.getElementById('adminName').value.trim();
            const email = document.getElementById('adminEmail').value.trim();
            const password = document.getElementById('adminPassword').value;

            if (!name || !email) {
                alert('Please fill in all required fields');
                return;
            }

            if (!id && !password) {
                alert('Password is required for new admin');
                return;
            }

            if (password && password.length < 8) {
                alert('Password must be at least 8 characters');
                return;
            }

            try {
                const url = id ? `/admin/api/users/${id}` : '/admin/api/users';
                const method = id ? 'PUT' : 'POST';
                
                const body = { name, email };
                if (password) body.password = password;

                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(body)
                });

                if (!response.ok) throw new Error('Failed to save admin');

                closeAdminModal();
                loadAdmins();
                showNotification(id ? 'Admin updated successfully' : 'Admin created successfully', 'success');
            } catch (error) {
                console.error('Error saving admin:', error);
                alert('Error saving admin. Please try again.');
            }
        }

        async function deleteAdmin(id) {
            if (!confirm('Are you sure you want to delete this admin?')) return;

            try {
                const response = await fetch(`/admin/api/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (!response.ok) throw new Error('Failed to delete admin');

                loadAdmins();
                showNotification('Admin deleted successfully', 'success');
            } catch (error) {
                console.error('Error deleting admin:', error);
                alert('Error deleting admin. Please try again.');
            }
        }

        // ==========================================
        // ==========================================
        // ANNOUNCEMENTS MANAGEMENT
        // ==========================================

        async function loadAnnouncements() {
            // Populate classroom dropdown
            const select = document.getElementById('announcementClassroom');
            if (select.options.length <= 1 && classrooms.length > 0) {
                classrooms.forEach(c => {
                    if (c.is_active) {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.name;
                        select.appendChild(opt);
                    }
                });
            }

            try {
                const response = await fetch('/admin/api/announcements', {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                const data = await response.json();
                const tbody = document.getElementById('announcementsTableBody');

                if (!data.announcements || data.announcements.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 2rem; color: var(--text-secondary);">No announcements yet</td></tr>';
                    return;
                }

                tbody.innerHTML = data.announcements.map(a => {
                    const isActive = a.is_active && !a.is_expired;
                    const statusBadge = isActive
                        ? '<span class="badge badge-success">Active</span>'
                        : a.is_expired
                            ? '<span class="badge badge-secondary">Expired</span>'
                            : '<span class="badge badge-secondary">Stopped</span>';

                    const expiresAt = new Date(a.expires_at);
                    const timeStr = expiresAt.toLocaleString();

                    return `
                        <tr>
                            <td>${a.classroom ? a.classroom.name : 'Unknown'}</td>
                            <td style="font-size: 0.8125rem; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${a.message}</td>
                            <td>${statusBadge}</td>
                            <td style="font-size: 0.8125rem;">${timeStr}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    ${isActive ? `<button class="btn-icon" onclick="stopAnnouncement(${a.id})" title="Stop">⏹️</button>` : ''}
                                    <button class="btn-icon" onclick="deleteAnnouncement(${a.id})" title="Delete" style="color: var(--danger);">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
            } catch (error) {
                console.error('Error loading announcements:', error);
            }
        }

        async function sendAnnouncement(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const btn = document.getElementById('sendAnnouncementBtn');

            btn.disabled = true;
            btn.textContent = 'Sending...';

            try {
                const response = await fetch('/admin/api/announcements', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        classroom_id: parseInt(formData.get('classroom_id')),
                        message: formData.get('message'),
                        duration: parseInt(formData.get('duration')),
                        repeat_interval: parseInt(formData.get('repeat_interval')),
                    })
                });

                const result = await response.json();
                if (result.success) {
                    showNotification(result.message, 'success');
                    form.message.value = '';
                    loadAnnouncements();
                } else {
                    showNotification(result.message || 'Failed to send', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Failed to send announcement', 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Send';
            }
        }

        async function stopAnnouncement(id) {
            try {
                await fetch(`/admin/api/announcements/${id}/stop`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                showNotification('Announcement stopped', 'success');
                loadAnnouncements();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function deleteAnnouncement(id) {
            if (!confirm('Delete this announcement?')) return;
            try {
                await fetch(`/admin/api/announcements/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                showNotification('Announcement deleted', 'success');
                loadAnnouncements();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // SETTINGS MANAGEMENT
        // ==========================================
        
        function saveSettings() {
            const settings = {
                system_name: document.getElementById('systemName').value,
                system_email: document.getElementById('systemEmail').value,
                timezone: document.getElementById('timezone').value,
                allow_late_submission: document.getElementById('allowLateSubmission').checked,
                show_score_immediately: document.getElementById('showScoreImmediately').checked,
                allow_review_answers: document.getElementById('allowReviewAnswers').checked
            };

            // Save to localStorage for now (can be moved to backend later)
            localStorage.setItem('systemSettings', JSON.stringify(settings));
            showNotification('Settings saved successfully', 'success');
        }

        function resetSettings() {
            document.getElementById('systemName').value = 'ExamJe System';
            document.getElementById('systemEmail').value = 'admin@examje.com';
            document.getElementById('timezone').value = 'Asia/Kuala_Lumpur';
            document.getElementById('allowLateSubmission').checked = true;
            document.getElementById('showScoreImmediately').checked = true;
            document.getElementById('allowReviewAnswers').checked = true;
            
            localStorage.removeItem('systemSettings');
            showNotification('Settings reset to defaults', 'success');
        }

        // Upload Logo
        async function uploadLogo() {
            const fileInput = document.getElementById('logoFile');
            if (!fileInput.files.length) return;

            const formData = new FormData();
            formData.append('logo', fileInput.files[0]);

            try {
                const response = await fetch('/admin/api/upload-logo', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const result = await response.json();
                if (result.success) {
                    document.getElementById('logoPreview').src = result.logo_url;
                    showNotification(result.message, 'success');
                } else {
                    showNotification(result.message || 'Upload failed', 'error');
                }
            } catch (error) {
                showNotification('Failed to upload logo', 'error');
            }
            fileInput.value = '';
        }

        async function resetLogo() {
            if (!confirm('Reset logo to default?')) return;

            try {
                const response = await fetch('/admin/api/reset-logo', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const result = await response.json();
                if (result.success) {
                    document.getElementById('logoPreview').src = result.logo_url;
                    showNotification(result.message, 'success');
                }
            } catch (error) {
                showNotification('Failed to reset logo', 'error');
            }
        }

        // Change Password
        async function changePassword(e) {
            e.preventDefault();

            // Clear previous errors
            document.getElementById('currentPasswordError').style.display = 'none';
            document.getElementById('newPasswordError').style.display = 'none';
            document.getElementById('confirmPasswordError').style.display = 'none';

            const currentPassword = document.getElementById('currentPassword').value;
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword.length < 8) {
                document.getElementById('newPasswordError').textContent = 'Password must be at least 8 characters';
                document.getElementById('newPasswordError').style.display = 'block';
                return;
            }

            if (newPassword !== confirmPassword) {
                document.getElementById('confirmPasswordError').textContent = 'Passwords do not match';
                document.getElementById('confirmPasswordError').style.display = 'block';
                return;
            }

            const btn = document.getElementById('changePasswordBtn');
            btn.disabled = true;
            btn.textContent = 'Changing...';

            try {
                const response = await fetch('/admin/api/change-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        password: newPassword,
                        password_confirmation: confirmPassword
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    if (data.errors?.current_password) {
                        document.getElementById('currentPasswordError').textContent = data.errors.current_password[0];
                        document.getElementById('currentPasswordError').style.display = 'block';
                    }
                    if (data.errors?.password) {
                        document.getElementById('newPasswordError').textContent = data.errors.password[0];
                        document.getElementById('newPasswordError').style.display = 'block';
                    }
                    return;
                }

                // Clear form
                document.getElementById('changePasswordForm').reset();
                showNotification('Password changed successfully', 'success');
            } catch (error) {
                console.error('Error changing password:', error);
                showNotification('Error changing password. Please try again.', 'error');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Change Password';
            }
        }

        function loadSettings() {
            const saved = localStorage.getItem('systemSettings');
            if (saved) {
                const settings = JSON.parse(saved);
                document.getElementById('systemName').value = settings.system_name || 'ExamJe System';
                document.getElementById('systemEmail').value = settings.system_email || 'admin@examje.com';
                document.getElementById('timezone').value = settings.timezone || 'Asia/Kuala_Lumpur';
                document.getElementById('allowLateSubmission').checked = settings.allow_late_submission !== false;
                document.getElementById('showScoreImmediately').checked = settings.show_score_immediately !== false;
                document.getElementById('allowReviewAnswers').checked = settings.allow_review_answers !== false;
            }
        }

        // Update loadPageData to include admins and settings
        const originalLoadPageData2 = loadPageData;
        loadPageData = function(page) {
            originalLoadPageData2(page);
            if (page === 'admins') {
                loadAdmins();
            } else if (page === 'announcements') {
                loadAnnouncements();
            } else if (page === 'settings') {
                loadSettings();
            }
        };

        // Preload essential data on page load
        loadDashboardStats();
        loadClassrooms();
        loadCategories();

        // ==========================================
        // NOTIFICATIONS
        // ==========================================
        let notifLastSeen = localStorage.getItem('notifLastSeen') || '2000-01-01T00:00:00';

        function toggleNotifications() {
            const dropdown = document.getElementById('notifDropdown');
            dropdown.classList.toggle('open');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const wrapper = document.getElementById('notifWrapper');
            if (wrapper && !wrapper.contains(e.target)) {
                document.getElementById('notifDropdown').classList.remove('open');
            }
        });

        function markAllRead() {
            localStorage.setItem('notifLastSeen', new Date().toISOString());
            document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
            document.getElementById('notifBadge').style.display = 'none';
        }

        function timeAgo(dateStr) {
            const now = new Date();
            const date = new Date(dateStr);
            const diff = Math.floor((now - date) / 1000);
            if (diff < 60) return 'just now';
            if (diff < 3600) return Math.floor(diff / 60) + 'm ago';
            if (diff < 86400) return Math.floor(diff / 3600) + 'h ago';
            return Math.floor(diff / 86400) + 'd ago';
        }

        async function loadNotifications() {
            try {
                const response = await fetch('/admin/api/exam-sessions');
                const data = await response.json();

                const sessions = (data.sessions || data)
                    .filter(s => s.completed_at)
                    .sort((a, b) => new Date(b.completed_at) - new Date(a.completed_at))
                    .slice(0, 10);

                const list = document.getElementById('notifList');
                const lastSeen = new Date(notifLastSeen);
                let unreadCount = 0;

                if (sessions.length === 0) {
                    list.innerHTML = '<div class="notif-empty">No notifications yet</div>';
                    return;
                }

                list.innerHTML = sessions.map(s => {
                    const isUnread = new Date(s.completed_at) > lastSeen;
                    if (isUnread) unreadCount++;
                    const score = s.score !== null ? s.score + '%' : 'N/A';
                    const passed = s.score >= 50;
                    const studentName = s.student ? s.student.name : 'A student';
                    const classroomName = s.classroom ? s.classroom.name : 'an exam';

                    return `
                        <div class="notif-item ${isUnread ? 'unread' : ''}">
                            <div class="notif-icon ${passed ? 'success' : 'warning'}">${passed ? '✅' : '⚠️'}</div>
                            <div class="notif-body">
                                <div class="notif-text"><strong>${studentName}</strong> completed <strong>${classroomName}</strong> with score <strong>${score}</strong></div>
                                <div class="notif-time">${timeAgo(s.completed_at)}</div>
                            </div>
                        </div>
                    `;
                }).join('');

                const badge = document.getElementById('notifBadge');
                if (unreadCount > 0) {
                    badge.textContent = unreadCount > 9 ? '9+' : unreadCount;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            } catch (error) {
                console.error('Error loading notifications:', error);
                document.getElementById('notifList').innerHTML = '<div class="notif-empty">Failed to load</div>';
            }
        }

        loadNotifications();
    </script>

    <!-- Footer -->
    <div style="position: fixed; bottom: 0; right: 0; left: 250px; text-align: center; padding: 0.75rem 0; color: var(--text-secondary); font-size: 0.75rem; background: var(--bg-primary); border-top: 1px solid var(--border-color); z-index: 5; transition: left 0.3s ease;">
        © {{ date('Y') }} ExamJe by <a href="https://azfarmiskam.site" target="_blank" style="color: inherit; text-decoration: underline;">AzfarMiskam</a>. All rights reserved. v{{ config('app.version') }}
    </div>
</body>
</html>
