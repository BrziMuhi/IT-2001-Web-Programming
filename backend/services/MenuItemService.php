<?php
require_once __DIR__ . '/BaseService.php';
require_once __DIR__ . '/../dao/MenuItemDao.php';
require_once __DIR__ . '/../dao/CategoryDao.php';

class MenuItemService extends BaseService
{
    const MAX_PRICE = 1000;

    private $category_dao;

    public function __construct()
    {
        parent::__construct(new MenuItemDao(), 'Menu item');
        $this->category_dao = new CategoryDao();
    }

    public function add($item)
    {
        $item = $this->normalize($item);
        $item['is_available'] = $item['is_available'] ?? 1;
        return parent::add($item);
    }

    public function update($item, $id)
    {
        return parent::update($this->normalize($item), $id);
    }

    protected function validate($item, $id = null)
    {
        Validator::required($item, ['category_id', 'name', 'price']);

        Validator::integer($item['category_id'], 'category_id', 1);
        if ($this->category_dao->get_by_id($item['category_id']) === null) {
            throw new ValidationException("The selected category does not exist.");
        }

        Validator::length($item['name'], 'name', 2, 100);
        Validator::no_html($item['name'], 'name');

        Validator::number($item['price'], 'price', 0.01, self::MAX_PRICE);

        if (!empty($item['description'])) {
            Validator::length($item['description'], 'description', 0, 1000);
            Validator::no_html($item['description'], 'description');
        }
        if (!empty($item['image_url'])) {
            Validator::length($item['image_url'], 'image_url', 0, 255);
            Validator::no_html($item['image_url'], 'image_url');
        }
    }

    public function get_by_category($category_id)
    {
        Validator::integer($category_id, 'category_id', 1);
        if ($this->category_dao->get_by_id($category_id) === null) {
            throw new NotFoundException("Category not found.");
        }
        return $this->dao->get_by_category($category_id);
    }

    public function get_available()
    {
        return $this->dao->get_available();
    }

    private function normalize($item)
    {
        if (isset($item['name'])) {
            $item['name'] = trim($item['name']);
        }

        if (array_key_exists('is_available', $item)) {
            $item['is_available'] = in_array($item['is_available'], ['on', 'true', '1', 1, true], true) ? 1 : 0;
        }
        return $this->empty_to_null($item, ['description', 'image_url']);
    }
}
