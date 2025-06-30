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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['nieuwe_lijst'])) {
        $stmt = $pdo->prepare("INSERT INTO user_lijst (user_id, name) VALUES (:user_id, :name)");
        $stmt->execute([':user_id' => $userId, ':name' => trim($_POST['nieuwe_lijst'])]);
        $success = "Lijst toegevoegd!";
    } elseif (!empty($_POST['title']) && !empty($_POST['lijst_id']) && in_array($_POST['priority'], ['laag', 'gemiddeld', 'hoog'])) {
        $stmt = $pdo->prepare("INSERT INTO todos (user_id, title, lijst_id, priority) VALUES (:user_id, :title, :lijst_id, :priority)");
        $stmt->execute([
            ':user_id' => $userId,
            ':title' => trim($_POST['title']),
            ':lijst_id' => (int)$_POST['lijst_id'],
            ':priority' => $_POST['priority']
        ]);
    } else {
        $error = "Titel en prioriteit zijn verplicht.";
    }
}

$standaardLijsten = $pdo->query("SELECT * FROM lijst ORDER BY name ASC")->fetchAll();
$stmt = $pdo->prepare("SELECT * FROM user_lijst WHERE user_id = :user_id ORDER BY name ASC");
$stmt->execute([':user_id' => $userId]);
$persoonlijkeLijsten = $stmt->fetchAll();

$query = "
    SELECT * FROM todos 
    WHERE user_id = :user_id " .
    (isset($_GET['lijst_id']) && is_numeric($_GET['lijst_id']) ? "AND lijst_id = :lijst_id" : "") . "
    ORDER BY 
        CASE priority
            WHEN 'hoog' THEN 1
            WHEN 'gemiddeld' THEN 2
            WHEN 'laag' THEN 3
        END,
        created_at DESC
";
$stmt = $pdo->prepare($query);
$params = [':user_id' => $userId];
if (isset($_GET['lijst_id']) && is_numeric($_GET['lijst_id'])) {
    $params[':lijst_id'] = (int)$_GET['lijst_id'];
}
$stmt->execute($params);
$todos = $stmt->fetchAll();
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
                <?php foreach ($standaardLijsten as $lijst): ?>
                    <option value="<?= $lijst['id'] ?>"><?= htmlspecialchars($lijst['name']) ?></option>
                <?php endforeach; ?>
            </optgroup>
            <optgroup label="📝 Jouw lijsten">
                <?php foreach ($persoonlijkeLijsten as $lijst): ?>
                    <option value="<?= $lijst['id'] ?>">📝 <?= htmlspecialchars($lijst['name']) ?></option>
                <?php endforeach; ?>
            </optgroup>
        </select>
        <select name="priority" required>
            <option value="">-- Kies prioriteit --</option>
            <option value="laag">Laag</option>
            <option value="gemiddeld">Gemiddeld</option>
            <option value="hoog">Hoog</option>
        </select>
        <button type="submit">➕ Taak toevoegen</button>
    </form>

    <h3>📂 Nieuwe lijst aanmaken</h3>
    <form method="post">
        <input type="text" name="nieuwe_lijst" placeholder="Bijv. Werk, School,..." required>
        <button type="submit" name="submit_lijst">➕ Lijst toevoegen</button>
    </form>

    <?php if (!empty($error)): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if (!empty($success)): ?><p class="success"><?= htmlspecialchars($success) ?></p><?php endif; ?>

    <h3>📋 Filter op lijst</h3>
    <form method="get">
        <select name="lijst_id" onchange="this.form.submit()" class="filter-dropdown">
            <option value="">🔁 Alle taken</option>
            <optgroup label="📁 Standaard lijsten">
                <?php foreach ($standaardLijsten as $lijst): ?>
                    <option value="<?= $lijst['id'] ?>" <?= ($_GET['lijst_id'] ?? '') == $lijst['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lijst['name']) ?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
            <optgroup label="📝 Jouw lijsten">
                <?php foreach ($persoonlijkeLijsten as $lijst): ?>
                    <option value="<?= $lijst['id'] ?>" <?= ($_GET['lijst_id'] ?? '') == $lijst['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($lijst['name']) ?>
                    </option>
                <?php endforeach; ?>
            </optgroup>
        </select>
    </form>

    <ul class="lijst">
        <?php foreach ($todos as $todo): ?>
            <li class="item <?= $todo['is_done'] ? 'done' : '' ?>">
                <a href="item.php?id=<?= $todo['id'] ?>" class="titel-link">
                    <?= htmlspecialchars($todo['title']) ?>
                </a>
                <strong class="priority">[<?= htmlspecialchars($todo['priority']) ?>]</strong>
                <span class="acties">
                    <?php if (!$todo['is_done']): ?>
                        <a href="#" class="markeer-done" data-id="<?= $todo['id'] ?>">✅</a>
                    <?php endif; ?>
                    <a href="#" class="verwijder-taak" data-id="<?= $todo['id'] ?>">🗑️</a>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="logout"><a href="logout.php">Uitloggen</a></div>
</div>

<script>
document.querySelectorAll('.markeer-done').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        fetch('markeren_verwijderen.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'done', id: btn.dataset.id })
        }).then(res => res.json()).then(data => {
            if (data.status === 'success') {
                btn.closest('.item').classList.add('done');
                btn.remove();
            }
        });
    });
});

document.querySelectorAll('.verwijder-taak').forEach(btn => {
    btn.addEventListener('click', e => {
        e.preventDefault();
        if (!confirm("Weet je zeker dat je deze taak wil verwijderen?")) return;
        fetch('markeren_verwijderen.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', id: btn.dataset.id })
        }).then(res => res.json()).then(data => {
            if (data.status === 'success') {
                btn.closest('.item').remove();
            }
        });
    });
});
</script>
</body>
</html>
