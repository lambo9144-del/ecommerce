<?php include("db.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="logo">
            <img src="data:image/png;base64,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" alt="Logo">
        </div>
        <!-- space between -->
        <ul class="nav-links">
            <li><a href="main.php">Home</a></li>
            <li><a href="product.php">Product</a></li>
            <li><a href="#">Cart</a></li>
            <li>
                <?php if(isset($_SESSION["username"]) && $_SESSION["username"]!="") {?>
                <a href="#"><?=$_SESSION['username']?></a>
                <div class="submenu">
                    <ul class="dropdown">
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    </ul>
                </div>
                <?php }else{ ?>
                    <a href="index.php">Login</a>
                    <a href="register.php">Register</a>
                <?php } ?>
            </li>
        </ul>
    </nav>
