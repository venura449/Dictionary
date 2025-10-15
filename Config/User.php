<?php
require_once 'dbconn.php';

class UserRepository {
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }

    public function findByEmail($email) {
        $emailEsc = $this->conn->real_escape_string($email);
        $sql = "SELECT id, name, email, password_hash, created_at FROM users WHERE email = '$emailEsc' LIMIT 1";
        $res = $this->conn->query($sql);
        if ($res && $res->num_rows > 0) {
            return $res->fetch_assoc();
        }
        return null;
    }

    public function create($name, $email, $password) {
        $nameEsc = $this->conn->real_escape_string($name);
        $emailEsc = $this->conn->real_escape_string($email);
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $hashEsc = $this->conn->real_escape_string($hash);
        $sql = "INSERT INTO users (name, email, password_hash, created_at) VALUES ('$nameEsc', '$emailEsc', '$hashEsc', NOW())";
        if ($this->conn->query($sql)) {
            return $this->conn->insert_id;
        }
        return false;
    }
}

?>


