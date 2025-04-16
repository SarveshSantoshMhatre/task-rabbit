<?php
session_start();
include_once "php/config.php";

if (!isset($_SESSION['unique_id']) || $_SESSION['user_type'] != 'tasker') {
    header("location: login.php");
    exit();
}

$user_id = $_SESSION['unique_id'];

// Update the profession in the taskers table
$update_sql = "UPDATE taskers SET profession = 'Carpenter' WHERE tasker_id = ?";
$update_stmt = mysqli_prepare($conn, $update_sql);
mysqli_stmt_bind_param($update_stmt, "i", $user_id);

if (mysqli_stmt_execute($update_stmt)) {
    header("location: tasks.php?success=Profession updated successfully");
} else {
    header("location: tasks.php?error=Failed to update profession");
}
?> 