<?php
// Database connection
include '../conn.php';

// Start session
session_start();

// Ensure only logged-in users can access this page (optional)
if (!isset($_SESSION['userID'])) {
    header('Location: ../login.php');
    exit();
}

// Handle form submission for adding or editing user profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = isset($_POST['userID']) ? intval($_POST['userID']) : null;
    $username = $_POST['username'] ?? '';
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : ''; // Password hashing
    $role = 'Organizer'; // Set the role to "organizer" for all users

    if ($userID) {
        // Update user profile
        $sql = "UPDATE users SET username = ?, name = ?, address = ?, email = ?, phone = ?, password = ?, role = ? WHERE userID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssi", $username, $name, $address, $email, $phone, $password, $role, $userID);
        $stmt->execute();
        $message = $stmt->affected_rows > 0 ? "Profile updated successfully." : "No changes were made.";
    } else {
        // Add new user profile
        $sql = "INSERT INTO users (username, name, address, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $username, $name, $address, $email, $phone, $password, $role);
        $stmt->execute();
        $message = $stmt->affected_rows > 0 ? "Profile added successfully." : "Failed to add profile.";
    }
}

// Handle user profile deletion
if (isset($_GET['delete'])) {
    $userID = intval($_GET['delete']);
    $sql = "DELETE FROM users WHERE userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $message = $stmt->affected_rows > 0 ? "Profile deleted successfully." : "Failed to delete profile.";
}

// Fetch all users with the role of 'organizer'
$sql = "SELECT userID, username, name, address, email, phone FROM users WHERE role = 'organizer'";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Profiles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage Admin Profiles</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add Profile Button -->
    <button class="btn btn-secondary mb-3" id="toggleFormButton">Add New Admin</button>

    <!-- Add or Edit Profile Form -->
    <div class="card mb-4" id="profileForm" style="display: none;">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="userID" id="userID">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary" id="submitButton">Add Admin</button>
            </form>
        </div>
    </div>

    <!-- User Profiles Table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Name</th>
            <th>Address</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['userID']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['name']) ?></td>
                <td><?= htmlspecialchars($user['address']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= htmlspecialchars($user['phone']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning edit-button"
                            data-id="<?= $user['userID'] ?>"
                            data-username="<?= htmlspecialchars($user['username']) ?>"
                            data-name="<?= htmlspecialchars($user['name']) ?>"
                            data-address="<?= htmlspecialchars($user['address']) ?>"
                            data-email="<?= htmlspecialchars($user['email']) ?>"
                            data-phone="<?= htmlspecialchars($user['phone']) ?>">Edit</button>
                        <a href="?page=organizer&delete=<?= $user['userID'] ?>" class="btn btn-sm btn-danger"
                        onclick="return confirm('Are you sure you want to delete this profile?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Toggle form visibility
    const toggleButton = document.getElementById('toggleFormButton');
    const profileForm = document.getElementById('profileForm');

    toggleButton.addEventListener('click', () => {
        if (profileForm.style.display === 'none') {
            profileForm.style.display = 'block';
            toggleButton.style.display = 'none'; // Hide the button when form is visible
        } else {
            profileForm.style.display = 'none';
            toggleButton.style.display = 'block'; // Show the button when form is hidden
        }
    });

    // Populate the form with user data for editing
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', () => {
            profileForm.style.display = 'block'; // Ensure form is visible when editing
            toggleButton.style.display = 'none'; // Hide the button when editing
            document.getElementById('userID').value = button.dataset.id;
            document.getElementById('username').value = button.dataset.username;
            document.getElementById('name').value = button.dataset.name;
            document.getElementById('address').value = button.dataset.address;
            document.getElementById('email').value = button.dataset.email;
            document.getElementById('phone').value = button.dataset.phone;
            document.getElementById('submitButton').textContent = 'Update Profile';
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
