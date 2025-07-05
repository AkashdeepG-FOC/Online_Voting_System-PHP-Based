<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voting_system5";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
