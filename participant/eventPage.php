<?php
// Database connection
include '../conn.php';

// Fetch all events
$sql = "SELECT * FROM events";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../template/css/header.css">
    <link rel="stylesheet" href="../template/css/eventPage.css">
    <title>Events - Like Ahmad Venture</title>
</head>
<body>

<?php include '../template/php/headerP.php'; ?> <!-- Include the header from header.php -->
    <h1>Explore Our Learning Events</h1>
    <div class="features-text">
        <h2>Unlock Your Potential</h2>
        <p>We believe in stress-free learning with a student-centered approach, ensuring full guidance and support from our expert educators.</p>
    </div>

    <div class="features-container">
        <div class="features">
            <?php
            while ($event = $result->fetch_assoc()) {
                // Use the 'img' field from the database to set the image source
                echo "<div class='feature'>";
                $basePath = "../organizer/";
                $imagePath = $basePath . $event['img'];
                echo "<img src='$imagePath' alt='events' style='height: 160px;' />";
                echo "<h3><a href='eventDetail.php?id=" . $event['eventID'] . "'>" . htmlspecialchars($event['eventName']) . "</a></h3>";
                echo "</div>";
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
