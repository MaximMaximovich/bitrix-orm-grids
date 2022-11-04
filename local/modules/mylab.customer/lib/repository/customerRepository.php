<?php

namespace Mylab\Customer\Repository;

use Bitrix\Main\Application;
use Mylab\Customer\Orm\CustomerTable;
use Bitrix\Main\ORM;


/**
 *  Класс для работы с CustomerTable
 *
 * Class CustomerRepository
 * @package Ylab\Meetings\Repository
 */
class CustomerRepository extends BaseRepository
{
    /**
     *  Получение элемента по ID и параметру 'select'
     *
     * @param $id
     * @param $select
     * @return ORM\Objectify\EntityObject|mixed|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function fetchOne($id, $select)
    {
        return CustomerTable::getByPrimary($id, array('select' => $select))->fetch();
    }

    /**
     * Медод возвращает массив с выборкой по передаваемым параметрам
     *
     * @param $filter
     * @param $select
     * @param $order
     * @param $offset
     * @param $limit
     * @return array|mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function fetchAll($filter, $select, $order, $offset, $limit)
    {
        return CustomerTable::GetList([
          'filter' => $filter,
          "count_total" => true,
          'select' => $select,
          'order' => $order,
          "offset" => $offset,
          "limit" => $limit,
          'cache' => array(
            'ttl' => 3600,
            'cache_joins' => true,
          )
        ])->fetchAll();
    }

    /**
     * Метод возвращает количество записей для конкретного запроса
     *
     * @param $filter
     * @return int
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getCount($filter): int
    {
        return CustomerTable::getCount($filter);
    }

    /**
     * Добавление customer
     *
     * @param $filter
     * @param $fields
     * @return ORM\Data\AddResult|mixed
     * @throws \Exception
     */
    public function add($filter, $fields)
    {
        return CustomerTable::add($fields);
    }

    /**
     * Редактирование customer
     *
     * @param $id
     * @param $fields
     * @return ORM\Data\UpdateResult|mixed
     * @throws \Exception
     */
    public function update($id, $fields)
    {
        $res = CustomerTable::update($id, $fields);
        $this->clearCache();
        return $res;
    }

    /**
     * Удаление customer
     *
     * @param $id
     * @return ORM\Data\DeleteResult|ORM\Data\UpdateResult|mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        /** @var \Bitrix\Main\ORM\Data\UpdateResult $result */
        if (is_array($id)) {
            foreach ($id as $item)
                $result = CustomerTable::delete($item);
        } else {
            $result = CustomerTable::delete($id);
        }

        $this->clearCache();

        return $result;
    }

    /**
     * Очистка кэша
     *
     * @return mixed|void
     */
    public function clearCache()
    {
        $tableName = "orm_". CustomerTable::getTableName();
        $managedcache = Application::getInstance()->getManagedCache();
        $managedcache->cleanDir($tableName);
    }
}