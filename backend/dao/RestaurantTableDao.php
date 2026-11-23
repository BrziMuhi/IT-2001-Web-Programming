<?php
require_once __DIR__ . '/BaseDao.php';

class RestaurantTableDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('restaurant_tables', ['table_number', 'capacity', 'is_active']);
    }

    public function get_all()
    {
        return $this->query("SELECT * FROM restaurant_tables ORDER BY table_number");
    }

    public function get_by_table_number($table_number)
    {
        return $this->query_unique(
            "SELECT * FROM restaurant_tables WHERE table_number = :table_number",
            ['table_number' => $table_number]
        );
    }

    public function get_available_for_guests($guests)
    {
        return $this->query(
            "SELECT * FROM restaurant_tables
             WHERE is_active = 1 AND capacity >= :guests
             ORDER BY capacity, table_number",
            ['guests' => (int) $guests]
        );
    }
}
