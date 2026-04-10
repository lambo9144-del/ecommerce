<?php
// Ensure session is started in your header.php!
include('header.php'); 
include('pfp.php');

// Make sure user is logged in
$userId = $_SESSION['user_id'] ?? 0;
if(!$userId) {
    die("Please login first.");
}

// Handle upload
if(isset($_POST['upload_pfp'])){
    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        echo "<script>alert('Please select a file to upload.');</script>";
    } else {
        $newPfp = uploadProfilePicture($_FILES['image'], $userId, $conn);
        if($newPfp){
            echo "<script>alert('Profile picture updated successfully!'); window.location.href = window.location.pathname;</script>";
        } else {
            echo "<script>alert('Failed to upload. Ensure it is a JPG/PNG/GIF under 2MB.');</script>";
        }
    }
}

// Get current user info
$qry = $conn->prepare("SELECT * FROM users WHERE id=?");
$qry->bind_param("i", $userId);
$qry->execute();
$user = $qry->get_result()->fetch_assoc();
?>

<div class="profile-page">

    <!-- Use htmlspecialchars for security -->
    <h2>Welcome, <?=htmlspecialchars($user['username'])?></h2>

    <!-- Current Profile Picture -->
    <?php $profileImage = !empty($user['image']) ? $user['image'] : 'default.png'; ?>
    <img src="./uploads/<?=htmlspecialchars($profileImage)?>" width="120" style="border-radius:50%; display:block; margin-bottom:10px;" alt="Profile Picture">

    <!-- Button to show upload form -->
    <button onclick="document.getElementById('pfpForm').style.display='block'; this.style.display='none';">
        Change Profile Picture
    </button>

    <!-- Hidden upload form -->
    <div id="pfpForm" style="display:none; margin-top:10px;">
        <form method="post" enctype="multipart/form-data">
            <!-- Added accept attribute to filter files in the file picker -->
            <input type="file" name="image" accept="image/jpeg,image/png,image/gif" required>
            <button type="submit" name="upload_pfp">Upload</button>
            <button type="button" onclick="document.getElementById('pfpForm').style.display='none'; document.querySelector('.profile-page button').style.display='block';">Cancel</button>
        </form>
    </div>

</div>

<?php include('footer.php'); ?>
