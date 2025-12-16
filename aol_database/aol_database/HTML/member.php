<?php
session_start();
include "../PHP/config.php";

$success = false;
$already = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $alamat = trim($_POST["address"]);

    if (ctype_digit($phone) && strlen($phone) >= 12 && strlen($phone) <= 15) {

        $stmtCheck = mysqli_prepare($con, "SELECT id_member FROM member WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmtCheck, "s", $email);
        mysqli_stmt_execute($stmtCheck);
        mysqli_stmt_store_result($stmtCheck);

        if (mysqli_stmt_num_rows($stmtCheck) > 0) {
            $already = true;
        } else {
            $id_member = "MEM-" . strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));

            $stmtInsert = mysqli_prepare($con, "INSERT INTO member (id_member, nama_member, email, no_telp, alamat) VALUES (?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param($stmtInsert, "sssss", $id_member, $nama, $email, $phone, $alamat);

            if (mysqli_stmt_execute($stmtInsert)) {
                $_SESSION["member_id"] = $id_member;
                $_SESSION["nama_member"] = $nama;
                $_SESSION["email_member"] = $email;

                $success = true;
            } else {
                error_log("Insert member gagal: " . mysqli_error($con));
            }
            mysqli_stmt_close($stmtInsert);
        }
        mysqli_stmt_close($stmtCheck);
    } 
}
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Daftar Member FFKS Mart</title>

    <link rel="stylesheet" href="../CSS/member.css">
    <link rel="shortcut icon" href="../ASSET/logo-Url.png" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hidden {
            display: none;
        }

        #popupSuccess {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            width: 90%;
            max-width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .popup-content h2 {
            color: #1eb853;
            margin-bottom: 10px;
        }

        .btn-main {
            background: #1eb853;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            width: 100%;
            font-weight: 600;
        }

        .btn-close {
            background: #f1f1f1;
            color: #333;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            width: 100%;
        }
    </style>
</head>

<body>

    <?php if ($success): ?>
        <div id="popupSuccess">
            <div class="popup-content">
                <img src="../ASSET/Iconsurel.png" alt="Success" style="width: 50px; margin-bottom: 10px;">
                <h2>Pendaftaran Berhasil!</h2>
                <p>Selamat! Anda sekarang menjadi member resmi FFKS Mart.</p>

                <button id="btnGoDiscount" class="btn-main">Belanja Diskon Member</button>
                <button id="btnGoHome" class="btn-close">Kembali ke Home</button>
            </div>
        </div>
    <?php endif; ?>

    <div class="wrap">
        <div class="card" role="main">
            <h1>Daftar Menjadi Member</h1>
            <p class="lead">Dapatkan diskon khusus member untuk setiap pembelian!</p>

            <?php if ($already): ?>
                <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center;">
                    Email ini sudah terdaftar sebagai member. <br>
                    <a href="login-member.php" style="color: #b91c1c; font-weight: bold;">Login di sini</a>
                </div>
            <?php endif; ?>

            <form id="memberForm" method="POST" action="">

                <div class="field">
                    <label for="name">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required placeholder="Contoh: Budi Santoso" />
                </div>

                <div class="field">
                    <label for="email">Email Aktif</label>
                    <input id="email" name="email" type="email" required placeholder="nama@email.com" />
                </div>

                <div class="field">
                    <label for="phone">No. WhatsApp / Telepon</label>
                    <input 
                        id="phone" 
                        name="phone" 
                        type="text" 
                        required 
                        placeholder="0812..." 
                        minlength="12" 
                        maxlength="15" 
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        title="Harus berupa angka, minimal 12 digit dan maksimal 15 digit"
                    />
                </div>

                <div class="field full">
                    <label for="address">Alamat Lengkap</label>
                    <textarea id="address" name="address" required placeholder="Nama jalan, nomor rumah, kota..."></textarea>
                </div>

                <div class="actions full">
                    <span class="note">Data Anda aman bersama kami.</span>
                    <button type="submit" style="background: #1eb853; color: white; cursor: pointer;">Daftar Sekarang</button>
                </div>

            </form>

            <div class="have-account">
                <p>Sudah jadi member?</p>
                <a href="login-member.php" class="have-account-btn">Masuk disini</a>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const btnPromo = document.getElementById("btnGoDiscount");
            const btnHome = document.getElementById("btnGoHome");

            if (btnPromo) {
                btnPromo.onclick = () => {
                    window.location.href = "../HTML/member-diskon.php";
                };
            }

            if (btnHome) {
                btnHome.onclick = () => {
                    window.location.href = "../HTML/index.php";
                };
            }
        });
    </script>

</body>

</html>