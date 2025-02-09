<?php

// Include necessary files for database connection and session management
include '../conn.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$userID = $_SESSION['userID'];

// Get the participantID (assuming it's passed in the URL or fetched from session)
$participantID = isset($_GET['participantID']) ? $_GET['participantID'] : '';  // You can also use session if available

if (empty($participantID)) {
    echo "Participant ID is required.";
    exit();
}

// Fetch the amount for the given participantID
$sql = "SELECT amount FROM participants WHERE participantID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $participantID);
$stmt->execute();
$stmt->bind_result($amount);
$stmt->fetch();
$stmt->close();

// Check if the amount is found
if ($amount === null) {
    echo "Amount for the participant not found.";
    exit();
}

// Handle payment method selection
$paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($paymentMethod == 'fpx') {
        // Process FPX online banking
        $date = trim($_POST['date']);

        // Insert payment into database
        $sql = "INSERT INTO payments (participantID, amount, payment_method, status, date) VALUES (?, ?, ?, 'paid', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $participantID, $amount, $paymentMethod, $date);

        if ($stmt->execute()) {
            echo "<script>
            alert('Payment Success');
            window.location.href = 'eventPage.php'; // Redirect after alert
        </script>";
            exit();
        } else {
            echo "<script>alert('Error processing the payment.');</script>";
        }
    } else if ($paymentMethod == 'cash') {
        // Process cash payment (Manual Payment)
        $date = trim($_POST['date']);

        $sql = "INSERT INTO payments (participantID, amount, payment_method, status, date) VALUES (?, ?, ?, 'pending', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiss", $participantID, $amount, $paymentMethod, $date);

        if ($stmt->execute()) {
            echo "<script>
            alert('Cash payment confirmed! Please make the payment during event registration.');
            window.location.href = 'eventPage.php'; // Redirect after alert
        </script>";
            exit();
        } else {
            echo "<script>alert('Error processing the payment.');</script>";
        }
    } else {
        echo "<script>alert('Please select a payment method.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Formal background style */
        body {
            font-family: 'Lora', 'Open Sans', sans-serif;
            background: linear-gradient(to right, #e6e6e6, #ffffff);
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .payment-container {
            width: 500px;
            padding: 30px;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        /* Date input styling */
        .date-input {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 2px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: 0.3s;
            cursor: pointer;
            background-color: #f9f9f9;
        }

        .date-input:hover, .date-input:focus {
            border-color: #007bff;
            background: #f0f8ff;
        }

        .payment-option {
            margin: 15px 0;
            text-align: left;
        }

        .payment-option label{
            font-size: 15px;
        }

        .bank-options {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
        }

        .bank-option {
            display: inline-block;
            text-align: center;
            cursor: pointer;
        }

        .bank-option input {
            display: none;
        }

        .bank-option label {
            display: block;
            padding: 10px;
            /* border: 2px solid #ddd; */
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .bank-option img {
            width: 80px;
            height: auto;
        }

        .bank-option input:checked + label {
            background: #f0f8ff;
        }

        .payment-button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        .payment-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="payment-container">
    <h1>Make a Payment</h1>

    <form action="make-payment.php?participantID=<?php echo $participantID; ?>" method="POST">
        <!-- Date Input -->
        <div class="payment-option">
            <label for="date">Date:</label>
            <br>
            <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" class="date-input" required>
        </div>

        <!-- Amount Display -->
        <div class="payment-option">
            <label>Amount: RM <?php echo number_format($amount, 2); ?></label>
            <input type="hidden" name="amount" value="<?php echo $amount; ?>">
        </div>

        <!-- Payment Method Selection -->
        <div class="payment-option">
            <input type="radio" name="payment_method" value="cash" id="cash" required onclick="toggleBanks(false)">
            <label for="cash">Cash Payment</label>
        </div>

        <div class="payment-option">
            <input type="radio" name="payment_method" value="fpx" id="fpx" required onclick="toggleBanks(true)">
            <label for="fpx">Online Banking (FPX)</label>
        </div>

        <!-- Bank Selection -->
        <div id="bank-selection" class="bank-options" style="display: none;">
            <div class="bank-option">
                <input type="radio" name="bank" id="maybank" value="Maybank">
                <label for="maybank"><img src="../images/maybank.jpg" alt="Maybank"></label>
            </div>
            <div class="bank-option">
                <input type="radio" name="bank" id="cimb" value="CIMB">
                <label for="cimb"><img src="../images/cimb.jpg" alt="CIMB"></label>
            </div>
            <div class="bank-option">
                <input type="radio" name="bank" id="publicbank" value="Public Bank">
                <label for="publicbank"><img src="../images/public-bank.png" alt="Public Bank"></label>
            </div>
            <div class="bank-option">
                <input type="radio" name="bank" id="rhb" value="RHB">
                <label for="rhb"><img src="../images/rhb.png" alt="RHB"></label>
            </div>
            <div class="bank-option">
                <input type="radio" name="bank" id="hongleong" value="Hong Leong Bank">
                <label for="hongleong"><img src="../images/hong-leong.png" alt="Hong Leong Bank"></label>
            </div>
            <div class="bank-option">
                <input type="radio" name="bank" id="ambank" value="AmBank">
                <label for="ambank"><img src="../images/ambank.png" alt="AmBank"></label>
            </div>
            <div class="bank-option">
                <input type="radio" name="bank" id="bsn" value="BSN">
                <label for="hongleong"><img src="../images/bsn.jpg" alt="BSN"></label>
            </div>
            <div class="bank-option">
                <input type="radio" name="bank" id="bank-islam" value="Bank Islam">
                <label for="ambank"><img src="../images/Bank-Islam.jpg" alt="BankIslam"></label>
            </div>
        </div>
<br>
        <!-- Submit Button -->
        <button type="submit" class="payment-button">Proceed with Payment</button>
    </form>
</div>

<script>
    function toggleBanks(show) {
        document.getElementById("bank-selection").style.display = show ? "flex" : "none";
    }
</script>

</body>
</html>


<?php
// Close the database connection
$conn->close();
?>
