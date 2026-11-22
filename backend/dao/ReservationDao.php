<?php
require_once __DIR__ . '/BaseDao.php';

class ReservationDao extends BaseDao
{
    private const SELECT = "SELECT r.*,
                                   CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                                   t.table_number
                            FROM reservations r
                            JOIN users u ON u.id = r.user_id
                            LEFT JOIN restaurant_tables t ON t.id = r.table_id";

    public function __construct()
    {
        parent::__construct('reservations', [
            'user_id', 'table_id', 'reservation_date', 'reservation_time',
            'guests', 'special_requests', 'status'
        ]);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY r.reservation_date, r.reservation_time");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE r.id = :id", ['id' => $id]);
    }

    public function get_by_user($user_id)
    {
        return $this->query(
            self::SELECT . " WHERE r.user_id = :user_id ORDER BY r.reservation_date, r.reservation_time",
            ['user_id' => $user_id]
        );
    }

    public function get_by_date($date)
    {
        return $this->query(
            self::SELECT . " WHERE r.reservation_date = :date ORDER BY r.reservation_time",
            ['date' => $date]
        );
    }

    public function count_upcoming_by_user($user_id)
    {
        $row = $this->query_unique(
            "SELECT COUNT(*) AS total FROM reservations
             WHERE user_id = :user_id
               AND status IN ('pending', 'confirmed')
               AND reservation_date >= CURDATE()",
            ['user_id' => $user_id]
        );
        return (int) $row['total'];
    }

    public function count_upcoming_by_table($table_id)
    {
        $row = $this->query_unique(
            "SELECT COUNT(*) AS total FROM reservations
             WHERE table_id = :table_id
               AND status IN ('pending', 'confirmed')
               AND reservation_date >= CURDATE()",
            ['table_id' => $table_id]
        );
        return (int) $row['total'];
    }

    public function get_conflicts($table_id, $date, $time, $exclude_id = 0)
    {
        return $this->query(
            "SELECT * FROM reservations
             WHERE table_id = :table_id
               AND reservation_date = :date
               AND status IN ('pending', 'confirmed')
               AND id <> :exclude_id
               AND ABS(TIME_TO_SEC(TIMEDIFF(reservation_time, :time))) < 2 * 60 * 60",
            ['table_id' => $table_id, 'date' => $date, 'time' => $time, 'exclude_id' => (int) $exclude_id]
        );
    }
}
