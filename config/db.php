<?php
$conn = new mysqli("localhost", "root", "", "infosec");

if ($conn->connect_error) {
    die("Connection failed");
}
$conn->set_charset("utf8mb4");
?>