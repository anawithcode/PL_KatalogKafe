<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\QueryBuilder;
use PDO;

final class Menu
{
    public const CATEGORIES = ['Kopi', 'Non-Kopi', 'Pastry', 'Makanan'];
    public const SORTS = ['newest', 'name', 'price_low', 'price_high', 'stock'];

    public function __construct(private PDO $pdo) {}

    private function query(): QueryBuilder
    {
        return new QueryBuilder($this->pdo, 'menus');
    }

    public function paginate(array $filters, int $page = 1, int $perPage = 6): array
    {
        $page = max(1, $page);
        $base = $this->applyFilters($this->query(), $filters);
        $total = $base->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = min($page, $lastPage);

        $query = $this->applyFilters($this->query(), $filters);
        match ($filters['sort'] ?? 'newest') {
            'name' => $query->orderBy('name', 'ASC'),
            'price_low' => $query->orderBy('price', 'ASC'),
            'price_high' => $query->orderBy('price', 'DESC'),
            'stock' => $query->orderBy('stock', 'DESC'),
            default => $query->orderBy('created_at', 'DESC')->orderBy('id', 'DESC'),
        };

        $items = $query->limit($perPage)->offset(($page - 1) * $perPage)->get();
        return [
            'items' => $items,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'last_page' => $lastPage,
            'from' => $total === 0 ? 0 : ($page - 1) * $perPage + 1,
            'to' => min($page * $perPage, $total),
        ];
    }

    private function applyFilters(QueryBuilder $query, array $filters): QueryBuilder
    {
        if (!empty($filters['search'])) {
            $query->where('name', 'LIKE', '%' . $filters['search'] . '%');
        }
        if (!empty($filters['category']) && in_array($filters['category'], self::CATEGORIES, true)) {
            $query->where('category', '=', $filters['category']);
        }
        if (($filters['availability'] ?? '') !== '') {
            $query->where('is_available', '=', (int) $filters['availability']);
        }
        return $query;
    }

    public function find(int $id): ?array
    {
        return $this->query()->where('id', '=', $id)->first();
    }

    public function create(array $data): int
    {
        return $this->query()->insert($data + [
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function update(int $id, array $data): int
    {
        return $this->query()->where('id', '=', $id)->update($data + [
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete(int $id): int
    {
        return $this->query()->where('id', '=', $id)->delete();
    }

    public function stats(): array
    {
        return [
            'total' => $this->query()->count(),
            'available' => $this->query()->where('is_available', '=', 1)->count(),
            'unavailable' => $this->query()->where('is_available', '=', 0)->count(),
            'stock' => (int) $this->query()->sum('stock'),
        ];
    }
}
