<?php
$flashMessage = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#264653">
    <title><?= e($title) ?> · Senja Kopi</title>
    <link rel="icon" href="<?= url('/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Literata:opsz,wght@7..72,500;7..72,600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('/assets/app.css') ?>">
    <script src="<?= url('/assets/app.js') ?>" defer></script>
</head>
<body>
<a class="skip-link" href="#main-content">Lewati ke konten</a>
<div class="app-shell">
    <aside class="sidebar" id="sidebar" aria-label="Navigasi utama">
        <div class="brand">
            <span class="brand-mark"><?= icon('coffee', 28) ?></span>
            <div><strong>Senja</strong><span>ruang kopi</span></div>
        </div>
        <button class="sidebar-close icon-button" type="button" data-sidebar-close aria-label="Tutup navigasi"><?= icon('close') ?></button>
        <nav class="main-nav">
            <p class="nav-label">Ruang kerja</p>
            <a class="nav-link active" href="<?= url('/menus') ?>" aria-current="page">
                <?= icon('menu') ?><span>Katalog menu</span><span class="nav-dot"></span>
            </a>
        </nav>
        <div class="sidebar-note">
            <span class="note-line"></span>
            <p>Diseduh dengan ketelitian,<br>dikelola dengan sederhana.</p>
            <small>PHP · MVC · PDO</small>
        </div>
    </aside>
    <button class="sidebar-backdrop" type="button" data-sidebar-close aria-label="Tutup navigasi" tabindex="-1"></button>

    <div class="workspace">
        <header class="topbar">
            <button class="mobile-menu icon-button" type="button" data-sidebar-open aria-controls="sidebar" aria-expanded="false" aria-label="Buka navigasi"><?= icon('menu') ?></button>
            <div class="breadcrumb"><span>Senja Kopi</span><span>/</span><strong><?= e($title) ?></strong></div>
            <div class="topbar-meta"><span class="status-dot"></span> Database terhubung</div>
        </header>

        <main id="main-content" class="main-content">
            <?php if ($flashMessage): ?>
                <div class="alert alert-<?= e($flashMessage['type']) ?>" role="status" data-alert>
                    <?= icon($flashMessage['type'] === 'success' ? 'check' : 'info') ?>
                    <span><?= e($flashMessage['message']) ?></span>
                    <button type="button" class="alert-close" data-alert-close aria-label="Tutup pesan"><?= icon('close', 18) ?></button>
                </div>
            <?php endif; ?>
            <?= $content ?>
        </main>
        <footer class="footer"><span>Senja Kopi — Sistem Katalog Menu</span><span>Query Builder berbasis PDO</span></footer>
    </div>
</div>

<dialog class="delete-dialog" id="delete-dialog" aria-labelledby="delete-title">
    <form method="dialog" class="dialog-card">
        <button class="dialog-close icon-button" value="cancel" aria-label="Tutup dialog"><?= icon('close') ?></button>
        <span class="dialog-icon"><?= icon('trash', 26) ?></span>
        <p class="eyebrow">Konfirmasi penghapusan</p>
        <h2 id="delete-title">Hapus menu ini?</h2>
        <p><strong id="delete-menu-name"></strong> akan dihapus permanen dari katalog. Tindakan ini tidak dapat dibatalkan.</p>
        <div class="dialog-actions">
            <button class="button button-secondary" value="cancel">Batal</button>
            <button class="button button-danger" type="button" id="confirm-delete">Ya, hapus menu</button>
        </div>
    </form>
</dialog>
</body>
</html>
