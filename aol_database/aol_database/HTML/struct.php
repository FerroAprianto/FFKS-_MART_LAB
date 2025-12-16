<?php
include("../PHP/config.php");

$id = isset($_GET['id']) ? $_GET['id'] : "";
if ($id === "") {
    die("ID struk tidak ditemukan.");
}

$trxQuery = mysqli_query($con, "
    SELECT t.*, m.nama_metode
    FROM transaksi t
    LEFT JOIN metode_pembayaran m ON t.id_metode = m.id_metode
    WHERE t.id_transaksi = '$id'
    LIMIT 1
");

if (!$trxQuery || mysqli_num_rows($trxQuery) === 0) {
    die("Data transaksi tidak ditemukan.");
}

$data = mysqli_fetch_assoc($trxQuery);

$detailQuery = mysqli_query($con, "
    SELECT d.*, p.nama_produk
    FROM detail_transaksi d
    LEFT JOIN produk p ON d.id_produk = p.id_produk
    WHERE d.id_transaksi = '$id'
");

$formattedTotal = number_format($data['total_bayar'], 0, ',', '.');
$formattedDate = date('d F Y, H:i', strtotime($data['tanggal_transaksi']));
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Struk Pembayaran</title>
    <link rel="stylesheet" href="../CSS/struct.css" />
    <link rel="shortcut icon" href="../ASSET/logo-Url.png" />
</head>

<body>

    <div class="receipt-card">
        <p class="receipt-title">Struk Pembayaran</p>
        <p class="receipt-sub">Terima kasih telah berbelanja di FFKS MART.</p>
        <div class="line"></div>

        <div class="info">
            <div class="row">
                <span>ID Transaksi</span>
                <span><?= $data['id_transaksi'] ?></span>
            </div>
            <div class="row">
                <span>Metode Pembayaran</span>
                <span><?= $data['nama_metode'] ?></span>
            </div>
            <div class="row">
                <span>Status</span>
                <span><?= ucfirst($data['status_pembayaran']) ?></span>
            </div>
            <div class="row">
                <span>Tanggal</span>
                <span><?= $formattedDate ?></span>
            </div>
        </div>

        <div class="section-title">Detail Pembelian</div>
        <div class="items">

            <?php while ($row = mysqli_fetch_assoc($detailQuery)) : ?>
                <div class="item">
                    <span><?= htmlspecialchars($row['nama_produk']) ?> (x<?= $row['qty'] ?>)</span>
                    <span>Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></span>
                </div>
            <?php endwhile; ?>

        </div>

        <div class="line"></div>
        <div class="total">
            <span>Total Bayar</span>
            <span>Rp <?= $formattedTotal ?></span>
        </div>

        <div class="actions">
            <button class="back-btn" onclick="window.location.href='../HTML/index.php'">Kembali ke Beranda</button>
            <button class="back-btn alt" onclick="window.print()">Cetak Struk</button>
        </div>
    </div>

</body>

</html>