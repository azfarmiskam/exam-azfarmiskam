<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard - EzExam</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/css/admin.css'])
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <!-- Sidebar Header -->
            <div class="sidebar-header">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                    <div class="brand-icon">📝</div>
                    <span class="brand-text">EzExam</span>
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
                            <a href="#" class="nav-link" data-page="students">
                                <span class="nav-icon">👥</span>
                                <span class="nav-text">Students</span>
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
                    <button class="action-btn" title="Notifications">
                        <span>🔔</span>
                        <span class="badge">3</span>
                    </button>
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
                            <h3>Welcome to EzExam Admin Dashboard!</h3>
                            <p>Get started by creating your first classroom or adding questions to the question bank.</p>
                        </div>
                    </div>
                </div>

                <!-- Classrooms Content -->
                <div class="spa-content" id="page-classrooms">
                    <div class="card">
                        <div class="card-body">
                            <h3>Classrooms Management</h3>
                            <p>Classroom management features coming soon...</p>
                        </div>
                    </div>
                </div>

                <!-- Questions Content -->
                <div class="spa-content" id="page-questions">
                    <div class="card">
                        <div class="card-body">
                            <h3>Question Bank</h3>
                            <p>Question bank features coming soon...</p>
                        </div>
                    </div>
                </div>

                <!-- Categories Content -->
                <div class="spa-content" id="page-categories">
                    <div class="card">
                        <div class="card-body">
                            <h3>Categories</h3>
                            <p>Category management features coming soon...</p>
                        </div>
                    </div>
                </div>

                <!-- Students Content -->
                <div class="spa-content" id="page-students">
                    <div class="card">
                        <div class="card-body">
                            <h3>Students</h3>
                            <p>Student management features coming soon...</p>
                        </div>
                    </div>
                </div>

                <!-- Results Content -->
                <div class="spa-content" id="page-results">
                    <div class="card">
                        <div class="card-body">
                            <h3>Exam Results</h3>
                            <p>Results viewing features coming soon...</p>
                        </div>
                    </div>
                </div>

                <!-- Analytics Content -->
                <div class="spa-content" id="page-analytics">
                    <div class="card">
                        <div class="card-body">
                            <h3>Analytics</h3>
                            <p>Analytics features coming soon...</p>
                        </div>
                    </div>
                </div>

                <!-- Activity Content -->
                <div class="spa-content" id="page-activity">
                    <div class="card">
                        <div class="card-body">
                            <h3>Activity Logs</h3>
                            <p>Activity log features coming soon...</p>
                        </div>
                    </div>
                </div>

                <!-- Admins Content -->
                <div class="spa-content" id="page-admins">
                    <div class="card">
                        <div class="card-body">
                            <h3>Admin Users</h3>
                            <p>Admin user management features coming soon...</p>
                        </div>
                    </div>
                </div>

                <!-- Settings Content -->
                <div class="spa-content" id="page-settings">
                    <div class="card">
                        <div class="card-body">
                            <h3>Settings</h3>
                            <p>Settings features coming soon...</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
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
            
            // Save state to localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });

        // Restore sidebar state
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            toggleIcon.textContent = '▶';
        }

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
                    'students': 'Students',
                    'results': 'Exam Results',
                    'analytics': 'Analytics',
                    'activity': 'Activity Logs',
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
            });
        });

        // Restore current page
        const currentPage = localStorage.getItem('currentPage');
        if (currentPage) {
            const targetLink = document.querySelector(`.nav-link[data-page="${currentPage}"]`);
            if (targetLink) {
                targetLink.click();
            }
        }

        // Load dashboard stats (placeholder - will be replaced with API calls)
        function loadDashboardStats() {
            // This will be replaced with actual API calls
            document.getElementById('totalClassrooms').textContent = '0';
            document.getElementById('totalQuestions').textContent = '0';
            document.getElementById('totalStudents').textContent = '0';
            document.getElementById('totalExams').textContent = '0';
        }

        // Load stats on page load
        loadDashboardStats();
    </script>
</body>
</html>
