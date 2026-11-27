<?php
/**
 * Peringatan "database belum terhubung".
 * Butuh $db_aktif.
 */
?>
<?php if (!$db_aktif): ?>
<div class="cek-db mb-4">
    <strong><i class="bi bi-database-exclamation text-warning"></i> Database belum terhubung.</strong>
    <div class="small text-muted mt-1">
        Nyalakan MySQL di XAMPP, lalu import file <code>NERF_TokoBangunan.sql</code> melalui phpMyAdmin.
    </div>
</div>
<?php endif; ?>
