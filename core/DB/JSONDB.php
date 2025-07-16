<?php
declare(strict_types=1);

namespace Core\DB;

class JSONDB
{
    private string $dataDir;
    private array $schemas;
    private array $tables = [];

    public function __construct(string $dataDir, array $schemas)
    {
        $this->dataDir = rtrim($dataDir, DIRECTORY_SEPARATOR);
        $this->schemas = $schemas;
    }

    public function table(string $name): Table
    {
        if (!isset($this->tables[$name])) {
            if (!isset($this->schemas[$name])) {
                throw new \Exception("No schema defined for table: {$name}");
            }

            $tableDir = $this->dataDir . DIRECTORY_SEPARATOR . $name;
            $schema = new Schema($this->schemas[$name]);
            $storage = new Storage($tableDir);

            $this->tables[$name] = new Table($storage, $schema);
        }
        return $this->tables[$name];
    }
}