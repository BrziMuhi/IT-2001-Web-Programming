<?php
require_once __DIR__ . '/../dao/BaseDao.php';
require_once __DIR__ . '/../utils/exceptions.php';
require_once __DIR__ . '/../utils/Validator.php';

class BaseService
{
    public function __construct(protected BaseDao $dao, protected string $name = 'Item') {}

    public function get_all()
    {
        return $this->dao->get_all();
    }

    public function get_by_id($id)
    {
        $entity = $this->dao->get_by_id($id);
        if ($entity === null) {
            throw new NotFoundException("{$this->name} not found.");
        }
        return $entity;
    }

    public function add($entity)
    {
        $this->validate($entity);
        $created = $this->dao->add($entity);
        return $this->dao->get_by_id($created['id']);
    }

    public function update($entity, $id)
    {
        $existing = $this->get_by_id($id);
        $entity = array_merge($existing, $entity);
        $this->validate($entity, $id);
        $this->dao->update($entity, $id);
        return $this->dao->get_by_id($id);
    }

    public function delete($id)
    {
        $this->get_by_id($id);
        $this->dao->delete($id);
    }

    protected function validate($entity, $id = null) {}

    protected function empty_to_null($entity, $fields)
    {
        foreach ($fields as $field) {
            if (isset($entity[$field]) && trim((string) $entity[$field]) === '') {
                $entity[$field] = null;
            }
        }
        return $entity;
    }
}
