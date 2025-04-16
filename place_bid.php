<?php
session_start();
include_once "php/config.php";

if (!isset($_SESSION['unique_id']) || $_SESSION['user_type'] != 'tasker') {
    header("location: login.php");
    exit();
}

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
$user_id = $_SESSION['unique_id'];

// Get task details
$sql = "SELECT t.*, u.fname, u.lname 
        FROM tasks t 
        JOIN users u ON t.user_id = u.unique_id 
        WHERE t.task_id = ? AND t.status = 'open' 
        AND (t.bid_deadline IS NULL OR t.bid_deadline > NOW())";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $task_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$task = mysqli_fetch_assoc($result);

if (!$task) {
    header("location: tasks.php");
    exit();
}

// Check if tasker has already placed a bid
$sql = "SELECT * FROM bids WHERE task_id = ? AND tasker_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    header("location: tasks.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = floatval($_POST['amount']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Debug: Log bid information
    error_log("Attempting to place bid - Task ID: " . $task_id);
    error_log("Tasker ID: " . $user_id);
    error_log("Amount: " . $amount);
    
    // Validate bid amount
    if ($amount <= 0) {
        $error = "Bid amount must be greater than 0";
    } elseif ($amount > $task['budget']) {
        $error = "Bid amount cannot exceed task budget";
    } else {
        $sql = "INSERT INTO bids (task_id, tasker_id, amount, message) 
                VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iids", $task_id, $user_id, $amount, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            // Debug: Log successful bid placement
            error_log("Bid placed successfully - Bid ID: " . mysqli_insert_id($conn));
            header("location: tasks.php");
            exit();
        } else {
            // Debug: Log error
            error_log("Error placing bid: " . mysqli_error($conn));
            $error = "Error placing bid. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Place Bid</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <section class="form place-bid">
            <header>Place Bid</header>
            <form action="#" method="POST" autocomplete="off">
                <?php if (isset($error)): ?>
                    <div class="error-text"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div class="task-details">
                    <h3><?php echo $task['title']; ?></h3>
                    <p><?php echo $task['description']; ?></p>
                    <div class="task-info">
                        <span><i class="fas fa-user"></i> Posted by: <?php echo $task['fname'] . ' ' . $task['lname']; ?></span>
                        <span><i class="fas fa-tag"></i> <?php echo $task['profession']; ?></span>
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo $task['location']; ?></span>
                        <span><i class="fas fa-dollar-sign"></i> Budget: <?php echo number_format($task['budget'], 2); ?></span>
                        <?php if ($task['bid_deadline']): ?>
                            <span><i class="fas fa-clock"></i> Bids due: <?php echo date('M d, Y H:i', strtotime($task['bid_deadline'])); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="field input">
                    <label>Your Bid Amount ($)</label>
                    <input type="number" name="amount" placeholder="Enter your bid amount" min="0.01" step="0.01" max="<?php echo $task['budget']; ?>" required>
                </div>
                
                <div class="field input">
                    <label>Message to Client</label>
                    <textarea name="message" placeholder="Explain why you're the best fit for this task" required></textarea>
                </div>
                
                <div class="field button">
                    <input type="submit" value="Place Bid">
                </div>
            </form>
        </section>
    </div>
</body>
</html> 