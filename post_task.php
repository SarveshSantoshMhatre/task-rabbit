<?php
session_start();
include_once "php/config.php";

if (!isset($_SESSION['unique_id']) || $_SESSION['user_type'] != 'user') {
    header("location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $profession = mysqli_real_escape_string($conn, $_POST['profession']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $budget = floatval($_POST['budget']);
    $bid_deadline = !empty($_POST['bid_deadline']) ? $_POST['bid_deadline'] : null;
    $user_id = $_SESSION['unique_id'];

    // Validate inputs
    if (strlen($title) < 5) {
        $error = "Title must be at least 5 characters long";
    } elseif (strlen($description) < 20) {
        $error = "Description must be at least 20 characters long";
    } elseif ($budget <= 0) {
        $error = "Budget must be greater than 0";
    } else {
        $sql = "INSERT INTO tasks (user_id, title, description, profession, location, budget, bid_deadline) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issssds", $user_id, $title, $description, $profession, $location, $budget, $bid_deadline);
        
        if (mysqli_stmt_execute($stmt)) {
            header("location: tasks.php");
            exit();
        } else {
            $error = "Error posting task. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Task - TaskRabbit</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php include_once "header.php"; ?>

    <div class="wrapper">
        <section class="form post-task">
            <header>Post a New Task</header>
            <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off" id="postTaskForm">
                <div class="error-text" style="display: none; color: #d63031; background: rgba(214, 48, 49, 0.1); padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: 500; white-space: pre-line;"></div>
                
                <div class="field input">
                    <label>Task Title</label>
                    <input type="text" name="title" placeholder="Enter task title" required>
                </div>
                
                <div class="field input">
                    <label>Description</label>
                    <textarea name="description" placeholder="Describe your task in detail" required></textarea>
                </div>
                
                <div class="field input">
                    <label>Profession Required</label>
                    <select name="profession" required>
                        <option value="">Select Profession</option>
                        <option value="Carpenter">Carpenter</option>
                        <option value="Plumber">Plumber</option>
                        <option value="Electrician">Electrician</option>
                        <option value="Painter">Painter</option>
                        <option value="Handyman">Handyman</option>
                    </select>
                </div>
                
                <div class="field input">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="Enter task location" required>
                </div>
                
                <div class="field input">
                    <label>Budget (Rs)</label>
                    <input type="number" name="budget" placeholder="Enter your budget" min="0" step="0.01" required>
                </div>
                
                <div class="field button">
                    <input type="submit" value="Post Task">
                </div>
            </form>
        </section>
    </div>

    <?php include_once "footer.php"; ?>

    <script>
        const form = document.querySelector("#postTaskForm");
        const errorText = form.querySelector(".error-text");

        form.onsubmit = (e) => {
            e.preventDefault();
            
            // Clear any previous error messages
            errorText.style.display = "none";
            errorText.textContent = "";
            
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "php/post_task.php", true);
            xhr.onload = () => {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        try {
                            const data = JSON.parse(xhr.response);
                            if (data.status === 'error') {
                                errorText.style.display = "block";
                                errorText.textContent = data.message;
                            } else if (data.status === 'success') {
                                window.location.href = "tasks.php";
                            }
                        } catch (e) {
                            console.error("Error parsing response:", e);
                            errorText.style.display = "block";
                            errorText.textContent = "An error occurred. Please try again.";
                        }
                    } else {
                        errorText.style.display = "block";
                        errorText.textContent = "Server error. Please try again.";
                    }
                }
            }
            
            xhr.onerror = () => {
                errorText.style.display = "block";
                errorText.textContent = "Network error. Please check your connection.";
            }
            
            let formData = new FormData(form);
            xhr.send(formData);
        }
    </script>
</body>
</html> 