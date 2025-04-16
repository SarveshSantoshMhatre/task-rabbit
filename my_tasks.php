<?php
session_start();
if(!isset($_SESSION['unique_id'])){
    header("location: index.php");
}
include_once "php/config.php";
include_once "header.php";

// Get user type
$sql = mysqli_query($conn, "SELECT user_type FROM users WHERE unique_id = {$_SESSION['unique_id']}");
$row = mysqli_fetch_assoc($sql);
$user_type = $row['user_type'];

// Verify user is a tasker
if($user_type != 'tasker') {
    header("location: users.php");
    exit();
}
?>

<div class="wrapper">
    <section class="tasks">
        <header>
            <div class="content">
                <h2>My Tasks</h2>
                <div class="task-filters">
                    <select id="taskFilter">
                        <option value="assigned">Assigned Tasks</option>
                        <option value="accepted">Accepted Tasks</option>
                        <option value="completed">Completed Tasks</option>
                    </select>
                </div>
            </div>
        </header>
        <div id="taskList" class="tasks-list">
            <!-- Tasks will be loaded here -->
        </div>
    </section>
</div>

<style>
.tasks {
    padding: 20px;
    max-width: 1200px;
    margin: 80px auto 50px;
}

.tasks header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 20px;
    border-bottom: 2px solid var(--light-gray);
    margin-bottom: 30px;
}

.task-filters select {
    padding: 10px;
    border: 2px solid var(--light-gray);
    border-radius: 8px;
    font-size: 16px;
    background: var(--white);
    cursor: pointer;
}

.task-item {
    background: var(--white);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: var(--shadow);
    transition: transform 0.2s ease;
}

.task-item:hover {
    transform: translateY(-2px);
}

.task-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 15px;
}

.task-header h3 {
    color: var(--text-color);
    font-size: 20px;
    margin: 0;
}

.task-category {
    background: var(--primary-color);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 14px;
}

.task-description {
    color: var(--text-color);
    margin-bottom: 15px;
    line-height: 1.6;
}

.task-details {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
}

.task-location, .task-budget {
    display: flex;
    align-items: center;
    gap: 5px;
    color: var(--text-color);
}

.task-budget {
    color: var(--primary-color);
    font-weight: 600;
}

.complete-task {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s ease;
}

.complete-task:hover {
    background: var(--secondary-color);
}

.no-tasks, .error {
    text-align: center;
    padding: 40px;
    color: var(--text-color);
    font-size: 18px;
}

.error {
    color: #dc3545;
}
</style>

<script src="javascript/my_tasks.js"></script>
</body>
</html> 