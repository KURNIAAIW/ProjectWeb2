<?php
$servername = "127.0.0.1";  // Change to your server name if different
$username = "root";         // Change to your database username
$password = "";             // Change to your database password
$dbname = "db_projectweb2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
