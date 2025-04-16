<?php
session_start();
include_once "php/config.php";

if (!isset($_SESSION['unique_id']) || $_SESSION['user_type'] != 'user') {
    header("location: login.php");
    exit();
}

if (isset($_GET['task_id'])) {
    $task_id = mysqli_real_escape_string($conn, $_GET['task_id']);
    $user_id = $_SESSION['unique_id'];

    // Verify that the task belongs to the user and is still open
    $verify_sql = "SELECT status FROM tasks WHERE task_id = ? AND user_id = ?";
    $verify_stmt = mysqli_prepare($conn, $verify_sql);
    mysqli_stmt_bind_param($verify_stmt, "ii", $task_id, $user_id);
    mysqli_stmt_execute($verify_stmt);
    $verify_result = mysqli_stmt_get_result($verify_stmt);
    
    if ($verify_row = mysqli_fetch_assoc($verify_result)) {
        if ($verify_row['status'] == 'open') {
            // Start transaction
            mysqli_begin_transaction($conn);
            
            try {
                // Delete all bids for this task
                $delete_bids_sql = "DELETE FROM bids WHERE task_id = ?";
                $delete_bids_stmt = mysqli_prepare($conn, $delete_bids_sql);
                mysqli_stmt_bind_param($delete_bids_stmt, "i", $task_id);
                mysqli_stmt_execute($delete_bids_stmt);
                
                // Delete the task
                $delete_task_sql = "DELETE FROM tasks WHERE task_id = ?";
                $delete_task_stmt = mysqli_prepare($conn, $delete_task_sql);
                mysqli_stmt_bind_param($delete_task_stmt, "i", $task_id);
                mysqli_stmt_execute($delete_task_stmt);
                
                // Commit transaction
                mysqli_commit($conn);
                header("location: tasks.php?success=Task deleted successfully");
            } catch (Exception $e) {
                // Rollback transaction on error
                mysqli_rollback($conn);
                header("location: tasks.php?error=Failed to delete task");
            }
        } else {
            header("location: tasks.php?error=Cannot delete a task that is not open");
        }
    } else {
        header("location: tasks.php?error=Invalid task or unauthorized access");
    }
} else {
    header("location: tasks.php");
}
?> 