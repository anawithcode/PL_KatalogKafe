<section class="page-heading">
    <div>
        <p class="eyebrow">Dapur & etalase</p>
        <h1>Katalog yang tertata,<br><em>rasa yang dijaga.</em></h1>
        <p class="page-description">Kelola seluruh menu kafe, pantau stok, dan atur ketersediaannya dari satu tempat.</p>
    </div>
    <a class="button button-primary heading-action" href="<?= url('/menus/create') ?>"><?= icon('plus') ?> Tambah menu</a>
</section>

<section class="stats" aria-label="Ringkasan katalog">
    <div class="stat"><span>Total menu</span><strong><?= e($stats['total']) ?></strong><small>item terdaftar</small></div>
    <div class="stat"><span>Siap dipesan</span><strong><?= e($stats['available']) ?></strong><small>menu tersedia</small></div>
    <div class="stat"><span>Total stok</span><strong><?= e($stats['stock']) ?></strong><small>porsi & produk</small></div>
    <div class="stat stat-accent"><span>Perlu perhatian</span><strong><?= e($stats['unavailable']) ?></strong><small>menu nonaktif</small></div>
</section>

<section class="catalog" aria-labelledby="catalog-title">
    <div class="catalog-topline">
        <div><p class="eyebrow">Daftar inventori</p><h2 id="catalog-title">Semua menu</h2></div>
        <span class="record-count"><?= e($pagination['total']) ?> data ditemukan</span>
    </div>

    <form class="filters" method="get" action="<?= url('/menus') ?>">
        <label class="search-field">
            <span class="sr-only">Cari nama menu</span><?= icon('search') ?>
            <input type="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Cari nama menu…">
        </label>
        <label><span class="sr-only">Filter kategori</span>
            <select name="category">
                <option value="">Semua kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= e($category) ?>" <?= $filters['category'] === $category ? 'selected' : '' ?>><?= e($category) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label><span class="sr-only">Filter ketersediaan</span>
            <select name="availability">
                <option value="">Semua status</option>
                <option value="1" <?= $filters['availability'] === '1' ? 'selected' : '' ?>>Tersedia</option>
                <option value="0" <?= $filters['availability'] === '0' ? 'selected' : '' ?>>Tidak tersedia</option>
            </select>
        </label>
        <label><span class="sr-only">Urutkan menu</span>
            <select name="sort">
                <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Terbaru</option>
                <option value="name" <?= $filters['sort'] === 'name' ? 'selected' : '' ?>>Nama A–Z</option>
                <option value="price_low" <?= $filters['sort'] === 'price_low' ? 'selected' : '' ?>>Harga terendah</option>
                <option value="price_high" <?= $filters['sort'] === 'price_high' ? 'selected' : '' ?>>Harga tertinggi</option>
                <option value="stock" <?= $filters['sort'] === 'stock' ? 'selected' : '' ?>>Stok terbanyak</option>
            </select>
        </label>
        <button class="button button-filter" type="submit">Terapkan</button>
        <?php if (array_filter([$filters['search'], $filters['category'], $filters['availability']]) || $filters['sort'] !== 'newest'): ?>
            <a class="reset-filter" href="<?= url('/menus') ?>">Reset</a>
        <?php endif; ?>
    </form>

    <?php if ($pagination['items']): ?>
        <div class="menu-table" role="table" aria-label="Daftar menu kafe">
            <div class="menu-table-head" role="row">
                <span role="columnheader">Menu</span><span role="columnheader">Kategori</span><span role="columnheader">Harga</span><span role="columnheader">Stok</span><span role="columnheader">Status</span><span role="columnheader">Aksi</span>
            </div>
            <div class="menu-table-body">
                <?php foreach ($pagination['items'] as $index => $menu): ?>
                    <article class="menu-row" role="row">
                        <div class="menu-identity" role="cell">
                            <span class="menu-number"><?= str_pad((string) ($pagination['from'] + $index), 2, '0', STR_PAD_LEFT) ?></span>
                            <div><a href="<?= url('/menus/show?id=' . $menu['id']) ?>" class="menu-name"><?= e($menu['name']) ?></a><span class="menu-excerpt"><?= e($menu['description']) ?></span></div>
                        </div>
                        <div role="cell" data-label="Kategori"><span class="category-tag"><?= e($menu['category']) ?></span></div>
                        <div role="cell" data-label="Harga" class="price"><?= e(rupiah($menu['price'])) ?></div>
                        <div role="cell" data-label="Stok" class="stock <?= (int) $menu['stock'] <= 5 ? 'stock-low' : '' ?>"><?= e($menu['stock']) ?> <small>item</small></div>
                        <div role="cell" data-label="Status"><span class="availability <?= $menu['is_available'] ? 'is-available' : 'is-unavailable' ?>"><span></span><?= $menu['is_available'] ? 'Tersedia' : 'Nonaktif' ?></span></div>
                        <div class="row-actions" role="cell">
                            <a class="icon-button" href="<?= url('/menus/show?id=' . $menu['id']) ?>" aria-label="Lihat <?= e($menu['name']) ?>" title="Lihat detail"><?= icon('eye', 18) ?></a>
                            <a class="icon-button" href="<?= url('/menus/edit?id=' . $menu['id']) ?>" aria-label="Edit <?= e($menu['name']) ?>" title="Edit menu"><?= icon('edit', 18) ?></a>
                            <form method="post" action="<?= url('/menus/delete?id=' . $menu['id']) ?>" class="delete-form" data-menu-name="<?= e($menu['name']) ?>">
                                <?= csrf_field() ?>
                                <button class="icon-button danger-link" type="submit" aria-label="Hapus <?= e($menu['name']) ?>" title="Hapus menu"><?= icon('trash', 18) ?></button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="pagination-wrap">
            <p>Menampilkan <?= e($pagination['from']) ?>–<?= e($pagination['to']) ?> dari <?= e($pagination['total']) ?> menu</p>
            <?php if ($pagination['last_page'] > 1): ?>
                <nav class="pagination" aria-label="Navigasi halaman">
                    <?php
                    $query = array_filter(['search' => $filters['search'], 'category' => $filters['category'], 'availability' => $filters['availability'], 'sort' => $filters['sort']], fn ($value) => $value !== '');
                    $pageUrl = fn (int $page): string => url('/menus?' . http_build_query($query + ['page' => $page]));
                    ?>
                    <?php if ($pagination['page'] > 1): ?><a href="<?= $pageUrl($pagination['page'] - 1) ?>" aria-label="Halaman sebelumnya"><?= icon('chevron-left', 18) ?></a><?php endif; ?>
                    <?php for ($page = 1; $page <= $pagination['last_page']; $page++): ?>
                        <a href="<?= $pageUrl($page) ?>" class="<?= $page === $pagination['page'] ? 'current' : '' ?>" <?= $page === $pagination['page'] ? 'aria-current="page"' : '' ?>><?= $page ?></a>
                    <?php endfor; ?>
                    <?php if ($pagination['page'] < $pagination['last_page']): ?><a href="<?= $pageUrl($pagination['page'] + 1) ?>" aria-label="Halaman berikutnya"><?= icon('chevron-right', 18) ?></a><?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <span><?= icon('search', 32) ?></span><h3>Belum ada menu yang cocok</h3>
            <p>Coba ubah kata kunci atau filter, atau tambahkan menu baru ke katalog.</p>
            <a class="button button-secondary" href="<?= url('/menus') ?>">Lihat semua menu</a>
        </div>
    <?php endif; ?>
</section>
