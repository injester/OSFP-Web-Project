<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    $is_secret = isset($_POST['is_secret']) && $_POST['is_secret'] === '1'; // Secret announcement flag

    if ($message !== '') {
        try {
            // Handle secret announcements for automated wins
            if ($is_secret) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'Announcement' LIMIT 1");
                $stmt->execute();
                $announcementUser = $stmt->fetch();

                if (!$announcementUser) {
                    echo json_encode(['status' => 'error', 'message' => 'Announcement user not found in the database.']);
                    exit;
                }

                $announcementUserId = $announcementUser['id'];

                // Post the message as "Announcement"
                $stmt = $pdo->prepare("INSERT INTO messages (user_id, username, message) VALUES (:user_id, :username, :message)");
                $stmt->execute([
                    'user_id' => $announcementUserId,
                    'username' => 'Announcement',
                    'message' => $message
                ]);

                echo json_encode(['status' => 'success', 'message' => 'Secret announcement posted successfully.']);
                exit;
            }

            // Fetch user details for handling /post or regular messages
            $user_id = $_SESSION['user_id'];
            $stmt = $pdo->prepare("SELECT username, rank FROM users WHERE id = :user_id");
            $stmt->execute(['user_id' => $user_id]);
            $user = $stmt->fetch();

            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'User not found in the database.']);
                exit;
            }

            // Handle /post for admin users
            if (strpos($message, '/post') === 0) {
                if ($user['rank'] >= 10) {
                    $announcementMessage = trim(substr($message, 5)); // Extract message after /post

                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = 'Announcement' LIMIT 1");
                    $stmt->execute();
                    $announcementUser = $stmt->fetch();

                    if (!$announcementUser) {
                        echo json_encode(['status' => 'error', 'message' => 'Announcement user not found in the database.']);
                        exit;
                    }

                    // Insert the admin announcement as "Announcement"
                    $stmt = $pdo->prepare("INSERT INTO messages (user_id, username, message) VALUES (:user_id, :username, :message)");
                    $stmt->execute([
                        'user_id' => $announcementUser['id'],
                        'username' => 'Announcement',
                        'message' => $announcementMessage
                    ]);

                    echo json_encode(['status' => 'success', 'message' => 'Admin announcement posted successfully.']);
                    exit;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'You are not authorized to use /post.']);
                    exit;
                }
            }

            // Handle regular messages for non-admin users
            $stmt = $pdo->prepare("INSERT INTO messages (user_id, username, message) VALUES (:user_id, :username, :message)");
            $stmt->execute([
                'user_id' => $user_id,
                'username' => $user['username'],
                'message' => $message
            ]);

            echo json_encode(['status' => 'success', 'message' => 'Message posted successfully.']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']);
    }
}
