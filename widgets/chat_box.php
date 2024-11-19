<div class="chat-container bg-gray-900 p-4 rounded-lg">
    <h2 class="text-2xl font-bold text-white mb-4">Order Chat</h2>

    <div class="chat-box bg-gray-800 p-4 rounded-lg mb-4 overflow-y-auto" id="chat-box" style="max-height: 400px;">
        <!-- Pinned Admin message -->
        <?php if (!empty($adminMessage)): ?>
            <div class="pinned-message bg-yellow-500 text-black p-2 rounded-lg mb-4">
                <strong>OSFP Admin:</strong> <?php echo $adminMessage; ?>
                <small class="block text-gray-600"><?php echo date('M d, Y, h:i A'); ?></small>
            </div>
        <?php endif; ?>

        <!-- Display the chat messages -->
        <?php foreach ($messages as $message): ?>
            <?php 
                $isAdmin = $message['sender_id'] != $_SESSION['user_id'];
                $bubbleClass = $isAdmin ? 'admin-message bg-blue-500' : 'user-message bg-green-500';
                $senderName = $isAdmin ? 'OSFP Admin' : 'You';
            ?>
            <div class="chat-message mb-2 <?php echo $bubbleClass; ?> p-2 rounded-lg max-w-xs <?php echo $isAdmin ? 'ml-auto' : 'mr-auto'; ?>">
                <strong><?php echo $senderName; ?>:</strong> 
                <?php echo htmlspecialchars($message['message']); ?> 
                <small class="block text-gray-300"><?php echo timeAgo($message['timestamp']); ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Form to send a new message via AJAX -->
    <form id="send-message-form" class="flex space-x-2">
        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
        <input type="text" name="message" placeholder="Say something..." autocomplete="off" class="flex-grow p-2 rounded-lg bg-gray-700 text-white" required>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Send</button>
    </form>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const sendMessageForm = document.getElementById('send-message-form');
    const messageInput = sendMessageForm.querySelector('input[name="message"]');
    const chatBox = document.getElementById('chat-box');

    sendMessageForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(sendMessageForm);

        // Clear the input field and disable the form to prevent duplicate submissions
        messageInput.value = ''; 
        messageInput.disabled = true; 

        fetch('widgets/send_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            chatBox.innerHTML += data; // Add new message to the chat
            messageInput.disabled = false; // Re-enable input field
            messageInput.focus(); // Focus input field for further messages
        })
        .catch(error => {
            console.error('Error:', error);
            messageInput.disabled = false; // Re-enable input field on error
        });
    });

    // Automatically refresh the chat every few seconds to keep it dynamic
    setInterval(function() {
        const order_id = "<?php echo $order_id; ?>";
        fetch(`widgets/load_chat.php?order_id=${order_id}`)
        .then(response => response.text())
        .then(data => {
            chatBox.innerHTML = data; // Update the chat dynamically
        });
    }, 500); // Refresh every 5 seconds
});
