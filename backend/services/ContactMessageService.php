<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/ContactMessageDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class ContactMessageService extends BaseService
{
    private $user_dao;

    public function __construct()
    {
        parent::__construct(new ContactMessageDao(), 'Message');
        $this->user_dao = new UserDao();
    }

    public function add($message)
    {
        return parent::add($this->normalize($message));
    }

    public function update($message, $id)
    {
        return parent::update($this->normalize($message), $id);
    }

    public function get_by_user($user_id)
    {
        Validator::integer($user_id, 'user_id', 1);
        if ($this->user_dao->get_by_id($user_id) === null) {
            throw new NotFoundException("User not found.");
        }
        return $this->dao->get_by_user($user_id);
    }

    protected function validate($message, $id = null)
    {
        Validator::required($message, ['name', 'email', 'subject', 'message']);

        Validator::length($message['name'], 'name', 2, 100);
        Validator::email($message['email']);
        Validator::length($message['subject'], 'subject', 3, 150);
        Validator::length($message['message'], 'message', 10, 2000);

        foreach (['name', 'subject', 'message'] as $field) {
            Validator::no_html($message[$field], $field);
        }

        if (!empty($message['user_id'])) {
            Validator::integer($message['user_id'], 'user_id', 1);
            if ($this->user_dao->get_by_id($message['user_id']) === null) {
                throw new ValidationException("The selected user does not exist.");
            }
        }
    }

    private function normalize($message)
    {
        foreach (['name', 'subject', 'message'] as $field) {
            if (isset($message[$field])) {
                $message[$field] = trim($message[$field]);
            }
        }
        if (isset($message['email'])) {
            $message['email'] = strtolower(trim($message['email']));
        }
        return $this->empty_to_null($message, ['user_id']);
    }
}
