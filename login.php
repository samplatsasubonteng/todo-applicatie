<?php
session_start();
require_once 'database.php';

$db = new Database();
$pdo = $db->connect();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $stmt = $pdo->prepare('SELECT * FROM user WHERE email = :email');
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['loggedin'] = true;

            header('Location: index.php');
            exit;
        } else {
            $error = "E-mailadres of wachtwoord is onjuist.";
        }
    } catch (PDOException $e) {
        $error = "Er ging iets mis bij het inloggen.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inloggen</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
    <h2>Inloggen</h2>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

    <form method="post">
        <label for="email">E-mailadres:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Wachtwoord:</label>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Inloggen">
    </form>

    <p>Nog geen account? <a href="register.php">Aanmelden</a></p>
</body>
</html>
