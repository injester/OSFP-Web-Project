<?php
session_start();
require 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize response
$response = [
    'success' => false,
    'message' => 'An error occurred while updating the profile.',
];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];

    // Update RSN or Email if provided
    if (isset($_POST['rsn']) || isset($_POST['email'])) {
        $updateData = [];
        
        if (isset($_POST['rsn'])) {
            $updateData['rsn'] = trim($_POST['rsn']);
        }

        if (isset($_POST['email'])) {
            $email = trim($_POST['email']);
            
            // Check if email is already in use by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
            $stmt->execute(['email' => $email, 'id' => $userId]);
            if ($stmt->rowCount() > 0) {
                $response['message'] = 'The email address is already in use.';
                echo json_encode($response);
                exit;
            }

            $updateData['email'] = $email;
        }

        // Update RSN and/or Email if needed
        if (!empty($updateData)) {
            $setClause = [];
            foreach ($updateData as $key => $value) {
                $setClause[] = "$key = :$key";
            }
            $query = "UPDATE users SET " . implode(", ", $setClause) . " WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $updateData['id'] = $userId; // Add ID for where clause
            $stmt->execute($updateData);

            $response['success'] = true;
            $response['message'] = 'Profile updated successfully!';
        }
    }

    // Handle password change separately
    if (!empty($_POST['old_password']) && !empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $oldPassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($newPassword !== $confirmPassword) {
            $response['message'] = 'New password and confirmation do not match.';
            echo json_encode($response);
            exit;
        }

        if (strlen($newPassword) < 6) {
            $response['message'] = 'New password must be at least 6 characters.';
            echo json_encode($response);
            exit;
        }

        // Verify old password
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        $user = $stmt->fetch();

        if ($user && password_verify($oldPassword, $user['password'])) {
            // Update the password
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute(['password' => $hashedPassword, 'id' => $userId]);

            $response['success'] = true;
            $response['message'] = 'Password updated successfully!';
        } else {
            $response['message'] = 'Incorrect old password.';
        }
    }

    echo json_encode($response);
}
?>
