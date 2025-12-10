<?php
require "user.php";
$user = new User();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $_POST['fullname'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $pob = $_POST['pob'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $message = "Password must be at least 8 characters long.";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $message = "Password must contain at least one uppercase letter.";
    } elseif (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $message = "Password must contain at least one special character (e.g., !@#$%^&*).";
    } else {
        $message = $user->register(
            $fullname,
            $phone,
            $address,
            $dob,
            $pob,
            $gender,
            $email,
            $username,
            $password
        );
        
        if ($message === "Registration successful!") {
            header("Location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Registration</title>
    <link rel="stylesheet" href="register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="container">

    <div class="logo-box">
        <img src="logo.jpg" alt="Company Logo" class="logo">
        <h2>E.P.S</h2>
    </div>

    <div class="form-card">
        <h1>Registration Form</h1>

        <?php if ($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php" id="registrationForm">
            <div class="row">
                <div class="col">
                    <label>Full Name</label>
                    <input type="text" name="fullname" required>

                    <label>Phone Number (+63)</label>
                    <input type="text" name="phone" required pattern="\+63[0-9]{10}">

                    <label>Full Address</label>
                    <input type="text" name="address" required>

                    <label>Date of Birth</label>
                    <input type="date" name="dob" required>
                </div>

                <div class="col">
                    <label>Place of Birth</label>
                    <input type="text" name="pob" required>

                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="" disabled selected>Select gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>

                    <label>Email Address</label>
                    <input type="email" name="email" required>

                    <label>Username</label>
                    <input type="text" name="username" required>

                    <label>Password</label>
                    <input type="password" name="password" id="password" required>
                    
              
                    <div class="password-strength" id="strengthContainer" style="display: none;">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    
               
                    <div class="password-requirements" id="passwordRequirements" style="display: none;">
                        <div class="requirement invalid" id="reqLength">
                            <i class=""></i> At least 8 characters
                        </div>
                        <div class="requirement invalid" id="reqUppercase">
                            <i class=""></i> At least one uppercase letter
                        </div>
                        <div class="requirement invalid" id="reqSpecial">
                            <i class=""></i> At least one special character
                        </div>
                    </div>

                    
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirmPassword" required>
                    
                   
                    <div class="password-match" id="passwordMatch" style="display: none;">
                        <i id="matchIcon"></i> <span id="matchText"></span>
                    </div>
                </div>
            </div>

            <button class="submit-btn" type="submit" id="submitBtn">Register</button>
        </form>

        <div class="back-link">
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const strengthBar = document.getElementById('strengthBar');
    const strengthContainer = document.getElementById('strengthContainer');
    const passwordRequirements = document.getElementById('passwordRequirements');
    const passwordMatch = document.getElementById('passwordMatch');
    const matchIcon = document.getElementById('matchIcon');
    const matchText = document.getElementById('matchText');
    const submitBtn = document.getElementById('submitBtn');
    const reqLength = document.getElementById('reqLength');
    const reqUppercase = document.getElementById('reqUppercase');
    const reqSpecial = document.getElementById('reqSpecial');
    
   
    passwordInput.addEventListener('focus', function() {
        passwordRequirements.style.display = 'block';
    });
    
    
    passwordInput.addEventListener('blur', function() {
        if (this.value === '') {
            strengthContainer.style.display = 'none';
            passwordRequirements.style.display = 'none';
        }
    });
    
    function checkPasswordStrength(password) {
        let strength = 0;
        let hasError = false;
        
        if (password.length >= 8) {
            strength += 33;
            reqLength.className = 'requirement valid';
            reqLength.innerHTML = '<i class="fas fa-check"></i> At least 8 characters';
        } else {
            hasError = true;
            reqLength.className = 'requirement invalid';
            reqLength.innerHTML = '<i class="fas fa-times"></i> At least 8 characters';
        }
        
        if (/[A-Z]/.test(password)) {
            strength += 33;
            reqUppercase.className = 'requirement valid';
            reqUppercase.innerHTML = '<i class="fas fa-check"></i> At least one uppercase letter';
        } else {
            hasError = true;
            reqUppercase.className = 'requirement invalid';
            reqUppercase.innerHTML = '<i class="fas fa-times"></i> At least one uppercase letter';
        }

        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            strength += 34;
            reqSpecial.className = 'requirement valid';
            reqSpecial.innerHTML = '<i class="fas fa-check"></i> At least one special character';
        } else {
            hasError = true;
            reqSpecial.className = 'requirement invalid';
            reqSpecial.innerHTML = '<i class="fas fa-times"></i> At least one special character';
        }
        
        strengthBar.style.width = strength + '%';
        
        if (strength < 33) {
            strengthBar.style.background = '#ff4444';
        } else if (strength < 66) {
            strengthBar.style.background = '#ffa726';
        } else {
            strengthBar.style.background = '#4CAF50';
        }
        
        return { strength, hasError };
    }
    
    function checkPasswordMatch(password, confirmPassword) {
        if (confirmPassword === '') {
            passwordMatch.style.display = 'none';
            return false;
        }
        
        passwordMatch.style.display = 'flex';
        
        if (password === confirmPassword) {
            matchIcon.className = 'fas fa-check';
            matchIcon.style.color = '#4CAF50';
            matchText.textContent = 'Passwords match';
            matchText.style.color = '#4CAF50';
            return true;
        } else {
            matchIcon.className = 'fas fa-times';
            matchIcon.style.color = '#ff4444';
            matchText.textContent = 'Passwords do not match';
            matchText.style.color = '#ff4444';
            return false;
        }
    }
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (password.length > 0) {
            strengthContainer.style.display = 'block';
            passwordRequirements.style.display = 'block';
        } else {
            strengthContainer.style.display = 'none';
            passwordRequirements.style.display = 'none';
            passwordMatch.style.display = 'none';
            updateSubmitButton(false, false);
            return;
        }
        
        const { strength, hasError } = checkPasswordStrength(password);
        const passwordsMatch = checkPasswordMatch(password, confirmPassword);
        
        updateSubmitButton(strength === 100, passwordsMatch);
    });
    
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;
        
        const passwordsMatch = checkPasswordMatch(password, confirmPassword);
        const { strength } = checkPasswordStrength(password);
        
        updateSubmitButton(strength === 100, passwordsMatch);
    });
    
    function updateSubmitButton(isPasswordStrong, passwordsMatch) {
        if (isPasswordStrong && passwordsMatch) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.6';
            submitBtn.style.cursor = 'not-allowed';
        }
    }
    
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert("Passwords do not match.");
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            alert("Password must be at least 8 characters long.");
            return false;
        }
        
        if (!/[A-Z]/.test(password)) {
            e.preventDefault();
            alert("Password must contain at least one uppercase letter.");
            return false;
        }
        
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            e.preventDefault();
            alert("Password must contain at least one special character (e.g., !@#$%^&*).");
            return false;
        }
    });
</script>
</body>
</html>