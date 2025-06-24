<?php
require_once 'database.php';

$db = new Database();
$pdo = $db->connect();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare('INSERT INTO user (email, password) VALUES (:email, :password)');
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $hashedPassword);

        if ($stmt->execute()) {
            $success = "Registratie gelukt! Je kan nu <a href='login.php'>inloggen</a>.";
        }
    } catch (PDOException $e) {
        $error = "Registratie mislukt: mogelijk bestaat dit e-mailadres al.";
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Aanmelden</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body>
    <h2>Aanmelden</h2>
    <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
    
    <form method="post">
        <label for="email">E-mailadres:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Wachtwoord:</label>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Aanmelden">
    </form>

    <p>Heb je al een account? <a href="login.php">Inloggen</a></p>
</body>
</html>
