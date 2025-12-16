<?php
session_start();
include("../PHP/config.php"); // Pastikan path config benar

$id_user = isset($_SESSION['valid']) ? $_SESSION['valid'] : NULL;
$id_member = isset($_SESSION['member_id']) ? $_SESSION['member_id'] : NULL;

if ($id_user === NULL && $id_member === NULL) {
    die("Silakan login terlebih dahulu.");
}

$id_member = $_SESSION['member_id'];

// Cek Member
$cekMember = mysqli_query($con, "SELECT * FROM member WHERE id_member = '$id_member' LIMIT 1");
if (!$cekMember || mysqli_num_rows($cekMember) === 0) {
    header("Location: ../HTML/index.php?error=notmember");
    exit;
}
$member = mysqli_fetch_assoc($cekMember);

// QUERY PENTING: Ambil semua produk yang memiliki diskon (atau semua produk)
// Kita ambil dari tabel 'produk' yang sudah ditambah kolom 'diskon_persen'
$queryProduk = mysqli_query($con, "SELECT * FROM produk WHERE diskon_persen > 0"); 
// Catatan: Jika ingin menampilkan SEMUA produk (diskon maupun tidak), hapus bagian "WHERE diskon_persen > 0"
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo Member</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="../ASSET/logo-Url.png" />

    <style>
        /* CSS SAMA SEPERTI SEBELUMNYA */
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap");
        :root { --main--color: #008148; --second--color: #1e3932; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }
        body { background: #f2f2f2; }
        header { position: fixed; width: 100%; top: 0; right: 0; z-index: 1000; display: flex; align-items: center; justify-content: space-between; background: #fff; box-shadow: 0 4px 41px rgb(14 55 54 / 14%); padding: 15px 10%; }
        .logo { display: flex; align-items: center; gap: 15px; }
        .logo img { width: 40px; }
        .logoname { font-weight: 700; font-size: 1.2rem; color: var(--second--color); }
        .navbar { display: flex; flex: 1; justify-content: center; gap: 20px; }
        .navbar a { font-size: 1rem; color: var(--second--color); font-weight: 600; text-transform: uppercase; padding: 11px 20px; text-decoration: none; }
        .navbar a:hover { color: var(--main--color); }
        .cart-icon { position: relative; cursor: pointer; }
        .cart-icon i { font-size: 24px; }
        .cart-icon span { position: absolute; top: -8px; right: -10px; background: var(--main--color); color: #fff; font-size: 12px; padding: 1px 3px; border-radius: 50%; }
        section { padding: 100px 10% 50px; }
        .heading { text-align: center; margin-bottom: 2rem; }
        .heading span { font-size: 1rem; font-weight: 600; color: var(--second--color); }
        .heading h1 { font-size: 2rem; color: var(--main--color); }
        .shop-container { display: flex; flex-wrap: wrap; gap: 1rem; }
        .shop-container .box { flex: 1 1 15rem; background: var(--main--color); padding: 20px; display: flex; text-align: center; flex-direction: column; align-items: center; margin-top: 1rem; border-radius: 0.5rem; color: #fff; }
        .shop-container .box .box-img { width: 150px; height: 150px; margin-top: -50px; }
        .shop-container .box .box-img img { width: 100%; height: 100%; object-fit: contain; }
        .stars { margin: 1rem 0 0.1rem; }
        .stars .bx { color: #ebdbc8; }
        .box .btn { border: 2px solid #ebdbc8; color: #ebdbc8; padding: 7px 16px; border-radius: 40px; margin-top: 0.5rem; background: transparent; cursor: pointer; }
        .box .btn:hover { background: #ebdbc8; color: var(--second--color); }
        .cart-model { position: fixed; top: 0; right: -100%; max-width: 500px; width: 100%; height: 100vh; background: white; padding: 2rem; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); z-index: 10001; transition: all 0.5s cubic-bezier(0.075, 0.82, 0.165, 1); }
        .open-cart { right: 0; }
        .cart-head { display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #ebdbc8; padding-bottom: 1rem; }
        .close-btn { background: #ebdbc8; padding: 5px 12px; border-radius: 4px; cursor: pointer; border: none; }
        .cart-item { display: flex; align-items: center; padding: 10px; border-radius: 5px; background-color: #ebdbc8; gap: 1rem; color: black; margin-bottom: 10px; }
        .cart-item-image { width: 60px; height: 60px; object-fit: contain; background: white; padding: 5px; border-radius: 8px; }
        .cart-item-details { flex-grow: 1; }
        .cart-item-name { font-weight: 600; color: #488f6f; font-size: 0.9rem; }
        .cart-item-price { font-size: 0.85rem; }
        .quantity-controls { display: flex; align-items: center; gap: 0.5rem; }
        .quantity-controls button { background: #eee; border: none; padding: 5px 8px; cursor: pointer; border-radius: 5px; }
        .remove-item { background: #ff4757; color: white; border: none; padding: 5px 8px; border-radius: 5px; cursor: pointer; }
        .cart-total { font-weight: 600; font-size: 1.2rem; text-align: right; border-top: 1px solid #ebdbc8; padding-top: 1rem; margin-top: 1rem; }
        .checkout-btn { padding: 10px 0; background: var(--main--color); color: #fff; text-align: center; border-radius: 8px; cursor: pointer; margin-top: 10px; }
        .product-discount { color: #ffeb3b; font-weight: bold; font-size: 14px; margin-top: 5px; display: block; }
        .discount-badge { background: #ff5252; color: white; padding: 2px 8px; border-radius: 6px; font-size: 12px; margin-right: 5px; }
        .product-price { text-decoration: line-through; color: #ccc; font-size: 13px; }
        @media(max-width:768px) { header { flex-direction: column; gap: 10px; padding: 10px 5%; } .navbar { flex-wrap: wrap; } .shop-container { justify-content: center; } }
    </style>
</head>

<body>

    <header>
        <div class="logo">
            <img src="../ASSET/Iconsurel.png" alt="Logo">
            <span class="logoname">FFKS MART</span>
        </div>
        <nav class="navbar">
            <a href="../HTML/index.php">Home</a>
            <a href="#shop">Promo</a>
        </nav>
        <div class="cart-icon" id="cart-icon">
            <i class='bx bx-shopping-bag'></i>
            <span id="cart-count">0</span>
        </div>
    </header>

    <section class="shop" id="shop">
        <div class="heading">
            <span>SELAMAT DATANG, <b><?php echo htmlspecialchars($member['nama_member']); ?></b></span>
            <h1>Dapatkan diskon meriah sebagai member</h1>
        </div>

        <div class="shop-container">
            <?php 
            // === BAGIAN OTOMATIS (LOOPING) ===
            // Cek apakah ada produk promo
            if(mysqli_num_rows($queryProduk) > 0) {
                while($row = mysqli_fetch_assoc($queryProduk)) {
                    // Siapkan variabel agar kode lebih rapi
                    $id_produk = $row['id_produk'];
                    $nama_produk = $row['nama_produk'];
                    $gambar = $row['gambar']; // Nama file di DB, misal: lengkeng.png
                    $harga = $row['harga'];
                    $diskon = $row['diskon_persen']; // Diambil dari kolom baru
            ?>
            
            <div class="box" data-id="<?php echo $id_produk; ?>" data-diskon="<?php echo $diskon; ?>">
                <div class="box-img">
                    <img src="../ASSET/<?php echo $gambar; ?>" alt="<?php echo $nama_produk; ?>">
                </div>
                <div class="stars">
                    <i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star"></i><i class="bx bxs-star-half"></i>
                </div>
                <h2 class="product-name"><?php echo $nama_produk; ?></h2>
                
                <span class="product-price" data-price="<?php echo $harga; ?>">
                    <?php echo "Rp " . number_format($harga, 0, ',', '.'); ?>
                </span>
                
                <span class="product-discount"></span>
                
                <a class="btn" onclick="addToCart(this.parentElement)">Tambahkan</a>
            </div>

            <?php 
                } // Akhir While
            } else {
                echo "<p style='text-align:center; width:100%;'>Belum ada produk promo saat ini.</p>";
            }
            ?>
        </div>
    </section>

    <div class="cart-model" id="cart-model">
        <div class="cart-head">
            <h2>Your Cart</h2>
            <button class="close-btn" onclick="toggleCart(false)">X</button>
        </div>
        <ul id="cart-items"></ul>
        <div class="cart-total">Total: <span id="total-price">Rp 0</span></div>
        <div class="checkout-btn" onclick="goCheckout()">Checkout</div>
    </div>

    <script>
        const ID_MEMBER = "<?php echo $id_member; ?>";
        let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];
        const formatRp = num => "Rp " + num.toLocaleString("id-ID");

        function updateLocalStorage() {
            localStorage.setItem("cartItems", JSON.stringify(cartItems));
            localStorage.setItem("id_member", ID_MEMBER);
        }

        // Fungsi Load Awal untuk menghitung tampilan diskon
        document.addEventListener("DOMContentLoaded", () => {
            updateCartDisplay();
            
            // Loop semua box produk untuk hitung harga diskon tampilan awal
            document.querySelectorAll(".box").forEach(box => {
                const hargaAsli = parseFloat(box.querySelector(".product-price").dataset.price);
                const diskon = parseFloat(box.dataset.diskon);
                
                if(diskon > 0) {
                    const hargaDiskon = hargaAsli - (hargaAsli * diskon / 100);
                    box.querySelector(".product-discount").innerHTML =
                        `<span class='discount-badge'>${diskon}% OFF</span> ${formatRp(hargaDiskon)}`;
                } else {
                    // Jika tidak ada diskon
                    box.querySelector(".product-discount").innerHTML = formatRp(hargaAsli);
                    box.querySelector(".product-price").style.display = 'none'; // Sembunyikan harga coret
                }
            });
        });

        function addToCart(box) {
            const id = box.dataset.id;
            const name = box.querySelector(".product-name").textContent;
            const img = box.querySelector("img").src;

            const hargaAsli = parseFloat(box.querySelector(".product-price").dataset.price);
            const diskon = parseFloat(box.dataset.diskon);
            const hargaSetelahDiskon = hargaAsli - (hargaAsli * diskon / 100);

            const existing = cartItems.find(item => item.id === id);

            if (existing) {
                existing.quantity++;
            } else {
                cartItems.push({
                    id: id,
                    name: name,
                    image: img,
                    price: hargaSetelahDiskon,
                    original_price: hargaAsli,
                    quantity: 1,
                    id_member: ID_MEMBER
                });
            }

            updateLocalStorage();
            updateCartDisplay();
            toggleCart(true);
        }

        function removeItem(id) {
            cartItems = cartItems.filter(item => item.id !== id);
            updateLocalStorage();
            updateCartDisplay();
        }

        function changeQuantity(id, delta) {
            const item = cartItems.find(i => i.id === id);
            if (!item) return;

            item.quantity += delta;
            if (item.quantity <= 0) {
                removeItem(id);
            } else {
                updateLocalStorage();
                updateCartDisplay();
            }
        }

        function toggleCart(open) {
            const cart = document.getElementById("cart-model");
            if (open) cart.classList.add("open-cart");
            else cart.classList.remove("open-cart");
        }

        function updateCartDisplay() {
            const cartList = document.getElementById("cart-items");
            const totalEl = document.getElementById("total-price");
            const countEl = document.getElementById("cart-count");

            cartList.innerHTML = "";
            let total = 0;
            let count = 0;

            cartItems.forEach(item => {
                const li = document.createElement("li");
                li.className = "cart-item";
                li.innerHTML = `
            <img src="${item.image}" class="cart-item-image">
            <div class="cart-item-details">
                <div class="cart-item-name">${item.name}</div>
                <div class="cart-item-price">${formatRp(item.price)} x ${item.quantity}</div>
                ${item.original_price > item.price ? `<div style="font-size:10px; color:grey; text-decoration:line-through;">${formatRp(item.original_price)}</div>` : ''}
            </div>
            <div class="quantity-controls">
                <button onclick="changeQuantity('${item.id}', -1)">-</button>
                <button onclick="changeQuantity('${item.id}', 1)">+</button>
            </div>
            <button class="remove-item" onclick="removeItem('${item.id}')">X</button>
        `;
                cartList.appendChild(li);
                total += item.price * item.quantity;
                count += item.quantity;
            });

            totalEl.textContent = formatRp(total);
            countEl.textContent = count;
        }

        document.getElementById("cart-icon").onclick = () => toggleCart(true);

        function goCheckout() {
            if (cartItems.length === 0) {
                alert("Keranjang kosong!");
                return;
            }
            localStorage.setItem("id_member", ID_MEMBER);
            window.location.href = "../HTML/checkout.php";
        }
    </script>
</body>
</html>