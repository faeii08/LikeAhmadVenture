<?php

// Database connection
include '../conn.php';

// Start session
session_start();

// Initialize variables for error messages
$nameError = $ageError = "" ;
$successMessage = "";

// Get userID from session
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;

// Fetch user details if user is logged in
if ($userID) {
    $sql = "SELECT * FROM users WHERE userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $userResult = $stmt->get_result();
    
    // Check if user details are found
    if ($userResult->num_rows > 0) {
        $user = $userResult->fetch_assoc();
    } else {
        echo "User not found.";
        exit();
    }
}

// Fetch students under the same userID
$students = [];
if ($userID) {
    $sql = "SELECT id, pName, age FROM students WHERE userID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
}


// Retrieve the eventDetailID from query parameter
$eventDetailID = isset($_GET['eventDetailID']) ? $_GET['eventDetailID'] : null;

$amount = 0;
$description = 'null';

// Fetch the amount from event_details table based on eventDetailID
if ($eventDetailID) {
    $sql = "SELECT eventID, price, description FROM event_details WHERE eventDetailID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventDetailID);
    $stmt->execute();
    $stmt->bind_result($eventID,$amount, $description);
    $stmt->fetch();
    $stmt->close();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize input values
    $name = trim($_POST['name']);
    $age = trim($_POST['age']);
    $amount = trim($_POST['amount']);

    // Validate form data
    if (empty($name)) {
        $nameError = "Name is required.";
    } else if (empty($age)) {
        $ageError = "Age is required.";
    } else if ($age < 2 || $age > 17) {
        $ageError = "Age must be between 2 and 17.";
    }

    // If no errors, save participant data
    if (empty($nameError) && empty($ageError)) {
        $sql = "INSERT INTO participants (name, age, userID, eventDetailID, amount) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiis", $name, $age, $userID, $eventDetailID, $amount);

        if ($stmt->execute()) {
            // Fetch the participantID of the newly inserted participant
            $participantID = $stmt->insert_id;  // Get the last inserted ID

            $successMessage = "Participant registered successfully!";
            // Redirect to the payment page with participantID
            header("Location: make-payment.php?participantID=$participantID"); // Redirect to payment page
            exit();
        } else {
            $errorMessage = "An error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <title>Register Participant - Like Ahmad Venture</title>
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

        header h1 {
            margin: 0;
        }

        .register-container {
            width: 50%;
            margin: 50px auto;
            padding: 30px;
            background: #F2F1F0;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .register-container form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-size: 14px;
            color: #333;
        }

        .form-group input, select {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .form-group .error {
            color: #e74c3c;
            font-size: 12px;
        }

        .register-button {
            grid-column: span 2;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        .register-button:hover {
            background-color: #218838;
        }

        .success-message {
            grid-column: span 2;
            text-align: center;
            color: #28a745;
            font-size: 16px;
        }

        /* Read-only input styles */
        .readonly-input {
            background-color: #EFF3EA;
            border: 1px solid #ddd;
            cursor: not-allowed;
        }

        a {
            color: red;
            font-size: 20px;
        }

        .back-button:hover {
            background-color: #3674B5;
        }
    </style>

    <script>
        function fillAge() {
    var select = document.getElementById("name");
    var ageInput = document.getElementById("age");
    var selectedOption = select.options[select.selectedIndex];

    // Get the selected student's age and update the age input field
    if (selectedOption.value !== "") {
        ageInput.value = selectedOption.getAttribute("data-age");
    } else {
        ageInput.value = "";
    }
}

    </script>
</head>
<body>

    <div class="register-container">
        <a href="eventDetail.php?eventDetailID=<?php echo htmlspecialchars($eventDetailID); ?>&id=<?php echo htmlspecialchars($eventID); ?>">
            <i class="fa fa-chevron-circle-left" aria-hidden="true">&nbsp;Back</i>
        </a>

        <h1>Participant Registration</h1>
        <form method="POST">
            <div class="success-message">
                <?php if (!empty($successMessage)) { echo $successMessage; } ?>
            </div>
            <div class="form-group">
                <label for="name">Select Childrens:</label>
                <select name="name" id="name" onchange="fillAge()" required>
                    <option value="">-- Select Childrens --</option>
                    <?php foreach ($students as $student) { ?>
                        <option value="<?php echo htmlspecialchars($student['pName']); ?>" 
                                data-age="<?php echo htmlspecialchars($student['age']); ?>">
                            <?php echo htmlspecialchars($student['pName']); ?>
                        </option>
                    <?php } ?>
                </select>
                <span class="error"><?php echo $nameError; ?></span>
            </div>

            <div class="form-group">
                <label for="age">Participant Age:</label>
                <input type="number" name="age" id="age" value="" readonly required>
                <span class="error"><?php echo $ageError; ?></span>
            </div>

            <!-- Display the amount -->
            <div class="form-group">
                <label for="amount">Amount:</label>
                <input type="number" name="amount" id="amount" value="<?php echo isset($amount) ? htmlspecialchars($amount) : ''; ?>" class="readonly-input" readonly>
            </div>

            <div class="form-group">
                <label for="userName">Event:</label>
                <input type="text" id="userName" value="<?php echo isset($description) ? htmlspecialchars($description) : ''; ?>" class="readonly-input" readonly>
            </div>

            <div class="form-group">
                <label for="userName">Guardian Name:</label>
                <input type="text" id="userName" value="<?php echo htmlspecialchars($user['name']); ?>" class="readonly-input" readonly>
            </div>

            <div class="form-group">
                <label for="userEmail">Guardian Email:</label>
                <input type="email" id="userEmail" value="<?php echo htmlspecialchars($user['email']); ?>" class="readonly-input" readonly>
            </div>

            <div class="form-group">
                <label for="phone">Phone No:</label>
                <input type="text" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="readonly-input" readonly>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" value="<?php echo htmlspecialchars($user['address']); ?>" class="readonly-input" readonly>
            </div>

            <button type="submit" class="register-button">Register Participant</button>
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>