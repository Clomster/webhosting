<?php
session_start();
if(!isset($_SESSION["username"])){
    header("Location: profiles.php");
    exit();
}

require "user.php";
$user = new User();


$users = json_decode(file_get_contents("users.json"), true);
$currentUser = null;
foreach($users as $userData) {
    if($userData['username'] === $_SESSION['username']) {
        $currentUser = $userData;
        break;
    }
}


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_picture"])) {
    $uploadDir = "uploads/";
    if(!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $filename = $_SESSION['username'] . "_" . time() . "_" . basename($_FILES["profile_picture"]["name"]);
    $targetFile = $uploadDir . $filename;
    
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    
    if(in_array($imageFileType, $allowedTypes)) {
        if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {

            foreach($users as &$u) {
                if($u['username'] === $_SESSION['username']) {
                    $u['profile_picture'] = $targetFile;
                    break;
                }
            }
            file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));
            $currentUser['profile_picture'] = $targetFile;
            $uploadMessage = "Profile picture updated successfully!";
        } else {
            $uploadError = "Error uploading file.";
        }
    } else {
        $uploadError = "Only JPG, JPEG, PNG & GIF files are allowed.";
    }
}


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $fullname = $_POST["fullname"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $dob = $_POST["dob"];
    $pob = $_POST["pob"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];
    
    foreach($users as &$u) {
        if($u['username'] === $_SESSION['username']) {
            $u['fullname'] = $fullname;
            $u['phone'] = $phone;
            $u['address'] = $address;
            $u['dob'] = $dob;
            $u['pob'] = $pob;
            $u['gender'] = $gender;
            $u['email'] = $email;
            break;
        }
    }
    file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));
    $currentUser = array_merge($currentUser, [
        'fullname' => $fullname,
        'phone' => $phone,
        'address' => $address,
        'dob' => $dob,
        'pob' => $pob,
        'gender' => $gender,
        'email' => $email
    ]);
    $updateMessage = "Profile updated successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="edit.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
     
        <header class="dashboard-header">
            <div class="header-content">
                <h1><i class="fas fa-user-circle"></i> Employee Profile</h1>
                <div class="header-actions">
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                    <a href="logout.php" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </header>

        <div class="dashboard-content">
           
            <section class="profile-section">
                <div class="profile-card">

                    <div class="profile-picture-section">
                        <div class="profile-picture-container">
                            <img src="<?php echo isset($currentUser['profile_picture']) && file_exists($currentUser['profile_picture']) ? $currentUser['profile_picture'] : 'default-avatar.png'; ?>" 
                                 alt="Profile Picture" class="profile-img" id="profileImage">
                            
                            <form method="POST" action="dashboard.php" enctype="multipart/form-data" class="upload-form">
                                <div class="file-input-wrapper">
                                    <label for="profile_picture" class="file-label">
                                        <i class="fas fa-camera"></i> Change Photo
                                    </label>
                                    <input type="file" name="profile_picture" id="profile_picture" 
                                           accept="image/*" class="file-input" onchange="previewImage(event)">
                                    <button type="submit" class="upload-btn">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                </div>
                                <small class="file-note">Max size: 5MB (JPG, PNG, GIF)</small>
                            </form>
                            
                            <?php if(isset($uploadMessage)): ?>
                                <div class="message success"><?php echo $uploadMessage; ?></div>
                            <?php endif; ?>
                            <?php if(isset($uploadError)): ?>
                                <div class="message error"><?php echo $uploadError; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                   
                    <div class="profile-info-section">
                        <h2 class="profile-title">Personal Information</h2>
                        
                        <?php if(isset($updateMessage)): ?>
                            <div class="message success"><?php echo $updateMessage; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="dashboard.php" class="profile-form">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fullname">
                                        <i class="fas fa-user"></i> Full Name
                                    </label>
                                    <input type="text" id="fullname" name="fullname" 
                                           value="<?php echo htmlspecialchars($currentUser['fullname'] ?? ''); ?>" 
                                           placeholder="Enter your full name" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="email">
                                        <i class="fas fa-envelope"></i> Email Address
                                    </label>
                                    <input type="email" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($currentUser['email'] ?? ''); ?>" 
                                           placeholder="Enter your email" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">
                                        <i class="fas fa-phone"></i> Phone Number
                                    </label>
                                    <input type="text" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?>" 
                                           placeholder="+63 XXX-XXX-XXXX" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">
                                        <i class="fas fa-calendar"></i> Date of Birth
                                    </label>
                                    <input type="date" id="dob" name="dob" 
                                           value="<?php echo htmlspecialchars($currentUser['dob'] ?? ''); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="pob">
                                        <i class="fas fa-map-marker-alt"></i> Place of Birth
                                    </label>
                                    <input type="text" id="pob" name="pob" 
                                           value="<?php echo htmlspecialchars($currentUser['pob'] ?? ''); ?>" 
                                           placeholder="City, Province" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="gender">
                                        <i class="fas fa-venus-mars"></i> Gender
                                    </label>
                                    <select id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?php echo ($currentUser['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="Female" <?php echo ($currentUser['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="Other" <?php echo ($currentUser['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group full-width">
                                    <label for="address">
                                        <i class="fas fa-home"></i> Address
                                    </label>
                                    <textarea id="address" name="address" rows="3" 
                                              placeholder="Enter your complete address" required><?php echo htmlspecialchars($currentUser['address'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="update-btn">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <a href="dashboard.php" class="back-btn">
                                    <i class="fas fa-home"></i> Back to Home
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
        
        <footer class="dashboard-footer">
            <p>&copy; 2025 Employee Profile System</p>
        </footer>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImage');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
        
      
        setTimeout(function() {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s';
                setTimeout(() => message.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>