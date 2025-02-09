<?php

// Database configuration
$host = 'localhost';
$db = 'eventbooks';
$user = 'root';
$pass = '';

// Create a MySQLi connection
$conn = new mysqli($host, $user, $pass, $db);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

?>