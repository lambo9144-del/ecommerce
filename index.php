<?php 
    include "db.php";

    "Hello World <br>";
    
    $name="john";
    $age=31;
    $gender="male";
    $age+=5;
    
    "My name is $name, I am $age years old and I am a $gender.";

    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $username= $_POST["username"];
        $password=$_POST["password"];

        // $qry="SELECT * FROM users WHERE username='$username' AND password='123'";
        // $sttr=mysqli_query($conn,$qry);
        // $num=mysqli_num_rows($sttr);
        // if($num>0){
        //     $_SESSION["username"]=$username;
        //     echo "<script>alert('Login successful');
        //     window.location.href='main.php';
        //     </script>";
        // } else {
        //     echo "<script>alert('Login failed');
        //     window.location.href='index.php';
        //     </script>";
        // }

        
        $qry=$conn->prepare("SELECT * FROM users WHERE username=?");
        $qry->bind_param("s",$username);
        $qry->execute();
        $result=$qry->get_result();
        if($result->num_rows>0){
            $user = $result->fetch_assoc();
            
            if (password_verify($password, $user['password'])) {
                $_SESSION["username"]=$user["username"];
                $_SESSION["user_id"]=$user["id"];
                echo "<script>alert('Login successful');
                window.location.href='main.php';
                </script>";
            } else {
                echo "Login failed";
            }
        }
    }  
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <h1>Login</h1>
        <div class="card">
            <form action="index.php" method="post">

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password">
                </div>
                <a href="register.php">did not have account, click me for register</a><br>
               <button type="submit" name="login">Login</button>
            </form>
            
        </div>
    </div>
</body>
</html>