<?php
// Define an asset function if you need to generate URLs for assets
function asset($path) {
    return $path; // Update this function to return the full URL/path as needed
}

// Get the current page from the URL, default to 'dashboard'
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="../template/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .sidebar {
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #373A40;
            padding: 20px;
            color: #fff;
        }

        .sidebar h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #ffc107;
        }

        .sidebar a {
            display: block;
            color: #ddd;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a.active {
            background-color: #007bff;
            color: #fff;
        }

        .sidebar .submenu {
            padding-left: 20px;
            margin-top: 5px;
        }

        .submenu a {
            font-size: 14px;
        }

        .main-content {
            margin-left: 270px;
            padding: 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h1>Admin Dashboard</h1>
    <a href="?page=dashboard" class="<?= $current_page === 'dashboard' ? 'active' : '' ?>">
        <i class="fas fa-home">&nbsp;&nbsp;&nbsp;</i> Dashboard
    </a>

    <!-- Profile Section with Submenu -->
    <a href="#profileMenu" data-bs-toggle="collapse" aria-expanded="false">
        <i class="bi bi-person-lines-fill">&nbsp;&nbsp;&nbsp;</i> Profile
    </a>
    <div class="submenu collapse" id="profileMenu">
        <a href="?page=myProfile" class="<?= $current_page === 'myProfile' ? 'active' : '' ?>">
            <i class="fas fa-user">&nbsp;&nbsp;&nbsp;</i> My Profile
        </a>
        <a href="?page=organizer" class="<?= $current_page === 'organizer' ? 'active' : '' ?>">
            <i class="fas fa-users">&nbsp;&nbsp;&nbsp;</i> Admin
        </a>
    </div>

    <a href="#eventMenu" data-bs-toggle="collapse" aria-expanded="false">
        <i class="fas fa-calendar-alt">&nbsp;&nbsp;&nbsp;</i> Event
    </a>
    <div class="submenu collapse" id="eventMenu">
        <a href="?page=events" class="<?= $current_page === 'events' ? 'active' : '' ?>">
            <i class="fas fa-calendar">&nbsp;&nbsp;&nbsp;</i> Category
        </a>
        <a href="?page=categories" class="<?= $current_page === 'categories' ? 'active' : '' ?>">
            <i class="fas fa-tags">&nbsp;&nbsp;&nbsp;</i> Events
        </a>
    </div>

    <a href="?page=payment" class="<?= $current_page === 'payment' ? 'active' : '' ?>">
        <i class="fa fa-credit-card-alt"></i>&nbsp;&nbsp;&nbsp;</i> Payment
    </a>

    <a href="?page=report" class="<?= $current_page === 'report' ? 'active' : '' ?>">
        <i class="fas fa-home">&nbsp;&nbsp;&nbsp;</i> Report
    </a>

    <a href="../logout.php">
    <i class="fa fa-sign-out">&nbsp;&nbsp;&nbsp;</i> Logout
    </a>

    
</div>

<!-- Main Content Area -->
<div class="main-content">
    <?php
    // Load the corresponding content based on the current page
    switch ($current_page) {
        case 'events':
            include_once('events.php'); 
            break;
        case 'categories':
            include_once('categories.php');
            break;
        case 'myProfile':
            include_once('myProfile.php'); 
            break;
        case 'organizer':
            include_once('organizer.php');
            break;
        case 'report':
            include_once('view-report.php');
            break;
        case 'payment':
            include_once('view-payment.php');
            break;
        case 'dashboard':
        default:
            include_once('dashboard.php');
            break;
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
