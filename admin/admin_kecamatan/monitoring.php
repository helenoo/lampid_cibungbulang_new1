<?php
$active_page = 'monitoring';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';

// KAMUS NAMA BULAN DALAM BAHASA INDONESIA
$nama_bulan_indonesia = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$bulan_filter = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('n');
$tahun_filter = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

$semua_desa_result = $conn->query("SELECT id, nama_desa FROM desa ORDER BY nama_desa ASC");

$desa_sudah_upload = [];
$stmt = $conn->prepare("SELECT id_arsip, id_desa, tanggal_upload FROM arsip_lampid WHERE bulan = ? AND tahun = ?");
if ($stmt) {
    $stmt->bind_param("ii", $bulan_filter, $tahun_filter);
    $stmt->execute();
    $result_uploaded = $stmt->get_result();
    if ($result_uploaded) {
        while($row = $result_uploaded->fetch_assoc()){
            if (isset($row['id_desa'])) {
                $desa_sudah_upload[$row['id_desa']] = [
                    'id_arsip' => $row['id_arsip'],
                    'tanggal' => $row['tanggal_upload']
                ];
            }
        }
    }
    $stmt->close();
}
?>
<main class="main-content">
    <div class="content">
        <div class="welcome-header">
            <h2>Monitoring Upload Laporan Desa</h2>
            <p>Pilih periode untuk melihat status upload laporan dari setiap desa.</p>
        </div>
        <form method="GET" action="monitoring.php" class="filter-form">
            <div class="form-group">
                <label for="bulan">Bulan:</label>
                <select name="bulan" id="bulan">
                    <?php foreach ($nama_bulan_indonesia as $nomor_bulan => $nama_bulan): ?>
                    <option value="<?php echo $nomor_bulan; ?>" <?php if($nomor_bulan == $bulan_filter) echo 'selected'; ?>>
                        <?php echo $nama_bulan; ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tahun">Tahun:</label>
                <input type="number" name="tahun" id="tahun" value="<?php echo $tahun_filter; ?>" min="2020" max="<?php echo date('Y'); ?>">
            </div>
            <button type="submit" class="button">Filter</button>
        </form>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Desa</th>
                        <th>Status Upload</th>
                        <th>Waktu Upload</th>
                        <th style="text-align: center;">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($semua_desa_result && $semua_desa_result->num_rows > 0): $nomor = 1; ?>
                        <?php while($desa = $semua_desa_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $nomor++; ?></td>
                            <td><?php echo htmlspecialchars($desa['nama_desa']); ?></td>
                            <?php if (isset($desa['id']) && array_key_exists($desa['id'], $desa_sudah_upload)): 
                                $upload_info = $desa_sudah_upload[$desa['id']];
                            ?>
                                <td><span class="status status-sudah">Sudah Upload</span></td>
                                <td><?php echo date("d F Y, H:i:s", strtotime($upload_info['tanggal'])); ?></td>
                                <td style="text-align: center;">
                                    <a href="download.php?id=<?php echo $upload_info['id_arsip']; ?>&type=perkembangan" class="button" style="padding: 5px 10px; font-size: 0.8rem; margin: 2px;">Unduh Perkembangan</a>
                                    <a href="download.php?id=<?php echo $upload_info['id_arsip']; ?>&type=umur" class="button button-secondary" style="padding: 5px 10px; font-size: 0.8rem; margin: 2px;">Unduh Kel. Umur</a>
                                </td>
                            <?php else: ?>
                                <td><span class="status status-belum">Belum Upload</span></td>
                                <td>-</td>
                                <td style="text-align: center;">-</td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align: center;">Tidak ada data desa ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>