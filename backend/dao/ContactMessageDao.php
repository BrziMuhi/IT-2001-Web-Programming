<?php
require_once __DIR__ . '/BaseDao.php';

class ContactMessageDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('contact_messages', ['user_id', 'name', 'email', 'subject', 'message']);
    }

    public function get_all()
    {
        return $this->query("SELECT * FROM contact_messages ORDER BY sent_at DESC, id DESC");
    }

    public function get_by_user($user_id)
    {
        return $this->query(
            "SELECT * FROM contact_messages WHERE user_id = :user_id ORDER BY sent_at DESC, id DESC",
            ['user_id' => $user_id]
        );
    }
}
