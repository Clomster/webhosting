<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="container-decoration">
            <h1 class="login-header">Log In</h1>
            <p class="login-subtitle">Sign in to your account</p>
        </div>

        <?php
        session_start();
        require "user.php";
        $user=new User();

        if($_SERVER['REQUEST_METHOD']==='POST'){
            $username=$_POST['username'];
            $password=$_POST['password'];

            $message=$user->login($username, $password);
            if($message==="Login successful"){
                $_SESSION['username']=$username;
                header("Location: dashboard.php");
            } else {
                echo '<div class="error show">' . $message . "</div>";
            }
        }
        ?>

        <form method="POST" action="login.php" class="login-form">
            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text" placeholder="Enter your username" name="username" id="username" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" placeholder="Enter your password" name="password" id="password" class="form-input" required>
            </div>

            <button type="submit" class="login-btn">Sign In</button>
        </form>

        <div class="login-links">
            <a href="index.php" class="login-link">← Back to Homepage</a>
            <a href="register.php" class="login-link">Create New Account</a>
        </div>
    </div>
</body>
</html>