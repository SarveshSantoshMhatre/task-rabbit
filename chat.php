<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "php/config.php";

if(!isset($_SESSION['unique_id'])){
    header("location: communication.php");
    exit();
}

if(!isset($_GET['user_id'])) {
    header("location: users.php");
    exit();
}

$user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
$sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");

if(!$sql) {
    die("Database error: " . mysqli_error($conn));
}

if(mysqli_num_rows($sql) > 0){
    $row = mysqli_fetch_assoc($sql);
} else {
    header("location: users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo $row['fname'] . " " . $row['lname']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <link rel="stylesheet" href="style.css">
    <style>
        .wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .chat-area {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .chat-area header {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            background: #f8f8f8;
            border-bottom: 1px solid #eee;
        }
        .back-icon {
            color: #4CAF50;
            font-size: 1.2em;
            margin-right: 20px;
            text-decoration: none;
        }
        .user-info {
            display: flex;
            align-items: center;
            flex: 1;
        }
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .details span {
            font-size: 1.1em;
            font-weight: 500;
            color: #333;
        }
        .details p {
            color: #666;
            font-size: 0.9em;
            margin: 0;
        }
        .tasker-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 20px;
        }
        .profession, .rate, .rating {
            background: #4CAF50;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.9em;
        }
        .chat-box {
            height: 500px;
            overflow-y: auto;
            padding: 20px;
            background: #f9f9f9;
        }
        .typing-area {
            display: flex;
            padding: 15px;
            background: #f8f8f8;
            border-top: 1px solid #eee;
        }
        .input-field {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
            font-size: 1em;
        }
        .send-button {
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .send-button:hover {
            background: #45a049;
        }
        .message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        .outgoing {
            align-items: flex-end;
        }
        .incoming {
            align-items: flex-start;
        }
        .message-content {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 10px;
            position: relative;
        }
        .outgoing .message-content {
            background: #4CAF50;
            color: white;
        }
        .incoming .message-content {
            background: #e9e9e9;
            color: #333;
        }
        .message-time {
            font-size: 0.8em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include_once "header.php"; ?>
    <div class="wrapper">
        <section class="chat-area">
            <header>
                <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
                <div class="user-info">
                    <img src="php/image.php?id=<?php echo $row['unique_id']; ?>" alt="Profile Image">
                    <div class="details">
                        <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
                        <p><?php echo $row['status']; ?></p>
                    </div>
                </div>
                <?php if($row['user_type'] == 'tasker') { 
                    $tasker_info = mysqli_query($conn, "SELECT * FROM taskers WHERE tasker_id = {$row['user_id']}");
                    if(mysqli_num_rows($tasker_info) > 0) {
                        $tasker = mysqli_fetch_assoc($tasker_info);
                ?>
                    <div class="tasker-profile">
                        <div class="profession"><?php echo $tasker['profession']; ?></div>
                        <div class="rate">Rs<?php echo $tasker['hourly_rate']; ?>/hr</div>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <span><?php echo $tasker['rating']; ?></span>
                        </div>
                    </div>
                <?php }} ?>
            </header>
            <div class="chat-box">
                <!-- Messages will be loaded here -->
            </div>
            <form action="#" class="typing-area">
                <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
                <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
                <button class="send-button"><i class="fab fa-telegram-plane"></i></button>
            </form>
        </section>
    </div>

    <script src="javascript/chat.js"></script>
</body>
</html>
