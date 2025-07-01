<?php
class Comment {
    private $todo_id;
    private $user_id;
    private $inhoud;

    public function __construct($todo_id, $user_id, $inhoud) {
        $this->todo_id = $todo_id;
        $this->user_id = $user_id;
        $this->inhoud = $inhoud;
    }

    public function getTodoId() {
        return $this->todo_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getInhoud() {
        return $this->inhoud;
    }

    public function save($pdo) {
        $stmt = $pdo->prepare("INSERT INTO comments (todo_id, user_id, inhoud) VALUES (:todo_id, :user_id, :inhoud)");
        $stmt->execute([
            ':todo_id' => $this->todo_id,
            ':user_id' => $this->user_id,
            ':inhoud' => $this->inhoud
        ]);
    }
}
