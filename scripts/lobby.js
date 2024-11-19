document.addEventListener('DOMContentLoaded', function () {
    const messageForm = document.getElementById('send-message-form');
    const messageInput = document.getElementById('message-input');
    const chatMessages = document.getElementById('chat-messages');
    let userIsScrolling = false;

    // Load messages and auto-scroll when a new message is added
    async function loadMessages() {
        try {
            const response = await fetch('load_chat.php');
            const messages = await response.text();
            chatMessages.innerHTML = messages;

            // Auto-scroll to the bottom if user is not manually scrolling
            if (!userIsScrolling) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        } catch (error) {
            console.error("Error loading messages:", error);
        }
    }

    // Monitor if user is scrolling
    chatMessages.addEventListener('scroll', () => {
        userIsScrolling = chatMessages.scrollTop + chatMessages.clientHeight < chatMessages.scrollHeight;
    });

    // Handle message submission
    messageForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (message === '') return;

        try {
            const response = await fetch('lobby.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ message })
            });
            const result = await response.json();
            if (result.status === 'success') {
                messageInput.value = ''; // Clear input field after sending
                loadMessages();  // Reload messages after sending
            } else {
                console.error(result.message);
            }
        } catch (error) {
            console.error("Error sending message:", error);
        }
    });

    // Load messages on initial load and scroll to the bottom
    loadMessages();
    setInterval(loadMessages, 1000);
});
