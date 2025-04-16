<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Location - TaskRabbit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        .map-container {
            height: 100vh;
            width: 100%;
            position: relative;
            margin-top: 70px;
        }
        
        #map {
            height: 100%;
            width: 100%;
            z-index: 1;
        }

        .map-info {
            position: absolute;
            top: 20px;
            left: 20px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            max-width: 300px;
        }

        .map-info h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .map-info p {
            color: #666;
            margin-bottom: 15px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #45a049;
            transform: translateY(-2px);
        }

        .leaflet-popup-content-wrapper {
            border-radius: 8px;
            padding: 10px;
        }

        .leaflet-popup-content {
            margin: 10px;
        }

        .leaflet-popup-content h3 {
            color: #2c3e50;
            margin: 0 0 10px 0;
        }

        .leaflet-popup-content p {
            color: #666;
            margin: 0;
        }

        .custom-icon {
            background-color: #4CAF50;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <?php include_once "header.php"; ?>
    
    <div class="map-info">
        <h2>Pillai College of Engineering</h2>
        <p>New Panvel, Navi Mumbai, Maharashtra 410206</p>
        <a href="index.php" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>

    <div class="map-container">
        <div id="map"></div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // College coordinates
        const collegeLocation = [18.9902, 73.1277];
        
        // Create map options
        const mapOptions = {
            center: collegeLocation,
            zoom: 15
        };

        // Create map object
        const map = L.map('map', mapOptions);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Create custom icon
        const customIcon = L.divIcon({
            className: 'custom-icon',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
        });

        // Add marker with custom icon
        const marker = L.marker(collegeLocation, {
            icon: customIcon
        }).addTo(map);

        // Add popup to marker
        marker.bindPopup(`
            <div>
                <h3>Pillai College of Engineering</h3>
                <p>New Panvel, Navi Mumbai, Maharashtra 410206</p>
            </div>
        `).openPopup();

        // Add a circle to show the approximate campus area
        L.circle(collegeLocation, {
            color: '#4CAF50',
            fillColor: '#4CAF50',
            fillOpacity: 0.2,
            radius: 200
        }).addTo(map);

        // Add some nearby points of interest
        const nearbyLocations = [
            {
                name: "Campus Entrance",
                coords: [18.9912, 73.1287]
            },
            {
                name: "Main Building",
                coords: [18.9892, 73.1267]
            },
            {
                name: "Sports Complex",
                coords: [18.9907, 73.1257]
            }
        ];

        // Add markers for nearby locations
        nearbyLocations.forEach(location => {
            L.marker(location.coords, {
                icon: customIcon
            }).addTo(map).bindPopup(`
                <div>
                    <h3>${location.name}</h3>
                    <p>Part of Pillai College Campus</p>
                </div>
            `);
        });

        // Add a polygon to outline the campus area
        const campusArea = [
            [18.9912, 73.1287],
            [18.9892, 73.1267],
            [18.9907, 73.1257],
            [18.9922, 73.1277]
        ];

        L.polygon(campusArea, {
            color: '#4CAF50',
            fillColor: '#4CAF50',
            fillOpacity: 0.1,
            weight: 2
        }).addTo(map);
    </script>
</body>
</html> 