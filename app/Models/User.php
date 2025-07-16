<?php
declare(strict_types=1);

namespace App\Models;

use Core\DB\BaseModel;

class User extends BaseModel
{
    protected function tableName(): string
    {
        return 'users';
    }

    protected function schema(): array
    {
        return [
            'username' => ['type' => 'string', 'required' => true, 'minLength' => 3],
            'password' => ['type' => 'string', 'required' => true],
            'role'     => ['type' => 'string', 'required' => false],
            'email'    => ['type' => 'string', 'required' => false],
        ];
    }

    public function findByUsername(string $username): ?array
    {
        foreach ($this->yieldAll() as $record) {
            if (isset($record['username']) && strcasecmp($record['username'], $username) === 0) {
                return $record;
            }
        }
        return null;
    }
}