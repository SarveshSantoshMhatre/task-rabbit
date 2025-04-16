<?php
session_start();
if(!isset($_SESSION['unique_id'])){
    header("location: index.php");
}

include_once "php/config.php";

// Get task details
if(isset($_GET['task_id'])) {
    $task_id = mysqli_real_escape_string($conn, $_GET['task_id']);
    $user_id = $_SESSION['unique_id'];
    
    // Verify task belongs to this user and is completed
    $sql = mysqli_query($conn, "SELECT t.*, u.fname, u.lname, u.img, tk.profession 
                               FROM tasks t 
                               JOIN users u ON t.tasker_id = u.user_id 
                               JOIN taskers tk ON t.tasker_id = tk.tasker_id
                               WHERE t.task_id = {$task_id} 
                               AND t.user_id = (SELECT user_id FROM users WHERE unique_id = {$user_id})
                               AND t.status = 'completed'");
    
    if(mysqli_num_rows($sql) > 0) {
        $row = mysqli_fetch_assoc($sql);
    } else {
        header("location: users.php");
        exit();
    }
} else {
    header("location: users.php");
    exit();
}
?>

<?php include_once "header.php"; ?>

<div class="wrapper">
    <section class="form rate-tasker">
        <header>Rate Your Tasker</header>
        <form id="rateForm" method="POST" autocomplete="off">
            <div class="error-text" style="display: none;"></div>
            
            <div class="tasker-info">
                <img src="php/image.php?id=<?php echo htmlspecialchars($row['unique_id']); ?>" alt="Profile Image">
                <div class="details">
                    <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
                    <p><?php echo $row['profession']; ?></p>
                </div>
            </div>
            
            <div class="field input">
                <label>Rating</label>
                <div class="rating-stars">
                    <i class="fas fa-star" data-rating="1"></i>
                    <i class="fas fa-star" data-rating="2"></i>
                    <i class="fas fa-star" data-rating="3"></i>
                    <i class="fas fa-star" data-rating="4"></i>
                    <i class="fas fa-star" data-rating="5"></i>
                </div>
                <input type="hidden" name="rating" id="rating" required>
            </div>
            
            <div class="field input">
                <label>Review (Optional)</label>
                <textarea name="comment" placeholder="Share your experience with this tasker"></textarea>
            </div>
            
            <div class="field button">
                <input type="submit" value="Submit Review">
            </div>
        </form>
    </section>
</div>

<style>
.rate-tasker {
    max-width: 500px;
    margin: 80px auto 50px;
}

.tasker-info {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
    padding: 15px;
    background: var(--light-gray);
    border-radius: 10px;
}

.tasker-info img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.tasker-info .details span {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-color);
}

.tasker-info .details p {
    color: var(--primary-color);
    font-weight: 500;
}

.rating-stars {
    display: flex;
    gap: 10px;
    margin: 10px 0;
}

.rating-stars i {
    font-size: 24px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.3s ease;
}

.rating-stars i.active {
    color: #ffd700;
}

.rating-stars i:hover {
    color: #ffd700;
}

textarea {
    width: 100%;
    height: 100px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    resize: none;
    font-family: inherit;
}
</style>

<script>
const form = document.querySelector(".rate-tasker form");
const errorText = form.querySelector(".error-text");
const stars = document.querySelectorAll(".rating-stars i");
const ratingInput = document.getElementById("rating");

// Handle star rating selection
stars.forEach(star => {
    star.addEventListener("click", () => {
        const rating = star.getAttribute("data-rating");
        ratingInput.value = rating;
        
        // Update star display
        stars.forEach(s => {
            if(s.getAttribute("data-rating") <= rating) {
                s.classList.add("active");
            } else {
                s.classList.remove("active");
            }
        });
    });
});

form.onsubmit = (e) => {
    e.preventDefault();
    
    if(!ratingInput.value) {
        errorText.style.display = "block";
        errorText.textContent = "Please select a rating";
        return;
    }
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/submit_rating.php", true);
    xhr.onload = () => {
        if(xhr.readyState === XMLHttpRequest.DONE) {
            if(xhr.status === 200) {
                let data = xhr.response;
                if(data === "success") {
                    alert("Thank you for your review!");
                    window.location.href = "users.php";
                } else {
                    errorText.style.display = "block";
                    errorText.textContent = data;
                }
            }
        }
    }
    
    let formData = new FormData(form);
    formData.append("task_id", "<?php echo $task_id; ?>");
    xhr.send(formData);
}
</script>

<?php include_once "footer.php"; ?> 