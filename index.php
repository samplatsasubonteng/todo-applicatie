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
$success = '';

// ✅ POST-handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nieuwe lijst toevoegen
    if (isset($_POST['submit_lijst'], $_POST['nieuwe_lijst'])) {
        $nieuweLijstNaam = trim($_POST['nieuwe_lijst']);
        if (!empty($nieuweLijstNaam)) {
            $stmt = $pdo->prepare("INSERT INTO user_lijst (user_id, name) VALUES (:user_id, :name)");
            $stmt->execute([':user_id' => $userId, ':name' => $nieuweLijstNaam]);
            $success = "Lijst toegevoegd!";
        }
    }

    // Nieuwe taak toevoegen
    if (isset($_POST['title'], $_POST['lijst_id']) && empty($_POST['submit_lijst'])) {
        $title = trim($_POST['title']);
        $lijstId = (int)$_POST['lijst_id'];
        if (!empty($title)) {
            $stmt = $pdo->prepare("INSERT INTO todos (user_id, title, lijst_id) VALUES (:user_id, :title, :lijst_id)");
            $stmt->execute([':user_id' => $userId, ':title' => $title, ':lijst_id' => $lijstId]);
        } else {
            $error = "Titel mag niet leeg zijn.";
        }
    }
}

// ✅ Acties via GET
if (isset($_GET['done'])) {
    $stmt = $pdo->prepare("UPDATE todos SET is_done = 1 WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => (int)$_GET['done'], ':user_id' => $userId]);
}

if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => (int)$_GET['delete'], ':user_id' => $userId]);
}

// ✅ Lijsten en taken ophalen
$lijsten = $pdo->query("SELECT * FROM lijst ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM user_lijst WHERE user_id = :user_id ORDER BY name ASC");
$stmt->execute([':user_id' => $userId]);
$eigenLijsten = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM todos WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt->execute([':user_id' => $userId]);
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

    <h3>📝 Nieuwe taak toevoegen</h3>
    <form method="post">
        <input type="text" name="title" placeholder="Wat moet je doen?" required>
        <select name="lijst_id" required>
            <option value="">-- Kies een lijst --</option>
            <optgroup label="📁 Standaard lijsten">
                <?php foreach ($lijsten as $lijst): ?>
                    <option value="<?= $lijst['id'] ?>"><?= htmlspecialchars($lijst['name']) ?></option>
                <?php endforeach; ?>
            </optgroup>
            <optgroup label="👤 Jouw lijsten">
                <?php foreach ($eigenLijsten as $lijst): ?>
                    <option value="<?= $lijst['id'] ?>">📝 <?= htmlspecialchars($lijst['name']) ?></option>
                <?php endforeach; ?>
            </optgroup>
        </select>
        <button class="formulier" type="submit">➕ Taak toevoegen</button>
    </form>

    <h3>📂 Nieuwe lijst aanmaken</h3>
    <form method="post" class="zelfGemaakt">
        <input type="text" name="nieuwe_lijst" placeholder="Bijv. Vakantie plannen..." required>
        <button type="submit" name="submit_lijst">➕ Lijst toevoegen</button>
    </form>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p class="success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <ul class="lijst">
        <?php foreach ($todos as $todo): ?>
            <li class="item <?= $todo['is_done'] ? 'done' : '' ?>">
                <?= htmlspecialchars($todo['title']) ?>
                <span class="acties">
                    <?php if (!$todo['is_done']): ?>
                        <a href="?done=<?= $todo['id'] ?>" title="Markeer als gedaan">✅</a>
                    <?php endif; ?>
                    <a href="?delete=<?= $todo['id'] ?>" title="Verwijderen" onclick="return confirm('Weet je zeker dat je deze taak wil verwijderen?')">🗑️</a>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="logout">
        <a href="logout.php">Uitloggen</a>
    </div>
</div>
</body>
</html>
