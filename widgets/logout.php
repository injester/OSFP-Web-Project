<?php
session_start();
require '../config.php'; // Ensure correct path to config.php

// Update the user's online status to offline before logging out
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("UPDATE users SET is_online = 0 WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
}

// Destroy the session and redirect to login
session_destroy();
header('Location: ../login.php'); // Ensure this is an absolute path
exit;
?>
