<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401); // Niet ingelogd
    echo json_encode(['status' => 'error', 'message' => 'Niet ingelogd']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Alleen POST toegestaan']);
    exit;
}

$db = new Database();
$pdo = $db->connect();
$userId = $_SESSION['user_id'];

// 📥 Gegevens uit JavaScript
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['action'], $data['id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Ongeldige input']);
    exit;
}

$taskId = (int)$data['id'];
$action = $data['action'];

// ✅ Markeer als gedaan
if ($action === 'done') {
    $stmt = $pdo->prepare("UPDATE todos SET is_done = 1 WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $taskId, ':user_id' => $userId]);
    echo json_encode(['status' => 'success', 'message' => 'Taak gemarkeerd als gedaan']);
    exit;
}

// ❌ Verwijder taak
if ($action === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $taskId, ':user_id' => $userId]);
    echo json_encode(['status' => 'success', 'message' => 'Taak verwijderd']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Ongeldige actie']);
