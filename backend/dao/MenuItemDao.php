<?php
require_once __DIR__ . '/BaseDao.php';

class MenuItemDao extends BaseDao
{
    private const SELECT = "SELECT m.*, c.name AS category_name
                            FROM menu_items m
                            JOIN categories c ON c.id = m.category_id";

    public function __construct()
    {
        parent::__construct('menu_items', ['category_id', 'name', 'description', 'price', 'image_url', 'is_available']);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY c.id, m.name");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE m.id = :id", ['id' => $id]);
    }

    public function get_by_category($category_id)
    {
        return $this->query(
            self::SELECT . " WHERE m.category_id = :category_id ORDER BY m.name",
            ['category_id' => $category_id]
        );
    }

    public function get_available()
    {
        return $this->query(self::SELECT . " WHERE m.is_available = 1 ORDER BY c.id, m.name");
    }

    public function count_by_category($category_id)
    {
        $row = $this->query_unique(
            "SELECT COUNT(*) AS total FROM menu_items WHERE category_id = :category_id",
            ['category_id' => $category_id]
        );
        return (int) $row['total'];
    }
}
