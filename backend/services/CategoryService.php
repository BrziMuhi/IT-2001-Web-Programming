<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/CategoryDao.php';
require_once __DIR__ . '/../dao/MenuItemDao.php';

class CategoryService extends BaseService
{
    private $menu_item_dao;

    public function __construct()
    {
        parent::__construct(new CategoryDao(), 'Category');
        $this->menu_item_dao = new MenuItemDao();
    }

    public function add($category)
    {
        return parent::add($this->normalize($category));
    }

    public function update($category, $id)
    {
        return parent::update($this->normalize($category), $id);
    }

    protected function validate($category, $id = null)
    {
        Validator::required($category, ['name']);
        Validator::length($category['name'], 'name', 2, 50);
        Validator::no_html($category['name'], 'name');

        if (!empty($category['description'])) {
            Validator::length($category['description'], 'description', 0, 255);
            Validator::no_html($category['description'], 'description');
        }

        $existing = $this->dao->get_by_name($category['name']);
        if ($existing && $existing['id'] != $id) {
            throw new ValidationException("A category named '{$category['name']}' already exists.");
        }
    }

    public function delete($id)
    {
        $this->get_by_id($id);

        $items = $this->menu_item_dao->count_by_category($id);
        if ($items > 0) {
            throw new ValidationException("This category still contains $items menu item(s). Move or delete them first.");
        }
        $this->dao->delete($id);
    }

    private function normalize($category)
    {
        if (isset($category['name'])) {
            $category['name'] = trim($category['name']);
        }
        return $this->empty_to_null($category, ['description']);
    }
}
