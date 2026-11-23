<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ReservationDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/RestaurantTableDao.php';

class ReservationService extends BaseService
{
    const OPENING_TIME = '09:00';
    const LAST_BOOKING_TIME = '22:00';
    const MAX_GUESTS = 8;
    const MAX_UPCOMING_RESERVATIONS = 3;
    const STATUSES = ['pending', 'confirmed', 'cancelled', 'completed'];

    private $user_dao;
    private $table_dao;

    public function __construct()
    {
        parent::__construct(new ReservationDao(), 'Reservation');
        $this->user_dao = new UserDao();
        $this->table_dao = new RestaurantTableDao();
    }

    public function add($reservation)
    {
        $reservation = $this->normalize($reservation);
        $reservation['status'] = 'pending';
        $reservation['table_id'] = null;

        $this->validate($reservation);

        Validator::in_future(
            $reservation['reservation_date'] . ' ' . $reservation['reservation_time'],
            'reservation_date'
        );

        if ($this->dao->count_upcoming_by_user($reservation['user_id']) >= self::MAX_UPCOMING_RESERVATIONS) {
            throw new ValidationException(
                "A customer can have at most " . self::MAX_UPCOMING_RESERVATIONS
                . " upcoming reservations. Cancel one first."
            );
        }

        $created = $this->dao->add($reservation);
        return $this->dao->get_by_id($created['id']);
    }

    public function update($reservation, $id)
    {
        return parent::update($this->normalize($reservation), $id);
    }

    public function cancel($id)
    {
        $reservation = $this->get_by_id($id);

        if (!in_array($reservation['status'], ['pending', 'confirmed'])) {
            throw new ValidationException("Only pending or confirmed reservations can be cancelled.");
        }
        $this->dao->update(['status' => 'cancelled'], $id);
        return $this->dao->get_by_id($id);
    }

    public function get_by_user($user_id)
    {
        Validator::integer($user_id, 'user_id', 1);
        if ($this->user_dao->get_by_id($user_id) === null) {
            throw new NotFoundException("User not found.");
        }
        return $this->dao->get_by_user($user_id);
    }

    public function get_by_date($date)
    {
        Validator::date($date, 'date');
        return $this->dao->get_by_date($date);
    }

    protected function validate($reservation, $id = null)
    {
        Validator::required($reservation, ['user_id', 'reservation_date', 'reservation_time', 'guests']);

        Validator::integer($reservation['user_id'], 'user_id', 1);
        if ($this->user_dao->get_by_id($reservation['user_id']) === null) {
            throw new ValidationException("The selected customer does not exist.");
        }

        Validator::date($reservation['reservation_date'], 'reservation_date');
        Validator::time($reservation['reservation_time'], 'reservation_time');

        $time = substr($reservation['reservation_time'], 0, 5);
        if ($time < self::OPENING_TIME || $time > self::LAST_BOOKING_TIME) {
            throw new ValidationException(
                "Reservations are possible between " . self::OPENING_TIME . " and " . self::LAST_BOOKING_TIME . "."
            );
        }

        Validator::integer($reservation['guests'], 'guests', 1, self::MAX_GUESTS);

        if (!empty($reservation['special_requests'])) {
            Validator::length($reservation['special_requests'], 'special_requests', 0, 500);
            Validator::no_html($reservation['special_requests'], 'special_requests');
        }

        if (!empty($reservation['status'])) {
            Validator::one_of($reservation['status'], 'status', self::STATUSES);
        }

        if (!empty($reservation['table_id'])) {
            $this->check_table($reservation, $id);
        }
    }

    private function check_table($reservation, $id)
    {
        Validator::integer($reservation['table_id'], 'table_id', 1);

        $table = $this->table_dao->get_by_id($reservation['table_id']);
        if ($table === null) {
            throw new ValidationException("The selected table does not exist.");
        }
        if (!$table['is_active']) {
            throw new ValidationException("Table {$table['table_number']} is not in use.");
        }
        if ($table['capacity'] < $reservation['guests']) {
            throw new ValidationException(
                "Table {$table['table_number']} seats only {$table['capacity']} guests."
            );
        }

        $conflicts = $this->dao->get_conflicts(
            $reservation['table_id'],
            $reservation['reservation_date'],
            $reservation['reservation_time'],
            $id ?? 0
        );
        if (count($conflicts) > 0) {
            throw new ValidationException(
                "Table {$table['table_number']} is already booked within 2 hours of that time."
            );
        }
    }

    private function normalize($reservation)
    {
        if (!empty($reservation['reservation_time']) && strlen($reservation['reservation_time']) === 5) {
            $reservation['reservation_time'] .= ':00';
        }
        return $this->empty_to_null($reservation, ['special_requests', 'table_id']);
    }
}
