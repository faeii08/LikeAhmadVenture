<?php
// Database connection
include 'conn.php';

// Start session
session_start();

// Initialize error message
$errorMsg = "";

// Handle login request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userName = trim($_POST['userName']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($userName) || empty($password)) {
        $errorMsg = "Username or password cannot be empty.";
    } else {
        // Query to fetch user details
        $sql = "SELECT * FROM users WHERE userName = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $row['password'])) {
                // Set session variables
                $_SESSION['userID'] = $row['userID'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['role'] = $row['role'];

                // Redirect based on role
                if ($row['role'] === 'Organizer') {
                    header("Location: organizer/sidebar.php");
                } elseif ($row['role'] === 'Participant') {
                    header("Location: participant/eventPage.php");
                } else {
                    $errorMsg = "Invalid role.";
                }
                exit();
            } else {
                $errorMsg = "Incorrect password.";
            }
        } else {
            $errorMsg = "User not found.";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Like Ahmad Venture</title>
    <link rel="stylesheet" href="styles.css"> <!-- External CSS for cleaner code -->
</head>
<style>
    /* General Page Styling */
    body {
        font-family: 'Lora', 'Open Sans', sans-serif;
        background: linear-gradient(to right, #2C3E50, #4E5D6C);
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        flex-direction: column;
    }

    /* Login Form Styling */
    form {
        background: #FFFFFF;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0px 5px 10px rgba(0, 0, 0, 0.15);
        width: 100%;
        max-width: 380px;
        text-align: center;
        /* border-top: 5px solid #578FCA; */
    }

    h2 {
        text-align: center;
        color: #333;
        font-weight: bold;
        font-size: 22px;
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        color: #333;
        font-weight: bold;
        text-align: left;
    }

    input, button {
        padding: 12px; /* Increase padding for better appearance */
        font-size: 16px; /* Improve readability */
    }

    input {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #BDC3C7;
        border-radius: 5px;
        box-sizing: border-box;
    }

    /* Button Styling */
    button {
        width: 100%;
        padding: 10px;
        background-color: #578FCA;
        border: none;
        border-radius: 5px;
        color: white;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: 0.3s;
    }

    button:hover {
        background-color: #3674B5;
    }

    /* Error Message Styling */
    .error {
        color: red;
        font-size: 14px;
        margin-bottom: 10px;
        background: #FFD2D2;
        padding: 8px;
        border-radius: 5px;
    }

    /* Register Link Styling */
    .register-link {
        text-align: center;
        margin-top: 15px;
    }

    .register-link a {
        text-decoration: none;
        color: #007bff;
        font-weight: bold;
    }

    .register-link a:hover {
        text-decoration: underline;
    }

</style>
<body>
    <form method="POST">
        <h2>Login</h2>

        <?php if (!empty($errorMsg)): ?>
            <div class="error"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <label for="userName">Username:</label>
        <input type="text" id="userName" name="userName" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>

        <div class="register-link">
            <p>Not registered? <a href="participant/register.php">Register here</a></p>
        </div>
    </form>

</body>
</html>
