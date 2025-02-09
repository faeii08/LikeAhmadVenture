<?php
// Start session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="template/css/header.css">
    <title>Home - Like Ahmad Venture</title>
    <style>
        body {
    font-family: Lora, Open Sans;
    margin: 0;
    padding: 0;
    background-color: #686D76;
}

.hero {
    text-align: center;
    padding: 0;
    background-color: #f4f4f9;
    position: relative;
    height: 60vh; /* Ensure the div takes up the full viewport height */
}

.hero img {
    width: 100%;
    height: 100%;
    object-fit: cover; /* Ensures the image covers the entire div */
}

/* Features Section Styles */
.features-container {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    padding: 8px 20px;
}

.features-text {
    flex: 1;
    text-align: left;
}

.features-text h1 {
    font-size: 30px;
    color: #EEEEEE;
    margin: 20px 0;
}

.features-text p {
    font-size: 18px;
    color: #EEEEEE;
    margin: 0 0 30px;
}

.features-text .learn-more {
    padding: 10px 20px;
    background-color: #578FCA;
    color: #fff;
    text-decoration: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
}

.features-text .learn-more:hover {
    background-color: #3674B5;
}

.features {
    flex: 2;
    display: flex;
    justify-content: space-around;
    gap: 20px;
}

.feature {
    text-align: center;
    padding: 20px;
    background-color: #4E5258;
    border-radius: 8px;
    flex: 1;
    max-width: 300px;
}

.feature h3 {
    font-size: 20px;
    color: #578FCA;
    margin-bottom: 10px;
}

.feature p {
    font-size: 16px;
    color: #EEEEEE;
}

    </style>
</head>
<body>
    <?php include 'template/php/header.php'; ?> <!-- Include the header from header.php -->

    <div class="hero">
        <img src="images/homebg6.jpg" alt="Children Learning">
    </div>

    <div class="features-container">
        <div class="features-text">
            <h1>Let's Explore The World with Us</h1>
            <p>Sharing the knowledge of early childhood education according to an Islamic perspective and emphasizing the important aspects of education</p>
            <a href="about.php" class="learn-more">Learn more</a>
        </div>

        <div class="features">
            <div class="feature">
                <h3>Fardhu Ain</h3>
                <p>Offers lessons on basic Islamic knowledge, learning Iqra and the Quran, and exam preparation for UPKK and PSRA</p>
            </div>
            <div class="feature">
                <h3>School Holiday Program</h3>
                <p>Includes Quranic ventures, improving religious practices, and project-based learning like Q-STEAM</p>
            </div>
            <div class="feature">
                <h3>Short Course</h3>
                <p>Provides sessions on Al-Fatihah reflection, puberty education for boys and girls, and basic Tajweed lessons</p>
            </div>
        </div>
    </div>

</body>
</html>
