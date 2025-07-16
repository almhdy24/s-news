<?php
namespace App\Models;

use Core\DB\JSONDB;
use Core\DB\Schema;
use Core\DB\Storage;
use Core\DB\Table;

class VisitorLog
{
    private Table $table;

    public function __construct(?JSONDB $jsonDb = null)
    {
        // Define schema locally inside the model
        $schema = new Schema([
            'ip'         => ['type' => 'string', 'required' => true],
            'user_agent' => ['type' => 'string', 'required' => false],
            'url'        => ['type' => 'string', 'required' => true],
            'timestamp'  => ['type' => 'datetime', 'required' => true],
        ]);

        $storagePath = __DIR__ . '/../../data/visitor_logs'; // Adjust as needed
        $storage = new Storage($storagePath);

        // You can add indexing here if supported
        $this->table = new Table($storage, $schema, ['ip', 'url']);

        // If JSONDB is provided, use its factory method instead (optional injection)
        if ($jsonDb !== null) {
            $this->table = $jsonDb->table('visitor_logs');
        }
    }

    public function all(): array
    {
        return $this->table->all();
    }

    public function findByIpUrlDate(string $ip, string $url, string $date): ?array
    {
        foreach ($this->table->all() as $record) {
            if (
                $record['ip'] === $ip &&
                str_starts_with($record['url'], $url) &&
                str_starts_with($record['timestamp'], $date)
            ) {
                return $record;
            }
        }
        return null;
    }

    public function create(array $data): int
    {
        return $this->table->create($data);
    }

    public function count(?callable $filter = null): int
    {
        $count = 0;
        foreach ($this->table->yieldAll() as $record) {
            if ($filter === null || $filter($record)) {
                $count++;
            }
        }
        return $count;
    }

    public function findUniqueIpsAll(): array
    {
        $uniqueIps = [];
        foreach ($this->table->yieldAll() as $record) {
            $uniqueIps[$record['ip']] = true;
        }
        return array_keys($uniqueIps);
    }

    public function findUniqueIpsToday(): array
    {
        $today = date('Y-m-d');
        $uniqueIps = [];

        foreach ($this->table->yieldAll() as $record) {
            if (str_starts_with($record['timestamp'], $today)) {
                $uniqueIps[$record['ip']] = true;
            }
        }

        return array_keys($uniqueIps);
    }
}