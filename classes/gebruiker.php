<?php
class User {
    private $email;
    private $hashedPassword;

    public function __construct($email, $password) {
        $this->setEmail($email);
        $this->setPassword($password);
    }

    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Ongeldig e-mailadres.");
        }
        $this->email = $email;
    }

    public function setPassword($password) {
        if (strlen($password) < 6) {
            throw new Exception("Wachtwoord moet minstens 6 tekens zijn.");
        }
        $this->hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getEmail() {
        return $this->email;
    }

    public function getHashedPassword() {
        return $this->hashedPassword;
    }
}
