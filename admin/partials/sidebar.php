<aside class="sidebar">
    <div class="sidebar-header">
        <h3>LAMPID</h3>
        <p>Kec. Cibungbulang</p>
    </div>
    <ul class="sidebar-nav">
        <li class="<?php echo ($active_page == 'dashboard') ? 'active' : ''; ?>">
            <a href="dashboard.php">Dashboard</a>
        </li>
        <li class="<?php echo ($active_page == 'monitoring') ? 'active' : ''; ?>">
            <a href="monitoring.php">Monitoring Desa</a>
        </li>
        <li class="<?php echo ($active_page == 'grafik') ? 'active' : ''; ?>">
            <a href="grafik.php">Grafik Tahunan</a>
        </li>
        <li class="<?php echo ($active_page == 'kategori') ? 'active' : ''; ?>">
            <a href="kategori.php">Data Kategori</a>
        </li>
        <li class="<?php echo ($active_page == 'profile') ? 'active' : ''; ?>">
            <a href="profile.php">Input & Profile</a>
        </li>

        <li class="<?php echo ($active_page == 'update_profile') ? 'active' : ''; ?>">
            <a href="update_profile.php">Ganti Password</a>
        </li>
        
        <li>
            <a href="../../logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar dari sesi ini?');">Logout</a>
        </li>

    </ul>
</aside>