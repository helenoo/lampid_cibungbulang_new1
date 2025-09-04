<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin_desa') {
    header("Location: ../../login.php");
    exit();
}
require_once '../../includes/db_connect.php';

$nama_desa = "Tidak Ditemukan";
if (isset($_SESSION['id_desa'])) {
    $stmt_desa = $conn->prepare("SELECT nama_desa FROM desa WHERE id = ?");
    if ($stmt_desa) {
        $stmt_desa->bind_param("i", $_SESSION['id_desa']);
        $stmt_desa->execute();
        $result_desa = $stmt_desa->get_result();
        if ($result_desa->num_rows > 0) {
            $desa = $result_desa->fetch_assoc();
            $nama_desa = $desa['nama_desa'];
        }
        $stmt_desa->close();
    }
}

$stmt_arsip = $conn->prepare("SELECT bulan, tahun, tanggal_upload FROM arsip_lampid WHERE id_desa = ? ORDER BY tahun DESC, bulan DESC");
$stmt_arsip->bind_param("i", $_SESSION['id_desa']);
$stmt_arsip->execute();
$riwayat_uploads = $stmt_arsip->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Desa - <?php echo htmlspecialchars($nama_desa); ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=FINAL_DESA">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="admin-page">
    <main class="main-content-desa">
        <div class="content">
            <div class="welcome-header">
                <h2>Dashboard Admin Desa - <?php echo htmlspecialchars($nama_desa); ?></h2>
                <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>!</p>
            </div>

            <div class="desa-dashboard-grid">
                
                <div class="template-section form-wrapper">
                    <h3><i class="fas fa-file-download"></i> Unduh Template Laporan</h3>
                    <p>Unduh template di bawah ini untuk mengisi laporan. Setelah diisi, unggah kembali di samping kanan.</p>
                    <div class="template-links">
                        <a href="../../templates/DATA PERKEMBANGAN PENDUDUK.xlsx" class="button" download>Template Perkembangan Penduduk (Excel)</a>
                        <a href="../../templates/LAPORAN BULANAN PENDUDUK MENURUT KELOMPOK UMUR.xlsx" class="button" download>Template Kelompok Umur (Excel)</a>
                        <a href="../../templates/LAPORAN BULANAN PENDUDUK MENURUT KELOMPOK UMUR.docx" class="button button-secondary" download>Template Kelompok Umur (Word)</a>
                    </div>
                </div>

                <div class="upload-section">
                    <a href="upload.php" class="button button-upload" style="margin-bottom: 2rem; display: block; text-align: center;">
                        <i class="fas fa-upload"></i> Klik di Sini untuk Upload Laporan
                    </a>
                    <div class="table-wrapper">
                        <h3 style="margin-bottom: 1rem;">Riwayat Upload Laporan</h3>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Periode Laporan</th>
                                    <th>Waktu Upload</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($riwayat_uploads && $riwayat_uploads->num_rows > 0): ?>
                                    <?php while($row = $riwayat_uploads->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo date("F Y", mktime(0, 0, 0, $row['bulan'], 1, $row['tahun'])); ?></td>
                                            <td><?php echo date("d F Y, H:i:s", strtotime($row['tanggal_upload'])); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="2" style="text-align: center;">Belum ada data yang diupload.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <a href="update_profile.php" class="button button-secondary">Ganti Password</a>
            <a href="../../logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar?');" class="button button-logout">Logout</a>
        </div>
    </main>
</body>
</html>