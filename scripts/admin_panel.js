document.addEventListener('DOMContentLoaded', function () {
    // Section toggles and buttons
    const revealUsersBtn = document.getElementById('reveal-users');
    const revealOrdersBtn = document.getElementById('reveal-orders');
    const usersSection = document.getElementById('users-section');
    const ordersSection = document.getElementById('orders-section');

    // Toggle between Users and Orders sections
    if (revealUsersBtn && revealOrdersBtn) {
    }

    // User Search Functionality
    const searchUsersInput = document.getElementById('search-users');
    const userTableRows = document.querySelectorAll('#user-table tr');

    if (searchUsersInput) {
        searchUsersInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            userTableRows.forEach(row => {
                const rowText = Array.from(row.cells).map(cell => cell.innerText.toLowerCase()).join(' ');
                row.style.display = rowText.includes(query) ? '' : 'none';
            });
        });
    } else {
        console.error("User search input not found.");
    }

    // Order Search Functionality
    const searchOrdersInput = document.getElementById('search-orders');
    const orderTableRows = document.querySelectorAll('#order-table tr');

    if (searchOrdersInput) {
        searchOrdersInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            orderTableRows.forEach(row => {
                const rowText = Array.from(row.cells).map(cell => cell.innerText.toLowerCase()).join(' ');
                row.style.display = rowText.includes(query) ? '' : 'none';
            });
        });
    } else {
        console.error("Order search input not found.");
    }

    // Edit User Modal and Form Handling
    const editButtons = document.querySelectorAll('.edit-user');
    const modal = document.getElementById('edit-user-modal');
    const closeModal = document.getElementById('close-modal');
    const editForm = document.getElementById('edit-user-form');

    if (editButtons.length > 0 && modal && closeModal && editForm) {
        // Open modal with prefilled data
        editButtons.forEach(button => {
            button.addEventListener('click', function () {
                const userId = this.getAttribute('data-user-id');
                const row = this.closest('tr');
                const username = row.querySelector('td:nth-child(2)').innerText;
                const email = row.querySelector('td:nth-child(3)').innerText;
                const rsn = row.querySelector('td:nth-child(4)').innerText;
                const balance = row.querySelector('td:nth-child(6)').innerText.replace('M', '').trim();

                // Fill form fields
                document.getElementById('edit-user-id').value = userId;
                document.getElementById('edit-username').value = username;
                document.getElementById('edit-email').value = email;
                document.getElementById('edit-rsn').value = rsn;
                document.getElementById('edit-balance').value = balance;

                modal.classList.remove('hidden'); // Show modal
            });
        });

        // Close modal on "Cancel"
        closeModal.addEventListener('click', function () {
            modal.classList.add('hidden');
        });

        // Submit form with AJAX
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(editForm);

            fetch('panel/edit_user.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('User updated successfully!');
                    modal.classList.add('hidden');
                    location.reload(); // Reload page to reflect changes
                } else {
                    alert('Failed to update user: ' + data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    } else {
        console.error("Required elements for the edit user functionality are missing.");
    }
});
