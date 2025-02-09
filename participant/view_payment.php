<?php
// Include necessary files for database connection and session management
include '../conn.php';
session_start();

// Get userID from session
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;

// Check if the user is logged in
if (!$userID) {
    header("Location: login.php");
    exit();
}

// Query to fetch payments and participant name using JOIN
$sql = "
    SELECT p.name AS participant_name, py.paymentID, py.amount, py.payment_method, py.date, py.status
    FROM payments py
    JOIN participants p ON py.participantID = p.participantID
    WHERE p.userID = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$paymentResult = $stmt->get_result();

// Check if there are any payments
$payments = [];
while ($payment = $paymentResult->fetch_assoc()) {
    $payments[] = $payment;
}

$sql2 = "
    SELECT e.eventDate, e.description
    FROM event_details e
    JOIN participants p ON e.eventDetailID = p.eventDetailID
    WHERE p.userID = ?
";

$stmts = $conn->prepare($sql2);
$stmts->bind_param("i", $userID);
$stmts->execute();
$eventsResult = $stmts->get_result();

$events = [];
while ($event = $eventsResult->fetch_assoc()){
    $events[] = $event;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>View Payments</title>
    <link rel="stylesheet" href="../template/css/header.css">
    <style>
        body {
            font-family: Lora, Open Sans;
            background-color: #686D76;
            margin: 0;
            padding: 0;
            height: 100vh;
        }
        .container {
            width: 80%;
            margin: 50px auto;
            padding: 30px;
            background-color: #E3E6E8;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333333;
        }
        .payment-table {
            width: 100%;
            margin-top: 30px;
            border-collapse: collapse;
        }
        .payment-table th, .payment-table td {
            padding: 12px;
            text-align: center;
            border: 1px solid #4D4D4D;
        }
        .payment-table th {
            background-color: #B0C4DE;
        }
        .icon {
            cursor: pointer;
            font-size: 20px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 20% auto;
            padding: 20px;
            border-radius: 8px;
            width: 50%;
            text-align: left;
        }
        .close {
            float: right;
            font-size: 24px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<?php include '../template/php/headerP.php'; ?>
<div class="container">
    <h1>Payment Details</h1>

    <?php if (count($payments) > 0): ?>
        <table class="payment-table">
            <thead>
                <tr>
                    <th>Participant Name</th>
                    <th>Payment ID</th>
                    <th>Amount (RM)</th>
                    <th>Payment Method</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $index => $payment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($payment['participant_name']); ?></td>
                        <td><?php echo htmlspecialchars($payment['paymentID']); ?></td>
                        <td><?php echo number_format($payment['amount'], 2); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($payment['date']); ?></td>
                        <td>
                            <span class="icon" onclick="openModal(<?php echo $index; ?>)">
                                <?php echo ($payment['status'] === 'paid') ? '✔' : '✘'; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No payments found for this participant.</p>
    <?php endif; ?>
</div>

<!-- Modal Structure -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Payment Details</h2>
        <p id="modal-text"></p>
    </div>
</div>

<script>
    function openModal(index) {
        var payments = <?php echo json_encode($payments); ?>;
        var events = <?php echo json_encode($events); ?>;
        var payment = payments[index];
        var event = events[index];
        var modalText = `
                    Event Name      : ${event.description}<br>
                    Event Date      : ${event.eventDate}<br>
                    Participant Name: ${payment.participant_name}<br>
                    Amount          : RM${payment.amount}<br>
                    Status          : ${payment.status}`;
        document.getElementById('modal-text').innerHTML = modalText;
        document.getElementById('modal').style.display = 'block';
    }
    function closeModal() {
        document.getElementById('modal').style.display = 'none';
    }
</script>

</body>
</html>

<?php $conn->close(); ?>