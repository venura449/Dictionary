<?php
// Database configuration
$servername = "127.0.0.1";   // or "localhost" if connecting from host
$username   = "root";
$password   = "";        // change to your DB password
$database   = "Dictionary";  // change to your database name
$charset    = "utf8mb4";

// Create connection using MySQLi (Object-Oriented)
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Set proper charset for Sinhala text
if (!$conn->set_charset($charset)) {
    printf("Error loading character set utf8mb4: %s\n", $conn->error);
    exit();
}

// ✅ Optional: Success message for testing
// echo "✅ Connected successfully to database '$database'";

?>
