<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin_kecamatan') {
    die("Akses ditolak. Anda tidak memiliki izin untuk mengunduh file ini.");
}

require_once '../../includes/db_connect.php';

if (isset($_GET['id']) && isset($_GET['type'])) {
    $id_arsip = (int)$_GET['id'];
    $file_type = $_GET['type'];

    if ($file_type == 'perkembangan') {
        $column_path = 'path_perkembangan_penduduk';
        $column_name = 'file_perkembangan_penduduk';
    } elseif ($file_type == 'umur') {
        $column_path = 'path_kelompok_umur';
        $column_name = 'file_kelompok_umur';
    } else {
        die("Tipe file tidak valid.");
    }

    $stmt = $conn->prepare("SELECT $column_path, $column_name FROM arsip_lampid WHERE id_arsip = ?");
    $stmt->bind_param("i", $id_arsip);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $file_data = $result->fetch_assoc();
        $file_path_from_db = $file_data[$column_path];
        $file_name_original = $file_data[$column_name];

        // PERBAIKAN LOGIKA PATH
        // Cek apakah path di DB adalah path lama (mengandung '../..')
        if (strpos($file_path_from_db, '../../') === 0) {
            // Jika path lama, hapus bagian '../../'
            $clean_path = substr($file_path_from_db, 6);
            $server_file_path = dirname(__DIR__, 2) . '/' . $clean_path;
        } else {
            // Jika path baru (sudah benar)
            $server_file_path = dirname(__DIR__, 2) . '/' . $file_path_from_db;
        }

        if (file_exists($server_file_path)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . basename($file_name_original) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($server_file_path));

            ob_clean();
            flush();
            readfile($server_file_path);
            exit;
        } else {
            die("File tidak ditemukan di server. Path yang dicari: " . $server_file_path);
        }
    } else {
        die("Data arsip tidak ditemukan di database.");
    }
    $stmt->close();
} else {
    die("Parameter tidak lengkap.");
}
?>