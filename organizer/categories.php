<?php
// Database connection
include '../conn.php';

// Start session
session_start();

// Handle form submission for adding or editing event details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventDetailID = isset($_POST['eventDetailID']) ? intval($_POST['eventDetailID']) : null;
    $eventID = $_POST['eventID'] ?? '';
    $description = $_POST['description'] ?? '';
    $eventDate = $_POST['eventDate'] ?? '';
    $price = $_POST['price'] ?? '';
    $totalParticipant = $_POST['totalParticipant'] ?? '';

    if ($eventDetailID) {
        // Update event detail
        $sql = "UPDATE event_details SET eventID = ?, description = ?, eventDate = ?, price = ?, totalParticipant = ? WHERE eventDetailID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdii", $eventID, $description, $eventDate, $price, $totalParticipant, $eventDetailID);
        $stmt->execute();
        $message = $stmt->affected_rows > 0 ? "Event detail updated successfully." : "No changes were made.";
    } else {
        // Add new event detail
        $sql = "INSERT INTO event_details (eventID, description, eventDate, price, totalParticipant) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdi", $eventID, $description, $eventDate, $price, $totalParticipant);
        $stmt->execute();
        $message = $stmt->affected_rows > 0 ? "Event detail added successfully." : "Failed to add event detail.";
    }
}

// Handle event detail deletion
if (isset($_GET['delete'])) {
    $eventDetailID = intval($_GET['delete']);
    $sql = "DELETE FROM event_details WHERE eventDetailID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventDetailID);
    $stmt->execute();
    $message = $stmt->affected_rows > 0 ? "Event detail deleted successfully." : "Failed to delete event detail.";
}

// Fetch all event details with eventName
$sql = "
    SELECT ed.eventDetailID, e.eventName, ed.description, ed.eventDate, ed.price, ed.totalParticipant
    FROM event_details ed
    JOIN events e ON ed.eventID = e.eventID
";
$result = $conn->query($sql);
$eventDetails = $result->fetch_all(MYSQLI_ASSOC);

// Fetch all events for the dropdown
$sql = "SELECT eventID, eventName FROM events";
$eventsResult = $conn->query($sql);
$events = $eventsResult->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Event Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Manage Event Details</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Add Event Detail Button -->
    <button class="btn btn-secondary mb-3" id="toggleFormButton">Add Event Detail</button>

    <!-- Add or Edit Event Details Form -->
    <div class="card mb-4" id="eventForm" style="display: none;">
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="eventDetailID" id="eventDetailID">
                <div class="mb-3">
                    <label for="eventID" class="form-label">Event Name</label>
                    <select class="form-select" id="eventID" name="eventID" required>
                        <option value="" disabled selected>Select an event</option>
                        <?php foreach ($events as $event): ?>
                            <option value="<?= htmlspecialchars($event['eventID']) ?>">
                                <?= htmlspecialchars($event['eventName']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="eventDate" class="form-label">Event Date</label>
                    <input type="date" class="form-control" id="eventDate" name="eventDate" required>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                </div>
                <div class="mb-3">
                    <label for="totalParticipant" class="form-label">Total Participants</label>
                    <input type="number" class="form-control" id="totalParticipant" name="totalParticipant" required>
                </div>
                <button type="submit" class="btn btn-primary" id="submitButton">Add Event Detail</button>
            </form>
        </div>
    </div>

    <!-- Event Details Table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>No</th>
            <th>Event Name</th>
            <th width="40%">Description</th>
            <th>Event Date</th>
            <th>Price</th>
            <th width="10%">Total Participants</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($eventDetails as $detail): ?>
            <tr>
                <td><?= htmlspecialchars($detail['eventDetailID']) ?></td>
                <td><?= htmlspecialchars($detail['eventName']) ?></td>
                <td><?= htmlspecialchars($detail['description']) ?></td>
                <td><?= htmlspecialchars($detail['eventDate']) ?></td>
                <td><?= htmlspecialchars($detail['price']) ?></td>
                <td><?= htmlspecialchars($detail['totalParticipant']) ?></td>
                <td>
                    <button class="btn btn-sm btn-warning edit-button"
                            data-id="<?= $detail['eventDetailID'] ?>"
                            data-eventid="<?= htmlspecialchars($detail['eventName']) ?>"
                            data-description="<?= htmlspecialchars($detail['description']) ?>"
                            data-eventdate="<?= htmlspecialchars($detail['eventDate']) ?>"
                            data-price="<?= htmlspecialchars($detail['price']) ?>"
                            data-totalparticipant="<?= htmlspecialchars($detail['totalParticipant']) ?>">Edit</button>
                    <a href="eventDetails.php?delete=<?= $detail['eventDetailID'] ?>" class="btn btn-sm btn-danger"
                       onclick="return confirm('Are you sure you want to delete this event detail?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Toggle form visibility
    const toggleButton = document.getElementById('toggleFormButton');
    const eventForm = document.getElementById('eventForm');

    toggleButton.addEventListener('click', () => {
        if (eventForm.style.display === 'none') {
            eventForm.style.display = 'block';
            toggleButton.style.display = 'none'; // Hide the button when form is visible
        } else {
            eventForm.style.display = 'none';
            toggleButton.style.display = 'block'; // Show the button when form is hidden
        }
    });

    // Populate the form with event detail data for editing
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', () => {
            eventForm.style.display = 'block'; // Ensure form is visible when editing
            toggleButton.style.display = 'none'; // Hide the button when editing
            document.getElementById('eventDetailID').value = button.dataset.id;
            document.getElementById('eventID').value = button.dataset.eventid;
            document.getElementById('description').value = button.dataset.description;
            document.getElementById('eventDate').value = button.dataset.eventdate;
            document.getElementById('price').value = button.dataset.price;
            document.getElementById('totalParticipant').value = button.dataset.totalparticipant;
            document.getElementById('submitButton').textContent = 'Update Event Detail';
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
