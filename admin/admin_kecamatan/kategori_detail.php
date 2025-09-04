<?php
$page_title = 'Detail Kategori Data';
$active_page = 'kategori';

// --- PERBAIKAN PATH ---
// Path dari admin_kecamatan/ ke partials/ harus naik satu level (../)
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';

// Validasi kategori setelah header di-include
$kategori = $_GET['kategori'] ?? 'Lahir';
if (!in_array($kategori, ['Lahir', 'Mati', 'Pindah', 'Datang'])) {
    // Redirect ke halaman kategori jika kategori tidak valid
    header('Location: kategori.php');
    exit();
}

// Ambil tahun yang dipilih dari URL, atau gunakan tahun saat ini
$selected_year = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

// Ambil daftar tahun unik dari database untuk filter dropdown
$years_q = $conn->query("SELECT DISTINCT YEAR(tanggal_data) as tahun FROM data_lampid ORDER BY tahun DESC");

// Siapkan query utama untuk mengambil data per desa
$sql = "
    SELECT
        d.nama_desa,
        COALESCE(SUM(dl.jumlah), 0) as total_jumlah
    FROM
        desa d
    LEFT JOIN
        data_lampid dl ON d.id = dl.desa_id AND dl.kategori = ? AND YEAR(dl.tanggal_data) = ?
    GROUP BY
        d.id, d.nama_desa
    ORDER BY
        d.nama_desa ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $kategori, $selected_year);
$stmt->execute();
$result = $stmt->get_result();
$total_kecamatan = 0;
?>

<main class="main-content">
    <div class="content">
        <div class="welcome-header">
            <h2>Data Detail: <?php echo htmlspecialchars($kategori); ?> Tahun <?php echo $selected_year; ?></h2>
            <p>Berikut adalah rincian jumlah penduduk untuk kategori "<?php echo strtolower(htmlspecialchars($kategori)); ?>" di setiap desa.</p>
        </div>

        <form method="GET" action="kategori_detail.php" class="filter-form">
            <input type="hidden" name="kategori" value="<?php echo htmlspecialchars($kategori); ?>">
            <div class="form-group">
                <label for="tahun">Pilih Tahun:</label>
                <select name="tahun" id="tahun" onchange="this.form.submit()">
                    <?php if ($years_q && $years_q->num_rows > 0): ?>
                        <?php while($year_row = $years_q->fetch_assoc()): ?>
                            <option value="<?php echo $year_row['tahun']; ?>" <?php if ($year_row['tahun'] == $selected_year) echo 'selected'; ?>>
                                <?php echo $year_row['tahun']; ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="<?php echo date('Y'); ?>"><?php echo date('Y'); ?></option>
                    <?php endif; ?>
                </select>
            </div>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Desa</th>
                        <th>Jumlah Jiwa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nama_desa']); ?></td>
                            <td><?php echo number_format($row['total_jumlah']); ?></td>
                        </tr>
                        <?php $total_kecamatan += $row['total_jumlah']; ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Tidak ada data untuk ditampilkan pada tahun <?php echo $selected_year; ?>.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>TOTAL KECAMATAN</strong></td>
                        <td><strong><?php echo number_format($total_kecamatan); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br>
        <a href="kategori.php" class="button button-secondary">Kembali ke Ringkasan Kategori</a>
    </div>
</main>

<?php
$stmt->close();
// --- PERBAIKAN PATH ---
require_once __DIR__ . '/../partials/footer.php';
?>