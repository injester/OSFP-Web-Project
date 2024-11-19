<?php
require 'config.php';

try {
    $stmt = $pdo->query("SELECT messages.username, messages.message, messages.created_at, users.rank 
                         FROM messages 
                         JOIN users ON messages.user_id = users.id 
                         ORDER BY messages.created_at ASC 
                         LIMIT 50");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($messages as $message) {
        if ($message['rank'] >= 100) {
            // Display special announcement message without a username
            echo "<div class='win-message'>
                          🔔               {$message['message']}  🔔
                  </div>";
        } else {
            // Display regular user message
            $rankColor = "rank-color-{$message['rank']}";
            echo "<div class='chat-message bg-gray-700 chat-message-container'>
                    <img src='assets/ranks/{$message['rank']}.png' alt='Rank Icon' class='chat-icon'>
                    <span class='username {$rankColor}'>{$message['username']}:</span>
                    <span class='message-content'>{$message['message']}</span>
                  </div>";
        }
    }
} catch (PDOException $e) {
    echo "<p>Error loading messages</p>";
}
?>
