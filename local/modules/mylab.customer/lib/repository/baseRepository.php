<?php

namespace Mylab\Customer\Repository;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\Application;


/**
 *  Абстрактный класс для работы с ОРМ
 *
 * Class BaseRepository
 * @package Mylab\Customer\Repository
 */
abstract class BaseRepository implements RepositoryInterface
{

    /**
     * Получение элемента по ID и параметру 'select'
     *
     * @param $id
     * @param $select
     * @return \Bitrix\Main\ORM\Objectify\EntityObject|mixed|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function fetchOne($id, $select)
    {
        return DataManager::getByPrimary($id, array('select' => $select))->fetchObject();
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
        return DataManager::GetList([
          'filter' => $filter,
          "count_total" => true,
          'select' => $select,
          'order' => $order,
          "offset" => $offset,
          "limit" => $limit,
          'cache' => array(
            'ttl' => 3600,
            'cache_joins' => false,
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
        return DataManager::getCount($filter);
    }


    /**
     * Добавление элемента
     *
     * @param $filter
     * @param $fields
     * @return \Bitrix\Main\ORM\Data\AddResult|mixed
     * @throws \Exception
     */
    public function add($filter, $fields)
    {
        return DataManager::add($fields);
    }


    /**
     * Редактирование элемента
     *
     * @param $id
     * @param $fields
     * @return \Bitrix\Main\ORM\Data\UpdateResult|mixed
     * @throws \Exception
     */
    public function update($id, $fields)
    {
        $res = DataManager::update($id, $fields);
        $this->clearCache();
        return $res;
    }


    /**
     * Удаление элемента
     *
     * @param $id
     * @return \Bitrix\Main\ORM\Data\DeleteResult|\Bitrix\Main\ORM\Data\UpdateResult|mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        /** @var \Bitrix\Main\ORM\Data\UpdateResult $result */
        if (is_array($id)) {
            foreach ($id as $item)
                $result = DataManager::delete($item);
        } else {
            $result = DataManager::delete($id);
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
        $tableName = "orm_". DataManager::getTableName();
        $managedcache = Application::getInstance()->getManagedCache();
        $managedcache->cleanDir($tableName);
    }


}