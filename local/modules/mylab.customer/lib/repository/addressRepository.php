<?php

namespace Mylab\Customer\Repository;

use Bitrix\Main\Application;
use Mylab\Customer\Orm\AddressTable;
use Bitrix\Main\ORM;


/**
 *  Класс для работы с AddressTable
 *
 * Class AddressRepository
 * @package Mylab\Customer\Repository
 */
class AddressRepository extends BaseRepository
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
        return AddressTable::getByPrimary($id, array('select' => $select))->fetch();
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
        return AddressTable::GetList([
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
        return AddressTable::getCount($filter);
    }

    /**
     *  Добавление адреса
     *
     * @param $filter
     * @param $fields
     * @return ORM\Data\AddResult
     * @throws \Exception
     */
    public function add($filter, $fields): \Bitrix\Main\ORM\Data\AddResult
    {
        return AddressTable::add($fields);
    }

    /**
     * Редактирование адреса
     *
     * @param $id
     * @param $fields
     * @return ORM\Data\UpdateResult
     * @throws \Exception
     */
    public function update($id, $fields): \Bitrix\Main\ORM\Data\UpdateResult
    {
        $res = AddressTable::update($id, $fields);
        $this->clearCache();
        return $res;
    }

    /**
     * Удаление адреса
     *
     * @param $id
     * @return ORM\Data\DeleteResult|ORM\Data\UpdateResult|mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        if (is_array($id)) {
            foreach ($id as $item)
                $result = AddressTable::delete($item);
        } else {
            $result = AddressTable::delete($id);
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
        $tableName = "orm_". AddressTable::getTableName();
        $managedcache = Application::getInstance()->getManagedCache();
        $managedcache->cleanDir($tableName);
    }
}