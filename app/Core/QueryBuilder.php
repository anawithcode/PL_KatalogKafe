<?php

declare(strict_types=1);

namespace App\Core;

use InvalidArgumentException;
use PDO;
use RuntimeException;

/**
 * Query Builder ringan berbasis PDO.
 * Nilai selalu dikirim melalui prepared statement; identifier divalidasi ketat.
 */
final class QueryBuilder
{
    private array $columns = ['*'];
    private array $wheres = [];
    private array $bindings = [];
    private array $orders = [];
    private ?int $limitValue = null;
    private ?int $offsetValue = null;
    private bool $distinctValue = false;

    public function __construct(private PDO $pdo, private string $table)
    {
        $this->identifier($table);
    }

    public function select(string ...$columns): self
    {
        foreach ($columns as $column) {
            if ($column !== '*') {
                $this->identifier($column);
            }
        }
        $this->columns = $columns ?: ['*'];
        return $this;
    }

    public function distinct(): self
    {
        $this->distinctValue = true;
        return $this;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $this->identifier($column);
        $operator = strtoupper($operator);
        if (!in_array($operator, ['=', '!=', '<>', '>', '>=', '<', '<=', 'LIKE'], true)) {
            throw new InvalidArgumentException('Operator WHERE tidak didukung.');
        }
        $this->wheres[] = ['boolean' => 'AND', 'column' => $column, 'operator' => $operator, 'value' => $value];
        return $this;
    }

    public function orWhere(string $column, string $operator, mixed $value): self
    {
        $this->identifier($column);
        $operator = strtoupper($operator);
        if (!in_array($operator, ['=', '!=', '<>', '>', '>=', '<', '<=', 'LIKE'], true)) {
            throw new InvalidArgumentException('Operator WHERE tidak didukung.');
        }
        $this->wheres[] = ['boolean' => 'OR', 'column' => $column, 'operator' => $operator, 'value' => $value];
        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->identifier($column);
        $direction = strtoupper($direction);
        if (!in_array($direction, ['ASC', 'DESC'], true)) {
            throw new InvalidArgumentException('Arah urutan tidak valid.');
        }
        $this->orders[] = [$column, $direction];
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limitValue = max(1, $limit);
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offsetValue = max(0, $offset);
        return $this;
    }

    public function get(): array
    {
        [$sql, $bindings] = $this->compileSelect($this->columns);
        return $this->run($sql, $bindings)->fetchAll();
    }

    public function first(): ?array
    {
        $this->limit(1);
        [$sql, $bindings] = $this->compileSelect($this->columns);
        $result = $this->run($sql, $bindings)->fetch();
        return $result === false ? null : $result;
    }

    public function count(): int
    {
        [$sql, $bindings] = $this->compileSelect(['COUNT(*) AS aggregate'], true);
        return (int) $this->run($sql, $bindings)->fetchColumn();
    }

    public function sum(string $column): float
    {
        $this->identifier($column);
        [$sql, $bindings] = $this->compileSelect(["COALESCE(SUM($column), 0) AS aggregate"], true);
        return (float) $this->run($sql, $bindings)->fetchColumn();
    }

    public function insert(array $data): int
    {
        if ($data === []) {
            throw new InvalidArgumentException('Data INSERT tidak boleh kosong.');
        }
        $columns = array_keys($data);
        foreach ($columns as $column) {
            $this->identifier((string) $column);
        }
        $quoted = array_map(fn (string $column): string => $this->quote($column), $columns);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = 'INSERT INTO ' . $this->quote($this->table)
            . ' (' . implode(', ', $quoted) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $this->run($sql, array_values($data));
        return (int) $this->pdo->lastInsertId();
    }

    public function update(array $data): int
    {
        if ($data === [] || $this->wheres === []) {
            throw new RuntimeException('UPDATE membutuhkan data dan kondisi WHERE.');
        }
        $sets = [];
        $bindings = [];
        foreach ($data as $column => $value) {
            $this->identifier((string) $column);
            $sets[] = $this->quote((string) $column) . ' = ?';
            $bindings[] = $value;
        }
        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql = 'UPDATE ' . $this->quote($this->table) . ' SET ' . implode(', ', $sets) . $whereSql;
        $statement = $this->run($sql, [...$bindings, ...$whereBindings]);
        return $statement->rowCount();
    }

    public function delete(): int
    {
        if ($this->wheres === []) {
            throw new RuntimeException('DELETE membutuhkan kondisi WHERE.');
        }
        [$whereSql, $whereBindings] = $this->compileWheres();
        $sql = 'DELETE FROM ' . $this->quote($this->table) . $whereSql;
        return $this->run($sql, $whereBindings)->rowCount();
    }

    private function compileSelect(array $columns, bool $aggregate = false): array
    {
        if (!$aggregate) {
            foreach ($columns as $column) {
                if ($column !== '*') {
                    $this->identifier($column);
                }
            }
        }
        $columnSql = implode(', ', array_map(
            fn (string $column): string => str_contains($column, '(') ? $column : ($column === '*' ? '*' : $this->quote($column)),
            $columns
        ));
        $sql = 'SELECT ' . ($this->distinctValue ? 'DISTINCT ' : '') . $columnSql
            . ' FROM ' . $this->quote($this->table);
        [$whereSql, $bindings] = $this->compileWheres();
        $sql .= $whereSql;
        if (!$aggregate && $this->orders !== []) {
            $sql .= ' ORDER BY ' . implode(', ', array_map(
                fn (array $order): string => $this->quote($order[0]) . ' ' . $order[1],
                $this->orders
            ));
        }
        if (!$aggregate && $this->limitValue !== null) {
            $sql .= ' LIMIT ' . $this->limitValue;
            if ($this->offsetValue !== null) {
                $sql .= ' OFFSET ' . $this->offsetValue;
            }
        }
        return [$sql, $bindings];
    }

    private function compileWheres(): array
    {
        if ($this->wheres === []) {
            return ['', []];
        }
        $sql = ' WHERE ';
        $bindings = [];
        foreach ($this->wheres as $index => $where) {
            $sql .= ($index > 0 ? ' ' . $where['boolean'] . ' ' : '')
                . $this->quote($where['column']) . ' ' . $where['operator'] . ' ?';
            $bindings[] = $where['value'];
        }
        return [$sql, $bindings];
    }

    private function run(string $sql, array $bindings): \PDOStatement
    {
        $statement = $this->pdo->prepare($sql);
        foreach (array_values($bindings) as $index => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : (is_bool($value) ? PDO::PARAM_BOOL : PDO::PARAM_STR);
            if ($value === null) {
                $type = PDO::PARAM_NULL;
            }
            $statement->bindValue($index + 1, $value, $type);
        }
        $statement->execute();
        $this->reset();
        return $statement;
    }

    private function reset(): void
    {
        $this->columns = ['*'];
        $this->wheres = [];
        $this->bindings = [];
        $this->orders = [];
        $this->limitValue = null;
        $this->offsetValue = null;
        $this->distinctValue = false;
    }

    private function identifier(string $identifier): void
    {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException('Nama tabel atau kolom tidak valid.');
        }
    }

    private function quote(string $identifier): string
    {
        return '`' . $identifier . '`';
    }
}
