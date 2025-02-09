<?php
// Start session and include database connection
session_start();
include_once('../conn.php');

// Get the logged-in user's ID (assuming the user is logged in and userID is stored in the session)
$userID = $_SESSION['userID'] ?? null;

if (!$userID) {
    // Redirect to login page if no user is logged in
    header("Location: login.php");
    exit();
}

// Fetch the user's profile data from the database
$sql = "SELECT * FROM users WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // Redirect if no user data is found
    header("Location: login.php");
    exit();
}

// Handle profile update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $userName = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';

    // Optionally hash the password before storing it
    if ($password) {
        $password = password_hash($password, PASSWORD_DEFAULT);
    }

    // Update the user's profile information
    $sql = "UPDATE users SET name = ?, username = ?, address = ?, phone = ?, password = ?, email = ? WHERE userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $name, $userName, $address, $phone, $password, $email, $userID);
    $stmt->execute();

    $message = $stmt->affected_rows > 0 ? "Profile updated successfully." : "No changes made.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">My Profile</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Edit Button to toggle form fields -->
    <button id="editButton" class="btn btn-warning mb-3">Edit Profile</button>

    <form method="POST" id="profileForm">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required readonly>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" required readonly>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" required readonly>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required readonly>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Phone Number</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required readonly>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password (leave blank to keep unchanged)</label>
            <input type="password" class="form-control" id="password" name="password" readonly>
        </div>

        <button type="submit" class="btn btn-primary" id="updateButton" disabled>Update Profile</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Toggle form fields between readonly and editable
    document.getElementById('editButton').addEventListener('click', function() {
        const formElements = document.querySelectorAll('#profileForm input');
        const updateButton = document.getElementById('updateButton');

        formElements.forEach(input => {
            input.readOnly = !input.readOnly;  // Toggle readonly attribute
        });

        // Enable or disable the submit button based on readonly state
        if (updateButton.disabled) {
            updateButton.disabled = false;
            this.textContent = "Cancel Edit";  // Change button text to "Cancel Edit"
        } else {
            updateButton.disabled = true;
            this.textContent = "Edit Profile";  // Change button text back to "Edit Profile"
        }
    });
</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
