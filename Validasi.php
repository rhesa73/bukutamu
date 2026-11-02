
<?php
session_start();
include "Koneksi.php";

// Memastikan form login dikirim
if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi input kosong
    if (empty($username) || empty($password)) {
        header("Location: Index.php?pesan=kosong");
        exit;
    }

    // Mengambil data user berdasarkan username
    $query = mysqli_prepare(
        $koneksi,
        "SELECT * FROM user WHERE username = ? LIMIT 1"
    );

    mysqli_stmt_bind_param($query, "s", $username);
    mysqli_stmt_execute($query);

    $result = mysqli_stmt_get_result($query);
    $data = mysqli_fetch_assoc($result);

    if ($data) {

        $passwordDB = $data['password'];

        // Mendukung password_hash, MD5 lama, dan password biasa lama
        $valid = password_verify($password, $passwordDB)
            || hash_equals($passwordDB, md5($password))
            || hash_equals($passwordDB, $password);

        if ($valid) {

            // Memperbarui password lama menjadi hash yang aman
            if (!password_verify($password, $passwordDB)) {

                $hashBaru = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $update = mysqli_prepare(
                    $koneksi,
                    "UPDATE user SET password = ? WHERE id_user = ?"
                );

                mysqli_stmt_bind_param(
                    $update,
                    "si",
                    $hashBaru,
                    $data['id_user']
                );

                mysqli_stmt_execute($update);
            }

            // Membuat session login
            session_regenerate_id(true);

            $_SESSION['login'] = true;
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['nama'] = $data['nama'];

            header("Location: Dashboard.php");
            exit;

        } else {
            header("Location: Index.php?pesan=gagal");
            exit;
        }

    } else {
        header("Location: Index.php?pesan=gagal");
        exit;
    }

} else {
    header("Location: Index.php");
    exit;
}
?>
