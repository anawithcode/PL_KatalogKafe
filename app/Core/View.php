<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts/main'): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = dirname(__DIR__) . '/Views/' . $view . '.php';
        $layoutFile = dirname(__DIR__) . '/Views/' . $layout . '.php';

        if (!is_file($viewFile) || !is_file($layoutFile)) {
            throw new \RuntimeException('View tidak ditemukan.');
        }

        ob_start();
        require $viewFile;
        $content = ob_get_clean();
        require $layoutFile;
    }
}
