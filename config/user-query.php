<?php
/** Data untuk halaman admin/user.php: daftar seluruh akun admin. */

$users = $koneksi->query('SELECT * FROM admin ORDER BY id_admin ASC')->fetch_all(MYSQLI_ASSOC);
