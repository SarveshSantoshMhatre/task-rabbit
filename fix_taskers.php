<?php
session_start();
include_once "php/config.php";

// This is an admin script, so we'll add some basic security
if (!isset($_SESSION['unique_id'])) {
    die("Unauthorized access");
}

// Get all taskers that need fixing
$sql = "SELECT u.unique_id, u.user_type, t.tasker_id 
        FROM users u 
        LEFT JOIN taskers t ON u.unique_id = t.tasker_id 
        WHERE u.user_type = 'tasker' 
        AND (t.tasker_id IS NULL OR t.profession IS NULL)";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "Found " . mysqli_num_rows($result) . " taskers that need fixing.<br>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        // Check if tasker record exists
        $check_sql = "SELECT * FROM taskers WHERE tasker_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "i", $row['unique_id']);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($check_result) == 0) {
            // Insert new tasker record
            $insert_sql = "INSERT INTO taskers (tasker_id, profession, bio, skills, location, hourly_rate) 
                          VALUES (?, 'Carpenter', '', '', '', 0)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "i", $row['unique_id']);
            mysqli_stmt_execute($insert_stmt);
            echo "Created tasker record for user ID: " . $row['unique_id'] . "<br>";
        } else {
            // Update existing tasker record
            $update_sql = "UPDATE taskers SET profession = 'Carpenter' WHERE tasker_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "i", $row['unique_id']);
            mysqli_stmt_execute($update_stmt);
            echo "Updated tasker record for user ID: " . $row['unique_id'] . "<br>";
        }
    }
    echo "Tasker records have been fixed.";
} else {
    echo "No taskers need fixing.";
}
?> 