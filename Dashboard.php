<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #800000; 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .dashboard-container {
            background: #f5f5dc; 
            padding: 40px;
            border-radius: 15px;
            width: 450px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            position: relative;
            animation: fadeInUp 0.8s forwards;
        }

        @keyframes fadeInUp {
            0% {
                transform: scale(0.95);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .welcome-section {
            margin-bottom: 30px;
        }

        .welcome-section h2 {
            color: #800000; 
            font-size: 30px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .welcome-section p {
            font-size: 18px;
            color: #800000; 
        }

        /* Dashboard Content Section */
        .dashboard-content p {
            font-size: 18px;
            color: #800000; 
        }

        /* Logout Button */
        .logout-btn {
            position: absolute;
            bottom: 20px;  
            right: 20px;   
            display: inline-block;
            padding: 10px 20px;   
            background-color: #800000; 
            color: #fff;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;   
            transition: transform 0.3s ease, background 0.3s ease, box-shadow 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #660000;  
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .logout-btn:active {
            transform: scale(1);
            box-shadow: none;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        
        <div class="welcome-section">
            <h2>Hi, PUPian!</h2>
            <p>Welcome, <?php echo $_SESSION['user']; ?> to the PUPsite :)</p>
        </div>

        <!-- Dashboard Content Section -->
        <div class="dashboard-content">
           
            <p>Have a nice day! <3</p>
        </div>

        <!-- Logout Button inside the container -->
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</body>
</html>
