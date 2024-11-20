<?php
session_start();
include 'database.php';

$message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    // Collect form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $organization = $_POST['organization'];
    $position = $_POST['position'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = 'Email is already taken!';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (email, password, first_name, last_name, organization, position) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $email, $hashed_password, $first_name, $last_name, $organization, $position);

        if ($stmt->execute()) {
            $message = 'Sign up successful!';
            echo "<script>window.location.href = 'signin.php';</script>";
            exit;
        } else {
            $message = 'Error: ' . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://th.bing.com/th/id/OIP._J2X1m_fXijbXrFqwBugUgHaE8?rs=1&pid=ImgDetMain') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        h2 {
            color: #000000;
            margin-bottom: 15px;
        }

        .container {
            background: #f5f5dc; 
            padding: 50px 30px; 
            border-radius: 8px;
            width: 455px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .form-group label {
            margin-bottom: 5px;
            font-size: 14px;
            color: black;
        }

        .form-group input, .form-group select {
            width: 100%; 
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        .form-group .error {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: none; /* Hidden by default */
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #800000;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #fdfd96;
        }
        
        .switch-link {
            color: #000000;
            cursor: pointer;
            margin-top: 10px;
            display: inline-block;
        }

        .alert {
            color: #000000;
            background-color: #ffffff;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #cc0000;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Create an account</h2>
        <form id="signupForm" action="signup.php" method="post">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
                <span class="error" id="first_name_error">First name should not contain numbers or special characters.</span>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
                <span class="error" id="last_name_error">Last name should not contain numbers or special characters.</span>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label for="organization">Organization:</label>
                <select name="organization" required>
                    <option value="ACES">ACES</option>
                    <option value="IBITS">IBITS</option>
                    <option value="PIEE">PIEE</option>
                    <option value="YES">YES</option>
                    <option value="JPIA">JPIA</option>
                </select>
            </div>
            <div class="form-group">
                <label for="position">Position:</label>
                <input type="text" name="position" placeholder="Position" required>
            </div>
            <button type="submit" name="signup">Sign Up</button>
        </form>
        <p class="switch-link">Already have an account? <a href="signin.php">SIGN IN</a></p>
    </div>

    <script>
        // Function to validate names
        function validateName(input, errorElementId) {
            const nameRegex = /^[A-Za-z]+$/;
            const errorElement = document.getElementById(errorElementId);

            if (!nameRegex.test(input.value)) {
                errorElement.style.display = 'block';
            } else {
                errorElement.style.display = 'none';
            }
        }

        // Add event listeners for real-time validation
        document.getElementById('first_name').addEventListener('input', function () {
            validateName(this, 'first_name_error');
        });

        document.getElementById('last_name').addEventListener('input', function () {
            validateName(this, 'last_name_error');
        });
    </script>
</body>
</html>
