<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/RestaurantTableDao.php';
require_once __DIR__ . '/../dao/ReservationDao.php';

class RestaurantTableService extends BaseService
{
    const MAX_CAPACITY = 12;

    private $reservation_dao;

    public function __construct()
    {
        parent::__construct(new RestaurantTableDao(), 'Table');
        $this->reservation_dao = new ReservationDao();
    }

    public function add($table)
    {
        $table = $this->normalize($table);
        $table['is_active'] = $table['is_active'] ?? 1;
        return parent::add($table);
    }

    public function update($table, $id)
    {
        return parent::update($this->normalize($table), $id);
    }

    protected function validate($table, $id = null)
    {
        Validator::required($table, ['table_number', 'capacity']);
        Validator::integer($table['table_number'], 'table_number', 1);
        Validator::integer($table['capacity'], 'capacity', 1, self::MAX_CAPACITY);

        $existing = $this->dao->get_by_table_number($table['table_number']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("Table number {$table['table_number']} already exists.");
        }
    }

    public function delete($id)
    {
        $this->get_by_id($id);

        $upcoming = $this->reservation_dao->count_upcoming_by_table($id);
        if ($upcoming > 0) {
            throw new ValidationException("This table has $upcoming upcoming reservation(s). Cancel or move them first.");
        }
        $this->dao->delete($id);
    }

    public function get_available_for_guests($guests)
    {
        Validator::integer($guests, 'guests', 1, self::MAX_CAPACITY);
        return $this->dao->get_available_for_guests($guests);
    }

    private function normalize($table)
    {
        if (array_key_exists('is_active', $table)) {
            $table['is_active'] = in_array($table['is_active'], ['on', 'true', '1', 1, true], true) ? 1 : 0;
        }
        return $table;
    }
}
