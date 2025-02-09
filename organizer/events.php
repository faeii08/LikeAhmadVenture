<?php
// Database connection
include '../conn.php';

// Start session
session_start();

// Handle form submission for creating and updating events
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventName = $_POST['eventName'] ?? '';
    $eventID = isset($_POST['eventID']) ? intval($_POST['eventID']) : null;

    // Handle image upload
    $imagePath = null;
    if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/images/'; // Use absolute path to the directory
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true); // Create the directory if it doesn't exist
        }

        $imagePath = $uploadDir . basename($_FILES['img']['name']);
        if (!move_uploaded_file($_FILES['img']['tmp_name'], $imagePath)) {
            $message = "Failed to upload the image. Check directory permissions.";
            $imagePath = null; // Reset image path if upload fails
        } else {
            $imagePath = 'images/' . basename($_FILES['img']['name']); // Save relative path for database
        }
    } elseif (isset($_FILES['img']) && $_FILES['img']['error'] !== UPLOAD_ERR_NO_FILE) {
        $message = "Image upload error: " . $_FILES['img']['error'];
    }

    if ($eventID) {
        // Update existing event
        if ($imagePath) {
            $sql = "UPDATE events SET eventName = ?, img = ? WHERE eventID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $eventName, $imagePath, $eventID);
        } else {
            $sql = "UPDATE events SET eventName = ? WHERE eventID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $eventName, $eventID);
        }
        $stmt->execute();
        $message = $stmt->affected_rows > 0 ? "Event updated successfully." : "No changes were made.";
    } else {
        // Create new event
        $sql = "INSERT INTO events (eventName, img) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $eventName, $imagePath);
        $stmt->execute();
        $message = $stmt->affected_rows > 0 ? "Event created successfully." : "Failed to create event.";
    }
}

// Handle event deletion
if (isset($_GET['delete'])) {
    $eventID = intval($_GET['delete']);

    // First, delete the related payments
    $sql = "DELETE FROM payments WHERE participantID IN (SELECT participantID FROM participants WHERE eventDetailID IN (SELECT eventDetailID FROM event_details WHERE eventID = ?))";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();

    // Then, delete the related participants
    $sql = "DELETE FROM participants WHERE eventDetailID IN (SELECT eventDetailID FROM event_details WHERE eventID = ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();

    // Next, delete the related event_details
    $sql = "DELETE FROM event_details WHERE eventID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();

    // Finally, delete the event itself
    $sql = "DELETE FROM events WHERE eventID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $eventID);
    $stmt->execute();

    $message = $stmt->affected_rows > 0 ? "Event deleted successfully." : "Failed to delete event.";
}



// Fetch all events
$sql = "SELECT * FROM events";
$result = $conn->query($sql);
$events = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            margin-top: 50px;
        }
        .form-container {
            display: none; /* Initially hide the form */
        }
        .form-container.active {
            display: block; /* Show the form when active */
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="mb-4">Manage Events</h1>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Button to create new event -->
    <button class="btn btn-primary mb-4" id="showFormButton">Create Event</button>

    <!-- Create or Edit Event Form -->
    <div class="card mb-4 form-container" id="eventForm">
        <div class="card-body">
            <form method="POST" action="events.php" enctype="multipart/form-data">
                <input type="hidden" name="eventID" id="eventID">
                <div class="mb-3">
                    <label for="eventName" class="form-label">Event Name</label>
                    <input type="text" class="form-control" id="eventName" name="eventName" required>
                </div>
                <div class="mb-3">
                    <label for="eventImage" class="form-label">Event Image</label>
                    <input type="file" class="form-control" id="eventImage" name="img">
                </div>
                <button type="submit" class="btn btn-primary" id="submitButton">Create Event</button>
            </form>
        </div>
    </div>

    <!-- Events Table -->
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $event): ?>
            <tr>
                <td><?= htmlspecialchars($event['eventID']) ?></td>
                <td><?= htmlspecialchars($event['eventName']) ?></td>
                <td>
                    <?php if (!empty($event['img'])): ?>
                        <img src="<?= htmlspecialchars($event['img']) ?>" alt="Event Image" width="100">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td>
                    <button class="btn btn-sm btn-warning edit-button"
                            data-id="<?= $event['eventID'] ?>"
                            data-name="<?= htmlspecialchars($event['eventName']) ?>">Edit</button>
                            <a href="?page=events&delete=<?= $event['eventID'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure you want to delete this event?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Show the form when "Create Event" is clicked
    document.getElementById('showFormButton').addEventListener('click', function() {
        document.getElementById('eventForm').classList.add('active'); // Show form
        this.style.display = 'none'; // Hide the "Create Event" button
    });

    // Populate the form with event data for editing
    document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('eventID').value = button.dataset.id;
            document.getElementById('eventName').value = button.dataset.name;
            document.getElementById('submitButton').textContent = 'Update Event';
            document.getElementById('eventForm').classList.add('active'); // Show the form
            document.getElementById('showFormButton').style.display = 'none'; // Hide "Create Event" button
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
