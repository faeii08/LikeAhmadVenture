<?php
// Database connection
include '../conn.php';

// Start session
session_start();

// Initialize variables for error messages
$usernameError = $emailError = $passwordError = $nameError = $addressError = $phoneError = "";
$successMessage = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input values
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $phone = $_POST['phone'];

    // Collect Participants Data
    $participants = $_POST['pName'];  // Array of participant names
    $ages = $_POST['age'];            // Array of participant ages

    // Define role
    $role = "Participant";

    // Validate form data
    if (empty($username)) {
        $usernameError = "Username is required.";
    } else if (empty($email)) {
        $emailError = "Email is required.";
    } else if (empty($password)) {
        $passwordError = "Password is required.";
    } else if (empty($name)) {
        $nameError = "Name is required.";
    } else if (empty($address)) {
        $addressError = "Address is required.";
    } else if (empty($phone)) {
        $phoneError = "Phone is required.";
    }

    // Check if there are no errors
    if (empty($usernameError) && empty($emailError) && empty($passwordError) && empty($nameError) && empty($addressError) && empty($phoneError)) {
        // Check if the email already exists
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $emailError = "Email is already registered.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert the new user into the database
            $sql = "INSERT INTO users (name, username, email, address, password, role, phone) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssss", $name, $username, $email, $address, $hashedPassword, $role, $phone);

            if ($stmt->execute()) {
                // Get the last inserted userID
                $userID = $stmt->insert_id;

                // Insert Participants into participants table
                $sql = "INSERT INTO students (userID, pName, age) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);

                for ($i = 0; $i < count($participants); $i++) {
                    $participantName = trim($participants[$i]);
                    $age = intval($ages[$i]);

                    // Execute insert for each participant
                    $stmt->bind_param("isi", $userID, $participantName, $age);
                    $stmt->execute();
                }

                $successMessage = "Registration successful!";
                header("Location: ../login.php");
                exit();
            } else {
                $passwordError = "An error occurred. Please try again.";
            }
        }
    }
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <title>Register - Like Ahmad Venture</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS for the Register Page */
        body {
            font-family: Lora, Open Sans;
            background-color: #686D76; 
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #373A40;
            color: #EEEEEE;
            text-align: center;
            padding: 20px;
        }

        h1 {
            margin: 0;
            font-size: 25px;
            color:#0040ff;
        }

        .register-container {
            width: 50%;
            margin: 20px auto;
            padding: 30px;
            background: #F2F1F0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
/* 
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-size: 14px;
            color: #333;
        }

        .form-group input {
            width: 95%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            margin-top: 8px;
        } */

        .form-group .error {
            color: #e74c3c;
            font-size: 12px;
        }

        /* Guardian, Participants, and Login fields */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            width: 30%;
            text-align: left;
            margin-right: 10px;
            font-size: 15px;
        }

        .form-group input {
            /* flex: 1; */
            padding: 10px;
            border: 2px solid black;
            border-radius: 5px;
            font-size: 16px;
            width: 90%;
        }

        .sections{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Participant fields aligned */
        .participant {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .participant label {
            font-weight: bold;
            width: 30%;
            text-align: left;
            margin-right: 10px;
            font-size: 15px;
        }

        .participant input {
            flex: 1;
            padding: 10px;
            border: 2px solid black;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Remove button styling */
        .participant .remove {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 5px;
            cursor: pointer;
            /* font-size: 10px; */
        }

        .participant .remove:hover {
            background-color: #c0392b;
        }

        .add {
            background-color: green;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 85%;
            margin-bottom: 1%;
            /* font-size: 10px; */
        }

        /* Register button */
        .register-button {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        .register-button:hover {
            background-color: #218838;
        }

        .section-title {
            display: flex;
            align-items: center;
            /* text-align: left; */
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .section-title::before,
        .section-title::after {
            content: "";
            flex: 1;
            height: 2px;
            background: black;
            margin: 0 10px;
        }

        .success-message {
            margin-top: 20px;
            color: #28a745;
            font-size: 16px;
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 15px;
        }

        .form-footer a {
            color: #007bff;
            font-weight: bold;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
    </style>

<script>
        function addParticipant() {
            let container = document.getElementById("participants");
            let index = container.children.length + 1;
            let div = document.createElement("div");
            div.classList.add("participant");
            div.innerHTML = `
                <label>Participant Name:</label>
                <input type="text" name="pName[]" required>
                <label>Participant Age:</label>
                <input type="number" name="age[]" required>
                <button type="button" onclick="removeParticipant(this)" class="remove"><i class="fa fa-times fa-xl" aria-hidden="true"></i></button>
                <br>
            `;
            container.appendChild(div);
        }

        function removeParticipant(button) {
            button.parentElement.remove();
        }
    </script>
</head>
<body>

    <div class="register-container">
        <h1>Register Account</h1>
    <div class="success-message">
                <?php if (!empty($successMessage)) { echo $successMessage; } ?>
            </div>
        <form action="register.php" method="POST">
        <div class="section">
            <h2 class="section-title">Guardian Information</h2>
            <div class="sections">
                <div class="form-group">
                    <label for="name">Guardian Name:</label>
                    <br>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <br>
                    <input type="number" name="phone" id="phone" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <br>
                    <input type="email" name="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Address:</label>
                    <br>
                    <input type="text" name="address" id="address" required>
                </div>
            </div>
        </div>
        
        
        <h2 class="section-title">Childrens</h2>
        <div id="participants">
            <button type="button" onclick="addParticipant()" class="add">Add Children</i></button>
            <div class="participant">
                <label>Participant Name:</label>
                <input type="text" name="pName[]" required>
                <label>Participant Age:</label>
                <input type="number" name="age[]" required>
                <button type="button" onclick="removeParticipant(this)" class="remove"><i class="fa fa-times fa-xl" aria-hidden="true"></i></button>
                
                <br>
            </div>
        </div>
        
        <div class="section">
            <h2 class="section-title">Login Information</h2>
            <div class="sections">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
            </div>
        
        </div>
        
        
        <button type="submit" class="register-button">Register</button>
    </form>

        <div class="form-footer">
            <p>Already have an account? <a href="../login.php">Login here</a></p>
        </div>
    </div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
