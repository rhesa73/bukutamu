
<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: Dashboard.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Buku Tamu</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">

        <h2>BUKU TAMU</h2>
        <p>Silakan login untuk melanjutkan</p>

        <?php
        if (isset($_GET['pesan'])) {
            if ($_GET['pesan'] == 'gagal') {
                echo "<p class='error'>Username atau password salah!</p>";
            } elseif ($_GET['pesan'] == 'kosong') {
                echo "<p class='error'>Username dan password wajib diisi!</p>";
            }
        }
        ?>

        <form action="Validasi.php" method="POST">

            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username"
                       placeholder="Masukkan username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                       placeholder="Masukkan password" required>
            </div>

            <button type="submit" name="login">LOGIN</button>

        </form>

    </div>
</div>

</body>
</html>
