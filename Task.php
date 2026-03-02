<?php

require_once 'Database.php';

class Task
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM tasks ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("INSERT INTO tasks (title, description, status) VALUES (?, ?, ?)");
        $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['status'] ?? 'pending'
        ]);
        return $this->getById($this->db->lastInsertId());
    }

    public function update($id, $data)
    {
        $task = $this->getById($id);
        if (!$task)
            return false;

        $fields = [];
        $params = [];

        if (isset($data['title'])) {
            $fields[] = "title = ?";
            $params[] = $data['title'];
        }
        if (isset($data['description'])) {
            $fields[] = "description = ?";
            $params[] = $data['description'];
        }
        if (isset($data['status'])) {
            $fields[] = "status = ?";
            $params[] = $data['status'];
        }

        if (empty($fields))
            return $task;

        $fields[] = "updated_at = CURRENT_TIMESTAMP";
        $params[] = $id;

        $query = "UPDATE tasks SET " . implode(", ", $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $this->getById($id);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}
