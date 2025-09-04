<?php
$page_title = 'Grafik Tahunan';
$active_page = 'grafik';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';

// KAMUS NAMA BULAN DALAM BAHASA INDONESIA
$nama_bulan_indonesia = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$selected_year = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');
$report_data = [
    'Lahir' => array_fill(1, 12, 0), 'Mati' => array_fill(1, 12, 0),
    'Pindah' => array_fill(1, 12, 0), 'Datang' => array_fill(1, 12, 0),
];
$sql = "SELECT MONTH(tanggal_data) as bulan, kategori, SUM(jumlah) as total FROM data_lampid WHERE YEAR(tanggal_data) = ? GROUP BY bulan, kategori";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $selected_year);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            if (isset($report_data[$row['kategori']])) {
                $report_data[$row['kategori']][$row['bulan']] = (int)$row['total'];
            }
        }
    }
    $stmt->close();
}

// Menggunakan kamus untuk label di grafik
$month_labels = array_values($nama_bulan_indonesia);
?>
<main class="main-content">
    <div class="content">
        <div class="welcome-header">
            <h2>Grafik Tahunan Data Kependudukan</h2>
            <p>Menampilkan tren data Lahir, Mati, Pindah, dan Datang per bulan untuk tahun yang dipilih.</p>
        </div>
        <form method="GET" action="grafik.php" class="filter-form">
            <div class="form-group">
                <label for="tahun">Pilih Tahun:</label>
                <select name="tahun" id="tahun">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php if ($y == $selected_year) echo 'selected'; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <button type="submit" class="button">Tampilkan Grafik</button>
        </form>
        <div class="chart-wrapper">
            <div class="chart-container" style="height: 450px;"><canvas id="yearlyChart"></canvas></div>
        </div>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('yearlyChart').getContext('2d');
    const chartData = {
        labels: <?php echo json_encode($month_labels); ?>,
        datasets: [
            { label: 'Lahir', data: <?php echo json_encode(array_values($report_data['Lahir'])); ?>, borderColor: '#28a745', backgroundColor: 'rgba(40, 167, 69, 0.1)', fill: true, tension: 0.3 },
            { label: 'Mati', data: <?php echo json_encode(array_values($report_data['Mati'])); ?>, borderColor: '#dc3545', backgroundColor: 'rgba(220, 53, 69, 0.1)', fill: true, tension: 0.3 },
            { label: 'Pindah', data: <?php echo json_encode(array_values($report_data['Pindah'])); ?>, borderColor: '#ffc107', backgroundColor: 'rgba(255, 193, 7, 0.1)', fill: true, tension: 0.3 },
            { label: 'Datang', data: <?php echo json_encode(array_values($report_data['Datang'])); ?>, borderColor: '#17a2b8', backgroundColor: 'rgba(23, 162, 184, 0.1)', fill: true, tension: 0.3 }
        ]
    };
    const yearlyChart = new Chart(ctx, {
        type: 'line', data: chartData,
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }, plugins: { legend: { position: 'top' }, tooltip: { mode: 'index', intersect: false, } }, interaction: { mode: 'index', intersect: false, } }
    });
});
</script>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>