<?php
// Database connection
include 'conn.php';

// Start session
session_start();

// Get the event ID from the URL
$eventID = isset($_GET['id']) ? $_GET['id'] : 0;

// Fetch the event data from the events table
$sql = "SELECT eventName FROM events WHERE eventID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $eventID);
$stmt->execute();
$result = $stmt->get_result();

// Check if the event exists
if ($result->num_rows == 1) {
    $event = $result->fetch_assoc();
} else {
    echo "Event not found.";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="template/css/eventDetails.css">
    <title>Event Details - <?php echo htmlspecialchars($event['eventName']); ?></title>

    <style>
        body {
        margin: 0;
        font-family: Lora, Open Sans;
        background-color: #686D76; /* Replace colors with your event page gradient */
        height: 100vh;
        color: #333;
        }

        table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: white; /* Keeps table background solid */
        }

        h1 {
            display: flex;
            align-items: center;
            /* justify-content: center; */
            gap: 400px; /* Spacing between the button and text */
            margin-left: 10%;
            margin-top: 5%;
        }

        .back-button {
            color: black;
            font-size: 20px;
        }
    </style>
</head>

<body>
<h1>
    <a href="eventPage.php" class="back-button">
        <i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Back
    </a>
    Event Details: <?php echo htmlspecialchars($event['eventName'] ?? ''); ?>
</h1>


    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Paricipant</th>
                <th>Registration</th>
            </tr>
        </thead>
        <tbody>
        <?php
            // Fetch the event details for the given eventID
            $sql = "SELECT * FROM event_details WHERE eventID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $eventID);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if there are event details for the eventID
            if ($result->num_rows > 0) {
                while ($eventDetail = $result->fetch_assoc()) {
                    // Fetch participant count and total participants needed for this event detail
                    $eventDetailID = $eventDetail['eventDetailID'];
                    $totalParticipantsNeeded = $eventDetail['totalParticipant'] ?? 0;

                    $participantCountSql = "SELECT COUNT(*) AS participantCount FROM participants WHERE eventDetailID = ?";
                    $participantCountStmt = $conn->prepare($participantCountSql);
                    $participantCountStmt->bind_param("i", $eventDetailID);
                    $participantCountStmt->execute();
                    $participantCountResult = $participantCountStmt->get_result();
                    $participantData = $participantCountResult->fetch_assoc();
                    $currentParticipants = $participantData['participantCount'] ?? 0;

                    // Determine if the button should be disabled
                    $isOverCapacity = $currentParticipants >= $totalParticipantsNeeded;

                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($eventDetail['eventDate']) . "</td>";
                    echo "<td>" . htmlspecialchars($event['eventName']) . "</td>";
                    echo "<td>" . htmlspecialchars($eventDetail['description']) . "</td>";
                    echo "<td>" . htmlspecialchars($eventDetail['price']) . "</td>";
                    echo "<td>" . $currentParticipants . " / " . $totalParticipantsNeeded . "</td>";
                    echo "<td>";
                    if ($isOverCapacity) {
                        echo "<button class='register-button' disabled>Full</button>";
                    } else {
                        echo "<button class='register-button' onclick='alertLogin()'>Register</button>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No event details found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- JavaScript -->
    <script>
        // Function to display a login alert
        function alertLogin() {
            alert('Please login to register for this event.');
            // Optionally redirect to the login page
            window.location.href = 'login.php';
        }
    </script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>