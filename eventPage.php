<?php
// Database connection
include 'conn.php';

// Fetch unique events
$sql = "SELECT DISTINCT * FROM events";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Like Ahmad Venture</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="template/css/header.css">
    <link rel="stylesheet" href="template/css/eventPage.css">
</head>
<body>

<?php include 'template/php/header.php'; ?> <!-- Include the header from header.php -->

<h1>Explore Our Learning Events</h1>

<div class="features-container">
    <div class="features">
        <?php
        if ($result->num_rows > 0) {
            while ($event = $result->fetch_assoc()) {
                $basePath = "organizer/";
                $imagePath = !empty($event['img']) ? $basePath . $event['img'] : "default-placeholder.jpg";

                echo "<div class='feature'>";
                echo "<img src='$imagePath' alt='Event Image' />";
                echo "<h3><a href='eventDetail.php?id=" . htmlspecialchars($event['eventID']) . "'>" . htmlspecialchars($event['eventName']) . "</a></h3>";
                
                // Display description if available
                if (!empty($event['description'])) {
                    echo "<p>" . htmlspecialchars($event['description']) . "</p>";
                }

                echo "</div>";
            }
        } else {
            echo "<p class='no-events'>No events available at the moment. Stay tuned for updates!</p>";
        }
        ?>
    </div>
</div>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
