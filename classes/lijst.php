<?php

class Lijst {
    private $user_id;
    private $name;

    public function __construct($user_id, $name) {
        $this->user_id = $user_id;
        $this->name = trim($name);
    }

    public function getName() {
        return $this->name;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function save($pdo) {
        $stmt = $pdo->prepare("INSERT INTO user_lijst (user_id, name) VALUES (:user_id, :name)");
        $stmt->execute([
            ':user_id' => $this->user_id,
            ':name' => $this->name
        ]);
    }
}
