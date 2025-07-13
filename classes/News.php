<?php
require_once 'JSONDB.php';

class News {
    private $db;

    public function __construct() {
        $this->db = new JSONDB(__DIR__ . '/../data/news.json');
    }

    public function all() {
        return array_reverse($this->db->all());
    }

    public function create($data) {
        return $this->db->insert($data);
    }

    public function find($id) {
        return $this->db->find($id);
    }

    public function update($id, $data) {
        $this->db->update($id, $data);
    }

    public function delete($id) {
        $this->db->delete($id);
    }
    public function paginate(int $page = 1, int $perPage = 5): array
{
    $all = $this->all();
    $all = array_reverse($all); // newest first

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