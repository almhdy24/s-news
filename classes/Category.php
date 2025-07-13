<?php
require_once 'JSONDB.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = new JSONDB(__DIR__ . '/../data/categories.json');
    }

    public function all() {
        return $this->db->all();
    }

    public function find($id) {
    foreach ($this->db->all() as $item) {
        if ((string)$item['id'] === (string)$id) return $item;
    }
    return null;
}

    public function create($data) {
        return $this->db->insert($data);
    }

    public function update($id, $data) {
        return $this->db->update($id, $data);
    }

    public function delete($id) {
        return $this->db->delete($id);
    }
    public function paginate(int $page = 1, int $perPage = 5): array
{
    $all = $this->all();

    $total = count($all);
    $totalPages = (int) ceil($total / $perPage);

    $page = max(1, min($page, $totalPages));

    $offset = ($page - 1) * $perPage;
    $items = array_slice($all, $offset, $perPage);

    return [
        'items' => $items,
        'total' => $total,
        'perPage' => $perPage,
        'currentPage' => $page,
        'totalPages' => $totalPages,
    ];
}
}