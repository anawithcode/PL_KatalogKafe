<?php
$errors = $_SESSION['errors'] ?? [];
$isEdit = $menu !== null;
$value = static fn (string $key, mixed $default = ''): mixed => old($key, $menu[$key] ?? $default);
$available = (int) old('is_available', $menu['is_available'] ?? 1);
unset($_SESSION['errors'], $_SESSION['old']);
?>
<section class="form-page-heading">
    <a class="back-link" href="<?= $isEdit ? url('/menus/show?id=' . $menu['id']) : url('/menus') ?>"><?= icon('arrow-left', 18) ?> Kembali</a>
    <p class="eyebrow"><?= $isEdit ? 'Perbarui detail' : 'Menu baru' ?></p>
    <h1><?= $isEdit ? 'Sempurnakan<br><em>racikan menu.</em>' : 'Tambah cerita<br><em>ke dalam katalog.</em>' ?></h1>
    <p class="page-description">Lengkapi informasi berikut. Kolom bertanda * wajib diisi.</p>
</section>

<form class="menu-form" method="post" action="<?= url($action) ?>" novalidate>
    <?= csrf_field() ?>
    <div class="form-main">
        <section class="form-section">
            <div class="form-section-title"><span>01</span><div><h2>Informasi menu</h2><p>Nama dan kategori yang mudah dikenali.</p></div></div>
            <div class="field-grid two-columns">
                <label class="field field-wide">
                    <span>Nama menu <b>*</b></span>
                    <input type="text" name="name" value="<?= e($value('name')) ?>" maxlength="100" required autofocus placeholder="Contoh: Kopi Susu Senja" aria-describedby="error-name">
                    <?php if (isset($errors['name'])): ?><small class="field-error" id="error-name"><?= e($errors['name']) ?></small><?php endif; ?>
                </label>
                <label class="field field-wide">
                    <span>Kategori <b>*</b></span>
                    <select name="category" required aria-describedby="error-category">
                        <option value="">Pilih kategori</option>
                        <?php foreach ($categories as $category): ?><option value="<?= e($category) ?>" <?= $value('category') === $category ? 'selected' : '' ?>><?= e($category) ?></option><?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['category'])): ?><small class="field-error" id="error-category"><?= e($errors['category']) ?></small><?php endif; ?>
                </label>
                <label class="field field-wide">
                    <span>Deskripsi</span>
                    <textarea name="description" rows="5" maxlength="1000" placeholder="Ceritakan rasa, bahan utama, atau penyajian menu…"><?= e($value('description')) ?></textarea>
                    <small class="field-hint">Maksimal 1.000 karakter.</small>
                    <?php if (isset($errors['description'])): ?><small class="field-error"><?= e($errors['description']) ?></small><?php endif; ?>
                </label>
            </div>
        </section>

        <section class="form-section">
            <div class="form-section-title"><span>02</span><div><h2>Harga & persediaan</h2><p>Pastikan angka sesuai dengan kondisi dapur.</p></div></div>
            <div class="field-grid two-columns">
                <label class="field">
                    <span>Harga <b>*</b></span>
                    <div class="input-prefix"><span>Rp</span><input type="number" name="price" value="<?= e($value('price')) ?>" min="0" max="100000000" step="1000" required placeholder="24000" aria-describedby="error-price"></div>
                    <?php if (isset($errors['price'])): ?><small class="field-error" id="error-price"><?= e($errors['price']) ?></small><?php endif; ?>
                </label>
                <label class="field">
                    <span>Jumlah stok <b>*</b></span>
                    <input type="number" name="stock" value="<?= e($value('stock', 0)) ?>" min="0" max="1000000" step="1" required placeholder="0" aria-describedby="error-stock">
                    <?php if (isset($errors['stock'])): ?><small class="field-error" id="error-stock"><?= e($errors['stock']) ?></small><?php endif; ?>
                </label>
            </div>
        </section>
    </div>

    <aside class="form-aside">
        <div class="availability-panel">
            <p class="eyebrow">Status penjualan</p>
            <h2>Tampilkan di katalog?</h2>
            <p>Menu aktif dapat dilihat dan dipesan oleh pelanggan.</p>
            <label class="switch">
                <input type="checkbox" name="is_available" value="1" <?= $available ? 'checked' : '' ?>>
                <span class="switch-track"><span></span></span>
                <strong>Menu tersedia</strong>
            </label>
        </div>
        <div class="form-submit-panel">
            <p><?= $isEdit ? 'Perubahan akan langsung tersimpan di database.' : 'Menu akan langsung ditambahkan ke katalog.' ?></p>
            <button class="button button-primary button-full" type="submit"><?= icon('check') ?> <?= $isEdit ? 'Simpan perubahan' : 'Tambahkan menu' ?></button>
            <a class="button button-ghost button-full" href="<?= $isEdit ? url('/menus/show?id=' . $menu['id']) : url('/menus') ?>">Batal</a>
        </div>
    </aside>
</form>
