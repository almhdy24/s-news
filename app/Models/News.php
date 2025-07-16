<?php
declare(strict_types=1);

namespace App\Models;

use Core\DB\BaseModel;
use Core\DB\Exception\ValidationException;

class News extends BaseModel
{
    protected function tableName(): string
    {
        return 'news';
    }

    protected function schema(): array
    {
        return [
            'title' => [
                'type' => 'string',
                'required' => true,
                'minLength' => 3,
                'maxLength' => 200,
                'label' => 'Title',
            ],
            'content' => [
                'type' => 'string',
                'required' => true,
                'minLength' => 10,
                'label' => 'Content',
            ],
            'category_id' => [
                'type' => 'int',
                'required' => true,
                'label' => 'Category',
            ],
            'status' => [
                'type' => 'string',
                'required' => true,
                'enum' => ['draft', 'published', 'archived'],
                'label' => 'Status',
            ],
            'created_at' => [
                'type' => 'string',
                'required' => true,
            ],
            'updated_at' => [
                'type' => 'string',
                'required' => false,
            ],
        ];
    }

    public function create(array $data): int
    {
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        return parent::insert($data);
    }

    public function update(string|int $id, array $data): bool
{
    return parent::update($id, $data);
}

    public function paginate(int $page = 1, int $perPage = 5, ?string $status = null): array
    {
        $records = array_reverse($this->all());

        if ($status !== null) {
            $records = array_filter($records, fn($item) =>
                isset($item['status']) && $item['status'] === $status
            );
        }

        $total = count($records);
        $totalPages = (int) ceil($total / $perPage);
        $page = max(1, min($page, max(1, $totalPages)));

        $offset = ($page - 1) * $perPage;
        $items = array_slice($records, $offset, $perPage);

        return [
            'items'       => $items,
            'total'       => $total,
            'perPage'     => $perPage,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ];
    }
}