<?php
// Fetch all users from the database
$users = [];
try {
    $stmt = $pdo->query("SELECT id, username, email, rsn, rank, balance, created_at, is_online FROM users ORDER BY created_at ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    error_log("Failed to fetch users: " . $e->getMessage());
}
?>

<div class="bg-gray-900 p-6 rounded-lg shadow-md w-full">
    <h2 class="text-2xl font-bold text-white mb-4">Manage Users</h2>
    <input type="text" id="search-users" class="w-full p-2 mb-4 rounded bg-gray-700 text-white" placeholder="Search by User ID, Username, Email, RSN, or Balance">

    <table class="min-w-full bg-gray-800 text-white text-center">
        <thead>
            <tr class="bg-gray-700">
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>RSN</th>
                <th>Rank</th>
                <th>Balance</th>
                <th>Created At</th>
                <th>Status</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody id="user-table">
            <?php foreach ($users as $index => $user): ?>
                <tr class="<?php echo $index % 2 === 0 ? 'bg-gray-700' : 'bg-gray-800'; ?>">
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['rsn']; ?></td>
                    <td><?php echo $user['rank']; ?></td>
                    <td><?php echo number_format($user['balance'], 2); ?>M</td>
                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                    <td>
                        <span class="<?php echo $user['is_online'] ? 'text-green-500' : 'text-red-500'; ?>">
                            <?php echo $user['is_online'] ? 'Online' : 'Offline'; ?>
                        </span>
                    </td>
                    <td>
                        <button class="edit-user" data-user-id="<?php echo $user['id']; ?>">
                            <img src="assets/edit.png" alt="Edit" width="24" height="24">
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Edit User Modal -->
<div id="edit-user-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-gray-800 p-8 rounded-lg">
        <h2 class="text-2xl font-bold mb-4">Edit User</h2>
        <form id="edit-user-form">
            <input type="hidden" id="edit-user-id" name="user_id">
            <div class="mb-4">
                <label for="edit-username" class="block text-white">Username:</label>
                <input type="text" id="edit-username" name="username" class="w-full p-2 rounded bg-gray-700 text-white" required>
            </div>
            <div class="mb-4">
                <label for="edit-email" class="block text-white">Email:</label>
                <input type="email" id="edit-email" name="email" class="w-full p-2 rounded bg-gray-700 text-white" required>
            </div>
            <div class="mb-4">
                <label for="edit-rsn" class="block text-white">RSN:</label>
                <input type="text" id="edit-rsn" name="rsn" class="w-full p-2 rounded bg-gray-700 text-white" required>
            </div>
            <div class="mb-4">
                <label for="edit-balance" class="block text-white">Balance (M):</label>
                <input type="number" id="edit-balance" name="balance" class="w-full p-2 rounded bg-gray-700 text-white" required>
            </div>
            <div class="flex justify-end">
                <button type="button" id="close-modal" class="mr-4 bg-red-500 text-white px-4 py-2 rounded">Cancel</button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Search and Edit Functionality Script
    document.addEventListener('DOMContentLoaded', function () {
        // Search functionality for users
        document.getElementById('search-users').addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll('#user-table tr');
            rows.forEach(row => {
                const rowText = Array.from(row.cells).map(cell => cell.innerText.toLowerCase()).join(' ');
                row.style.display = rowText.includes(query) ? '' : 'none';
            });
        });

        // Edit User Modal Handling
        const editButtons = document.querySelectorAll('.edit-user');
        const modal = document.getElementById('edit-user-modal');
        const closeModal = document.getElementById('close-modal');
        const editForm = document.getElementById('edit-user-form');

        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-user-id');
                const row = this.closest('tr');
                document.getElementById('edit-user-id').value = userId;
                document.getElementById('edit-username').value = row.cells[1].innerText;
                document.getElementById('edit-email').value = row.cells[2].innerText;
                document.getElementById('edit-rsn').value = row.cells[3].innerText;
                document.getElementById('edit-balance').value = row.cells[5].innerText.replace('M', '').trim();
                modal.classList.remove('hidden');
            });
        });

        // Close modal
        closeModal.addEventListener('click', function () {
            modal.classList.add('hidden');
        });

        // AJAX Form submission for updating user
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(editForm);
            fetch('panel/edit_user.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User updated successfully!');
                    modal.classList.add('hidden');
                    location.reload(); // Reload the page to reflect the changes
                } else {
                    alert('Failed to update user: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
</script>
