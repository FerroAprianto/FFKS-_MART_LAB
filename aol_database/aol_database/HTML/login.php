<?php
session_start();
include("../PHP/config.php");


if (isset($_SESSION['valid'])) {
    header("Location: index.php"); 
    exit;
}
if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php"); 
    exit;
}

$error_msg = "";


if (isset($_POST['submit'])) {
    
    // Ambil data & amankan string
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    
    $cekAdmin = mysqli_query($con, "SELECT * FROM admin WHERE username='$email' AND password='".md5($password)."'");

    if (mysqli_num_rows($cekAdmin) > 0) {
        $row = mysqli_fetch_assoc($cekAdmin);
        
     
        $_SESSION['admin_id'] = $row['id_admin'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']     = 'admin';

    
        header("Location: admin.php");
        exit;
    }

    $cekUser = mysqli_query($con, "SELECT * FROM users WHERE Email='$email'");
    $row = mysqli_fetch_assoc($cekUser);

    if (is_array($row) && !empty($row)) {
        
        if (password_verify($password, $row['Password'])) {
            
            // Set Session User
            $_SESSION['valid']    = $row['Id_user'];
            $_SESSION['username'] = $row['Username'];
            $_SESSION['email']    = $row['Email'];
            $_SESSION['age']      = $row['Age'];

           
            $cekMember = mysqli_query($con, "SELECT id_member FROM member WHERE email='".$row['Email']."' LIMIT 1");
            
            if ($m = mysqli_fetch_assoc($cekMember)) {
                
                $_SESSION['member_id'] = $m['id_member'];
            }

           
            header("Location: index.php");
            exit;

        } else {
            $error_msg = "Password salah!";
        }
    } else {
        $error_msg = "Email atau Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | FFKS MART</title>
    <link rel="stylesheet" href="../CSS/login.css">
    <link rel="shortcut icon" href="../ASSET/logo-Url.png" />
</head>
<body>
    <div class="container">
        <div class="box form-box">
            
            <?php if(!empty($error_msg)): ?>
            <div class="mesagge" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                <p><?php echo $error_msg; ?></p>
            </div>
            <?php endif; ?>

            <header>Login</header>
            
            <form action="" method="post">
                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Login" required>
                </div>
                
                <div class="links">
                    Belum punya akun? <a href="register.php">Register</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>