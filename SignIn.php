<?php
session_start();
include 'database.php';

$message = '';
$is_locked = false;
$seconds_left = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $now = new DateTime();
        $locked_until = $user['locked_until'] ? new DateTime($user['locked_until']) : null;

        if ($locked_until && $locked_until > $now) {
            $is_locked = true;
            $lock_time = $locked_until->diff($now);
            $seconds_left = $lock_time->s + ($lock_time->i * 60) + ($lock_time->h * 3600);
            $message = 'Account locked. Please try again after ' . $seconds_left . ' seconds.';
        } else {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user'] = $user['email'];
                $conn->query("UPDATE users SET attempt_count = 0 WHERE email = '$email'");
                header("Location: dashboard.php");
                exit;
            } else {
                $attempt_count = $user['attempt_count'] + 1;
                if ($attempt_count >= 3) {
                    $locked_until = new DateTime();
                    $locked_until->modify("+30 seconds");
                    $conn->query("UPDATE users SET attempt_count = 0, locked_until = '" . $locked_until->format('Y-m-d H:i:s') . "' WHERE email = '$email'");
                    $is_locked = true;
                    $seconds_left = 30;
                    $message = 'Account locked due to too many failed attempts. Please try again later.';
                } else {
                    $conn->query("UPDATE users SET attempt_count = $attempt_count WHERE email = '$email'");
                    $message = 'Incorrect email or password.';
                }
            }
        }
    } else {
        $message = 'No user found with this email.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIGN IN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('https://scontent.fmnl8-4.fna.fbcdn.net/v/t1.15752-9/462568131_3747560515558128_1908733486998032994_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeG_J1PtnwgqItxRnaYnPkVlvVfIfQhiXCm9V8h9CGJcKUy3-26FPEpe7KszcAevV-Gg1ge2jwXr127LiGrTWDtD&_nc_ohc=q461IeTFyGwQ7kNvgH4gtxb&_nc_zt=23&_nc_ht=scontent.fmnl8-4.fna&oh=03_Q7cD1QFOvPz_QgbQoiYLoz6PoohkqkFKREfclx5jmWH2jMDxwg&oe=675A6EE8') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: flex-end; 
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .greeting {
            position: absolute;
            top: 100px;
            left: 77%;
            transform: translateX(-50%);
            font-weight: bold;
            font-family: 'Arial Black', sans-serif; 
            color: white;
            font-size: 50px;
        }
        .container {
            background: #f5f5dc;
            padding: 30px;
            border-radius: 8px;
            width: 350px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transform: translateX(-96px);
        }
        h2 {
            color: #800000;
            margin-bottom: 15px;
        }
        input[type="email"], input[type="password"] {
            width: calc(100% - 1cm);
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        input[disabled] {
            background-color: #e0e0e0;
            cursor: not-allowed;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #800000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        button:hover {
            background-color: #fdfd96;
        }
        button[disabled] {
            background-color: #e0e0e0;
            cursor: not-allowed;
        }
        .switch-link {
            color: #800000;
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
        .timer {
            color: #cc0000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="greeting">Hi, PUPian!!</div>
    <div class="container">
        <h2>SIGN IN</h2>
        <?php if (!empty($message)): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($is_locked): ?>
            <div class="timer">Time remaining: <span id="countdown"><?php echo $seconds_left; ?></span> seconds</div>
        <?php endif; ?>

        <form action="signin.php" method="post">
            <input type="email" name="email" placeholder="Email" required <?php echo $is_locked ? 'disabled' : ''; ?>>
            <input type="password" name="password" placeholder="Password" required <?php echo $is_locked ? 'disabled' : ''; ?>>
            <button type="submit" name="signin" <?php echo $is_locked ? 'disabled' : ''; ?>>Sign In</button>
        </form>
        <p class="switch-link">Don't have an account? <a href="signup.php">SIGN UP</a></p>
    </div>

    <?php if ($is_locked): ?>
    <script>
        let countdown = <?php echo $seconds_left; ?>;
        const countdownElement = document.getElementById('countdown');

        const interval = setInterval(() => {
            if (countdown > 0) {
                countdown--;
                countdownElement.textContent = countdown;
            } else {
                clearInterval(interval);
                location.reload(); // Reload to enable fields after the lock expires
            }
        }, 1000);
    </script>
    <?php endif; ?>
</body>
</html>
