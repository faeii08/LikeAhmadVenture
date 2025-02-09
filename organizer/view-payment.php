<?php
// Include database connection and sidebar
include_once('../conn.php');

// Fetch all payment details
$paymentDetails = [];
$sql_all_payments = "SELECT p.participantID, p.name AS participant_name, p.age, u.phone, pay.amount, pay.status AS payment_status, pay.date, ev.eventName, u.username
                     FROM payments pay
                     JOIN participants p ON pay.participantID = p.participantID
                     JOIN users u ON p.userID = u.userID
                     JOIN event_details ed ON p.eventDetailID = ed.eventDetailID
                     JOIN events ev ON ed.eventID = ev.eventID";
$stmt_all_payments = $conn->prepare($sql_all_payments);
$stmt_all_payments->execute();
$result_all_payments = $stmt_all_payments->get_result();

while ($row_payment = $result_all_payments->fetch_assoc()) {
    $paymentDetails[] = $row_payment;
}

// Handle viewing details for a specific participant
if (isset($_GET['participantID'])) {
    $participantID = $_GET['participantID'];
    // Fetch detailed payment information for the specific participant
    $sql_payment_detail = "SELECT p.name AS participant_name, p.age, u.phone, pay.amount, pay.status AS payment_status, pay.date, ev.eventName, u.username
                           FROM payments pay
                           JOIN participants p ON pay.participantID = p.participantID
                           JOIN users u ON p.userID = u.userID
                           JOIN event_details ed ON p.eventDetailID = ed.eventDetailID
                           JOIN events ev ON ed.eventID = ev.eventID
                           WHERE p.participantID = ?";
    $stmt_payment_detail = $conn->prepare($sql_payment_detail);
    $stmt_payment_detail->bind_param("i", $participantID);
    $stmt_payment_detail->execute();
    $result_payment_detail = $stmt_payment_detail->get_result();

    // Fetch the payment details for the specific participant
    $paymentDetail = $result_payment_detail->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
        table {
            width: 100%;
        }
        .btn-back {
            margin-top: 20px;
        }
        .btn-view-details {
            margin-top: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>All Payments</h1>

    <?php if (isset($paymentDetail)): ?>
        <!-- Display Detailed Payment for a Single Participant -->
        <h3>Payment Details for <?= htmlspecialchars($paymentDetail['participant_name']) ?></h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Participant Name</th>
                    <th>Age</th>
                    <th>Guardian Name</th>
                    <th>Phone</th>
                    <th>Event Name</th>
                    <th>Amount Paid</th>
                    <th>Payment Status</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($paymentDetail['participant_name']) ?></td>
                    <td><?= htmlspecialchars($paymentDetail['age']) ?></td>
                    <td><?= htmlspecialchars($paymentDetail['username']) ?></td>
                    <td><?= htmlspecialchars($paymentDetail['phone']) ?></td>
                    <td><?= htmlspecialchars($paymentDetail['eventName']) ?></td>
                    <td><?= htmlspecialchars($paymentDetail['amount']) ?></td>
                    <td><?= htmlspecialchars($paymentDetail['payment_status']) ?></td>
                    <td><?= htmlspecialchars($paymentDetail['date']) ?></td>
                </tr>
            </tbody>
        </table>
        <a href="?page=payment" class="btn btn-primary btn-back">Back to All Payments</a>

    <?php else: ?>
        <!-- Display All Payment Records -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Participant Name</th>
                    <th>Age</th>
                    <th>Guardian Name</th>
                    <th>Phone</th>
                    <th>Event Name</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paymentDetails as $payment): ?>
                    <tr>
                        <td><?= htmlspecialchars($payment['participant_name']) ?></td>
                        <td><?= htmlspecialchars($payment['age']) ?></td>
                        <td><?= htmlspecialchars($payment['username']) ?></td>
                        <td><?= htmlspecialchars($payment['phone']) ?></td>
                        <td><?= htmlspecialchars($payment['eventName']) ?></td>
                        <td>
                            <a href="?page=payment&participantID=<?= urlencode($payment['participantID']) ?>" class="btn btn-info btn-sm">View Details</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
