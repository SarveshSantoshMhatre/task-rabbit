<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['unique_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskRabbit - Find Local Taskers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --primary-color: #4CAF50;
            --primary-dark: #45a049;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --white: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        .hero {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5)), url('images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--white);
            margin-top: 70px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100px;
            background: linear-gradient(to top, var(--white), transparent);
        }

        .hero-content {
            max-width: 900px;
            padding: 40px;
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            font-weight: 700;
        }

        .hero p {
            font-size: 1.3em;
            margin-bottom: 40px;
            line-height: 1.6;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        .cta-button {
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9em;
        }

        .cta-primary {
            background: var(--primary-color);
            color: var(--white);
            box-shadow: var(--shadow);
        }

        .cta-secondary {
            background: transparent;
            color: var(--white);
            border: 2px solid var(--white);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        .cta-primary:hover {
            background: var(--primary-dark);
        }

        .cta-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .categories {
            padding: 80px 20px;
            background: var(--white);
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
            color: var(--secondary-color);
        }

        .section-title h2 {
            font-size: 2.5em;
            margin-bottom: 15px;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--primary-color);
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .category-card {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
            cursor: pointer;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .category-icon {
            font-size: 2.5em;
            color: var(--primary-color);
            margin-bottom: 20px;
            background: rgba(76, 175, 80, 0.1);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            margin: 0 auto 20px;
        }

        .category-card h3 {
            margin-bottom: 15px;
            color: var(--secondary-color);
            font-size: 1.3em;
        }

        .category-card p {
            color: #666;
            line-height: 1.6;
        }

        .popular-tasks {
            padding: 80px 20px;
            background: var(--light-gray);
        }

        .tasks-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .task-card {
            background: var(--white);
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .task-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .task-card h3 {
            color: var(--secondary-color);
            margin-bottom: 20px;
            font-size: 1.4em;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-gray);
        }

        .task-list {
            list-style: none;
            padding: 0;
        }

        .task-list li {
            padding: 15px 0;
            border-bottom: 1px solid var(--light-gray);
            transition: var(--transition);
        }

        .task-list li:hover {
            background: rgba(76, 175, 80, 0.05);
            padding-left: 10px;
        }

        .task-list li:last-child {
            border-bottom: none;
        }

        .task-list a {
            color: var(--text-color);
            text-decoration: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 500;
        }

        .task-list a:hover {
            color: var(--primary-color);
        }

        .task-price {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 1.1em;
        }

        .user-welcome {
            text-align: center;
            margin: 50px 0;
            padding: 40px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: var(--shadow);
            max-width: 800px;
            margin: 50px auto;
        }

        .user-welcome h2 {
            color: var(--secondary-color);
            margin-bottom: 30px;
            font-size: 2em;
        }

        .user-actions {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 30px;
        }

        .multimedia-section {
            padding: 80px 20px;
            background: var(--white);
            text-align: center;
        }

        .video-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }

        .video-container h3 {
            color: var(--secondary-color);
            margin-bottom: 30px;
            font-size: 2em;
        }

        .video-container video {
            width: 100%;
            border-radius: 15px;
            box-shadow: var(--shadow);
        }

        .map-button-container {
            margin: 40px auto;
            text-align: center;
            max-width: 800px;
        }

        .map-button-container h3 {
            color: var(--secondary-color);
            margin-bottom: 20px;
            font-size: 2em;
        }

        .map-button {
            display: inline-block;
            padding: 15px 30px;
            background: var(--primary-color);
            color: var(--white);
            text-decoration: none;
            border-radius: 50px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .map-button:hover {
            transform: translateY(-3px);
            background: var(--primary-dark);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        #logoCanvas {
            display: block;
            margin: 40px auto;
            border-radius: 50%;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        #logoCanvas:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5em;
            }
            
            .hero p {
                font-size: 1.1em;
            }
            
            .cta-buttons {
                flex-direction: column;
                gap: 15px;
            }
            
            .section-title h2 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <?php include_once "header.php"; ?>
    
    <?php if($isLoggedIn): ?>
        <div class="user-welcome">
            <h2>Welcome back! What would you like to do?</h2>
            <div class="user-actions">
                <a href="users.php" class="cta-button cta-primary">Go to Messages</a>
                <a href="logout.php" class="cta-button cta-secondary">Logout</a>
            </div>
        </div>
    <?php endif; ?>

    <div class="hero">
        <div class="hero-content">
            <h1>Find Local Taskers for Any Job</h1>
            <p>Get help with home tasks, errands, and more from trusted local professionals</p>
            <?php if(!$isLoggedIn): ?>
                <div class="cta-buttons">
                    <a href="communication.php" class="cta-button cta-primary">Find a Tasker</a>
                    <a href="communication.php" class="cta-button cta-secondary">Become a Tasker</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <section class="categories">
        <h2>Available Services</h2>
        <div class="categories-grid">
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-hammer"></i>
                </div>
                <h3>Carpenter</h3>
                <p>Furniture assembly, repairs, and custom woodwork</p>
            </div>
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-tools"></i>
                </div>
                <h3>Plumber</h3>
                <p>Pipe repairs, installations, and maintenance</p>
            </div>
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Electrician</h3>
                <p>Wiring, installations, and electrical repairs</p>
            </div>
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-paint-roller"></i>
                </div>
                <h3>Painter</h3>
                <p>Interior and exterior painting services</p>
            </div>
            <div class="category-card">
                <div class="category-icon">
                    <i class="fas fa-wrench"></i>
                </div>
                <h3>Handyman</h3>
                <p>General repairs and home maintenance</p>
            </div>
        </div>
    </section>
    <div class="multimedia-section">
        <div class="video-container">
            <h3>Welcome to Our Community</h3>
            <video width="50%" controls>
                <source src="videos/welcome.mp4" type="video/mp4">
                
                <!-- English Subtitles (default) -->
                <track kind="subtitles" src="videos/subtitles_en.vtt" srclang="en" label="English" >
                
                <!-- Hindi Subtitles -->
                <track kind="subtitles" src="videos/subtitles_hi.vtt" srclang="hi" label="हिंदी (Hindi)">
                
                Your browser does not support the video tag.
            </video>
        </div>
  
        <div class="map-button-container">
            <h3>Find Our Location</h3>
            <a href="map.php" class="map-button">
                View Our Location
            </a>
        </div>
    </div>
    <canvas id="logoCanvas" width="100" height="100"></canvas>

    <script>
    const canvas = document.getElementById('logoCanvas');
    const ctx = canvas.getContext('2d');

    ctx.beginPath();
    ctx.arc(50, 50, 40, 0, 2 * Math.PI); // x, y, radius, startAngle, endAngle
    ctx.strokeStyle = 'green'; // Outline color
    ctx.lineWidth = 4;         // Outline thickness
    ctx.stroke();              // Draw the outline
    </script>


    

    <script>
        // Add smooth scrolling to all links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>

    <?php include_once "footer.php"; ?>
</body>
</html>
