<?php
include_once "init_session.php";

// Debug information
error_log("=== How It Works Page Loaded ===");
error_log("Session ID: " . session_id());
error_log("Session Data: " . print_r($_SESSION, true));
error_log("Cookie Data: " . print_r($_COOKIE, true));

$title = "How It Works - TaskChat";
?>
<?php include_once "header.php"; ?>
    <div class="wrapper">
        <section class="how-it-works">
            <header>
                <h1>How It Works</h1>
                <p>Learn how to get started with our platform</p>
            </header>
            
            <div class="steps-container">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Create Your Account</h3>
                        <p>Sign up as either a client or a tasker. Clients can post tasks, while taskers can find work opportunities.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Complete Your Profile</h3>
                        <p>Add your details, skills, and preferences to help others find you easily.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Post or Find Tasks</h3>
                        <p>Clients can post tasks with details and budget. Taskers can browse available tasks and apply.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">4</div>
                    <div class="step-content">
                        <h3>Connect and Communicate</h3>
                        <p>Use our chat system to discuss task details, negotiate terms, and stay in touch.</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-number">5</div>
                    <div class="step-content">
                        <h3>Complete Tasks</h3>
                        <p>Once a task is completed, mark it as done and provide feedback to help build the community.</p>
                    </div>
                </div>
            </div>

            <div class="user-guides">
                <h2>User Guides</h2>
                <div class="guide-cards">
                    <div class="guide-card">
                        <h3>For Clients</h3>
                        <ul>
                            <li>How to post a task</li>
                            <li>Finding the right tasker</li>
                            <li>Managing your tasks</li>
                            <li>Payment and reviews</li>
                        </ul>
                    </div>
                    <div class="guide-card">
                        <h3>For Taskers</h3>
                        <ul>
                            <li>Setting up your profile</li>
                            <li>Finding available tasks</li>
                            <li>Managing your applications</li>
                            <li>Getting paid</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="faq-section">
                <h2>Frequently Asked Questions</h2>
                <div class="faq-container">
                    <div class="faq-item">
                        <h3>How do I post a task?</h3>
                        <p>Click on the "Post Task" button, fill in the details, set your budget, and publish. Taskers will be able to see and apply for your task.</p>
                    </div>
                    <div class="faq-item">
                        <h3>How do I find tasks?</h3>
                        <p>Browse available tasks in your area, filter by category, and apply for tasks that match your skills.</p>
                    </div>
                    <div class="faq-item">
                        <h3>How does payment work?</h3>
                        <p>Payments are processed securely through our platform. Clients pay when posting tasks, and taskers receive payment upon completion.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="javascript/how_it_works.js"></script>
<?php include_once "footer.php"; ?> 