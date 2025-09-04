<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin_desa') {
    header("Location: ../../login.php");
    exit();
}
require_once '../../includes/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    date_default_timezone_set('Asia/Jakarta');

    $id_desa = $_SESSION['id_desa'];
    $id_admin = $_SESSION['admin_id'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];

    // --- LOGIKA PENGECEKAN DUPLIKAT ---
    // Cek apakah sudah ada data untuk desa, bulan, dan tahun yang sama
    $stmt_check = $conn->prepare("SELECT id_arsip FROM arsip_lampid WHERE id_desa = ? AND bulan = ? AND tahun = ?");
    $stmt_check->bind_param("iii", $id_desa, $bulan, $tahun);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Jika data sudah ada, batalkan upload dan kirim notifikasi error
        $stmt_check->close();
        header("Location: upload.php?status=gagal&pesan=Anda sudah pernah mengunggah laporan untuk periode " . date('F', mktime(0,0,0,$bulan,1)) . " " . $tahun . ".");
        exit();
    }
    $stmt_check->close();
    // --- AKHIR LOGIKA PENGECEKAN ---

    // Jika tidak ada duplikat, lanjutkan proses upload
    $tanggal_upload = date('Y-m-d H:i:s');

    $upload_dir_relative = 'uploads/';
    $upload_dir_server = dirname(__DIR__, 2) . '/' . $upload_dir_relative;

    if (!is_dir($upload_dir_server)) {
        mkdir($upload_dir_server, 0755, true);
    }

    // Proses upload file 1 (Perkembangan Penduduk)
    $file_perkembangan_name = basename($_FILES["file_perkembangan"]["name"]);
    $file_extension = pathinfo($file_perkembangan_name, PATHINFO_EXTENSION);
    $file_perkembangan_unique = uniqid() . '.' . $file_extension;
    $file_perkembangan_path_server = $upload_dir_server . $file_perkembangan_unique;
    $file_perkembangan_path_db = $upload_dir_relative . $file_perkembangan_unique;
    move_uploaded_file($_FILES["file_perkembangan"]["tmp_name"], $file_perkembangan_path_server);

    // Proses upload file 2 (Kelompok Umur)
    $file_umur_name = basename($_FILES["file_umur"]["name"]);
    $file_extension_umur = pathinfo($file_umur_name, PATHINFO_EXTENSION);
    $file_umur_unique = uniqid() . '.' . $file_extension_umur;
    $file_umur_path_server = $upload_dir_server . $file_umur_unique;
    $file_umur_path_db = $upload_dir_relative . $file_umur_unique;
    move_uploaded_file($_FILES["file_umur"]["tmp_name"], $file_umur_path_server);

    // Simpan informasi ke database
    $sql = "INSERT INTO arsip_lampid (id_desa, id_admin, bulan, tahun, file_perkembangan_penduduk, path_perkembangan_penduduk, file_kelompok_umur, path_kelompok_umur, tanggal_upload) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiissssss", $id_desa, $id_admin, $bulan, $tahun, $file_perkembangan_name, $file_perkembangan_path_db, $file_umur_name, $file_umur_path_db, $tanggal_upload);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php?status=upload_sukses");
    exit();
}
?>