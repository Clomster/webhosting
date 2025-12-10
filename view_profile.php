<?php
session_start();
require "user.php";

if(!isset($_SESSION["username"])){
    header("Location: login.php");
    exit();
}

$profileId = $_GET['id'] ?? null;

$users = json_decode(file_get_contents("users.json"), true);

if($profileId !== null && isset($users[$profileId])) {
    $profileUser = $users[$profileId];
} else {
    header("Location: profiles.php");
    exit();
}

$currentUsername = $_SESSION['username'];
$isOwnProfile = ($profileUser['username'] === $currentUsername);

if($isOwnProfile && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_profile"])) {
    $fullname = $_POST["fullname"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $dob = $_POST["dob"];
    $pob = $_POST["pob"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];

    $users[$profileId]['fullname'] = $fullname;
    $users[$profileId]['phone'] = $phone;
    $users[$profileId]['address'] = $address;
    $users[$profileId]['dob'] = $dob;
    $users[$profileId]['pob'] = $pob;
    $users[$profileId]['gender'] = $gender;
    $users[$profileId]['email'] = $email;
    
    file_put_contents("users.json", json_encode($users, JSON_PRETTY_PRINT));
    
    $profileUser = $users[$profileId];
    $updateMessage = "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($profileUser['fullname']); ?> - Profile</title>
    <link rel="stylesheet" href="profiles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="view-profile-container">

        <header class="view-profile-header">
            <div class="header-content">
                <div class="back-nav">
                    <a href="dashboard.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Back to Profiles
                    </a>
                </div>
                <h1>Employee Profile</h1>
                <div class="header-actions">
                    <?php if($isOwnProfile): ?>
                        <span class="current-user-badge">
                            <i class="fas fa-user-check"></i> Your Profile
                        </span>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </header>

        <div class="view-profile-content">

            <div class="profile-header-card">
                <div class="profile-header-left">
                    <div class="profile-image-large">
                        <img src="<?php echo isset($profileUser['profile_picture']) && file_exists($profileUser['profile_picture']) ? $profileUser['profile_picture'] : 'default-avatar.png'; ?>" 
                             alt="<?php echo htmlspecialchars($profileUser['fullname']); ?>" 
                             class="profile-img-large">
                    </div>
                    
                    <div class="profile-basic-info">
                        <h2><?php echo htmlspecialchars($profileUser['fullname']); ?></h2>
                        <p class="profile-email">
                            <i class="fas fa-envelope"></i>
                            <?php echo htmlspecialchars($profileUser['email']); ?>
                        </p>
                        <p class="profile-username">
                            <i class="fas fa-user"></i>
                            Username: <?php echo htmlspecialchars($profileUser['username']); ?>
                        </p>
                    </div>
                </div>
                
                <div class="profile-header-right">
                    <?php if(isset($updateMessage)): ?>
                        <div class="message success"><?php echo $updateMessage; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="profile-details-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-circle"></i> Personal Information</h3>
                </div>
                
                <form method="POST" action="view_profile.php?id=<?php echo $profileId; ?>" class="profile-details-form">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="details-grid">
                        <div class="detail-group">
                            <label class="detail-label">
                                <i class="fas fa-user"></i> Full Name
                            </label>
                            <input type="text" name="fullname" 
                                   value="<?php echo htmlspecialchars($profileUser['fullname']); ?>" 
                                   class="detail-value" <?php echo $isOwnProfile ? '' : 'readonly'; ?>>
                        </div>
                        
                        <div class="detail-group">
                            <label class="detail-label">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                            <input type="email" name="email" 
                                   value="<?php echo htmlspecialchars($profileUser['email']); ?>" 
                                   class="detail-value" <?php echo $isOwnProfile ? '' : 'readonly'; ?>>
                        </div>
                        
                        <div class="detail-group">
                            <label class="detail-label">
                                <i class="fas fa-phone"></i> Phone
                            </label>
                            <input type="text" name="phone" 
                                   value="<?php echo htmlspecialchars($profileUser['phone']); ?>" 
                                   class="detail-value" <?php echo $isOwnProfile ? '' : 'readonly'; ?>>
                        </div>
                        
                        <div class="detail-group">
                            <label class="detail-label">
                                <i class="fas fa-venus-mars"></i> Gender
                            </label>
                            <select name="gender" class="detail-value" <?php echo $isOwnProfile ? '' : 'disabled'; ?>>
                                <option value="Male" <?php echo ($profileUser['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
                                <option value="Female" <?php echo ($profileUser['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
                                <option value="Other" <?php echo ($profileUser['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="detail-group">
                            <label class="detail-label">
                                <i class="fas fa-calendar"></i> Date of Birth
                            </label>
                            <input type="date" name="dob" 
                                   value="<?php echo htmlspecialchars($profileUser['dob']); ?>" 
                                   class="detail-value" <?php echo $isOwnProfile ? '' : 'readonly'; ?>>
                        </div>
                        
                        <div class="detail-group">
                            <label class="detail-label">
                                <i class="fas fa-map-marker-alt"></i> Place of Birth
                            </label>
                            <input type="text" name="pob" 
                                   value="<?php echo htmlspecialchars($profileUser['pob']); ?>" 
                                   class="detail-value" <?php echo $isOwnProfile ? '' : 'readonly'; ?>>
                        </div>
                        
                        <div class="detail-group full-width">
                            <label class="detail-label">
                                <i class="fas fa-home"></i> Address
                            </label>
                            <textarea name="address" class="detail-value textarea" 
                                      rows="2" <?php echo $isOwnProfile ? '' : 'readonly'; ?>><?php echo htmlspecialchars($profileUser['address']); ?></textarea>
                        </div>
                    </div>
                    
                    <?php if($isOwnProfile): ?>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-save">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <footer class="view-profile-footer">
            <p>&copy; 2025 Employee Profile System</p>
        </footer>
    </div>
</body>
</html>