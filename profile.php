<?php 
include('header.php'); 
include("security.php");


if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    $name = $_POST["name"];
    $phone = $_POST["phone"];
    $gender = $_POST['gender'];
    $email = $_POST["email"];
    $address = $_POST["address"];

    $qry=$conn->prepare("UPDATE users SET name=?, phone=?, gender=?, email=?
    , address=? WHERE username=?");
    $qry->bind_param("ssssss",$name, $phone, $gender, $email, $address, 
$_SESSION["username"]);
    if($qry->execute()){
        echo "<script>alert('Profile updated successfully');</script>";
    }else{
        echo "<script>alert('Failed to update profile');</script>";
    }
}

$qry=$conn->prepare("SELECT * FROM users WHERE username=?");
$qry->bind_param("s", $_SESSION["username"]);
$qry->execute();
$result=$qry->get_result();
$user=$result->fetch_assoc();

?>


<div class="container profile-page">
    <div class="profile-card">

        <h1>My Profile</h1>
        <form action="" method="post" >
        <!-- <div class="form-group profile-image-wrap">
            <label for="profile_image" class="profile-image-label">
                <img
                    src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Ccircle cx='80' cy='80' r='80' fill='%23e8eef5'/%3E%3Ctext x='80' y='88' text-anchor='middle' font-family='sans-serif' font-size='16' fill='%23203040'%3EUpload%3C/text%3E%3C/svg%3E"
                    alt="Profile Image"
                    class="profile-img"
                    id="profilePreview"
                    width="160"
                    height="160"
                />
                <span class="upload-hint">Click image to upload</span>
            </label>
            <input type="file" id="profile_image" name="profile_image" class="hidden-file-input" accept="image/*">
        </div> -->

        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" class="form-control" value="<?=$_SESSION['username']?>" readonly>
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?=$user["name"]?>">
        </div>
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" class="form-control" value="<?=$user["phone"]?>">
        </div>
        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" class="form-control">
                <option value="">Select Gender</option>
                <option value="male" <?=$user["gender"] == "male" ? "selected" : ""?>>Male</option>
                <option value="female" <?=$user["gender"] == "female" ? "selected" : ""?>>Female</option>
            </select>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="form-control" value="<?=$user["email"]?>">
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <textarea id="address" name="address" rows="5" class="form-control"><?=$user["address"]?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>
</div>

<script>
    const profileInput = document.getElementById('profile_image');
    const profilePreview = document.getElementById('profilePreview');

    // Preview selected image immediately after choosing a file.
    profileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file.');
            profileInput.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            profilePreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>

<?php include('footer.php'); ?>