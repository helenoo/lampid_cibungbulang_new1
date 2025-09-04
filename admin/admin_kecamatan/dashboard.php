<?php
$page_title = 'Dashboard';
$active_page = 'dashboard';
include '../partials/header.php'; // Pastikan header.php tidak memiliki tag <body> atau <html>
include '../partials/sidebar.php';

// Ambil data info kecamatan
$info_q = $conn->query("SELECT * FROM info_kecamatan WHERE id = 1")->fetch_assoc();
$jumlah_desa = $conn->query("SELECT COUNT(id) as total FROM desa")->fetch_assoc()['total'];

// Query untuk semua insight sekaligus
$insights_q = $conn->query("
    WITH RankedData AS (
        SELECT
            d.nama_desa,
            dl.kategori,
            SUM(dl.jumlah) AS total_jumlah,
            ROW_NUMBER() OVER(PARTITION BY dl.kategori ORDER BY SUM(dl.jumlah) DESC) as rn
        FROM
            data_lampid dl
        JOIN
            desa d ON dl.desa_id = d.id
        GROUP BY
            dl.kategori, d.nama_desa
    )
    SELECT nama_desa, kategori, total_jumlah FROM RankedData WHERE rn = 1
");

$insights = [];
while ($row = $insights_q->fetch_assoc()) {
    $insights[$row['kategori']] = $row;
}

// Data untuk Grafik
$chart_data_result = $conn->query("
    SELECT kategori, SUM(jumlah) as total FROM data_lampid GROUP BY kategori
");

$labels = ['Lahir', 'Mati', 'Pindah', 'Datang'];
$data_values = [0, 0, 0, 0];
while($row = $chart_data_result->fetch_assoc()) {
    $index = array_search($row['kategori'], $labels);
    if ($index !== false) {
        $data_values[$index] = $row['total'];
    }
}

// Menyiapkan data untuk kartu insight dengan ikon dan warna
$insight_cards_data = [
    'Lahir' => ['title' => 'Kelahiran Tertinggi', 'icon' => 'fas fa-baby', 'color' => '#28a745'],
    'Mati' => ['title' => 'Kematian Tertinggi', 'icon' => 'fas fa-cross', 'color' => '#dc3545'],
    'Pindah' => ['title' => 'Perpindahan Keluar Tertinggi', 'icon' => 'fas fa-walking', 'color' => '#ffc107'],
    'Datang' => ['title' => 'Pendatang Tertinggi', 'icon' => 'fas fa-users', 'color' => '#17a2b8']
];
?>

<main class="dashboard-content">

    <div class="welcome-header">
        <h1>Dashboard Analitik Kependudukan</h1>
        <p>Selamat datang di pusat data LAMPID Kecamatan Cibungbulang.</p>
    </div>

    <div class="main-grid">
        <div class="chart-wrapper">
            <h2>Grafik Data LAMPID (Keseluruhan)</h2>
            <div class="chart-container">
                <canvas id="dashboardChart"></canvas>
            </div>
        </div>
        <div class="info-wrapper">
            <h2>Informasi Wilayah</h2>
            <div class="info-cards-grid">
                <div class="info-card">
                    <i class="fas fa-landmark icon"></i>
                    <div class="info-text">
                        <span>Jumlah Desa</span>
                        <p><?php echo $jumlah_desa; ?></p>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-users icon"></i>
                    <div class="info-text">
                        <span>Total Penduduk</span>
                        <p><?php echo number_format($info_q['jumlah_penduduk']); ?></p>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-ruler-combined icon"></i>
                    <div class="info-text">
                        <span>Luas Wilayah</span>
                        <p><?php echo $info_q['luas_wilayah']; ?> km²</p>
                    </div>
                </div>
                <div class="info-card">
                    <i class="fas fa-map-marked-alt icon"></i>
                    <div class="info-text">
                        <span>Luas Tanah</span>
                        <p><?php echo $info_q['luas_tanah']; ?> Ha</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="insight-section">
        <h2>💡 Insight Utama (Data Keseluruhan)</h2>
        <div class="insight-grid">
            <?php foreach ($insight_cards_data as $kategori => $details): ?>
                <?php if (isset($insights[$kategori])): ?>
                <div class="insight-card" style="border-left-color: <?php echo $details['color']; ?>;">
                    <div class="insight-icon">
                        <i class="<?php echo $details['icon']; ?>" style="color: <?php echo $details['color']; ?>;"></i>
                    </div>
                    <div class="insight-content">
                        <h4><?php echo $details['title']; ?></h4>
                        <p>
                            Diraih oleh <strong><?php echo htmlspecialchars($insights[$kategori]['nama_desa']); ?></strong>
                            <br>
                            dengan total <strong><?php echo number_format($insights[$kategori]['total_jumlah']); ?></strong> jiwa.
                        </p>
                    </div>
                </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</main>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    :root {
        --bg-color: #f4f7fc;
        --card-bg: #ffffff;
        --text-primary: #1D3557;
        --text-secondary: #666;
        --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --border-radius: 12px;
    }

    /* Ganti font default agar lebih modern */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-color);
    }

    .dashboard-content {
    padding: 2rem;
    flex-grow: 1;
    margin-left: 240px;
    }


    .welcome-header {
        margin-bottom: 2rem;
    }
    .welcome-header h1 {
        font-weight: 600;
        color: var(--text-primary);
    }
    .welcome-header p {
        color: var(--text-secondary);
    }

    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: var(--text-primary);
    }
    
    .chart-wrapper, .info-wrapper, .insight-section {
        background: var(--card-bg);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
    }

    .chart-container {
        position: relative;
        height: 350px;
    }

    .info-cards-grid {
        display: grid;
        grid-template-rows: repeat(4, 1fr);
        gap: 1rem;
    }

    .info-card {
        display: flex;
        align-items: center;
        background: #f9faff;
        padding: 1rem;
        border-radius: 8px;
    }
    .info-card .icon {
        font-size: 1.5rem;
        color: #4A90E2;
        margin-right: 1rem;
        width: 40px;
        text-align: center;
    }
    .info-card .info-text span {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }
    .info-card .info-text p {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    .insight-section {
        background-color: transparent;
        box-shadow: none;
        padding: 0;
    }
    
    .insight-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr); 
    gap: 20px;
    margin-top: 2rem;
    max-width: 800px; 
    margin-left: auto;   
    margin-right: auto;
    }

    .insight-card {
        display: flex;
        align-items: center;
        background: var(--card-bg);
        padding: 1.5rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        border-left: 5px solid;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .insight-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .insight-icon {
        font-size: 2rem;
        margin-right: 1.5rem;
    }
    
    .insight-content h4 {
        margin: 0 0 0.5rem 0;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .insight-content p {
        margin: 0;
        color: var(--text-secondary);
        font-size: 0.9rem;
        line-height: 1.5;
    }

    /* Responsif untuk layar kecil */
    @media (max-width: 992px) {
        .main-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">


<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('dashboardChart').getContext('2d');
    const dashboardChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Jumlah Total',
                data: <?php echo json_encode($data_values); ?>,
                backgroundColor: ['#28a745', '#dc3545', '#ffc107', '#17a2b8'],
                borderColor: '#fff',
                borderWidth: 2,
                borderRadius: 5,
                hoverBackgroundColor: ['#218838', '#c82333', '#e0a800', '#138496']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { 
                y: { 
                    beginAtZero: true,
                    grid: { color: '#eef2f7' }
                },
                x: {
                    grid: { display: false }
                }
            },
            plugins: { 
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#333',
                    titleFont: { size: 14 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 5
                }
            }
        }
    });
});
</script>

<?php include '../partials/footer.php'; ?>