<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Database;
use App\Core\View;
use App\Models\Menu;

final class MenuController
{
    private Menu $menus;

    public function __construct()
    {
        $this->menus = new Menu(Database::connection(require dirname(__DIR__, 2) . '/config/database.php'));
    }

    public function index(): void
    {
        $filters = [
            'search' => trim((string) ($_GET['search'] ?? '')),
            'category' => (string) ($_GET['category'] ?? ''),
            'availability' => (string) ($_GET['availability'] ?? ''),
            'sort' => in_array($_GET['sort'] ?? '', Menu::SORTS, true) ? $_GET['sort'] : 'newest',
        ];
        if (!in_array($filters['availability'], ['', '0', '1'], true)) {
            $filters['availability'] = '';
        }
        $pagination = $this->menus->paginate($filters, max(1, (int) ($_GET['page'] ?? 1)));

        View::render('menus/index', [
            'title' => 'Katalog Menu',
            'pagination' => $pagination,
            'stats' => $this->menus->stats(),
            'filters' => $filters,
            'categories' => Menu::CATEGORIES,
        ]);
    }

    public function create(): void
    {
        View::render('menus/form', [
            'title' => 'Tambah Menu',
            'menu' => null,
            'categories' => Menu::CATEGORIES,
            'action' => '/menus/store',
        ]);
    }

    public function store(): void
    {
        verify_csrf();
        [$data, $errors] = $this->validate($_POST);
        if ($errors !== []) {
            $this->backWithErrors($errors, '/menus/create');
        }

        $id = $this->menus->create($data);
        unset($_SESSION['old'], $_SESSION['errors']);
        flash('success', 'Menu baru berhasil ditambahkan.');
        redirect('/menus/show?id=' . $id);
    }

    public function show(): void
    {
        $menu = $this->findOrFail();
        View::render('menus/show', ['title' => $menu['name'], 'menu' => $menu]);
    }

    public function edit(): void
    {
        $menu = $this->findOrFail();
        View::render('menus/form', [
            'title' => 'Edit Menu',
            'menu' => $menu,
            'categories' => Menu::CATEGORIES,
            'action' => '/menus/update?id=' . $menu['id'],
        ]);
    }

    public function update(): void
    {
        verify_csrf();
        $menu = $this->findOrFail();
        [$data, $errors] = $this->validate($_POST);
        if ($errors !== []) {
            $this->backWithErrors($errors, '/menus/edit?id=' . $menu['id']);
        }

        $this->menus->update((int) $menu['id'], $data);
        unset($_SESSION['old'], $_SESSION['errors']);
        flash('success', 'Perubahan menu berhasil disimpan.');
        redirect('/menus/show?id=' . $menu['id']);
    }

    public function delete(): void
    {
        verify_csrf();
        $menu = $this->findOrFail();
        $this->menus->delete((int) $menu['id']);
        flash('success', 'Menu “' . $menu['name'] . '” berhasil dihapus.');
        redirect('/menus');
    }

    private function findOrFail(): array
    {
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        $menu = $id ? $this->menus->find((int) $id) : null;
        if (!$menu) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Menu tidak ditemukan']);
            exit;
        }
        return $menu;
    }

    private function validate(array $input): array
    {
        $data = [
            'name' => trim((string) ($input['name'] ?? '')),
            'category' => (string) ($input['category'] ?? ''),
            'description' => trim((string) ($input['description'] ?? '')),
            'price' => str_replace(',', '.', trim((string) ($input['price'] ?? ''))),
            'stock' => trim((string) ($input['stock'] ?? '')),
            'is_available' => isset($input['is_available']) ? 1 : 0,
        ];
        $errors = [];

        if ($data['name'] === '' || text_length($data['name']) > 100) {
            $errors['name'] = 'Nama menu wajib diisi dan maksimal 100 karakter.';
        }
        if (!in_array($data['category'], Menu::CATEGORIES, true)) {
            $errors['category'] = 'Pilih kategori yang tersedia.';
        }
        if (text_length($data['description']) > 1000) {
            $errors['description'] = 'Deskripsi maksimal 1.000 karakter.';
        }
        if (!is_numeric($data['price']) || (float) $data['price'] < 0 || (float) $data['price'] > 100000000) {
            $errors['price'] = 'Harga harus berupa angka antara 0 dan 100.000.000.';
        }
        if (filter_var($data['stock'], FILTER_VALIDATE_INT) === false || (int) $data['stock'] < 0 || (int) $data['stock'] > 1000000) {
            $errors['stock'] = 'Stok harus berupa bilangan bulat antara 0 dan 1.000.000.';
        }

        $data['price'] = number_format((float) $data['price'], 2, '.', '');
        $data['stock'] = (int) $data['stock'];
        return [$data, $errors];
    }

    private function backWithErrors(array $errors, string $path): never
    {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = [
            'name' => trim((string) ($_POST['name'] ?? '')),
            'category' => (string) ($_POST['category'] ?? ''),
            'description' => trim((string) ($_POST['description'] ?? '')),
            'price' => (string) ($_POST['price'] ?? ''),
            'stock' => (string) ($_POST['stock'] ?? ''),
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
        ];
        flash('error', 'Periksa kembali data yang belum sesuai.');
        redirect($path);
    }
}
