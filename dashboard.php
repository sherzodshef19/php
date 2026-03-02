<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Bootstrap Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .task-card {
            transition: transform 0.2s;
        }

        .task-card:hover {
            transform: translateY(-5px);
        }

        .status-badge {
            font-size: 0.8rem;
        }
    </style>
</head>

<body>

    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand mb-0 h1">Task Manager</span>
            <span class="text-light opacity-50">Pure PHP + SQLite</span>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <!-- Add Task Form -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-4">New Task</h5>
                        <form id="taskForm">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" id="title" class="form-control" required
                                    placeholder="What needs to be done?">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea id="description" class="form-control" rows="3"
                                    placeholder="Additional details (optional)"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Task</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Task List -->
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Tasks</h5>
                    <button class="btn btn-sm btn-outline-secondary" onclick="loadTasks()">Refresh</button>
                </div>
                <div id="taskList" class="row">
                    <!-- Tasks will be loaded here -->
                    <div class="col-12 text-center py-5 text-muted">Loading tasks...</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Dynamically detect the API URL based on the current script location
    const getApiUrl = () => {
        const path = window.location.pathname;
        const directory = path.substring(0, path.lastIndexOf('/'));
        return directory + '/index.php/tasks';
    };

    const API_URL = getApiUrl();
    console.log('API URL initialized as:', API_URL);

        // Load tasks on startup
        document.addEventListener('DOMContentLoaded', loadTasks);

        // Form submission
        document.getElementById('taskForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = document.getElementById('title').value;
            const description = document.getElementById('description').value;

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ title, description })
                });

                if (response.ok) {
                    document.getElementById('taskForm').reset();
                    loadTasks();
                } else {
                    alert('Error adding task');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });

        // Fetch and render tasks
        async function loadTasks() {
            try {
                const response = await fetch(API_URL);
                const tasks = await response.json();
                const container = document.getElementById('taskList');

                if (tasks.length === 0) {
                    container.innerHTML = '<div class="col-12 text-center py-5 text-muted">No tasks found. Create one!</div>';
                    return;
                }

                container.innerHTML = tasks.map(task => `
                <div class="col-12 mb-3">
                    <div class="card shadow-sm task-card">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 ${task.status === 'completed' ? 'text-decoration-line-through text-muted' : ''}">
                                    ${task.title}
                                </h6>
                                <p class="small text-muted mb-0">${task.description || 'No description'}</p>
                                <span class="badge ${task.status === 'completed' ? 'bg-success' : 'bg-warning text-dark'} status-badge mt-2">
                                    ${task.status}
                                </span>
                            </div>
                            <div class="btn-group">
                                ${task.status === 'pending' ? `
                                    <button class="btn btn-sm btn-outline-success" onclick="updateStatus(${task.id}, 'completed')">Done</button>
                                ` : ''}
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteTask(${task.id})">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            } catch (error) {
                console.error('Error loading tasks:', error);
                document.getElementById('taskList').innerHTML = '<div class="col-12 text-center py-5 text-danger">Error loading tasks.</div>';
            }
        }

        // Update task status
        async function updateStatus(id, status) {
            try {
                await fetch(\`\${API_URL}/\${id}\`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status })
            });
            loadTasks();
        } catch (error) {
            console.error('Error updating task:', error);
        }
    }

    // Delete task
    async function deleteTask(id) {
        if (!confirm('Are you sure?')) return;
        try {
            await fetch(\`\${API_URL}/\${id}\`, { method: 'DELETE' });
            loadTasks();
        } catch (error) {
            console.error('Error deleting task:', error);
        }
    }
    </script>
</body>

</html>