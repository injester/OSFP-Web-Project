<?php
header('Content-Type: application/json');
session_start();
require 'config.php';

$response = ['success' => false];

try {
    if (!isset($_POST['user_id']) || !isset($_POST['bet_amount'])) {
        throw new Exception('Missing required parameters.');
    }

    $user_id = (int)$_POST['user_id'];
    $bet_amount = (float)$_POST['bet_amount'];

    // Fetch the user's current rank directly from the users table
    $userStmt = $pdo->prepare("SELECT rank FROM users WHERE id = :user_id");
    $userStmt->execute(['user_id' => $user_id]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        throw new Exception("User not found in the users table.");
    }

    $rank = (int)$userData['rank'];

    // Skip all logic for users with rank 10 or higher
    if ($rank >= 10) {
        $response['success'] = true;
        $response['message'] = 'Admin users are not affected by wager or rank updates.';
        echo json_encode($response);
        exit;
    }

    // Fetch the user's current total wager and rakeback info
    $stmt = $pdo->prepare("SELECT total_wager, claimable_rakeback FROM raking WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $userRaking = $stmt->fetch(PDO::FETCH_ASSOC);

    // If no raking data exists, initialize it for the user
    if (!$userRaking) {
        $total_wager = $bet_amount;
        $claimable_rakeback = 0;

        $stmt = $pdo->prepare("INSERT INTO raking (user_id, username, total_wager, rank, claimable_rakeback) 
                               VALUES (:user_id, :username, :total_wager, :rank, :claimable_rakeback)");
        $stmt->execute([
            'user_id' => $user_id,
            'username' => $_POST['username'] ?? 'Unknown', // Fallback username
            'total_wager' => $total_wager,
            'rank' => 0,
            'claimable_rakeback' => $claimable_rakeback
        ]);
    } else {
        $total_wager = $userRaking['total_wager'] + $bet_amount;
        $claimable_rakeback = $userRaking['claimable_rakeback'];
    }

    // Update total wager
    $stmt = $pdo->prepare("UPDATE raking SET total_wager = :total_wager WHERE user_id = :user_id");
    $stmt->execute(['total_wager' => $total_wager, 'user_id' => $user_id]);

    // Define rank thresholds and rakeback rates
    $rankThresholds = [
        ['rank' => 0, 'minWager' => 0, 'rakeback' => 0.25],
        ['rank' => 1, 'minWager' => 10, 'rakeback' => 0.3],
        ['rank' => 2, 'minWager' => 50, 'rakeback' => 0.35],
        ['rank' => 3, 'minWager' => 100, 'rakeback' => 0.5],
        ['rank' => 4, 'minWager' => 250, 'rakeback' => 0.75],
        ['rank' => 5, 'minWager' => 500, 'rakeback' => 1.0],
        ['rank' => 6, 'minWager' => 1000, 'rakeback' => 1.25],
        ['rank' => 7, 'minWager' => 5000, 'rakeback' => 1.5],
        ['rank' => 8, 'minWager' => 10000, 'rakeback' => 2.0],
        ['rank' => 9, 'minWager' => 50000, 'rakeback' => 2.5]
    ];

    // Determine the new rank based on the updated total wager
    $newRank = 0;
    $rakebackRate = 0;
    foreach (array_reverse($rankThresholds) as $threshold) {
        if ($total_wager >= $threshold['minWager']) {
            $newRank = $threshold['rank'];
            $rakebackRate = $threshold['rakeback'];
            break;
        }
    }

    // Promote user if their new rank is higher than the current rank
    if ($newRank > $rank) {
        $stmt = $pdo->prepare("UPDATE raking SET rank = :rank WHERE user_id = :user_id");
        $stmt->execute(['rank' => $newRank, 'user_id' => $user_id]);

        $stmt = $pdo->prepare("UPDATE users SET rank = :rank WHERE id = :user_id");
        $stmt->execute(['rank' => $newRank, 'user_id' => $user_id]);

        $rank = $newRank;
    }

    // Calculate and update claimable rakeback
    $newRakeback = $bet_amount * ($rakebackRate / 100);
    $claimable_rakeback += $newRakeback;

    $stmt = $pdo->prepare("UPDATE raking SET claimable_rakeback = :claimable_rakeback WHERE user_id = :user_id");
    $stmt->execute(['claimable_rakeback' => $claimable_rakeback, 'user_id' => $user_id]);

    $response['success'] = true;
    $response['new_balance'] = $total_wager;
    $response['new_rank'] = $rank;
    $response['new_claimable_rakeback'] = $claimable_rakeback;
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

echo json_encode($response);
