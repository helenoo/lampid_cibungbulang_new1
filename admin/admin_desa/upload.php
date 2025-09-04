<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin_desa') {
    header("Location: ../../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Laporan</title>
    <link rel="stylesheet" href="../../assets/css/style.css?v=FINAL_DESA">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body class="admin-page">
    <main class="main-content-desa">
         <div class="content">
            <div class="welcome-header">
                <h2>Upload Arsip Data Lampid Bulanan</h2>
                <p>Silakan pilih periode dan unggah file laporan yang sesuai.</p>
            </div>

            <?php if(isset($_GET['status']) && $_GET['status'] == 'gagal'): ?>
                <div class="alert alert-danger">
                    <strong>Upload Gagal!</strong> <?php echo htmlspecialchars($_GET['pesan']); ?>
                </div>
            <?php endif; ?>

            <div class="form-wrapper">
                <form action="proses_upload.php" method="post" enctype="multipart/form-data" class="profile-form">
                    <div class="form-group">
                        <label for="bulan">Bulan Laporan:</label>
                        <select name="bulan" id="bulan" required>
                            <?php for($m=1; $m<=12; ++$m){ echo '<option value="'. $m .'">'. date('F', mktime(0, 0, 0, $m, 1)) .'</option>'; } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tahun">Tahun Laporan:</label>
                        <input type="number" name="tahun" id="tahun" value="<?php echo date('Y'); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="file_perkembangan">File Data Perkembangan Penduduk (XLSX):</label>
                        <input type="file" name="file_perkembangan" id="file_perkembangan" accept=".xlsx" required>
                    </div>

                    <div class="form-group">
                        <label for="file_umur">File Laporan Kelompok Umur (XLSX/DOCX):</label>
                        <input type="file" name="file_umur" id="file_umur" accept=".xlsx,.docx" required>
                    </div>
                    <br>
                    <button type="submit" class="button">Upload Arsip</button>
                    <a href="dashboard.php" class="button button-secondary">Kembali ke Dashboard</a>
                </form>
            </div>
        </div>
    </main>
</body>
</html>