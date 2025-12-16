<?php
session_start();
require_once __DIR__ . '/../PHP/config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $nama = trim($_POST['nama_member'] ?? '');

    if ($email === '' || $nama === '') {
        $errors[] = 'Nama dan email wajib diisi.';
    } else {

        $stmt = mysqli_prepare($con, "SELECT id_member, nama_member, email FROM member WHERE email = ? AND nama_member = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'ss', $email, $nama);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $_SESSION['member_id'] = $row['id_member'];
            $_SESSION['member_name'] = $row['nama_member'];

            header("Location: member-diskon.php");
            exit;
        } else {
            $errors[] = 'Nama & email tidak cocok!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Member</title>
    <link rel="stylesheet" href="../CSS/login.css" />
    <link rel="shortcut icon" href="../ASSET/logo-Url.png" />
</head>

<body>
    <div class="container">
        <div class="box form-box">

            <?php if ($errors): ?>
                <div class="mesagge">
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <header>Login Member</header>

            <form action="" method="post">
                <div class="field input">
                    <label for="nama_member">Nama Member</label>
                    <input type="text" name="nama_member" id="nama_member" required />
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" required />
                </div>

                <div class="field">
                    <input type="submit" class="btn" value="Login" />
                </div>

                <div class="links">
                    Belum punya akun? <a href="member.php">Daftar</a>
                </div>
            </form>

        </div>
    </div>
</body>

</html>