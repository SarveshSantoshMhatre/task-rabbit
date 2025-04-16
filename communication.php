<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['unique_id'])){
    header("location: users.php");
    exit();
}
?>
<?php include_once "header.php"; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<link rel="stylesheet" href="style.css">
<body>
  <div class="wrapper">
    <section class="form signup">
      <header>TaskRabbit </header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off" id="signupForm">
        <div class="error-text" style="display: none; color: #d63031; background: rgba(214, 48, 49, 0.1); padding: 12px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: 500; white-space: pre-line;"></div>
        <div class="name-details">
          <div class="field input">
            <label>First Name</label>
            <input type="text" name="fname" placeholder="First name" required>
          </div>
          <div class="field input">
            <label>Last Name</label>
            <input type="text" name="lname" placeholder="Last name" required>
          </div>
        </div>
        <div class="field input">
          <label>Email Address</label>
          <input type="text" name="email" placeholder="Enter your email" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter new password" required autocomplete="new-password">
          <i class="fas fa-eye toggle-password"></i>
        </div>
        <div class="field input">
          <label>Confirm Password</label>
          <input type="password" name="cpassword" placeholder="Confirm password" required autocomplete="new-password">
          <i class="fas fa-eye toggle-password"></i>
        </div>
        <div class="field input">
          <label>User Type</label>
          <select name="user_type" id="user_type" required>
            <option value="">Select User Type</option>
            <option value="user">I need a tasker</option>
            <option value="tasker">I am a tasker</option>
          </select>
        </div>
        <div id="tasker_fields" style="display: none;">
          <div class="field input">
            <label>Profession</label>
            <select name="profession" id="profession" required aria-label="Select your profession">
              <option value="">Select Profession</option>
              <option value="Carpenter">Carpenter</option>
              <option value="Plumber">Plumber</option>
              <option value="Electrician">Electrician</option>
              <option value="Painter">Painter</option>
              <option value="Handyman">Handyman</option>
            </select>
          </div>
          <div class="field input">
            <label>Bio</label>
            <textarea name="bio" placeholder="Tell us about yourself" aria-required="true"></textarea>
          </div>
          <div class="field input">
            <label>Skills</label>
            <input type="text" name="skills" placeholder="Enter your skills (comma separated)" aria-required="true">
          </div>
          <div class="field input">
            <label>Location</label>
            <input type="text" name="location" placeholder="Enter your location" aria-required="true">
          </div>
          <div class="field input">
            <label>Hourly Rate (Rs)</label>
            <input type="number" name="hourly_rate" placeholder="Enter your hourly rate" min="0" step="0.01" aria-required="true">
          </div>
        </div>
        <div class="field image">
          <label>Select Image</label>
          <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
        </div>
        <div class="field button">
          <button type="submit" id="continueBtn" aria-label="Continue to Chat">Continue to Chat</button>
        </div>
      </form>
      <div class="link">Already signed up? <a href="login.php">Login now</a></div>
    </section>
  </div>

  <script>
    // Debug script loading
    console.log('Before loading JavaScript files');
  </script>
  <script src="javascript/pass-show-hide.js"></script>
  <script>
    console.log('pass-show-hide.js loaded');
  </script>
  <script src="javascript/signup.js"></script>
  <script>
    console.log('signup.js loaded');
    
    // Add console logging for debugging
    document.addEventListener('DOMContentLoaded', function() {
      console.log('DOM loaded');
      const form = document.querySelector("#signupForm");
      console.log('Form found:', form);
      
      // Get error text element
      const errorText = form.querySelector(".error-text");
      console.log('Error text element found:', errorText);
      
      // Function to show error message
      function showError(message) {
        console.log('Showing error:', message);
        errorText.style.display = "block";
        errorText.textContent = message;
        errorText.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
      
      // Test form submission
      form.addEventListener('submit', function(e) {
        console.log('Form submit event triggered');
        e.preventDefault();
        console.log('Form submission prevented');
        
        // Test form data collection
        const formData = new FormData(form);
        console.log('Form data collected:', Object.fromEntries(formData));
        
        // Test AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'php/signup.php', true);
        xhr.onload = function() {
          console.log('XHR response:', xhr.responseText);
          try {
            const response = JSON.parse(xhr.responseText);
            if (response.status === 'error') {
              showError(response.message);
            }
          } catch (e) {
            console.error('Error parsing response:', e);
            showError('An error occurred while processing your request');
          }
        };
        xhr.send(formData);
      });
    });
  </script>

</body>
</html> 