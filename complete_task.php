<?php
session_start();
include_once "php/config.php";

if (!isset($_SESSION['unique_id']) || $_SESSION['user_type'] != 'tasker') {
    header("location: login.php");
    exit();
}

if (!isset($_GET['task_id'])) {
    header("location: my_bids.php");
    exit();
}

$task_id = intval($_GET['task_id']);
$tasker_id = $_SESSION['unique_id'];

// Verify that the task is assigned to this tasker and is in progress
$sql = "SELECT t.*, b.bid_id 
        FROM tasks t 
        JOIN bids b ON t.task_id = b.task_id 
        WHERE t.task_id = ? 
        AND b.tasker_id = ? 
        AND b.status = 'accepted' 
        AND t.status = 'in_progress'";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $task_id, $tasker_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("location: my_bids.php");
    exit();
}

$task = mysqli_fetch_assoc($result);

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Update task status to completed
    $sql = "UPDATE tasks SET status = 'completed' WHERE task_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $task_id);
    mysqli_stmt_execute($stmt);

    // Update bid status to completed
    $sql = "UPDATE bids SET status = 'completed' WHERE task_id = ? AND tasker_id = ? AND status = 'accepted'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $task_id, $tasker_id);
    mysqli_stmt_execute($stmt);

    // Commit transaction
    mysqli_commit($conn);

    // Redirect back to my_bids.php with success message
    header("Location: my_bids.php?success=1");
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    header("Location: my_bids.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Task</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <section class="form complete-task">
            <header>Complete Task</header>
            <form action="#" method="POST" autocomplete="off">
                <?php if (isset($error)): ?>
                    <div class="error-text"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="field">
                    <p>Are you sure you want to mark this task as completed?</p>
                    <p>This action cannot be undone.</p>
                </div>
                
                <div class="field button">
                    <input type="submit" value="Mark as Completed">
                </div>
            </form>
        </section>
    </div>
</body>
</html> 