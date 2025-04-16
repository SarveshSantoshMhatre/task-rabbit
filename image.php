<?php
session_start();
include_once "config.php";

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT img FROM users WHERE unique_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        // Decode the base64 image data
        $img_data = base64_decode($row['img']);
        
        // Set appropriate headers
        header("Content-Type: image/jpeg");
        header("Content-Length: " . strlen($img_data));
        
        // Output the image data
        echo $img_data;
    }
}
exit();
?> 