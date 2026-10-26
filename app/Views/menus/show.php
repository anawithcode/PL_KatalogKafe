<section class="detail-page">
    <a class="back-link" href="<?= url('/menus') ?>"><?= icon('arrow-left', 18) ?> Kembali ke katalog</a>
    <div class="detail-hero">
        <div class="detail-title">
            <span class="category-tag"><?= e($menu['category']) ?></span>
            <p class="eyebrow">Menu #<?= str_pad((string) $menu['id'], 3, '0', STR_PAD_LEFT) ?></p>
            <h1><?= e($menu['name']) ?></h1>
            <p><?= e($menu['description'] ?: 'Belum ada deskripsi untuk menu ini.') ?></p>
        </div>
        <div class="detail-price"><span>Harga jual</span><strong><?= e(rupiah($menu['price'])) ?></strong><small>per porsi / item</small></div>
    </div>

    <div class="detail-meta">
        <div><span>Ketersediaan</span><strong class="availability <?= $menu['is_available'] ? 'is-available' : 'is-unavailable' ?>"><span></span><?= $menu['is_available'] ? 'Tersedia' : 'Tidak tersedia' ?></strong></div>
        <div><span>Sisa stok</span><strong><?= e($menu['stock']) ?> item</strong></div>
        <div><span>Ditambahkan</span><strong><?= e(date('d M Y, H:i', strtotime($menu['created_at']))) ?></strong></div>
        <div><span>Terakhir diubah</span><strong><?= e(date('d M Y, H:i', strtotime($menu['updated_at']))) ?></strong></div>
    </div>

    <div class="detail-actions">
        <a class="button button-primary" href="<?= url('/menus/edit?id=' . $menu['id']) ?>"><?= icon('edit') ?> Edit menu</a>
        <form method="post" action="<?= url('/menus/delete?id=' . $menu['id']) ?>" class="delete-form" data-menu-name="<?= e($menu['name']) ?>">
            <?= csrf_field() ?><button class="button button-danger-outline" type="submit"><?= icon('trash') ?> Hapus menu</button>
        </form>
    </div>
</section>
