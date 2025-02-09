<?php
// Include database connection
include_once('../conn.php');

// Fetch total count of organizers
$sql_organizers = "SELECT COUNT(*) AS total_organizers FROM users WHERE role = 'organizer'";
$result_organizers = $conn->query($sql_organizers);
$total_organizers = $result_organizers->fetch_assoc()['total_organizers'];

// Fetch total count of events
$sql_events = "SELECT COUNT(*) AS total_events FROM events";
$result_events = $conn->query($sql_events);
$total_events = $result_events->fetch_assoc()['total_events'];

// Fetch total count of participants
$sql_participants = "SELECT COUNT(*) AS total_participants FROM participants";
$result_participants = $conn->query($sql_participants);
$total_participants = $result_participants->fetch_assoc()['total_participants'];

// Fetch organizers details
$sql_organizers_details = "SELECT name, email FROM users WHERE role = 'organizer'";
$result_organizers_details = $conn->query($sql_organizers_details);
$organizers = [];
while ($row = $result_organizers_details->fetch_assoc()) {
    $organizers[] = $row;
}

// Fetch event details
$sql_events_details = "SELECT eventName 
                       FROM events";
$result_events_details = $conn->query($sql_events_details);
$events = [];
while ($row = $result_events_details->fetch_assoc()) {
    $events[] = $row;
}

// Fetch participants details
$sql_participants_details = "SELECT p.name, p.age, u.name FROM participants p
                             JOIN users u ON p.userID = u.userID";
$result_participants_details = $conn->query($sql_participants_details);
$participants = [];
while ($row = $result_participants_details->fetch_assoc()) {
    $participants[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 20px;
        }
        .dashboard-card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">Overview</h1>

    <div class="row">
        <!-- Total Organizers -->
        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h4>Total Organizers</h4>
                </div>
                <div class="card-body">
                    <h2><?= htmlspecialchars($total_organizers) ?></h2>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#organizerModal">View Details</button>
                </div>
            </div>
        </div>

        <!-- Total Events -->
        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h4>Total Events</h4>
                </div>
                <div class="card-body">
                    <h2><?= htmlspecialchars($total_events) ?></h2>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#eventModal">View Details</button>
                </div>
            </div>
        </div>

        <!-- Total Participants -->
        <div class="col-md-4">
            <div class="card dashboard-card">
                <div class="card-header">
                    <h4>Total Participants</h4>
                </div>
                <div class="card-body">
                    <h2><?= htmlspecialchars($total_participants) ?></h2>
                    <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#participantModal">View Details</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Organizers Details -->
    <div class="modal fade" id="organizerModal" tabindex="-1" aria-labelledby="organizerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="organizerModalLabel">Organizer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($organizers as $organizer): ?>
                                <tr>
                                    <td><?= htmlspecialchars($organizer['name']) ?></td>
                                    <td><?= htmlspecialchars($organizer['email']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Events Details -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($events as $event): ?>
                                <tr>
                                    <td><?= htmlspecialchars($event['eventName']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Participants Details -->
    <div class="modal fade" id="participantModal" tabindex="-1" aria-labelledby="participantModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="participantModalLabel">Participant Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Age</th>
                                <th>Guardian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participants as $participant): ?>
                                <tr>
                                    <td><?= htmlspecialchars($participant['name']) ?></td>
                                    <td><?= htmlspecialchars($participant['age']) ?></td>
                                    <td><?= htmlspecialchars($participant['name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
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
