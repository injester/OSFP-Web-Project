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
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $rsn = $_POST['rsn'];

    // Check if the password length is at least 6 characters
    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } 
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } 
    else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email already exists
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        if ($stmt->rowCount() > 0) {
            $error = "The email is already in use.";
        } else {
            // Insert new user
            $stmt = $pdo->prepare('INSERT INTO users (username, email, password, rsn) VALUES (?, ?, ?, ?)');
            if ($stmt->execute([$username, $email, $hashed_password, $rsn])) {
                header("Location: login.php");
                exit;
            } else {
                $error = "Registration failed!";
            }
        }
    }
}
?>

<?php include('header.php'); ?>

<!-- Registration Page with Blurred Background and Vignette -->
<div class="min-h-screen flex items-center justify-center relative bg-black bg-opacity-70">
    <!-- Background Image with Blur Effect -->
    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat filter blur-2xl" style="background-image: url('assets/login.webp');"></div>

    <!-- Vignette Overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-black opacity-80"></div>

    <!-- Registration Form Container -->
    <div class="relative bg-gray-800 p-8 rounded-lg z-10 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center">Register for OSFP</h2>

        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-400 font-semibold mb-1">Username:</label>
                <input type="text" name="username" placeholder="Username" class="w-full p-2 bg-gray-700 text-white rounded" required>
            </div>
            <div>
                <label class="block text-gray-400 font-semibold mb-1">E-mail:</label>
                <input type="email" name="email" placeholder="Email" class="w-full p-2 bg-gray-700 text-white rounded" required>
            </div>
            <div>
                <label class="block text-gray-400 font-semibold mb-1">RSN <span class="italic text-gray-200 text-sm">(in-game username)</span>:</label>
                <input type="text" name="rsn" placeholder="RuneScape Name (RSN)" class="w-full p-2 bg-gray-700 text-white rounded" required>
            </div>
            <div>
                <label class="block text-gray-400 font-semibold mb-1">Password:</label>
                <input type="password" name="password" placeholder="Password" class="w-full p-2 bg-gray-700 text-white rounded" required>
            </div>
            <div>
                <label class="block text-gray-400 font-semibold mb-1">Confirm Password:</label>
                <input type="password" name="confirm_password" placeholder="Confirm Password" class="w-full p-2 bg-gray-700 text-white rounded" required>
            </div>

            <button type="submit" class="w-full bg-blue-500 py-2 rounded hover:bg-blue-600">Register</button>
        </form>

        <p class="mt-4 text-white text-center">Already have an account? <a href="login.php" class="text-yellow-400 hover:underline">Log in here</a></p>
    </div>
</div>
</body>
</html>
