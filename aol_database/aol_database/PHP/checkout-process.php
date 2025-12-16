<?php
session_start();
include("config.php");

// Cek apakah user sudah login
if (!isset($_SESSION['valid'])) {
    die("Akses ditolak. Silakan login user terlebih dahulu.");
}

// PERBAIKAN 1: Hapus intval(). 
// ID user di database adalah string (contoh: 'USR-0C02D'), bukan integer.
$id_user = $_SESSION['valid'];

$id_member = $_POST['id_member'] ?? null;
// Pastikan id_member null jika string kosong agar tidak error
if ($id_member === "") $id_member = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $bank = $_POST['bank'] ?? '';
    $cart = json_decode($_POST['cart'], true);

    if (empty($cart)) {
        die("Keranjang belanja kosong.");
    }

    // 1. Ambil ID Metode Pembayaran
    $stmtMetode = mysqli_prepare($con, "SELECT id_metode FROM metode_pembayaran WHERE nama_metode = ? LIMIT 1");
    mysqli_stmt_bind_param($stmtMetode, "s", $bank);
    mysqli_stmt_execute($stmtMetode);
    $resMetode = mysqli_stmt_get_result($stmtMetode);

    if ($row = mysqli_fetch_assoc($resMetode)) {
        $id_metode = $row['id_metode'];
    } else {
        $id_metode = 1; // Default jika tidak ditemukan
    }
    mysqli_stmt_close($stmtMetode);

    // 2. Buat ID Transaksi Baru
    $id_transaksi = "TRX-" . date("ym") . "-" . strtoupper(substr(md5(uniqid()), 0, 6));

    // 3. Hitung Total
    $total_belanja_kotor = 0;
    $total_bayar_bersih = 0;
    $total_diskon = 0;

    foreach ($cart as $item) {
        $qty = intval($item['quantity']);
        $harga_final = floatval($item['price']);
        $harga_asli = floatval($item['original_price'] ?? $item['price']);

        $total_belanja_kotor += ($harga_asli * $qty);
        $total_bayar_bersih += ($harga_final * $qty);
    }

    $total_diskon = $total_belanja_kotor - $total_bayar_bersih;

    // 4. Simpan ke Database
    mysqli_begin_transaction($con);

    try {
        // Insert Header Transaksi
        $sqlTrans = "INSERT INTO transaksi 
            (id_transaksi, id_member, id_user, id_admin, id_metode, tanggal_transaksi, total_belanja, total_diskon, total_bayar, status_pembayaran) 
            VALUES (?, ?, ?, NULL, ?, NOW(), ?, ?, ?, 'unpaid')";

        $stmtTrans = mysqli_prepare($con, $sqlTrans);
        
        // PERBAIKAN 2: Ubah format binding dari "ssiiddd" menjadi "sssiddd"
        // Urutan: 
        // 1. id_transaksi (s - string)
        // 2. id_member (s - string)
        // 3. id_user (s - string) <-- SEBELUMNYA 'i' (integer), INI PENYEBAB ERROR
        // 4. id_metode (i - integer)
        // 5. total_belanja (d - double)
        // 6. total_diskon (d - double)
        // 7. total_bayar (d - double)
        mysqli_stmt_bind_param(
            $stmtTrans,
            "sssiddd",
            $id_transaksi,
            $id_member,
            $id_user,
            $id_metode,
            $total_belanja_kotor,
            $total_diskon,
            $total_bayar_bersih
        );
        mysqli_stmt_execute($stmtTrans);
        mysqli_stmt_close($stmtTrans);

        // Siapkan Statement Detail & Diskon
        $sqlDetail = "INSERT INTO detail_transaksi 
            (id_transaksi, id_produk, qty, harga_satuan_saat_ini, subtotal) 
            VALUES (?, ?, ?, ?, ?)";
        $stmtDetail = mysqli_prepare($con, $sqlDetail);

        $sqlDiskon = "INSERT INTO diskon_produk 
            (id_transaksi, id_member, id_produk, jumlah_potongan) 
            VALUES (?, ?, ?, ?)";
        $stmtDiskon = mysqli_prepare($con, $sqlDiskon);

        // Loop Insert Item Keranjang
        foreach ($cart as $item) {
            $id_produk = $item['id'];
            $qty = intval($item['quantity']);
            $harga_final = floatval($item['price']);
            $harga_asli = floatval($item['original_price'] ?? $item['price']);

            $subtotal = $harga_final * $qty;

            // Bind Detail (s: string, s: string, i: int, d: double, d: double)
            mysqli_stmt_bind_param(
                $stmtDetail,
                "ssidd",
                $id_transaksi,
                $id_produk,
                $qty,
                $harga_final,
                $subtotal
            );
            mysqli_stmt_execute($stmtDetail);

            // Simpan Data Diskon Jika Ada
            if ($harga_asli > $harga_final && !empty($id_member)) {
                $potongan = ($harga_asli - $harga_final) * $qty;

                mysqli_stmt_bind_param(
                    $stmtDiskon,
                    "sssd",
                    $id_transaksi,
                    $id_member,
                    $id_produk,
                    $potongan
                );
                mysqli_stmt_execute($stmtDiskon);
            }
        }

        mysqli_stmt_close($stmtDetail);
        mysqli_stmt_close($stmtDiskon);

        mysqli_commit($con);

        // Redirect Sukses
        echo "
        <script>
            localStorage.removeItem('cartItems');
            window.location.href = '../HTML/checkout.php?success=1&id=$id_transaksi';
        </script>";
        exit;
        
    } catch (Exception $e) {
        mysqli_rollback($con);
        // Tampilkan pesan error yang lebih jelas
        die("Terjadi kesalahan transaksi: " . $e->getMessage());
    }
}
?>