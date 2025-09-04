<?php
$page_title = 'Input & Profile';
$active_page = 'profile';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';

// Inisialisasi variabel untuk data dan pesan
$info = null;
$update_message = '';
$update_status = '';
$input_message = '';
$input_status = '';

// Logika untuk UPDATE informasi kecamatan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_info'])) {
    // ... (Logika update info kecamatan Anda tetap sama)
    $jumlah_penduduk = $_POST['jumlah_penduduk'];
    $luas_wilayah = $_POST['luas_wilayah'];
    $luas_tanah = $_POST['luas_tanah'];
    $deskripsi_lampid = $_POST['deskripsi_lampid'];
    $tentang_kecamatan = $_POST['tentang_kecamatan'];

    $stmt = $conn->prepare("UPDATE info_kecamatan SET jumlah_penduduk=?, luas_wilayah=?, luas_tanah=?, deskripsi_lampid=?, tentang_kecamatan=? WHERE id=1");
    if ($stmt) {
        $stmt->bind_param("iddss", $jumlah_penduduk, $luas_wilayah, $luas_tanah, $deskripsi_lampid, $tentang_kecamatan);
        if ($stmt->execute()) {
            $update_message = 'Informasi kecamatan berhasil diperbarui!';
            $update_status = 'success';
        } else {
            $update_message = 'Gagal memperbarui informasi.';
            $update_status = 'danger';
        }
        $stmt->close();
    }
}

// Logika untuk INPUT data LAMPID
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['input_lampid'])) {
    $desa_id = $_POST['desa_id'];
    $kategori = $_POST['kategori'];
    $jumlah = $_POST['jumlah'];
    $tanggal_data = $_POST['tanggal_data'];
    $admin_id = $_SESSION['admin_id'];

    $stmt = $conn->prepare("INSERT INTO data_lampid (desa_id, kategori, jumlah, tanggal_data, diinput_oleh) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("isisi", $desa_id, $kategori, $jumlah, $tanggal_data, $admin_id);
        if ($stmt->execute()) {
            $input_message = 'Data LAMPID berhasil ditambahkan!';
            $input_status = 'success';
        } else {
            $input_message = 'Gagal menambahkan data LAMPID.';
            $input_status = 'danger';
        }
        $stmt->close();
    }
}

// Ambil data terbaru setelah proses POST
$info_result = $conn->query("SELECT * FROM info_kecamatan WHERE id = 1");
if ($info_result && $info_result->num_rows > 0) {
    $info = $info_result->fetch_assoc();
}

// Ambil daftar desa untuk dropdown
$desa_list_result = $conn->query("SELECT id, nama_desa FROM desa ORDER BY nama_desa ASC");

?>

<main class="main-content">
    <div class="content">
        <div class="profile-grid">

            <div class="form-wrapper">
                <div class="welcome-header">
                    <h2>Input Data LAMPID</h2>
                    <p>Masukkan data baru untuk Lahir, Mati, Pindah, atau Datang.</p>
                </div>

                <?php if($input_message): ?>
                    <div class="alert alert-<?php echo $input_status; ?>"><?php echo $input_message; ?></div>
                <?php endif; ?>
                
                <form action="profile.php" method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="desa_id">Pilih Desa</label>
                        <select id="desa_id" name="desa_id" required>
                            <option value="">-- Pilih Desa --</option>
                            <?php if ($desa_list_result): while($desa = $desa_list_result->fetch_assoc()): ?>
                                <option value="<?php echo $desa['id']; ?>"><?php echo htmlspecialchars($desa['nama_desa']); ?></option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <select id="kategori" name="kategori" required>
                            <option value="Lahir">Lahir</option>
                            <option value="Mati">Mati</option>
                            <option value="Pindah">Pindah</option>
                            <option value="Datang">Datang</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah Jiwa</label>
                        <input type="number" id="jumlah" name="jumlah" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_data">Tanggal Data</label>
                        <input type="date" id="tanggal_data" name="tanggal_data" required>
                    </div>
                    <button type="submit" name="input_lampid" class="button">Simpan Data</button>
                </form>
            </div>

            <div class="form-wrapper">
                <div class="welcome-header">
                    <h2>Profil Kecamatan</h2>
                    <p>Perbarui informasi umum mengenai Kecamatan Cibungbulang.</p>
                </div>

                <?php if($update_message): ?>
                    <div class="alert alert-<?php echo $update_status; ?>"><?php echo $update_message; ?></div>
                <?php endif; ?>
                
                <?php if ($info): ?>
                    <form action="profile.php" method="POST" class="profile-form">
                        <div class="form-group">
                            <label for="jumlah_penduduk">Jumlah Penduduk</label>
                            <input type="number" id="jumlah_penduduk" name="jumlah_penduduk" value="<?php echo htmlspecialchars($info['jumlah_penduduk']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="luas_wilayah">Luas Wilayah (km²)</label>
                            <input type="text" id="luas_wilayah" name="luas_wilayah" value="<?php echo htmlspecialchars($info['luas_wilayah']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="luas_tanah">Luas Tanah (Ha)</label>
                            <input type="text" id="luas_tanah" name="luas_tanah" value="<?php echo htmlspecialchars($info['luas_tanah']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="deskripsi_lampid">Deskripsi LAMPID</label>
                            <textarea id="deskripsi_lampid" name="deskripsi_lampid" rows="4" required><?php echo htmlspecialchars($info['deskripsi_lampid']); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="tentang_kecamatan">Tentang Kecamatan</label>
                            <textarea id="tentang_kecamatan" name="tentang_kecamatan" rows="4" required><?php echo htmlspecialchars($info['tentang_kecamatan']); ?></textarea>
                        </div>
                        <button type="submit" name="update_info" class="button">Update Informasi</button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-danger">Gagal memuat informasi kecamatan.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>