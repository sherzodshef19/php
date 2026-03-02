# Task Management API (Pure PHP)

A simple REST API for managing a To-Do list, built with pure PHP and SQLite.

## Features
- CRUD operations for tasks (Create, Read, Update, Delete)
- SQLite database
- Input validation
- JSON responses

## Requirements
- PHP 7.4 or higher
- PHP SQLite extension

## Installation & Setup

1. **Clone the repository** (if applicable) or copy the files to your server directory.
2. **Initialize the database**:
   Run the following command to create the `database.sqlite` file and the necessary tables:
   ```bash
   php init_db.php
   ```
3. **Start the local server**:
   You can use PHP's built-in server for testing:
   ```bash
   php -S localhost:8000
   ```
4. **Open the Dashboard**:
   Access the web interface at:
   `http://localhost:8000/dashboard.php`

## API Endpoints

| Method | Endpoint | Description |
| --- | --- | --- |
| `POST` | `/tasks` | Create a new task. Body: `{"title": "...", "description": "...", "status": "..."}` |
| `GET` | `/tasks` | Get all tasks. |
| `GET` | `/tasks/{id}` | Get a single task by ID. |
| `PUT` | `/tasks/{id}` | Update a task. Body: `{"title": "...", "status": "..."}` |
| `DELETE` | `/tasks/{id}` | Delete a task. |

## Example Usage (using curl)

### Create a Task
```bash
curl -X POST -H "Content-Type: application/json" -d '{"title":"My Task", "description":"Complete the assignment"}' http://localhost:8000/tasks
```

### Get all Tasks
```bash
curl http://localhost:8000/tasks
```
