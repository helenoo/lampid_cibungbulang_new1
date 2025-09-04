<?php
require_once 'includes/db_connect.php';

$info_kecamatan = null;
$info_result = $conn->query("SELECT * FROM info_kecamatan WHERE id = 1");
if ($info_result && $info_result->num_rows > 0) {
    $info_kecamatan = $info_result->fetch_assoc();
}

$rekap_data = ['Lahir' => 0, 'Mati' => 0, 'Pindah' => 0, 'Datang' => 0];
$rekap_query = $conn->query("SELECT kategori, SUM(jumlah) as total FROM data_lampid GROUP BY kategori");
if ($rekap_query) {
    while($row = $rekap_query->fetch_assoc()) {
        if (isset($rekap_data[$row['kategori']])) {
            $rekap_data[$row['kategori']] = $row['total'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi LAMPID - Kecamatan Cibungbulang</title>
    <link rel="stylesheet" href="assets/css/style.css?v=FINAL">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="landing-page">

    <header class="landing-header">
        <div class="container">
            <div class="logo">
                <img src="assets/image/logo.png" alt="Logo Kecamatan">
                <div>
                    <h1>LAMPID</h1>
                    <p>Kecamatan Cibungbulang</p>
                </div>
            </div>
            <a href="login.php" class="button">Login Admin</a>
        </div>
    </header>

    <main>
        <section class="hero">
            <div class="container">
                <h2>Selamat Datang di Sistem Informasi LAMPID</h2>
                <p class="subtitle">Pusat data Lahir, Mati, Pindah, dan Datang penduduk Kecamatan Cibungbulang.</p>
            </div>
        </section>

        <?php if ($info_kecamatan): ?>
        <section class="about">
            <div class="container">
                <div class="about-card">
                    <h3>Tentang Kecamatan</h3>
                    <p><?php echo nl2br(htmlspecialchars($info_kecamatan['tentang_kecamatan'])); ?></p>
                </div>
                <div class="about-card">
                    <h3>Deskripsi LAMPID</h3>
                    <p><?php echo nl2br(htmlspecialchars($info_kecamatan['deskripsi_lampid'])); ?></p>
                </div>
            </div>
        </section>
        <?php endif; ?>

    <footer class="landing-footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Kecamatan Cibungbulang. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>