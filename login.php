<?php
require 'config.php';
session_start(); // Ensure the session is started

// Redirect to index if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from DB
    $stmt = $pdo->prepare('SELECT * FROM users WHERE username = :username OR email = :username');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['rank'] = $user['rank']; // Store rank in session

        // Set the user as online
        $stmt = $pdo->prepare("UPDATE users SET is_online = 1 WHERE id = :id");
        $stmt->execute(['id' => $user['id']]);

        // Redirect to the homepage
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<?php include('header.php'); ?>

<!-- Login Page with Blurred Background and Vignette -->
<div class="min-h-screen flex items-center justify-center relative bg-black bg-opacity-70">
    <!-- Background Image with Blur Effect -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter blur-2xl" style="background-image: url('assets/login.webp');"></div>

    <!-- Vignette Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black opacity-80"></div>

    <!-- Login Form Container -->
    <div class="relative bg-gray-800 p-8 rounded-lg z-10 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Login to OSFP</h2>

        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-400 font-semibold mb-1">Username or E-mail:</label>
                <input type="text" name="username" placeholder="Username or Email" class="w-full p-2 bg-gray-700 text-white rounded" required>
            </div>
            <div>
                <label class="block text-gray-400 font-semibold mb-1">Password:</label>
                <input type="password" name="password" placeholder="Password" class="w-full p-2 bg-gray-700 text-white rounded" required>
            </div>

            <button type="submit" class="w-full bg-blue-500 py-2 rounded hover:bg-blue-600">Login</button>
        </form>

        <p class="mt-4 text-white text-center">New user? <a href="register.php" class="text-yellow-400 hover:underline">Create an account</a></p>
    </div>
</div>
</body>
</html>
