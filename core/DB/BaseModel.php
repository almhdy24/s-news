<?php
declare(strict_types=1);

namespace Core\DB;

use Core\App;

abstract class BaseModel
{
    protected Table $table;

    public function __construct(?string $dataDir = null)
    {
        $dataDir ??= App::dataPath();

        $db = new JSONDB($dataDir, [
            $this->tableName() => $this->schema()
        ]);

        $this->table = $db->table($this->tableName());
    }

    abstract protected function tableName(): string;
    abstract protected function schema(): array;

    public function all(): array
    {
        return $this->table->all();
    }

    public function find(int|string $id): ?array
    {
        return $this->table->find((int) $id);
    }

    public function create(array $data): int
    {
        return $this->table->create($data);
    }

    public function update(int|string $id, array $data): bool
    {
        return $this->table->update((int) $id, $data);
    }

    public function delete(int|string $id): bool
    {
        return $this->table->delete((int) $id);
    }

    public function yieldAll(): \Generator
    {
        return $this->table->yieldAll();
    }

    public function table(): Table
    {
        return $this->table;
    }
}