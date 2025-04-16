<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: index.php");
  }

  // Get current user's type
  $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
  $row = mysqli_fetch_assoc($sql);
  $user_type = isset($_SESSION['user_type']) ? $_SESSION['user_type'] : 'user';
?>

<?php include_once "header.php"; ?>

<div class="wrapper">
    <section class="users">
        <header>
            <div class="content">
                <img src="php/image.php?id=<?php echo $row['unique_id']; ?>" alt="Profile Image">
                <div class="details">
                    <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
                    <p><?php echo $row['status']; ?></p>
                </div>
            </div>
            <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">Logout</a>
        </header>
        <div class="search">
            <span class="text"><?php echo $user_type == 'user' ? 'Select a profession to chat' : 'Select a client to chat'; ?></span>
        </div>
        <?php if($user_type == 'user') { ?>
        <div class="profession-filter">
            <button class="filter-btn" data-profession="">All</button>
            <button class="filter-btn" data-profession="Carpenter">Carpenter</button>
            <button class="filter-btn" data-profession="Plumber">Plumber</button>
            <button class="filter-btn" data-profession="Electrician">Electrician</button>
            <button class="filter-btn" data-profession="Painter">Painter</button>
            <button class="filter-btn" data-profession="Handyman">Handyman</button>
        </div>
        <?php } ?>
        <div class="users-list">
            <!-- Users list will be loaded here -->
        </div>
    </section>
</div>

<?php include_once "footer.php"; ?>

<script src="javascript/users.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchText = document.querySelector(".search .text");
    const usersList = document.querySelector(".users-list");
    const filterButtons = document.querySelectorAll('.filter-btn');
    const userType = 'user';
    let currentProfession = '';

    // Function to load users with profession filter
    function loadUsers(profession = '') {
        console.log("Loading users with profession:", profession);
        currentProfession = profession; // Store the current profession
        
        let xhr = new XMLHttpRequest();
        let url = "php/get_users.php";
        if (profession) {
            url += "?profession=" + encodeURIComponent(profession);
        }
        xhr.open("GET", url, true);
        xhr.onload = function() {
            if(xhr.readyState === XMLHttpRequest.DONE) {
                if(xhr.status === 200) {
                    let data = xhr.response;
                    usersList.innerHTML = data;
                    console.log("Response for profession " + profession + ":", data);
                } else {
                    console.error("Error loading users:", xhr.status);
                }
            }
        }
        xhr.onerror = function() {
            console.error("Network error while loading users");
        }
        xhr.send();
    }

    // Handle profession filter clicks
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            console.log("Filter button clicked");
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            // Update search text
            const profession = this.getAttribute('data-profession');
            console.log("Selected profession:", profession);
            searchText.textContent = profession ? `Select a ${profession} to chat` : 'Select a profession to chat';
            // Load users with the selected profession
            loadUsers(profession);
        });
    });

    // Initial load of users
    loadUsers();

    // Periodically update the user list
    setInterval(function() {
        // Get the current profession from the active button or stored value
        const activeButton = document.querySelector('.filter-btn.active');
        const profession = activeButton ? activeButton.getAttribute('data-profession') : currentProfession;
        console.log("Periodic update - Current profession:", profession);
        loadUsers(profession);
    }, 500);
});
</script>
