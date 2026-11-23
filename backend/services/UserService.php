<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../utils/Roles.php';

class UserService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new UserDao(), 'User');
    }

    public function add($user)
    {
        $user['role'] = $user['role'] ?? Roles::USER;
        $user = $this->normalize($user);
        $this->validate($user);

        Validator::required($user, ['password']);
        Validator::password($user['password']);

        $user['password_hash'] = password_hash($user['password'], PASSWORD_BCRYPT);
        unset($user['password']);

        $created = $this->dao->add($user);
        return $this->dao->get_by_id($created['id']);
    }

    public function update($user, $id)
    {
        if (!empty($user['password'])) {
            Validator::password($user['password']);
            $user['password_hash'] = password_hash($user['password'], PASSWORD_BCRYPT);
        }
        unset($user['password']);

        return parent::update($this->normalize($user), $id);
    }

    protected function validate($user, $id = null)
    {
        Validator::required($user, ['first_name', 'last_name', 'email', 'role']);

        Validator::length($user['first_name'], 'first_name', 2, 50);
        Validator::no_html($user['first_name'], 'first_name');
        Validator::length($user['last_name'], 'last_name', 2, 50);
        Validator::no_html($user['last_name'], 'last_name');

        Validator::email($user['email']);
        Validator::one_of($user['role'], 'role', [Roles::ADMIN, Roles::USER]);

        if (!empty($user['phone'])) {
            Validator::length($user['phone'], 'phone', 0, 30);
            if (!preg_match('/^[0-9 +()\-]+$/', $user['phone'])) {
                throw new ValidationException("Field 'phone' may contain only digits, spaces and + ( ) -.");
            }
        }

        $existing = $this->dao->get_by_email($user['email']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("An account with this email already exists.");
        }
    }

    private function normalize($user)
    {
        foreach (['first_name', 'last_name'] as $field) {
            if (isset($user[$field])) {
                $user[$field] = trim($user[$field]);
            }
        }

        if (isset($user['email'])) {
            $user['email'] = strtolower(trim($user['email']));
        }
        return $this->empty_to_null($user, ['phone']);
    }
}
