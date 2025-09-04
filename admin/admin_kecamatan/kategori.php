<?php
$page_title = 'Data Kategori';
$active_page = 'kategori';
require_once __DIR__ . '/../partials/header.php';
require_once __DIR__ . '/../partials/sidebar.php';

$kategori_data = [];
$kategori_query = $conn->query("
    SELECT kategori, SUM(jumlah) as total_jumlah, COUNT(DISTINCT desa_id) as jumlah_desa
    FROM data_lampid
    GROUP BY kategori
    ORDER BY kategori ASC
");

if ($kategori_query) {
    while($row = $kategori_query->fetch_assoc()) {
        $kategori_data[] = $row;
    }
}

$kategori_icons = [
    'Lahir' => 'fas fa-baby', 'Mati' => 'fas fa-cross',
    'Pindah' => 'fas fa-walking', 'Datang' => 'fas fa-users'
];
?>

<main class="main-content">
    <div class="content">
        <div class="welcome-header">
            <h2>Data Berdasarkan Kategori</h2>
            <p>Rekapitulasi total data kependudukan untuk setiap kategori LAMPID.</p>
        </div>

        <div class="kategori-grid">
            <?php if (!empty($kategori_data)): ?>
                <?php foreach ($kategori_data as $item): ?>
                    <div class="kategori-card">
                        <div class="kategori-icon">
                            <i class="<?php echo $kategori_icons[$item['kategori']] ?? 'fas fa-question-circle'; ?>"></i>
                        </div>
                        <div class="kategori-info">
                            <h3><?php echo htmlspecialchars($item['kategori']); ?></h3>
                            <p>Total: <strong><?php echo number_format($item['total_jumlah']); ?></strong> Jiwa</p>
                            <span>Dari <?php echo $item['jumlah_desa']; ?> Desa</span>
                        </div>
                        <a href="kategori_detail.php?kategori=<?php echo urlencode($item['kategori']); ?>" class="kategori-detail-link">
                            Lihat Detail <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada data kategori untuk ditampilkan.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>