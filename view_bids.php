<?php
session_start();
include_once "php/config.php";

if (!isset($_SESSION['unique_id']) || $_SESSION['user_type'] != 'user') {
    header("location: login.php");
    exit();
}

if (!isset($_GET['task_id'])) {
    header("location: tasks.php");
    exit();
}

$task_id = intval($_GET['task_id']);
$user_id = $_SESSION['unique_id'];

// Verify that the task belongs to the current user
$sql = "SELECT * FROM tasks WHERE task_id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    header("location: tasks.php");
    exit();
}

$task = mysqli_fetch_assoc($result);

// Debug: Log task information
error_log("Task ID: " . $task_id);
error_log("User ID: " . $user_id);
error_log("Task Status: " . $task['status']);

// First, check if there are any bids for this task
$check_bids_sql = "SELECT COUNT(*) as bid_count FROM bids WHERE task_id = ?";
$check_stmt = mysqli_prepare($conn, $check_bids_sql);
mysqli_stmt_bind_param($check_stmt, "i", $task_id);
mysqli_stmt_execute($check_stmt);
$check_result = mysqli_stmt_get_result($check_stmt);
$bid_count = mysqli_fetch_assoc($check_result)['bid_count'];

error_log("Total bids for task: " . $bid_count);

// Get all bids for this task with detailed information
$sql = "SELECT b.*, u.fname, u.lname, u.img, u.unique_id, t.profession,
        (SELECT AVG(rating) FROM ratings WHERE tasker_id = u.unique_id) as avg_rating,
        (SELECT rating FROM ratings WHERE task_id = b.task_id AND user_id = ?) as user_rating
        FROM bids b
        INNER JOIN users u ON b.tasker_id = u.unique_id
        INNER JOIN taskers t ON b.tasker_id = t.tasker_id
        WHERE b.task_id = ?
        ORDER BY b.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $user_id, $task_id);
mysqli_stmt_execute($stmt);
$bids_result = mysqli_stmt_get_result($stmt);

// Debug: Log number of bids found
error_log("Number of bids found with details: " . mysqli_num_rows($bids_result));

// Debug: Log each bid found with all details
while ($bid = mysqli_fetch_assoc($bids_result)) {
    error_log("Bid ID: " . $bid['bid_id'] . 
              ", Tasker ID: " . $bid['tasker_id'] . 
              ", Bid Status: " . $bid['status'] . 
              ", Tasker Name: " . ($bid['fname'] ?? 'Unknown') . " " . ($bid['lname'] ?? ''));
}
mysqli_data_seek($bids_result, 0); // Reset the result pointer

// Debug: Check if there are any issues with the joins
$debug_sql = "SELECT b.*, u.unique_id as user_unique_id, t.tasker_id as tasker_tasker_id 
              FROM bids b 
              LEFT JOIN users u ON b.tasker_id = u.unique_id 
              LEFT JOIN taskers t ON b.tasker_id = t.tasker_id 
              WHERE b.task_id = ?";
$debug_stmt = mysqli_prepare($conn, $debug_sql);
mysqli_stmt_bind_param($debug_stmt, "i", $task_id);
mysqli_stmt_execute($debug_stmt);
$debug_result = mysqli_stmt_get_result($debug_stmt);

while ($row = mysqli_fetch_assoc($debug_result)) {
    error_log("Debug Join Check - Bid ID: " . $row['bid_id'] . 
              ", User Unique ID: " . $row['user_unique_id'] . 
              ", Tasker ID: " . $row['tasker_tasker_id']);
}

// Remove the setInterval for frequent refresh
// Only refresh when a bid is accepted or rejected
if(isset($_GET['refresh']) && $_GET['refresh'] == 'true') {
    header("Location: view_bids.php?task_id=" . $task_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Bids - TaskRabbit</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .view-bids {
            max-width: 1200px;
            margin: 80px auto 50px;
            padding: 20px;
        }

        .view-bids header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--light-gray);
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--primary-color);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .back-btn:hover {
            background: var(--secondary-color);
            color: white;
            text-decoration: none;
        }

        .task-info {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }

        .task-details {
            display: grid;
            gap: 15px;
        }

        .task-details p {
            margin: 0;
            line-height: 1.6;
        }

        .task-details strong {
            color: var(--text-color);
            font-weight: 600;
        }

        .bids-list {
            display: grid;
            gap: 20px;
        }

        .bid-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--shadow);
            transition: transform 0.3s;
        }

        .bid-card:hover {
            transform: translateY(-2px);
        }

        .bid-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .tasker-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .tasker-info img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }

        .tasker-info h3 {
            margin: 0;
            color: var(--text-color);
            font-size: 18px;
        }

        .profession {
            color: var(--primary-color);
            font-size: 14px;
            margin: 5px 0;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #ffc107;
        }

        .bid-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.accepted {
            background: #d4edda;
            color: #155724;
        }

        .status.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .bid-details {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 20px;
        }

        .bid-amount, .bid-message {
            background: var(--light-gray);
            padding: 15px;
            border-radius: 8px;
        }

        .bid-amount h4, .bid-message h4 {
            margin: 0 0 10px 0;
            color: var(--text-color);
            font-size: 16px;
        }

        .bid-amount p {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0;
        }

        .bid-message p {
            margin: 0;
            line-height: 1.6;
            color: var(--text-color);
        }

        .bid-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .accept-btn, .delete-btn, .chat-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .accept-btn {
            background: var(--primary-color);
            color: white;
        }

        .accept-btn:hover {
            background: var(--secondary-color);
            color: white;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background: #c82333;
            color: white;
        }

        .chat-btn {
            background: var(--light-gray);
            color: var(--text-color);
        }

        .chat-btn:hover {
            background: #e2e6ea;
            color: var(--text-color);
        }

        .no-bids {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .no-bids i {
            font-size: 48px;
            color: var(--light-gray);
            margin-bottom: 20px;
        }

        .no-bids p {
            color: var(--text-color);
            font-size: 18px;
            margin: 0;
        }

        .debug-info {
            display: none; /* Hide debug info in production */
        }
    </style>
</head>
<body>
    <?php include_once "header.php"; ?>

    <div class="view-bids">
        <header>
            <h2>Bids for: <?php echo htmlspecialchars($task['title']); ?></h2>
            <a href="tasks.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Tasks
            </a>
        </header>

        <div class="task-info">
            <div class="task-details">
                <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($task['location']); ?></p>
                <p><strong>Budget:</strong> Rs<?php echo number_format($task['budget'], 2); ?></p>
                <p><strong>Status:</strong> <span class="status <?php echo $task['status']; ?>"><?php echo ucfirst($task['status']); ?></span></p>
                
                <!-- Debug Information -->
                <div class="debug-info" style="background: #f8f9fa; padding: 15px; margin-top: 15px; border-radius: 8px;">
                    <h4>Debug Information:</h4>
                    <p>Task Status: <?php echo $task['status']; ?></p>
                    <p>Task ID: <?php echo $task_id; ?></p>
                    <p>User ID: <?php echo $user_id; ?></p>
                    <p>Number of Bids: <?php echo mysqli_num_rows($bids_result); ?></p>
                    <?php if (mysqli_num_rows($bids_result) > 0): ?>
                        <?php while ($bid = mysqli_fetch_assoc($bids_result)): ?>
                            <p>Bid Status: <?php echo $bid['status']; ?></p>
                            <p>Tasker ID: <?php echo $bid['tasker_id']; ?></p>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="bids-list">
            <?php 
            // Debug: Log the bids result
            error_log("Number of bids to display: " . mysqli_num_rows($bids_result));
            
            if (mysqli_num_rows($bids_result) > 0): 
                mysqli_data_seek($bids_result, 0); // Reset the pointer
                while ($bid = mysqli_fetch_assoc($bids_result)): 
                    // Debug: Log each bid being displayed
                    error_log("Displaying bid - ID: " . $bid['bid_id'] . ", Tasker: " . ($bid['fname'] ?? 'Unknown') . " " . ($bid['lname'] ?? ''));
            ?>
                    <div class="bid-card" data-status="<?php echo $bid['status']; ?>">
                        <div class="bid-header">
                            <div class="tasker-info">
                                <img src="php/image.php?id=<?php echo htmlspecialchars($bid['tasker_id']); ?>" alt="Tasker Image" class="tasker-image">
                                <div>
                                    <h3><?php echo htmlspecialchars(($bid['fname'] ?? 'Unknown') . ' ' . ($bid['lname'] ?? '')); ?></h3>
                                    <p class="profession"><?php echo htmlspecialchars($bid['profession'] ?? 'No profession set'); ?></p>
                                    <?php if ($bid['avg_rating']): ?>
                                        <div class="rating">
                                            <i class="fas fa-star"></i>
                                            <span><?php echo number_format($bid['avg_rating'], 1); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="bid-status status <?php echo $bid['status']; ?>">
                                <?php echo ucfirst($bid['status']); ?>
                            </div>
                        </div>

                        <div class="bid-details">
                            <div class="bid-amount">
                                <h4>Bid Amount</h4>
                                <p>Rs<?php echo number_format($bid['amount'], 2); ?></p>
                            </div>
                            <div class="bid-message">
                                <h4>Message</h4>
                                <p><?php echo nl2br(htmlspecialchars($bid['message'])); ?></p>
                            </div>
                        </div>

                        <div class="bid-actions">
                            <?php if ($task['status'] == 'open' && $bid['status'] == 'pending'): ?>
                                <form action="php/accept_bid.php" method="POST" class="accept-bid-form">
                                    <input type="hidden" name="bid_id" value="<?php echo $bid['bid_id']; ?>">
                                    <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                                    <button type="submit" class="accept-btn">
                                        <i class="fas fa-check"></i> Accept Bid
                                    </button>
                                </form>
                                <a href="delete_bid.php?bid_id=<?php echo $bid['bid_id']; ?>&task_id=<?php echo $task_id; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this bid?');">
                                    <i class="fas fa-trash"></i> Delete Bid
                                </a>
                            <?php elseif ($task['status'] == 'completed' && $bid['status'] == 'completed'): ?>
                                <div class="rating-section">
                                    <h3>Rate Your Experience</h3>
                                    <?php 
                                    // Debug information
                                    echo "<!-- Debug: Task Status: " . $task['status'] . " -->";
                                    echo "<!-- Debug: User ID: " . $user_id . " -->";
                                    echo "<!-- Debug: Task ID: " . $task_id . " -->";
                                    echo "<!-- Debug: Bid Status: " . $bid['status'] . " -->";
                                    echo "<!-- Debug: Tasker ID: " . $bid['tasker_id'] . " -->";
                                    
                                    // Check if user has already rated this task
                                    $check_rating = "SELECT rating_id FROM ratings WHERE task_id = ? AND user_id = ?";
                                    $stmt = mysqli_prepare($conn, $check_rating);
                                    mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
                                    mysqli_stmt_execute($stmt);
                                    $rating_result = mysqli_stmt_get_result($stmt);
                                    
                                    if (mysqli_num_rows($rating_result) == 0): 
                                    ?>
                                        <form action="php/submit_rating.php" method="POST" class="rating-form">
                                            <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                                            <input type="hidden" name="tasker_id" value="<?php echo $bid['tasker_id']; ?>">
                                            <div class="rating-input">
                                                <label>How would you rate this tasker?</label>
                                                <div class="star-rating">
                                                    <input type="radio" name="rating" value="5" id="star5" required>
                                                    <label for="star5"><i class="fas fa-star"></i></label>
                                                    <input type="radio" name="rating" value="4" id="star4">
                                                    <label for="star4"><i class="fas fa-star"></i></label>
                                                    <input type="radio" name="rating" value="3" id="star3">
                                                    <label for="star3"><i class="fas fa-star"></i></label>
                                                    <input type="radio" name="rating" value="2" id="star2">
                                                    <label for="star2"><i class="fas fa-star"></i></label>
                                                    <input type="radio" name="rating" value="1" id="star1">
                                                    <label for="star1"><i class="fas fa-star"></i></label>
                                                </div>
                                                <div class="rating-labels">
                                                    <span>Poor</span>
                                                    <span>Fair</span>
                                                    <span>Good</span>
                                                    <span>Very Good</span>
                                                    <span>Excellent</span>
                                                </div>
                                                <textarea name="review" placeholder="Write a review about your experience (optional)" class="review-textarea"></textarea>
                                                <button type="submit" class="submit-rating-btn">
                                                    <i class="fas fa-star"></i> Submit Rating
                                                </button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <div class="already-rated">
                                            <i class="fas fa-check-circle"></i>
                                            <span>You have already rated this task</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <a href="chat.php?task_id=<?php echo $task_id; ?>" class="chat-btn">
                                    <i class="fas fa-comments"></i> Chat with Tasker
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-bids">
                    <i class="fas fa-gavel"></i>
                    <p>No bids have been placed yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include_once "footer.php"; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle bid acceptance
            const acceptForms = document.querySelectorAll('.accept-bid-form');
            acceptForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to accept this bid? This will reject all other bids.')) {
                        e.preventDefault();
                    }
                });
            });

            // Handle bid deletion
            const deleteLinks = document.querySelectorAll('.delete-btn');
            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    if (!confirm('Are you sure you want to delete this bid?')) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html> 