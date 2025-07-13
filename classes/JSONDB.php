<?php

class JSONDB {
    private $file;

    public function __construct(string $filename) {
        $this->file = $filename;

        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (!file_exists($this->file)) {
            $this->save([]);
        }
    }

    public function all(): array {
        $content = @file_get_contents($this->file);
        if ($content === false) return [];

        $decoded = json_decode($content, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function save(array $data): void {
        @file_put_contents($this->file, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function insert(array $record): string {
        $data = $this->all();
        $record['id'] = uniqid();
        $data[] = $record;
        $this->save($data);
        return $record['id'];
    }

    public function find(string $id): ?array {
        foreach ($this->all() as $item) {
            if ((string)$item['id'] === (string)$id) {
                return $item;
            }
        }
        return null;
    }

    public function update(string $id, array $newData): bool {
        $data = $this->all();
        $found = false;

        foreach ($data as &$item) {
            if ((string)$item['id'] === (string)$id) {
                $item = array_merge($item, $newData);
                $found = true;
                break;
            }
        }

        if ($found) {
            $this->save($data);
        }

        return $found;
    }

    public function delete(string $id): bool {
        $data = $this->all();
        $originalCount = count($data);
        $data = array_filter($data, fn($item) => (string)$item['id'] !== (string)$id);
        $this->save(array_values($data));
        return count($data) < $originalCount;
    }


}