<?php
session_start();
include("../PHP/config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
    exit;
}

$produkDB = mysqli_query($con, "
    SELECT p.*, kp.nama_kategori 
    FROM produk p
    LEFT JOIN kategori_produk kp ON p.id_kategori = kp.id_kategori
");

$kategoriDB = mysqli_query($con, "SELECT * FROM kategori_produk");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FFKS MART</title>
    <link rel="stylesheet" href="../CSS/style.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css" />
    <link rel="shortcut icon" href="../ASSET/logo-Url.png" />
</head>

<body>
    <header>
        <a href="#" class="logo">
            <img src="../ASSET/Iconsurel.png" alt="logo" />
        </a>
        <h3 class="logoname">FFKS MART</h3>
        <div class="bx bx-menu" id="menu-icon"></div>

        <ul class="navbar">
            <li><a href="#home">Home</a></li>
            <li><a href="#shop">Shop</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
        </ul>

        <div class="cart-icon" id="cart-icon">
            <i class="bx bx-cart"></i>
            <span id="cart-count">0</span>
        </div>
    </header>
    <br><br>
    <section class="home" id="home">
        <div class="home-text">
            <span>Selamat Datang</span>
            <h1><b><?php echo htmlspecialchars($_SESSION['username']); ?></b></h1>
            <h2>Diskon Hari Ini,<br />Silahkan Tekan Ini</h2>

            <a href="../HTML/member.php" class="btn">Join Now</a>

            <br><br>
            <h4>Or</h4>
            <br>

            <a href="../PHP/logout.php" class="btn" style="background:transparent; color: var(--second--color);">Logout</a>
        </div>

        <div class="home-img">
            <img
                class="home-img-size"
                src="../ASSET/home.png"
                alt="Home Image"
                style="max-width: 1000px; width: 100%;" />
        </div>
    </section>

    <section class="shop" id="shop">
        <div class="heading">
            <span>Beli sekarang</span>
            <h1>Kebutuhan anda</h1>
            <div class="kategori-filter">
                <select id="kategoriSelect">
                    <option value="all">Semua Kategori</option>
                    <?php while ($kat = mysqli_fetch_assoc($kategoriDB)) : ?>
                        <option value="<?= $kat['id_kategori'] ?>">
                            <?= htmlspecialchars($kat['nama_kategori']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div class="shop-container">
            <?php
            if ($produkDB && mysqli_num_rows($produkDB) > 0) :
                while ($row = mysqli_fetch_assoc($produkDB)) :
                    $img = "../ASSET/" . ($row['gambar'] ?? "");
                    if (empty($row['gambar']) || !file_exists($img)) {
                        $img = "../ASSET/default.png";
                    }
            ?>
                    <div class="box"
                        data-id="<?= htmlspecialchars($row['id_produk']) ?>"
                        data-kategori="<?= htmlspecialchars($row['id_kategori']) ?>">
                        <div class="box-img">
                            <img class="product-img" src="<?= $img ?>" alt="Produk">
                        </div>
                        <h2 class="product-name"><?= htmlspecialchars($row['nama_produk']) ?></h2>
                        <span class="product-price">
                            Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                        </span>
                        <a href="javascript:" class="btn" onclick="addToCart(this.parentElement)">Tambahkan</a>
                    </div>
            <?php
                endwhile;
            endif;
            ?>
        </div>
    </section>

    <div class="cart-model" id="cart-model">
        <div class="cart-head">
            <h2>Your Cart</h2>
            <button class="close-btn">X</button>
        </div>
        <ul id="cart-items"></ul>
        <div class="cart-total">Total: <span id="total-price">Rp 0</span></div>

        <div class="checkout-btn" id="checkout-btn">
            Checkout
        </div>
    </div>

    <section class="about" id="about">
        <div class="heading">
            <span>Perkenalkan Kami</span>
            <h1>The Founders</h1>
        </div>
        <div class="container">
            <div class="about-img">
                <img
                    src="../ASSET/about.jpg"
                    alt="About Us"
                    style="width: 500%; max-width: 540px; border-radius: 20px; object-fit: cover;">
            </div>
            <div class="about-text">
                <h2>Today feel better with us</h2>
                <p>FFKS MART merupakan toko serbaguna yang berfokus menyediakan kebutuhan harian secara lengkap, mudah, dan terjangkau. Kami berkomitmen menghadirkan produk berkualitas, pelayanan cepat, serta pengalaman belanja yang nyaman. Dengan dukungan tim profesional, FFKS MART terus berkembang untuk memenuhi kebutuhan pelanggan dan menjadi pilihan utama masyarakat</p><br>
                <a href="#" class="btn">Learn More</a>
            </div>
        </div>
    </section>

    <section class="contact" id="contact">
        <div class="social">
            <a href="#"><i class="bx bxl-facebook"></i></a>
            <a href="#"><i class="bx bxl-twitter"></i></a>
            <a href="#"><i class="bx bxl-instagram"></i></a>
            <a href="#"><i class="bx bxl-youtube"></i></a>
        </div>
        <div class="links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
            <a href="#">Our Company</a>
        </div>
        <p>&#169; 2025 FFKS MART</p>
    </section>

    <script src="../JS/main.js"></script>
</body>

</html>