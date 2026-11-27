<?php
/** Alert flash + error untuk halaman otentikasi. Butuh $flash dan $error. */
$flash = $flash ?? null;
$error = $error ?? '';
?>
<?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['tipe']) ?> py-2 small"><?= e($flash['pesan']) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger py-2 small">
        <i class="bi bi-exclamation-circle"></i> <?= e($error) ?>
    </div>
<?php endif; ?>
