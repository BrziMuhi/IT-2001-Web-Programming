<?php
require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao
{
    private const SELECT = "SELECT id, first_name, last_name, email, phone, role, created_at FROM users";

    public function __construct()
    {
        parent::__construct('users', ['first_name', 'last_name', 'email', 'password_hash', 'phone', 'role']);
    }

    public function get_all()
    {
        return $this->query(self::SELECT . " ORDER BY last_name, first_name");
    }

    public function get_by_id($id)
    {
        return $this->query_unique(self::SELECT . " WHERE id = :id", ['id' => $id]);
    }

    public function get_by_email($email)
    {
        return $this->query_unique(self::SELECT . " WHERE email = :email", ['email' => $email]);
    }

    public function get_by_email_with_password($email)
    {
        return $this->query_unique("SELECT * FROM users WHERE email = :email", ['email' => $email]);
    }
}
