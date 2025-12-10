<?php
session_start();
require "user.php";


$users = json_decode(file_get_contents("users.json"), true);
$currentUsername = $_SESSION['username'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profiles</title>
    <link rel="stylesheet" href="profiles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="profiles-container">

        <header class="profiles-header">
            <div class="header-content">
                <h1><i class="fas fa-users"></i> Employee Profiles</h1>
                <div class="header-actions">
                    <span class="welcome-text">Welcome, <?php echo htmlspecialchars($currentUsername); ?></span>
                    <div class="action-buttons">
                        <a href="edit.php" class="btn btn-secondary">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                        <a href="logout.php" class="btn btn-logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </header>

<div class="search-container">
    <div class="search-box">
        <i class="fas fa-search search-icon"></i>
        <input type="text" 
               id="employeeSearch" 
               class="search-input" 
               placeholder="Search employees by name..."
               aria-label="Search employees">
        <button id="clearSearch" class="clear-search-btn" style="display: none;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div class="search-hint">
        <i class="fas fa-info-circle"></i>
        Type to filter employees by name
    </div>
</div>

        <div class="profiles-content">
  
            <div class="profiles-grid" id="profilesGrid">
                <?php if(empty($users)): ?>
                    <div class="no-profiles">
                        <i class="fas fa-users-slash"></i>
                        <h3>No Profiles Found</h3>
                        <p>No employee profiles have been created yet.</p>
                        <a href="register.php" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Add First Employee
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach($users as $index => $user): ?>
                        <div class="profile-card" data-user-id="<?php echo $index; ?>">
                            <div class="profile-image">
                                <img src="<?php echo isset($user['profile_picture']) && file_exists($user['profile_picture']) ? $user['profile_picture'] : 'default-avatar.png'; ?>" 
                                     alt="<?php echo htmlspecialchars($user['fullname']); ?>">
                                <div class="online-status <?php echo $user['username'] === $currentUsername ? 'online' : 'offline'; ?>"></div>
                            </div>
                            
                            <div class="profile-info">
                                <h3 class="profile-name"><?php echo htmlspecialchars($user['fullname']); ?></h3>
                                <p class="profile-position">
                                    <i class="fas fa-briefcase"></i>
                                    <span class="position-text"><?php echo htmlspecialchars($user['department'] ?? 'Employee'); ?></span>
                                </p>
                                <p class="profile-email">
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </p>
                                <p class="profile-phone">
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($user['phone']); ?>
                                </p>
                                
                                <div class="profile-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-venus-mars"></i>
                                        <?php echo htmlspecialchars($user['gender']); ?>
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-id-card"></i>
                                        ID: <?php echo isset($user['employee_id']) ? htmlspecialchars($user['employee_id']) : 'EMP-' . str_pad($index + 1, 3, '0', STR_PAD_LEFT); ?>
                                    </span>
                                </div>
                                
                                <div class="profile-actions">
                                    <a href="view_profile.php?id=<?php echo $index; ?>" class="btn btn-view">
                                        <i class="fas fa-eye"></i> View Profile
                                    </a>
                                    <?php if($user['username'] === $currentUsername): ?>
                                        <span class="current-user-badge">
                                            <i class="fas fa-user-check"></i> You
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="profiles-footer">
                <p class="profile-count">
                    <i class="fas fa-user-friends"></i>
                    Showing <?php echo count($users); ?> employee(s)
                </p>
            </div>
        </div>
    </div>

    <script>
    
        document.querySelectorAll('.profile-card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!e.target.closest('.btn-view')) {
                    this.style.transform = 'scale(0.98)';
                    setTimeout(() => {
                        this.style.transform = 'scale(1)';
                    }, 150);
                }
            });
        });

        
const employeeSearch = document.getElementById('employeeSearch');
const clearSearchBtn = document.getElementById('clearSearch');
const profileCards = document.querySelectorAll('.profile-card');

if (employeeSearch) {
    employeeSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        
        clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
        
        let visibleCount = 0;
        
        profileCards.forEach(card => {
            const profileName = card.querySelector('.profile-name').textContent.toLowerCase();
            const profilePosition = card.querySelector('.position-text').textContent.toLowerCase();
            const profileEmail = card.querySelector('.profile-email').textContent.toLowerCase();
            
            const matches = profileName.includes(searchTerm) || 
                          profilePosition.includes(searchTerm) || 
                          profileEmail.includes(searchTerm);
            
            if (matches || searchTerm === '') {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        const profileCountElement = document.querySelector('.profile-count');
        if (profileCountElement) {
            const totalCount = profileCards.length;
            if (searchTerm) {
                profileCountElement.innerHTML = `
                    <i class="fas fa-user-friends"></i>
                    Showing ${visibleCount} of ${totalCount} employee(s)
                    <span class="search-results-text"> - filtered by "${searchTerm}"</span>
                `;
            } else {
                profileCountElement.innerHTML = `
                    <i class="fas fa-user-friends"></i>
                    Showing ${totalCount} employee(s)
                `;
            }
        }
        
        const noResultsMessage = document.getElementById('noResultsMessage');
        if (visibleCount === 0 && searchTerm) {
            if (!noResultsMessage) {
                const profilesGrid = document.getElementById('profilesGrid');
                const messageDiv = document.createElement('div');
                messageDiv.id = 'noResultsMessage';
                messageDiv.className = 'no-results-message';
                messageDiv.innerHTML = `
                    <i class="fas fa-search"></i>
                    <h3>No Matching Employees</h3>
                    <p>No employees found matching "${searchTerm}"</p>
                `;
                profilesGrid.appendChild(messageDiv);
            }
        } else if (noResultsMessage) {
            noResultsMessage.remove();
        }
    });
    
    clearSearchBtn.addEventListener('click', function() {
        employeeSearch.value = '';
        employeeSearch.dispatchEvent(new Event('input'));
        employeeSearch.focus();
    });
    
    employeeSearch.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            this.dispatchEvent(new Event('input'));
        }
    });
}
    </script>
</body>
</html>