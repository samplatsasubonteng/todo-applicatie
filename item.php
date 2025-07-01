<?php
session_start();
require_once 'database.php';
require_once 'classes/comment.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "Geen taak-ID meegegeven.";
    exit;
}

$db = new Database();
$pdo = $db->connect();
$userId = $_SESSION['user_id'];
$todoId = (int)$_GET['id'];

// ✅ Haal taak op
$stmt = $pdo->prepare("SELECT * FROM todos WHERE id = :id AND user_id = :user_id");
$stmt->execute([':id' => $todoId, ':user_id' => $userId]);
$todo = $stmt->fetch();

if (!$todo) {
    echo "Taak niet gevonden.";
    exit;
}

// ✅ Comment toevoegen via OOP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['inhoud'])) {
    try {
        $inhoud = trim($_POST['inhoud']);
        $comment = new Comment($todoId, $userId, $inhoud);
        $comment->save($pdo);
    } catch (Exception $e) {
        echo "Fout bij toevoegen van commentaar: " . htmlspecialchars($e->getMessage());
    }
}

// ✅ Haal alle comments op
$stmt = $pdo->prepare("SELECT c.*, u.email FROM comments c JOIN user u ON c.user_id = u.id WHERE c.todo_id = :todo_id ORDER BY c.created_at DESC");
$stmt->execute([':todo_id' => $todoId]);
$comments = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Details van Taak</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
<div class="alles">
    <h2>📝 <?= htmlspecialchars($todo['title']) ?> [<?= htmlspecialchars($todo['priority']) ?>]</h2>
    <p><strong>Status:</strong> <?= $todo['is_done'] ? '✅ Voltooid' : '⏳ Nog bezig' ?></p>
    <p><a href="index.php">⬅️ Terug naar overzicht</a></p>

    <h3>💬 Commentaren</h3>
    <form method="post">
        <textarea name="inhoud" rows="3" placeholder="Typ je commentaar..." required></textarea>
        <br>
        <button type="submit">➕ Commentaar toevoegen</button>
    </form>

    <ul class="lijst">
        <?php foreach ($comments as $comment): ?>
            <li class="item">
                <p><?= nl2br(htmlspecialchars($comment['inhoud'])) ?></p>
                <small><em>Door <?= htmlspecialchars($comment['email']) ?> op <?= $comment['created_at'] ?></em></small>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
