<?php
declare(strict_types=1);

namespace Core\DB;

use Core\DB\Exception\ValidationException;
use Exception;

class Table
{
    private Storage $storage;
    private Schema $schema;
    private array $indexedFields;
    private int $lastId;

    public function __construct(Storage $storage, Schema $schema, array $indexedFields = [])
    {
        $this->storage = $storage;
        $this->schema = $schema;
        $this->indexedFields = $indexedFields;
        $this->lastId = $this->initLastId();
    }

    private function initLastId(): int
    {
        $max = 0;
        foreach (glob($this->storage->getDataDir() . '/*.json') as $file) {
            $id = (int) basename($file, '.json');
            $max = max($max, $id);
        }
        return $max;
    }

    public function all(): array
    {
        return iterator_to_array($this->yieldAll());
    }

    public function yieldAll(): \Generator
    {
        yield from $this->storage->loadAll();
    }

    public function find(int|string $id): ?array
    {
        $id = $this->sanitizeId($id);
        return $this->storage->loadRecord($id);
    }

    public function exists(int|string $id): bool
    {
        return $this->find($id) !== null;
    }

    public function create(array $record): int
    {
        $this->schema->validate($record);
        $this->lastId++;
        $record['id'] = $this->lastId;

        $this->storage->saveRecord($record);
        $this->updateIndexesOnCreate($record);

        return $record['id'];
    }

    public function update(int|string $id, array $updates): bool
    {
        $id = $this->sanitizeId($id);
        $existing = $this->storage->loadRecord($id);

        if ($existing === null) {
            return false;
        }

        $newRecord = array_merge($existing, $updates);
        $this->schema->validate($newRecord, true);
        $this->storage->saveRecord($newRecord);
        $this->updateIndexesOnUpdate($existing, $newRecord);

        return true;
    }

    public function save(array $record): int
    {
        if (isset($record['id']) && $this->exists($record['id'])) {
            $this->update($record['id'], $record);
            return (int) $record['id'];
        }
        return $this->create($record);
    }

    public function delete(int|string $id): bool
    {
        $id = $this->sanitizeId($id);
        $record = $this->storage->loadRecord($id);
        if ($record === null) return false;

        $deleted = $this->storage->deleteRecord($id);
        if ($deleted) {
            $this->updateIndexesOnDelete($record);
        }

        return $deleted;
    }

    public function findByField(string $field, mixed $value): array
    {
        if (in_array($field, $this->indexedFields, true)) {
            $ids = $this->storage->getIdsByIndex($field, $value);
            return array_filter(array_map(fn($id) => $this->find($id), $ids));
        }

        // fallback to full scan
        return array_filter(iterator_to_array($this->yieldAll()), fn($r) => ($r[$field] ?? null) === $value);
    }

    private function updateIndexesOnCreate(array $record): void
    {
        foreach ($this->indexedFields as $field) {
            if (isset($record[$field])) {
                $this->storage->updateIndex($field, $record[$field], $record['id'], true);
            }
        }
    }

    private function updateIndexesOnUpdate(array $oldRecord, array $newRecord): void
    {
        foreach ($this->indexedFields as $field) {
            $oldVal = $oldRecord[$field] ?? null;
            $newVal = $newRecord[$field] ?? null;

            if ($oldVal !== $newVal) {
                if ($oldVal !== null) {
                    $this->storage->updateIndex($field, $oldVal, $newRecord['id'], false);
                }
                if ($newVal !== null) {
                    $this->storage->updateIndex($field, $newVal, $newRecord['id'], true);
                }
            }
        }
    }

    private function updateIndexesOnDelete(array $record): void
    {
        foreach ($this->indexedFields as $field) {
            if (isset($record[$field])) {
                $this->storage->updateIndex($field, $record[$field], $record['id'], false);
            }
        }
    }

    private function sanitizeId(int|string $id): int
    {
        if (!is_numeric($id) || $id < 1) {
            throw new \InvalidArgumentException("Invalid ID: {$id}");
        }
        return (int) $id;
    }
}