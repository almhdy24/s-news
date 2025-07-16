<?php
declare(strict_types=1);

namespace Core\DB;

use Exception;

class Storage
{
    private string $tableDir;

    public function __construct(string $tableDir)
    {
        $this->tableDir = rtrim($tableDir, DIRECTORY_SEPARATOR);
        if (!is_dir($this->tableDir) && !mkdir($this->tableDir, 0755, true) && !is_dir($this->tableDir)) {
            throw new Exception("Cannot create table directory: {$this->tableDir}");
        }
    }

    public function getDataDir(): string
    {
        return $this->tableDir;
    }

    public function getRecordFile(int $id): string
    {
        return $this->tableDir . DIRECTORY_SEPARATOR . $id . '.json';
    }

    public function saveRecord(array $record): void
    {
        if (!isset($record['id'])) {
            throw new Exception('Record must have an id to save.');
        }

        $json = json_encode($record, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $file = $this->getRecordFile((int)$record['id']);

        if (file_put_contents($file, $json, LOCK_EX) === false) {
            throw new Exception("Failed to write record to file: $file");
        }
    }

    public function deleteRecord(int $id): bool
    {
        $file = $this->getRecordFile($id);
        return file_exists($file) && unlink($file);
    }

    public function loadRecord(int $id): ?array
    {
        $file = $this->getRecordFile($id);
        if (!file_exists($file)) {
            return null;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            throw new Exception("Failed to read file: $file");
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            throw new Exception("Invalid JSON data in file: $file");
        }

        return $data;
    }

    public function loadAll(): \Generator
    {
        $files = glob($this->tableDir . DIRECTORY_SEPARATOR . '*.json');
        foreach ($files as $file) {
            if (!is_readable($file)) continue;

            $content = file_get_contents($file);
            if ($content === false) continue;

            $data = json_decode($content, true);
            if (is_array($data)) {
                yield $data;
            }
        }
    }

    // ─────────────── Index Handling ───────────────

    private function getIndexDir(string $field): string
    {
        return $this->tableDir . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . $field;
    }

    private function sanitizeIndexValue(string $value): string
    {
        return preg_replace('/[^a-zA-Z0-9._-]/', '_', strtolower($value));
    }

    public function updateIndex(string $field, $value, int $id, bool $add = true): void
    {
        $indexDir = $this->getIndexDir($field);
        if (!is_dir($indexDir) && !mkdir($indexDir, 0755, true) && !is_dir($indexDir)) {
            throw new Exception("Cannot create index directory: {$indexDir}");
        }

        $fileValue = $this->sanitizeIndexValue((string)$value);
        $indexFile = $indexDir . DIRECTORY_SEPARATOR . $fileValue . '.idx';

        $ids = [];
        if (file_exists($indexFile)) {
            $content = file_get_contents($indexFile);
            $ids = json_decode($content, true) ?: [];
        }

        if ($add) {
            $ids[] = $id;
        } else {
            $ids = array_filter($ids, fn($existingId) => $existingId !== $id);
        }

        $ids = array_values(array_unique($ids));

        if (file_put_contents($indexFile, json_encode($ids), LOCK_EX) === false) {
            throw new Exception("Failed to update index: $indexFile");
        }
    }

    public function getIdsByIndex(string $field, $value): array
    {
        $indexDir = $this->getIndexDir($field);
        $fileValue = $this->sanitizeIndexValue((string)$value);
        $indexFile = $indexDir . DIRECTORY_SEPARATOR . $fileValue . '.idx';

        if (!file_exists($indexFile)) {
            return [];
        }

        $content = file_get_contents($indexFile);
        $ids = json_decode($content, true);

        return is_array($ids) ? $ids : [];
    }

    public function clearIndex(string $field, string $value): void
    {
        $indexFile = $this->getIndexDir($field) . DIRECTORY_SEPARATOR . $this->sanitizeIndexValue($value) . '.idx';
        if (file_exists($indexFile)) {
            unlink($indexFile);
        }
    }

    // ─────────────── ID Handling ───────────────

    public function getMaxId(): int
    {
        $files = glob($this->tableDir . DIRECTORY_SEPARATOR . '*.json');
        $max = 0;

        foreach ($files as $file) {
            $filename = basename($file, '.json');
            if (ctype_digit($filename)) {
                $id = (int)$filename;
                $max = max($max, $id);
            }
        }

        return $max;
    }

    public function generateId(): int
    {
        return $this->getMaxId() + 1;
    }
}