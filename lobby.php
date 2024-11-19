<div id="chat-widget" class="main-chat-container fixed top-[80px] right-[-385px] z-50 transition-all duration-300 rounded-lg">
    <div class="text-lg font-bold text-white mb-4">Chat</div>
    <div id="chat-messages" class="messages-container flex-grow bg-[#111827] rounded-lg p-4"></div>
    <form id="send-message-form" autocomplete="off" class="flex">
        <input type="text" id="message-input" placeholder="Type here..." class="bg-gray-700 text-white p-2 rounded-l w-full" required>
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-r">Send</button>
    </form>
    <button id="toggle-chat" class="absolute left-[-30px] top-1/2 transform -translate-y-1/2 bg-[#1f2937] w-8 h-24 rounded-r-lg">
        <img src="assets/left.png" alt="Toggle Chat" class="w-6 h-12 mx-auto">
    </button>
</div>

<script>
const chatWidget = document.getElementById('chat-widget');
const toggleChat = document.getElementById('toggle-chat');
const messageInput = document.getElementById('message-input');
const sendMessageForm = document.getElementById('send-message-form');
const chatMessages = document.getElementById('chat-messages');
let isOpen = false;
const toggleDelay = 200; // adjust delay here (in milliseconds)

// Toggle chat visibility by moving it in and out of view
toggleChat.addEventListener('click', () => {
    isOpen = !isOpen;
    chatWidget.style.right = isOpen ? '0px' : '-385px';
    toggleChat.querySelector('img').src = isOpen ? 'assets/right.png' : 'assets/left.png';
});

// Prevent page refresh on form submit and update messages dynamically
sendMessageForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const message = messageInput.value.trim();
    if (message === '') return;

    try {
        const response = await fetch('send_message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({ message })
        });

        const result = await response.json();
        if (result.status === 'success') {
            messageInput.value = ''; // Clear input
            loadMessages(); // Reload messages without refreshing the page
        } else {
            alert('Failed to send message: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred.');
    }
});

// Load messages and scroll to the bottom with a delay to ensure full scroll
async function loadMessages() {
    try {
        const response = await fetch('load_messages.php');
        const messages = await response.text();
        chatMessages.innerHTML = messages;
        setTimeout(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 50); // adjust delay here if needed
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

// Refresh messages every 3 seconds
setInterval(loadMessages, 1000);

// Initial load of messages and ensure scroll to bottom on page load
window.addEventListener('load', loadMessages);
</script>

<style>
.main-chat-container {
    padding: 20px;
    border-radius: 12px;
    background-color: #1f2937;
    height: calc(100vh - 160px);
    width: 400px;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease-in-out;
    position: fixed;
    right: -385px;
}

.messages-container {
    max-height: 75vh;
    background-color: #111827;
    border-radius: 8px;
    padding: 16px 10px;
    overflow-y: auto;
}
.win-message {
    background-color: orange;
    color: white;
    font-weight: bold;
    padding: 8px 12px;
    border-radius: 8px;
    display: inline-block;
    text-align: center;
    animation: glow 500s infinite alternate, breathing 60s infinite alternate;
}

@keyframes glow {
    from {
        box-shadow: 0 0 5px orange, 0 0 8px orange, 0 0 10px orange;
    }
    to {
        box-shadow: 0 0 7px orange, 0 0 10px orange, 0 0 12px orange;
    }
}

@keyframes breathing {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    99% { transform: scale(1.00001); }
    100% { transform: scale(1); }
}




/* Chat bubble styling with spacing and border radius */
.messages-container > div {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
    padding: 8px;
    background-color: #1f2937;
    border-radius: 10px;
    word-break: break-word;
    overflow-wrap: break-word;
}

.message-content {
    margin-left: 8px;
    word-break: break-word;
    overflow-wrap: break-word;
}

/* Hide scrollbars on all platforms */
.messages-container {
    scrollbar-width: none;
}

.messages-container::-webkit-scrollbar {
    display: none;
}

/* Rank-specific colors */
.rank-color-0 { color: gray; }
.rank-color-1 { color: white; }
.rank-color-2 { color: green; }
.rank-color-3 { color: lime; }
.rank-color-4 { color: blue; }
.rank-color-5 { color: cyan; }
.rank-color-6 { color: orange; }
.rank-color-7 { color: pink; }
.rank-color-8 { color: red; }



/* Keyframes animation definition with improved timing */
@keyframes rainbow-breathe {
    0% { color: #1E90FF; }  /* Blue */
    20% { color: #8A2BE2; }  /* Purple */
    40% { color: #FF6347; }  /* Tomato (Red) */
    60% { color: #FFD700; }  /* Gold (Yellow) */
    80% { color: #1E90FF; } /* Blue */

}

/* Breathing animation for Rank 9 (alternating colors) */
.rank-color-9 {
    color: blue;
    animation: rainbow-breathe 3s infinite;
}

/* Glowy gold effect for Rank 10 */
.rank-color-10 {
    color: gold;
    text-shadow: 0 0 5px gold, 0 0 10px gold;
    font-weight: bold;
}

.username {
    font-weight: bold;
    margin-left: 6px;
}

#send-message-form {
    display: flex;
    gap: 4px;
}

#message-input {
    width: 100%;
}

/* Toggle button styling */
#toggle-chat {
    cursor: pointer;
    position: absolute;
    left: -30px;
    width: 32px; /* Adjusted width */
    height: 96px; /* Adjusted height */
    background-color: #1f2937;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px 0 0 8px;
}
</style>
