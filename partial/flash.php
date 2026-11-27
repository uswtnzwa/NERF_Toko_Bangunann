<?php
/**
 * Flash message (pesan singkat) untuk toko depan.
 * Butuh $flash yang sudah diisi oleh ambil_flash().
 * Kalau halaman pemanggil belum took variabel itu, pesan dilewati saja
 * supaya tidak muncul warning "Undefined variable".
 */
$flash = $flash ?? null;
?>
<?php if ($flash): ?>
<div class="alert alert-<?= e($flash['tipe']) ?> alert-dismissible fade show mt-3 mb-0">
    <i class="bi bi-info-circle"></i> <?= e($flash['pesan']) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
