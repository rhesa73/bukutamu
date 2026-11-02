
<?php
session_start();
include "Koneksi.php";

// Cek apakah sudah login
if (!isset($_SESSION['login'])) {
    header("Location: Index.php");
    exit;
}

// Proses simpan data pengunjung
if (isset($_POST['simpan'])) {

    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $no_hp = trim($_POST['no_hp']);
    $keperluan = trim($_POST['keperluan']);

    if ($nama == "" || $alamat == "" || $no_hp == "" || $keperluan == "") {
        header("Location: Dashboard.php?pesan=kosong");
        exit;
    }

    $query = mysqli_prepare(
        $koneksi,
        "INSERT INTO buku_tamu (nama, alamat, no_hp, keperluan)
         VALUES (?, ?, ?, ?)"
    );

    mysqli_stmt_bind_param(
        $query,
        "ssss",
        $nama,
        $alamat,
        $no_hp,
        $keperluan
    );

    if (mysqli_stmt_execute($query)) {
        header("Location: Dashboard.php?pesan=berhasil");
    } else {
        header("Location: Dashboard.php?pesan=error");
    }
    exit;
}

// Proses hapus data pengunjung
if (isset($_POST['hapus'])) {

    $id = (int) $_POST['id_tamu'];

    $query = mysqli_prepare(
        $koneksi,
        "DELETE FROM buku_tamu WHERE id_tamu = ?"
    );

    mysqli_stmt_bind_param($query, "i", $id);
    mysqli_stmt_execute($query);

    header("Location: Dashboard.php?pesan=terhapus");
    exit;
}

// Mengambil data pengunjung
$data_tamu = mysqli_query(
    $koneksi,
    "SELECT * FROM buku_tamu ORDER BY tanggal DESC"
);

$total_tamu = mysqli_num_rows($data_tamu);

function aman($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Buku Tamu</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>

<div class="dashboard-container">

    <header class="header">
        <div>
            <h2>Buku Tamu</h2>
            <p>Selamat datang, <?= aman($_SESSION['nama']); ?>!</p>
        </div>

        <a href="Logout.php" class="btn-logout"
           onclick="return confirm('Yakin ingin logout?')">
            Logout
        </a>
    </header>

    <div class="content">

        <div class="stat-box">
            <h3>Total Pengunjung</h3>
            <h1><?= $total_tamu; ?></h1>
        </div>

        <div class="form-box">
            <h2>Form Buku Tamu</h2>
            <p>Silakan isi data pengunjung di bawah ini.</p>

            <?php if (isset($_GET['pesan'])): ?>

                <?php if ($_GET['pesan'] == 'berhasil'): ?>
                    <div class="success">
                        Data pengunjung berhasil disimpan!
                    </div>
                <?php elseif ($_GET['pesan'] == 'kosong'): ?>
                    <div class="error">
                        Semua kolom wajib diisi!
                    </div>
                <?php elseif ($_GET['pesan'] == 'terhapus'): ?>
                    <div class="success">
                        Data pengunjung berhasil dihapus!
                    </div>
                <?php elseif ($_GET['pesan'] == 'error'): ?>
                    <div class="error">
                        Gagal menyimpan data!
                    </div>
                <?php endif; ?>

            <?php endif; ?>

            <form method="POST" action="Dashboard.php">

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama"
                           placeholder="Masukkan nama lengkap" required>
                </div>

                <div class="form-group">
                    <label>Alamat</label>
                    <textarea name="alamat"
                              placeholder="Masukkan alamat"
                              required></textarea>
                </div>

                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="tel" name="no_hp"
                           placeholder="Masukkan nomor HP" required>
                </div>

                <div class="form-group">
                    <label>Keperluan</label>
                    <textarea name="keperluan"
                              placeholder="Tuliskan keperluan kunjungan"
                              required></textarea>
                </div>

                <button type="submit" name="simpan" class="btn-simpan">
                    Simpan Data
                </button>

            </form>
        </div>

        <div class="table-box">
            <h2>Daftar Pengunjung</h2>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No. HP</th>
                            <th>Keperluan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if ($total_tamu > 0): ?>

                        <?php $no = 1; ?>
                        <?php while ($tamu = mysqli_fetch_assoc($data_tamu)): ?>

                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= aman($tamu['nama']); ?></td>
                                <td><?= aman($tamu['alamat']); ?></td>
                                <td><?= aman($tamu['no_hp']); ?></td>
                                <td><?= aman($tamu['keperluan']); ?></td>
                                <td><?= aman($tamu['tanggal']); ?></td>
                                <td>
                                    <form method="POST"
                                          action="Dashboard.php"
                                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">

                                        <input type="hidden"
                                               name="id_tamu"
                                               value="<?= $tamu['id_tamu']; ?>">

                                        <button type="submit"
                                                name="hapus"
                                                class="btn-hapus">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="empty">
                                Belum ada data pengunjung.
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>
