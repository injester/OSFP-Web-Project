<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];  // Assuming this is set on login
$amount_played = $_POST['amount_played'];
$status = $_POST['status'];
$player_play = $_POST['player_play'];
$house_play = $_POST['house_play'];

// Insert history record
$stmt = $pdo->prepare("INSERT INTO history (user_id, username, amount_played, status, player_play, house_play, created_at) VALUES (:user_id, :username, :amount_played, :status, :player_play, :house_play, NOW())");
$stmt->execute([
    'user_id' => $user_id,
    'username' => $username,
    'amount_played' => $amount_played,
    'status' => $status,
    'player_play' => $player_play,
    'house_play' => $house_play
]);

echo json_encode(['success' => true]);
?>
