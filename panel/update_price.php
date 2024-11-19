<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['rank'] < 10) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require '../config.php'; // Include database connection

// Get the new price from POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['price_per_million']) && is_numeric($_POST['price_per_million'])) {
    $new_price = $_POST['price_per_million'];

    try {
        // Update price in the database
        $stmt = $pdo->prepare("UPDATE price SET price_per_million = :new_price WHERE id = 1");
        if ($stmt->execute(['new_price' => $new_price])) {
            echo json_encode(['success' => true, 'message' => 'Price updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update price in the database.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request or price value']);
}
