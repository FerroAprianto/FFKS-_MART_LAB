<?php
require_once __DIR__ . '/../PHP/config.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $age = intval($_POST['age'] ?? 0);
  $password = $_POST['password'] ?? '';

 
  if ($username === '' || $email === '' || $password === '') {
    $errors[] = 'Semua kolom harus diisi.';
  } 
  
  elseif ($age < 15) {
    $errors[] = 'Maaf, umur minimal untuk mendaftar adalah 15 tahun.';
  } 
  
  elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid.';
  } 
  else {
   
    $stmt = mysqli_prepare($con, 'SELECT Id_user FROM users WHERE Email = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
      $errors[] = 'Email sudah digunakan, silakan gunakan email lain.';
    }
    mysqli_stmt_close($stmt);

   
    if (empty($errors)) {
      $hash = password_hash($password, PASSWORD_DEFAULT);

      $insert = mysqli_prepare(
        $con,
        'INSERT INTO users (Username, Email, Age, Password) VALUES (?, ?, ?, ?)'
      );

      mysqli_stmt_bind_param($insert, 'ssis', $username, $email, $age, $hash);

      if (mysqli_stmt_execute($insert)) {
        $success = 'Registrasi berhasil! Silakan login.';
      } else {
        $errors[] = 'Terjadi kesalahan saat menyimpan data.';
      }

      mysqli_stmt_close($insert);
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <link rel="stylesheet" href="../CSS/login.css" />
    <link rel="shortcut icon" href="../ASSET/logo-Url.png" />
  </head>
  <body>
    <div class="container">
      <div class="box form-box">
        
        <?php if ($errors) : ?>
          <div class="mesagge">
            <?php foreach ($errors as $error) : ?>
              <p><?php echo $error; ?></p>
            <?php endforeach; ?>
          </div>
        <?php elseif ($success) : ?>
          <div class="mesagge ok">
            <p><?php echo $success; ?></p>
          </div>
        <?php endif; ?>

        <header>Register</header>
        <form action="" method="post">
          <div class="field input">
            <label for="username">Username</label>
            <input
              type="text"
              name="username"
              id="username"
              autocomplete="off"
              required
            />
          </div>

          <div class="field input">
            <label for="email">Email</label>
            <input
              type="email"
              name="email"
              id="email"
              autocomplete="off"
              required
            />
          </div>

          <div class="field input">
            <label for="age">Age</label>
            <input
              type="number"
              name="age"
              id="age"
              min="15" 
              autocomplete="off"
              required
            />
          </div>

          <div class="field input">
            <label for="password">Password</label>
            <input
              type="password"
              name="password"
              id="password"
              autocomplete="off"
              required
            />
          </div>

          <div class="field">
            <input type="submit" class="btn" name="submit" value="Register" />
          </div>
          <div class="links">
            Sudah punya akun?
            <a href="login.php">Sign in</a>
          </div>
        </form>
      </div>
    </div>
  </body>
</html>