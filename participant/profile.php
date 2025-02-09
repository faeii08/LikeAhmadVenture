<?php
// Include necessary files for database connection and session management
include '../conn.php';
session_start();

// Get userID from session
$userID = isset($_SESSION['userID']) ? $_SESSION['userID'] : null;

// Check if the user is logged in
if (!$userID) {
    header("Location: login.php");
    exit();
}

// Fetch participant details for the logged-in user
$sql = "SELECT * FROM users WHERE userID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$participantResult = $stmt->get_result();

// Check if participant exists
if ($participantResult->num_rows == 0) {
    echo "<p class='error-message'>No participant found for the current user.</p>";
    exit();
}

// Fetch participant data
$participant = $participantResult->fetch_assoc();

// Fetch students under the same userID
$students = [];
$studentSql = "SELECT * FROM students WHERE userID = ?";
$studentStmt = $conn->prepare($studentSql);
$studentStmt->bind_param("i", $userID);
$studentStmt->execute();
$studentsResult = $studentStmt->get_result();

while ($row = $studentsResult->fetch_assoc()) {
    $students[] = $row;
}

// Handle profile and student updates
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update User Profile
    if (isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['address'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];

        $updateSql = "UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE userID = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssssi", $name, $email, $phone, $address, $userID);
        $updateStmt->execute();
    }

    // Handle Student Insert/Update
    if (isset($_POST['students'])) {
        foreach ($_POST['students'] as $student) {
            $studentName = trim($student['name']);
            $studentAge = intval($student['age']);
            $studentId = isset($student['id']) ? intval($student['id']) : null;

            if (!empty($studentName) && $studentAge > 0) {
                if ($studentId) {
                    // Update existing student
                    $updateStudentSql = "UPDATE students SET pName = ?, age = ? WHERE id = ? AND userID = ?";
                    $updateStudentStmt = $conn->prepare($updateStudentSql);
                    $updateStudentStmt->bind_param("siii", $studentName, $studentAge, $studentId, $userID);
                    $updateStudentStmt->execute();
                } else {
                    // Insert new student
                    $insertStudentSql = "INSERT INTO students (userID, pName, age) VALUES (?, ?, ?)";
                    $insertStudentStmt = $conn->prepare($insertStudentSql);
                    $insertStudentStmt->bind_param("isi", $userID, $studentName, $studentAge);
                    $insertStudentStmt->execute();
                }
            }
        }
    }

    // Handle Student Deletion
    if (isset($_POST['delete_student'])) {
        $studentId = intval($_POST['delete_student']);
        $deleteSql = "DELETE FROM students WHERE id = ? AND userID = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("ii", $studentId, $userID);
        $deleteStmt->execute();
    }

    // Reload the page to show updated data
    header("Location: profile.php");
    exit();
}

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Profile</title>
    <link rel="stylesheet" href="../template/css/header.css">
    <style>
        body {
            font-family: Lora, Open Sans;
            background-color: #686D76;
            margin: 0;
            padding: 0;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            width: 100%;
            padding: 40px;
            background-color: transparent;
        }
        h1 {
            text-align: center;
            color: #EEEEEE;
        }
        .profile-info, .edit-form {
            width: 50%;
            margin: 30px auto;
            background-color: #4E5258;
            padding: 20px;
            border-radius: 8px;
            
        }
        .profile-info p, label {
            font-size: 18px;
            /* color: #EEEEEE; */
        }
        input, textarea {
            width: 95%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 6px;
            font-size: 15px;
        }
        button {
            /* width: 44%; */
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .update-btn {
            background-color: #4CAF50;
            color: white;
            width: 50%;
        }
        .cancel-btn {
            background-color: #FF5733;
            color: white;
            width: 49%;
        }
        .profile-info .edit-btn {
            background-color: #FF9D23;
            color: white;
            /* margin-left: 28%; */
            width: 100%;
        }
        .success-message, .error-message {
            text-align: center;
            margin-top: 10px;
            font-size: 16px;
        }
        .success-message {
            color: green;
        }
        .error-message {
            color: #D8000C;
            background-color: #FFD2D2;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #D8000C;
        }
        .hidden {
            display: none;
        }
        .students-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .student-item {
            background-color: #f3f3f3;
            padding: 10px;
            border-radius: 5px;
        }
        .add-btn {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: block; /* Make it a block element */
            margin-left: auto; /* Push to the right */
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .student-entry {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
    <?php include '../template/php/headerP.php'; ?>

    <div class="container">
        <div class="profile-info" id="profileInfo">
            <?php echo $message; ?>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($participant['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($participant['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($participant['phone']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($participant['address']); ?></p>

            <div class="student-list">
                <h2>Childrens Under Your Profile</h2>
                <?php if (!empty($students)) { ?>
                    <div class="students-container">
                        <?php foreach ($students as $student) { ?>
                            <div class="student-item">
                                <strong><?php echo htmlspecialchars($student['pName']); ?></strong> - <?php echo htmlspecialchars($student['age']); ?> years old
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <p>No students found under your profile.</p>
                <?php } ?>
            </div>
            <br>
            <button class="edit-btn" id="editBtn" onclick="toggleEditForm()">Edit Profile</button>
        </div>

        <form action="profile.php" method="POST" class="edit-form hidden" id="editForm">
            <div>
                <label for="name">Name</label><br>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($participant['name']); ?>" required>
                <br>

                <label for="email">Email</label><br>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($participant['email']); ?>" required>
                <br>

                <label for="phone">Phone</label><br>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($participant['phone']); ?>" required>
                <br>

                <label for="address">Address</label><br>
                <textarea name="address" id="address" required><?php echo htmlspecialchars($participant['address']); ?></textarea><br>
            </div>
    <div>
        <h3 style="font-size: 20px">Manage Students</h3>

        <button type="button" class="add-btn" onclick="addStudent()">Add Student</button>

    <br><br>

    <div id="studentList">
        <?php if (!empty($students)) { 
            foreach ($students as $index => $student) { ?>
                <div class="student-entry">
                    <input type="hidden" name="students[<?php echo $index; ?>][id]" value="<?php echo $student['id']; ?>">
                    <label for="student_name_<?php echo $index; ?>">Student Name</label><br>
                    <input type="text" name="students[<?php echo $index; ?>][name]" id="student_name_<?php echo $index; ?>" value="<?php echo htmlspecialchars($student['pName']); ?>" required>
                    <br>
                    
                    <label for="student_age_<?php echo $index; ?>">Student Age</label><br>
                    <input type="number" name="students[<?php echo $index; ?>][age]" id="student_age_<?php echo $index; ?>" value="<?php echo htmlspecialchars($student['age']); ?>" min="2" max="17" required>
                    <br>

                    <button type="submit" name="delete_student" value="<?php echo $student['id']; ?>" class="delete-btn">Delete</button>
                </div>
                <br>
        <?php } 
        } else { ?>
            <p>No students found under your profile.</p>
        <?php } ?>
    </div>

    </div>
    
    <button type="submit" class="update-btn">Update Profile</button>
    <button type="button" class="cancel-btn" onclick="cancelEdit()">Cancel</button>
</form>
    </div>

    <script>
        function toggleEditForm() {
            document.getElementById('profileInfo').classList.add('hidden');
            document.getElementById('editBtn').classList.add('hidden');
            document.getElementById('editForm').classList.remove('hidden');
        }
        function cancelEdit() {
            document.getElementById('profileInfo').classList.remove('hidden');
            document.getElementById('editBtn').classList.remove('hidden');
            document.getElementById('editForm').classList.add('hidden');
        }
        function addStudent() {
        let studentList = document.getElementById("studentList");
        let index = studentList.children.length; 

        let studentEntry = document.createElement("div");
        studentEntry.classList.add("student-entry");
        studentEntry.innerHTML = `
            <label for="student_name_${index}">Student Name</label><br>
            <input type="text" name="students[${index}][name]" id="student_name_${index}" required><br>
            
            <label for="student_age_${index}">Student Age</label><br>
            <input type="number" name="students[${index}][age]" id="student_age_${index}" min="2" max="17" required><br>

            <button type="button" class="delete-btn" onclick="this.parentElement.remove()">Delete</button>
            <br><br>
        `;
        studentList.appendChild(studentEntry);
    }
    document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener("input", function () {
            if (this.value < 2) {
                this.value = 2;
            } else if (this.value > 17) {
                this.value = 17;
            }
        });
    });
});
    </script>
</body>
</html>
