document.addEventListener('DOMContentLoaded', function () {
    const sendMessageForm = document.getElementById('send-message-form');
    const messageInput = sendMessageForm ? sendMessageForm.querySelector('input[name="message"]') : null;
    const chatBox = document.getElementById('chat-box');

    // Only add event listeners if elements exist
    if (sendMessageForm && messageInput) {
        sendMessageForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(sendMessageForm);
            messageInput.value = ''; // Clear input field
            messageInput.disabled = true; // Prevent further inputs

            fetch('widgets/send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                chatBox.innerHTML += data; // Append the new message
                messageInput.disabled = false; // Re-enable input field
                messageInput.focus(); // Focus input field for new message
            })
            .catch(error => {
                console.error('Error:', error);
                messageInput.disabled = false;
            });
        });
    }

    function loadChatMessages() {
        const orderId = sendMessageForm ? sendMessageForm.querySelector('input[name="order_id"]').value : null;

        if (orderId) {
            fetch(`widgets/load_chat.php?order_id=${orderId}`)
            .then(response => response.text())
            .then(data => {
                chatBox.innerHTML = data;
            })
            .catch(error => console.error('Error loading chat messages:', error));
        }
    }

    // Set an interval to refresh chat messages
    setInterval(loadChatMessages, 500); // Adjust refresh time as needed
    loadChatMessages();
});
