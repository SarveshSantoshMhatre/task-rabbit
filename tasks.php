<?php
session_start();
include_once "php/config.php";

if (!isset($_SESSION['unique_id'])) {
    header("location: login.php");
    exit();
}

$user_id = $_SESSION['unique_id'];
$user_type = $_SESSION['user_type'];

// Get tasks based on user type
if ($user_type == 'user') {
    $sql = "SELECT t.*, u.fname, u.lname, u.img 
            FROM tasks t 
            JOIN users u ON t.user_id = u.unique_id 
            WHERE t.user_id = ? 
            ORDER BY t.created_at DESC";
} else {
    // Get tasker's profession
    $profession_sql = "SELECT profession FROM taskers WHERE tasker_id = ?";
    $profession_stmt = mysqli_prepare($conn, $profession_sql);
    mysqli_stmt_bind_param($profession_stmt, "i", $user_id);
    mysqli_stmt_execute($profession_stmt);
    $profession_result = mysqli_stmt_get_result($profession_stmt);
    $profession_row = mysqli_fetch_assoc($profession_result);
    
    // Debug information
    echo "<!-- Debug: Tasker Profession: " . ($profession_row['profession'] ?? 'not set') . " -->";
    echo "<!-- Debug: Tasker ID: " . $user_id . " -->";
    
    // Check all tasks in the database
    $all_tasks_sql = "SELECT * FROM tasks";
    $all_tasks_result = mysqli_query($conn, $all_tasks_sql);
    echo "<!-- Debug: Total tasks in database: " . mysqli_num_rows($all_tasks_result) . " -->";
    
    // If profession is not set, show all open tasks
    if (empty($profession_row['profession'])) {
        $sql = "SELECT t.*, u.fname, u.lname, u.img 
                FROM tasks t 
                JOIN users u ON t.user_id = u.unique_id 
                WHERE t.status = 'open'
                AND t.user_id != ?
                ORDER BY t.created_at DESC";
    } else {
        $sql = "SELECT t.*, u.fname, u.lname, u.img 
                FROM tasks t 
                JOIN users u ON t.user_id = u.unique_id 
                WHERE t.profession = ?
                AND t.status = 'open'
                AND t.user_id != ?
                ORDER BY t.created_at DESC";
    }
    
    // Debug: Log the SQL query
    error_log("SQL Query: " . $sql);
    error_log("Profession: " . ($profession_row['profession'] ?? 'not set'));
    error_log("User ID: " . $user_id);
}

$stmt = mysqli_prepare($conn, $sql);
if ($user_type == 'user') {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
} else {
    if (empty($profession_row['profession'])) {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
    } else {
        mysqli_stmt_bind_param($stmt, "si", $profession_row['profession'], $user_id);
    }
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Get all tasks for which the current tasker has placed bids
$bids_sql = "SELECT task_id FROM bids WHERE tasker_id = ?";
$bids_stmt = mysqli_prepare($conn, $bids_sql);
mysqli_stmt_bind_param($bids_stmt, "i", $user_id);
mysqli_stmt_execute($bids_stmt);
$bids_result = mysqli_stmt_get_result($bids_stmt);
$bids = array();
while ($bid = mysqli_fetch_assoc($bids_result)) {
    $bids[] = $bid['task_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - TaskRabbit</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php include_once "header.php"; ?>

    <div class="tasks">
        <header>
            <h2><?php echo $user_type == 'user' ? 'My Tasks' : 'Available Tasks'; ?></h2>
            <?php if ($user_type == 'user'): ?>
                <a href="post_task.php" class="post-task-btn">
                    <i class="fas fa-plus"></i> Post New Task
                </a>
            <?php endif; ?>
        </header>

        <div class="task-filters">
            <select id="status-filter">
                <option value="all">All Status</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
            </select>
        </div>

        <div class="task-list">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="task-card" data-status="<?php echo $row['status']; ?>">
                        <div class="task-header">
                            <div class="task-info">
                                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                <div class="task-details">
                                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></span>
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></span>
                                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($row['profession']); ?></span>
                                    <span><i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                                </div>
                            </div>
                            <div class="task-status">
                                <span class="status <?php echo $row['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="task-description">
                            <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                        </div>
                        
                        <div class="task-footer">
                            <div class="task-budget">
                                <h4>Budget</h4>
                                <p>Rs<?php echo number_format($row['budget'], 2); ?></p>
                            </div>
                            
                            <div class="task-actions">
                                <?php if ($user_type == 'user'): ?>
                                    <?php if ($row['status'] == 'open'): ?>
                                        <a href="view_bids.php?task_id=<?php echo $row['task_id']; ?>" class="view-bids-btn">
                                            <i class="fas fa-gavel"></i> View Bids
                                        </a>
                                        <a href="delete_task.php?task_id=<?php echo $row['task_id']; ?>" class="delete-task-btn" onclick="return confirm('Are you sure you want to delete this task? This action cannot be undone.');">
                                            <i class="fas fa-trash"></i> Delete Task
                                        </a>
                                    <?php elseif ($row['status'] == 'in_progress'): ?>
                                        <a href="chat.php?task_id=<?php echo $row['task_id']; ?>" class="chat-btn">
                                            <i class="fas fa-comments"></i> Chat
                                        </a>
                                    <?php elseif ($row['status'] == 'completed'): ?>
                                        <?php
                                        // Check if user has already rated this task
                                        $check_rating = "SELECT rating_id FROM ratings WHERE task_id = ? AND user_id = ?";
                                        $stmt = mysqli_prepare($conn, $check_rating);
                                        mysqli_stmt_bind_param($stmt, "ii", $row['task_id'], $user_id);
                                        mysqli_stmt_execute($stmt);
                                        $rating_result = mysqli_stmt_get_result($stmt);
                                        
                                        if (mysqli_num_rows($rating_result) == 0):
                                        ?>
                                            <a href="view_bids.php?task_id=<?php echo $row['task_id']; ?>" class="rate-btn">
                                                <i class="fas fa-star"></i> Rate Me
                                            </a>
                                        <?php else: ?>
                                            <span class="rated-text">
                                                <i class="fas fa-check-circle"></i> Rated
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php if ($row['status'] == 'open' && !in_array($row['task_id'], $bids)): ?>
                                        <a href="place_bid.php?task_id=<?php echo $row['task_id']; ?>" class="bid-btn">
                                            <i class="fas fa-hand-holding-usd"></i> Place Bid
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-tasks">
                    <i class="fas fa-tasks"></i>
                    <p><?php echo $user_type == 'user' ? 'You haven\'t posted any tasks yet.' : 'No tasks available in your profession.'; ?></p>
                    <?php if ($user_type == 'user'): ?>
                        <a href="post_task.php" class="post-task-btn">Post Your First Task</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once "footer.php"; ?>

    <script>
        // Status filter functionality
        document.getElementById('status-filter').addEventListener('change', function() {
            const status = this.value;
            const taskCards = document.querySelectorAll('.task-card');
            
            taskCards.forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 