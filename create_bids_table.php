<?php
include_once "php/config.php";

// Check if bids table exists
$check_table = "SHOW TABLES LIKE 'bids'";
$result = mysqli_query($conn, $check_table);

if (mysqli_num_rows($result) == 0) {
    // Create bids table
    $create_table = "CREATE TABLE bids (
        bid_id INT PRIMARY KEY AUTO_INCREMENT,
        task_id INT NOT NULL,
        tasker_id INT NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        message TEXT,
        status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (task_id) REFERENCES tasks(task_id) ON DELETE CASCADE,
        FOREIGN KEY (tasker_id) REFERENCES users(unique_id) ON DELETE CASCADE
    )";
    
    if (mysqli_query($conn, $create_table)) {
        echo "Bids table created successfully!";
    } else {
        echo "Error creating bids table: " . mysqli_error($conn);
    }
} else {
    echo "Bids table already exists.";
}

// Show current bids
$show_bids = "SELECT * FROM bids";
$result = mysqli_query($conn, $show_bids);

echo "<h2>Current Bids:</h2>";
if (mysqli_num_rows($result) > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Bid ID</th><th>Task ID</th><th>Tasker ID</th><th>Amount</th><th>Status</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['bid_id'] . "</td>";
        echo "<td>" . $row['task_id'] . "</td>";
        echo "<td>" . $row['tasker_id'] . "</td>";
        echo "<td>" . $row['amount'] . "</td>";
        echo "<td>" . $row['status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No bids found in the database.";
}
?> 