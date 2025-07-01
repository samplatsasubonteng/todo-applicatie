<?php
require_once 'database.php';
require_once 'classes/gebruiker.php'; // ✅ Klasse importeren

$db = new Database();
$pdo = $db->connect();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $gebruiker = new User($_POST['email'], $_POST['password']);
        $email = $gebruiker->getEmail();
        $hashedPassword = $gebruiker->getHashedPassword();

        $stmt = $pdo->prepare('INSERT INTO user (email, password) VALUES (:email, :password)');
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $hashedPassword);

        if ($stmt->execute()) {
            $success = "Registratie gelukt! Je kan nu <a href='login.php'>inloggen</a>.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
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
    <link rel="stylesheet" href="css/login.css"> 
</head>
<body>
    <div class="rand">
        <h2>Aanmelden</h2>

        <?php if (!empty($error)) echo "<p class='form__error'>$error</p>"; ?>
        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

        <form method="post">
            <div class="field">
                <label for="email">E-mailadres:</label>
                <input type="email" name="email" required>
            </div>

            <div class="field">
                <label for="password">Wachtwoord:</label>
                <input type="password" name="password" required>
            </div>

            <input type="submit" value="Aanmelden">
        </form>

        <div class="aanmelden">
            Heb je al een account? <a href="login.php">Inloggen</a>
        </div>
    </div>
</body>
</html>
