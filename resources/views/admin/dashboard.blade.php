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
            form.is_active.checked = classroom.is_active;
            
            document.getElementById('classroomModal').style.display = 'flex';
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
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            deletingClassroomId = null;
        }

        // Update classroom badge
        function updateClassroomBadge() {
            const badge = document.querySelector('.nav-link[data-page="classrooms"] .nav-badge');
            if (badge) {
                badge.textContent = classrooms.length;
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            // Simple alert for now - can be enhanced with a toast notification system
            alert(message);
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
