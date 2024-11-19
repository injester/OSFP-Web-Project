<?php
session_start();
require '../config.php'; // Database connection

header('Content-Type: application/json');

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['rank'] < 10) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $fields_to_update = [];
    $params = ['user_id' => $user_id];

    if (!empty($_POST['username'])) {
        $fields_to_update[] = 'username = :username';
        $params['username'] = $_POST['username'];
    }
    if (!empty($_POST['email'])) {
        $fields_to_update[] = 'email = :email';
        $params['email'] = $_POST['email'];
    }
    if (!empty($_POST['rsn'])) {
        $fields_to_update[] = 'rsn = :rsn';
        $params['rsn'] = $_POST['rsn'];
    }
    if (isset($_POST['balance']) && is_numeric($_POST['balance'])) {
        $fields_to_update[] = 'balance = :balance';
        $params['balance'] = floatval($_POST['balance']);
    }

    if ($fields_to_update) {
        $sql = "UPDATE users SET " . implode(', ', $fields_to_update) . " WHERE id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No changes made']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'No fields to update']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Missing required data']);
}
?>
