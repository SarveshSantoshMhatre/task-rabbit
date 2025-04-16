<?php
session_start();
include_once "php/config.php";

if (!isset($_SESSION['unique_id']) || $_SESSION['user_type'] != 'user') {
    header("location: login.php");
    exit();
}

if (isset($_GET['bid_id']) && isset($_GET['task_id'])) {
    $bid_id = mysqli_real_escape_string($conn, $_GET['bid_id']);
    $task_id = mysqli_real_escape_string($conn, $_GET['task_id']);
    $user_id = $_SESSION['unique_id'];

    // Verify that the task belongs to the user and the bid is still pending
    $verify_sql = "SELECT b.status, t.user_id 
                   FROM bids b 
                   JOIN tasks t ON b.task_id = t.task_id 
                   WHERE b.bid_id = ? AND t.task_id = ? AND t.user_id = ?";
    
    $verify_stmt = mysqli_prepare($conn, $verify_sql);
    mysqli_stmt_bind_param($verify_stmt, "iii", $bid_id, $task_id, $user_id);
    mysqli_stmt_execute($verify_stmt);
    $verify_result = mysqli_stmt_get_result($verify_stmt);
    
    if ($verify_row = mysqli_fetch_assoc($verify_result)) {
        if ($verify_row['status'] == 'pending') {
            // Delete the bid
            $delete_sql = "DELETE FROM bids WHERE bid_id = ?";
            $delete_stmt = mysqli_prepare($conn, $delete_sql);
            mysqli_stmt_bind_param($delete_stmt, "i", $bid_id);
            
            if (mysqli_stmt_execute($delete_stmt)) {
                header("location: view_bids.php?task_id=" . $task_id . "&success=Bid deleted successfully");
            } else {
                header("location: view_bids.php?task_id=" . $task_id . "&error=Failed to delete bid");
            }
        } else {
            header("location: view_bids.php?task_id=" . $task_id . "&error=Cannot delete an accepted bid");
        }
    } else {
        header("location: view_bids.php?task_id=" . $task_id . "&error=Invalid bid or task");
    }
} else {
    header("location: tasks.php");
}
?> 