<?php
session_start();
require 'config.php';

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Initialize user data variables to avoid warnings
$balance = 0;
$username = '';
$rsn = '';
$email = '';

// Fetch user data if logged in
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();

    // Ensure data is set to avoid undefined warnings
    if ($user) {
        $balance = isset($user['balance']) ? number_format($user['balance'], 2) : '0.00';
        $username = isset($user['username']) ? htmlspecialchars($user['username']) : '';
        $rsn = isset($user['rsn']) ? htmlspecialchars($user['rsn']) : '';
        $email = isset($user['email']) ? htmlspecialchars($user['email']) : '';
    }
}
?>

<?php include('header.php'); ?>

<!-- Profile Settings Page with Blurred Background and Vignette -->
<div class="min-h-screen flex items-center justify-center relative bg-black bg-opacity-70">
    <!-- Background Image with Blur Effect -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter blur-2xl" style="background-image: url('assets/login.webp');"></div>

    <!-- Vignette Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black opacity-80"></div>

    <!-- Profile Settings Form Container -->
    <div class="relative bg-gray-800 p-8 rounded-lg z-10 w-full max-w-lg">
        <h2 class="text-2xl font-bold mb-6 text-center">Profile Settings</h2>

        <form id="profile-form" class="space-y-4">
            <div>
                <label class="block text-gray-400 font-semibold mb-1">Balance:</label>
                <p class="bg-gray-700 p-2 rounded text-green-500"><?php echo $balance; ?>M</p>
            </div>
            <div>
                <label class="block text-gray-400 font-semibold mb-1">Username:</label>
                <input type="text" name="username" value="<?php echo $username; ?>" class="w-full p-2 bg-gray-700 text-white rounded" disabled>
            </div>
            <div>
            <label class="block text-gray-400 mb-1">RSN <span class="italic text-gray-200 text-sm">(in-game username)</span>:</label>
                <div class="relative">
                    <input type="text" name="rsn" value="<?php echo $rsn; ?>" class="w-full p-2 bg-gray-700 text-white rounded pr-10" disabled>
                    <img src="assets/edit.png" alt="Edit" class="absolute top-2 right-2 w-6 h-6 cursor-pointer" onclick="toggleEdit('rsn')">
                </div>
            </div>
            <div>
                <label class="block text-gray-400 font-semibold mb-1">E-mail:</label>
                <div class="relative">
                    <input type="email" name="email" value="<?php echo $email; ?>" class="w-full p-2 bg-gray-700 text-white rounded pr-10" disabled>
                    <img src="assets/edit.png" alt="Edit" class="absolute top-2 right-2 w-6 h-6 cursor-pointer" onclick="toggleEdit('email')">
                </div>
            </div>

            <button type="submit" class="w-full bg-yellow-500 py-2 rounded hover:bg-yellow-600 font-bold">SAVE PROFILE</button>
        </form>

        <!-- Divider -->
        <hr class="my-4 border-gray-600">

        <h3 class="text-xl font-bold mb-4 text-center">Change Password</h3>
        <form id="password-form">
            <input type="password" name="old_password" placeholder="Old Password" class="w-full p-2 bg-gray-700 text-white rounded mb-3">
            <input type="password" name="new_password" placeholder="New Password" class="w-full p-2 bg-gray-700 text-white rounded mb-3">
            <input type="password" name="confirm_password" placeholder="Confirm New Password" class="w-full p-2 bg-gray-700 text-white rounded mb-4">

            <button type="submit" class="w-full bg-blue-500 py-2 rounded hover:bg-blue-600 font-bold">SAVE PASSWORD</button>
        </form>
    </div>
</div>

<script>
    function toggleEdit(field) {
        const input = document.querySelector(`input[name="${field}"]`);
        if (input.disabled) {
            input.disabled = false;
            input.focus();
        } else {
            input.disabled = true;
        }
    }

    // Separate form submissions
    document.querySelector('#profile-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData();

        // Only add fields to formData if they are enabled (edited)
        const rsnInput = document.querySelector('input[name="rsn"]');
        if (!rsnInput.disabled) {
            formData.append('rsn', rsnInput.value);
        }

        const emailInput = document.querySelector('input[name="email"]');
        if (!emailInput.disabled) {
            formData.append('email', emailInput.value);
        }

        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload(); // Refresh the page on success
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('An error occurred. Please try again.');
        });
    });

    document.querySelector('#password-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                location.reload(); // Refresh the page on success
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('An error occurred. Please try again.');
        });
    });
</script>
</body>
</html>

<?php include('footer.php'); ?>