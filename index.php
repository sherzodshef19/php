<?php

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once 'Task.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_parts = explode('/', trim($uri, '/'));

// Find the index of 'tasks' in the URI parts
$tasks_index = array_search('tasks', $uri_parts);

$taskModel = new Task();

// Handle OPTIONS request for CORS
if ($method === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Routes
// /tasks
if ($tasks_index !== false) {
    $id = isset($uri_parts[$tasks_index + 1]) && is_numeric($uri_parts[$tasks_index + 1])
        ? (int) $uri_parts[$tasks_index + 1]
        : null;

    switch ($method) {
        case 'GET':
            if ($id) {
                $task = $taskModel->getById($id);
                if ($task) {
                    echo json_encode($task);
                } else {
                    http_response_code(404);
                    echo json_encode(["message" => "Task not found"]);
                }
            } else {
                $tasks = $taskModel->getAll();
                echo json_encode($tasks);
            }
            break;

        case 'POST':
            $input = json_decode(file_get_contents("php://input"), true);
            if (empty($input['title'])) {
                http_response_code(400);
                echo json_encode(["message" => "Title is required"]);
                break;
            }
            $newTask = $taskModel->create($input);
            http_response_code(201);
            echo json_encode($newTask);
            break;

        case 'PUT':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["message" => "ID is required for update"]);
                break;
            }
            $input = json_decode(file_get_contents("php://input"), true);
            $updatedTask = $taskModel->update($id, $input);
            if ($updatedTask) {
                echo json_encode($updatedTask);
            } else {
                http_response_code(444); // User requirement says GET /tasks/{id} returns 404, maybe task not found
                http_response_code(404);
                echo json_encode(["message" => "Task not found"]);
            }
            break;

        case 'DELETE':
            if (!$id) {
                http_response_code(400);
                echo json_encode(["message" => "ID is required for deletion"]);
                break;
            }
            if ($taskModel->delete($id)) {
                echo json_encode(["message" => "Task deleted successfully"]);
            } else {
                http_response_code(404);
                echo json_encode(["message" => "Task not found"]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["message" => "Method not allowed"]);
            break;
    }
} else {
    http_response_code(404);
    echo json_encode(["message" => "Endpoint not found"]);
}
