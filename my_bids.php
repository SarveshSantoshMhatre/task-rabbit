<?php
session_start();
include_once "php/config.php";

if (!isset($_SESSION['unique_id']) || $_SESSION['user_type'] != 'tasker') {
    header("location: login.php");
    exit();
}

$user_id = $_SESSION['unique_id'];

// Get all bids for the tasker
$sql = "SELECT b.*, t.*, u.fname, u.lname, u.img,
        CASE 
            WHEN t.status = 'completed' THEN 'completed'
            WHEN b.status = 'accepted' THEN 'in_progress'
            ELSE b.status
        END as bid_status
        FROM bids b
        JOIN tasks t ON b.task_id = t.task_id
        JOIN users u ON t.user_id = u.unique_id
        WHERE b.tasker_id = ?
        ORDER BY b.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bids - TaskRabbit</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php include_once "header.php"; ?>

    <div class="bids">
        <header>
            <h2>My Bids</h2>
            <div class="bid-filters">
                <select id="statusFilter">
                    <option value="">All Bids</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="in_progress">In Progress</option>
                    <option value="rejected">Rejected</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </header>
        
        <div class="bids-list">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="bid-card" data-status="<?php echo $row['bid_status']; ?>">
                        <div class="bid-header">
                            <div class="task-info">
                                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                <div class="task-details">
                                    <span><i class="fas fa-user"></i> Client: <?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></span>
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></span>
                                    <span><i class="fas fa-clock"></i> Posted: <?php echo date('M d, Y', strtotime($row['created_at'])); ?></span>
                                </div>
                            </div>
                            <div class="bid-status">
                                <span class="status <?php echo $row['bid_status']; ?>">
                                    <?php 
                                    switch($row['bid_status']) {
                                        case 'pending':
                                            echo 'Pending Review';
                                            break;
                                        case 'accepted':
                                        case 'in_progress':
                                            echo 'In Progress';
                                            break;
                                        case 'rejected':
                                            echo 'Not Selected';
                                            break;
                                        case 'completed':
                                            echo 'Completed';
                                            break;
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="bid-details">
                            <div class="bid-amount">
                                <h4>Your Bid</h4>
                                <p>Rs<?php echo number_format($row['amount'], 2); ?></p>
                            </div>
                            <div class="bid-message">
                                <h4>Your Message</h4>
                                <p><?php echo htmlspecialchars($row['message']); ?></p>
                            </div>
                        </div>
                        
                        <div class="bid-actions">
                            <?php if ($row['bid_status'] == 'in_progress'): ?>
                                <a href="chat.php?task_id=<?php echo $row['task_id']; ?>" class="chat-btn">
                                    <i class="fas fa-comments"></i> Chat with Client
                                </a>
                                <a href="complete_task.php?task_id=<?php echo $row['task_id']; ?>" class="complete-btn">
                                    <i class="fas fa-check"></i> Mark as Completed
                                </a>
                            <?php elseif ($row['bid_status'] == 'completed'): ?>
                                <span class="completed-text">
                                    <i class="fas fa-check-circle"></i> Task completed on <?php echo date('M d, Y', strtotime($row['completion_date'])); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-bids">
                    <i class="fas fa-gavel"></i>
                    <p>You haven't placed any bids yet.</p>
                    <a href="tasks.php" class="find-tasks-btn">Find Tasks to Bid On</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once "footer.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusFilter = document.getElementById('statusFilter');
            const bidCards = document.querySelectorAll('.bid-card');
            
            function filterBids() {
                const selectedStatus = statusFilter.value;
                bidCards.forEach(card => {
                    if (!selectedStatus || card.dataset.status === selectedStatus) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            if (statusFilter) {
                statusFilter.addEventListener('change', filterBids);
            }
        });
    </script>
</body>
</html> 