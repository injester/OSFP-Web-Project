<?php
require '../config.php'; // Ensure database connection

// Check if the request is coming via POST and with valid data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data from the request
    $data = json_decode(file_get_contents('php://input'), true);

    // Ensure that user ID and amount are present in the request
    if (isset($data['user_id']) && isset($data['amount'])) {
        $user_id = $data['user_id'];
        $amount = $data['amount']; // Amount in Mills (OSRS GP)

        // Update user balance
        $stmt = $pdo->prepare("UPDATE users SET balance = balance + :amount WHERE id = :user_id");
        $stmt->execute([
            ':amount' => $amount,
            ':user_id' => $user_id
        ]);

        // Add a record to the order history
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, category, amount, status, created_at) VALUES (:user_id, 'Buy', :amount, 'Completed', NOW())");
        $stmt->execute([
            ':user_id' => $user_id,
            ':amount' => $amount
        ]);

        // Respond with success
        echo json_encode(['success' => true]);
    } else {
        // Respond with an error if required data is missing
        echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    }
} else {
    // Respond with an error if the request method is not POST
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
