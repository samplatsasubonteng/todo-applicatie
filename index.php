<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
}

$db = new Database();
$pdo = $db->connect();

$userId = $_SESSION['user_id'];
$error = '';

// Nieuwe taak toevoegen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    if (!empty($title)) {
        $stmt = $pdo->prepare("INSERT INTO todos (user_id, title) VALUES (:user_id, :title)");
        $stmt->bindValue(':user_id', $userId);
        $stmt->bindValue(':title', $title);
        $stmt->execute();
    } else {
        $error = "Titel mag niet leeg zijn.";
    }
}


if (isset($_GET['done'])) {
    $id = (int)$_GET['done'];
    $stmt = $pdo->prepare("UPDATE todos SET is_done = 1 WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $id, ':user_id' => $userId]);
}

$stmt = $pdo->prepare("SELECT * FROM todos WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->bindValue(':user_id', $userId);
$stmt->execute();
$todos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Homepagina</title>
    <link rel="stylesheet" href="css/index.css">
</head>
<body>
    <div class="alles">
        <h2>Welkom op je todo-app!</h2>
        <p class="email">Ingelogd als: <strong><?= htmlspecialchars($_SESSION['email']) ?></strong></p>

        <form method="post">
            <input type="text" name="title" placeholder="Wat moet je doen?" required>
            <button type="submit">Toevoegen</button>
        </form>

        <ul class="lijst">
            <?php foreach ($todos as $todo): ?>
                <li class="item <?= $todo['is_done'] ? 'done' : '' ?>" data-id="<?= $todo['id'] ?>">
                    <?= htmlspecialchars($todo['title']) ?>
                    <span class="acties">
                        <?php if (!$todo['is_done']): ?>
                            <a href="?done=<?= $todo['id'] ?>" title="Markeer als gedaan">✅</a>
                        <?php endif; ?>
                        <a href="#" class="delete-btn" data-id="<?= $todo['id'] ?>" title="Verwijderen">🗑️</a>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="logout">
            <a href="logout.php">Uitloggen</a>
        </div>
    </div>

    <script>
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm("Weet je zeker dat je deze taak wil verwijderen?")) {
                const id = this.dataset.id;

                fetch('verwijderen.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + encodeURIComponent(id)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.closest('li').remove();
                    } else {
                        alert(data.message);
                    }
                });
            }
        });
    });
    </script>
</body>
</html>
