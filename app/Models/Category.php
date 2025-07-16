<?php  
declare(strict_types=1);  
namespace App\Models;  

use Core\DB\JSONDB;  

class Category  
{  
    private \Core\DB\Table $table;  

    // Schema is moved to a method for separation of concerns and reusability
    private static function getSchema(): array
    {
        return [
            'name'       => ['type' => 'string', 'required' => true, 'minLength' => 2],
            'created_at' => ['type' => 'string', 'required' => true]
        ];
    }

    public function __construct()  
    {  
        $schema = self::getSchema();
        $db = new JSONDB(__DIR__ . '/../../data', ['categories' => $schema]);
        $this->table = $db->table('categories');  
    }

    public function all(): array  
    {  
        return $this->table->all();  
    }

    public function find(int|string $id): ?array  
    {  
        return $this->table->find((int)$id);  
    }

    public function create(array $data): int  
    {  
        // Simple validation logic before inserting
        if (isset($data['name']) && strlen($data['name']) >= 2) {
            return $this->table->create($data);  
        }
        return 0;  // Could also throw an exception or return a specific error
    }

    public function update(int|string $id, array $data): bool  
    {  
        // Simple validation logic before updating
        if (isset($data['name']) && strlen($data['name']) >= 2) {
            return $this->table->update((int)$id, $data);
        }
        return false;  // Could throw an exception or return error
    }

    public function delete(int|string $id): bool  
    {  
        return $this->table->delete((int)$id);  
    }
}