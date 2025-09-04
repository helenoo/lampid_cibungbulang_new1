<?php
$page_title = 'Update Profil';
$active_page = 'update_profile'; // Kita samakan dengan menu "Input & Profile"

// Path ini akan bekerja karena file ini ada di dalam admin_kecamatan
require_once __DIR__ . '/../partials/header.php';

$update_message = '';
$update_status = '';

// Proses form saat tombol "Update" ditekan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $admin_id = $_SESSION['admin_id'];
    $new_username = $_POST['new_username'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Cek apakah username baru diisi
    if (!empty($new_username)) {
        // Cek apakah password baru juga diisi
        if (!empty($new_password)) {
            // Cek apakah password konfirmasi cocok
            if ($new_password === $confirm_password) {
                // Hash password baru
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Siapkan query untuk update username DAN password
                $stmt = $conn->prepare("UPDATE admins SET username = ?, password = ? WHERE id = ?");
                $stmt->bind_param("ssi", $new_username, $hashed_password, $admin_id);
            } else {
                $update_message = 'Konfirmasi password tidak cocok.';
                $update_status = 'danger';
            }
        } else {
            // Jika hanya username yang diubah
            $stmt = $conn->prepare("UPDATE admins SET username = ? WHERE id = ?");
            $stmt->bind_param("si", $new_username, $admin_id);
        }
    } elseif (!empty($new_password)) {
        // Jika hanya password yang diubah
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $admin_id);
        } else {
            $update_message = 'Konfirmasi password tidak cocok.';
            $update_status = 'danger';
        }
    }

    // Eksekusi query jika statement sudah disiapkan
    if (isset($stmt) && $stmt->execute()) {
        $update_message = 'Profil berhasil diperbarui. Username baru Anda adalah "' . htmlspecialchars($new_username) . '". Silakan login kembali jika Anda mengubah kredensial.';
        $update_status = 'success';
        // Update session jika username diubah
        if (!empty($new_username)) {
            $_SESSION['admin_username'] = $new_username;
        }
    } elseif (empty($update_message)) {
        $update_message = 'Tidak ada perubahan yang dilakukan atau terjadi kesalahan.';
        $update_status = 'danger';
    }

    if (isset($stmt)) {
        $stmt->close();
    }
}
?>

<main class="main-content">
    <div class="content">
        <div class="welcome-header">
            <h2>Update Username & Password</h2>
            <p>Ubah informasi login Anda di sini. Kosongkan field yang tidak ingin diubah.</p>
        </div>

        <?php if($update_message): ?>
            <div class="alert alert-<?php echo $update_status; ?>"><?php echo $update_message; ?></div>
        <?php endif; ?>

        <div class="form-wrapper">
            <form action="update_profile.php" method="POST" class="profile-form">
                <div class="form-group">
                    <label for="new_username">Username Baru</label>
                    <input type="text" id="new_username" name="new_username" value="<?php echo htmlspecialchars($_SESSION['admin_username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Password Baru (Opsional)</label>
                    <input type="password" id="new_password" name="new_password" placeholder="Kosongkan jika tidak ingin diubah">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru di sini">
                </div>
                <button type="submit" class="button">Update Profil</button>
            </form>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/../partials/footer.php';
?>