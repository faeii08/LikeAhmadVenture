<?php
// Include database connection and sidebar
include_once('../conn.php');

// Handle filtering by event name
$eventNameFilter = isset($_GET['eventName']) ? $_GET['eventName'] : '';

// Fetch events for the filter dropdown
$sql_events = "SELECT eventName, eventID FROM events";
$events_result = $conn->query($sql_events);
$events = [];
while ($row = $events_result->fetch_assoc()) {
    $events[] = $row;
}

// Fetch report data with optional filtering by event name
$sql = "SELECT ed.description, ed.eventDate, ev.eventName,
               (SELECT COUNT(*) FROM participants WHERE eventDetailID = ed.eventDetailID) AS num_participants, ed.eventDetailID
        FROM event_details ed
        JOIN events ev ON ed.eventID = ev.eventID";

if ($eventNameFilter) {
    $sql .= " WHERE ev.eventName LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$eventNameFilter%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

// Handle printing
if (isset($_GET['print'])) {
    echo "<script>window.print();</script>";
    exit();
}

$eventParticipants = [];
// Fetch participants and include eventName in the query
if (isset($_GET['eventDetailID'])) {
    $eventDetailID = $_GET['eventDetailID'];
    $sql_participants = "SELECT p.name, p.age, u.phone, u.username AS user_name, ed.eventDate, pay.status, ev.eventName, ed.description
                         FROM participants p
                         JOIN event_details ed ON p.eventDetailID = ed.eventDetailID
                         JOIN events ev ON ed.eventID = ev.eventID
                         JOIN payments pay ON p.participantID = pay.participantID
                         JOIN users u ON p.userID = u.userID
                         WHERE p.eventDetailID = ?";
    $stmt_participants = $conn->prepare($sql_participants);
    $stmt_participants->bind_param("i", $eventDetailID);
    $stmt_participants->execute();
    $result_participants = $stmt_participants->get_result();

    while ($row_participant = $result_participants->fetch_assoc()) {
        $eventParticipants[] = $row_participant;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Add styles for the right sidebar */
        .container-fluid {
            display: flex;
            min-height: 100vh;
        }

        .row {
            width: 100%;
        }

        .col-md-9 {
            flex: 1; /* Take up the available space in the right sidebar */
            padding: 20px;
        }

        /* Ensure the content fills the sidebar */
        .printable-area {
            width: 100%;
        }

        /* Custom print styles */
        .company-logo, .company-name {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }

            .company-logo img {
                max-width: 50px;
                margin-bottom: 10px;
            }

            .company-name {
                font-size: 24px;
                font-weight: bold;
            }
        /* Handle printing styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .printable-area, .printable-area * {
                visibility: visible;
            }
            .printable-area {
                position: absolute;
                left: 0;
                top: 0;
            }

            /* Custom print styles */
            .company-logo, .company-name {
                display: block;
                text-align: center;
                margin-bottom: 20px;
            }

            .company-logo img {
                max-width: 50px;
                margin-bottom: 10px;
            }

            .company-name {
                font-size: 24px;
                font-weight: bold;
            }
        }

        /* Ensure the table fills the available space */
        table {
            width: 100%;
        }

        .btn-primary, .btn-secondary {
            margin-top: 10px;
        }

        .btn-navigation {
            margin-top: 10px;
        }

        /* Hide the print button for report table */
        .print-btn-hidden {
            display: none;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">

        <!-- Main Content -->
        <div class="col-md-9 mt-4">
            <h1 class="mb-4">Event Report</h1>

            <!-- Report Table -->
            <?php if (!isset($_GET['eventDetailID'])): ?>
                <div class="printable-area">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Description</th>
                                <th>Date</th>
                                <th>Number of Participants</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['eventName'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['description'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['eventDate'] ?? 'N/A') ?></td>
                                    <td align="center">
                                        <a href="sidebar.php?page=report&eventDetailID=<?= urlencode($row['eventDetailID']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($row['num_participants'] ?? 0) ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Print Button Hidden in Report Table -->
                <button onclick="window.print()" class="btn btn-secondary mt-3 print-btn-hidden">Print Report</button>
            <?php endif; ?>

            <!-- Event Participants Report Section -->
            <?php if (!empty($eventParticipants)): ?>
                <div class="printable-area">
                    <div class="company-logo">
                        <!-- Add your company logo here -->
                        <img src="../images/logo.png" alt="Logo" >
                    </div>
                    <div class="company-name">
                        <!-- Add your company name here -->
                        Like Ahmad Venture
                    </div>
                    <h3>Participants for Event: <?= htmlspecialchars($eventParticipants[0]['eventName'] ?? 'Event Not Found') ?></h3>
                    <h4>Event Description :</h4> 
                    <?= htmlspecialchars($eventParticipants[0]['description'] ?? 'Event Not Found') ?>
                    <br><br>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Participant Name</th>
                                <th>Age</th>
                                <th>Guardian Name</th>
                                <th>Phone</th>
                                <th>Date of Event</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($eventParticipants as $participant): ?>
                                <tr>
                                    <td><?= htmlspecialchars($participant['name']) ?></td>
                                    <td><?= htmlspecialchars($participant['age']) ?></td>
                                    <td><?= htmlspecialchars($participant['user_name']) ?></td>
                                    <td><?= htmlspecialchars($participant['phone']) ?></td>
                                    <td><?= htmlspecialchars($participant['eventDate']) ?></td>
                                    <td><?= htmlspecialchars($participant['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="sidebar.php?page=report" class="btn btn-primary">Back to Report Table</a>
                <!-- Print Button Visible in Participant Report Section -->
                <button onclick="window.print()" class="btn btn-secondary mt-3">Print Report</button>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>