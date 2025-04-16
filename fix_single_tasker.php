<?php
include_once "php/config.php";

// Tasker ID to fix
$tasker_id = 1274659752;

// Check if tasker record exists
$check_sql = "SELECT * FROM taskers WHERE tasker_id = ?";
$check_stmt = mysqli_prepare($conn, $check_sql);
mysqli_stmt_bind_param($check_stmt, "i", $tasker_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_result) == 0) {
    // Insert new tasker record
    $insert_sql = "INSERT INTO taskers (tasker_id, profession, bio, skills, location, hourly_rate) 
                  VALUES (?, 'Carpenter', '', '', '', 0)";
    $insert_stmt = mysqli_prepare($conn, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "i", $tasker_id);
    mysqli_stmt_execute($insert_stmt);
    echo "Created tasker record for user ID: " . $tasker_id;
} else {
    // Update existing tasker record
    $update_sql = "UPDATE taskers SET profession = 'Carpenter' WHERE tasker_id = ?";
    $update_stmt = mysqli_prepare($conn, $update_sql);
    mysqli_stmt_bind_param($update_stmt, "i", $tasker_id);
    mysqli_stmt_execute($update_stmt);
    echo "Updated tasker record for user ID: " . $tasker_id;
}
?> 