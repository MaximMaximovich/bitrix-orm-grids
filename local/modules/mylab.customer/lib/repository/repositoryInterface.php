<?php

namespace Mylab\Customer\Repository;


/**
 * Интерфейс для доступа к ОРМ
 *
 * Interface RepositoryInterface
 * @package Mylab\Customer\Repository
 */
interface RepositoryInterface
{

    /**
     * Выбрать один элемент
     *
     * @param $id
     * @param $select
     * @return mixed
     */
    public function fetchOne($id, $select);

    /**
     * Выбрать всё
     *
     * @param $filter
     * @param $select
     * @param $order
     * @param $offset
     * @param $limit
     * @return mixed
     */
    public function fetchAll($filter, $select, $order, $offset, $limit);

    /**
     * Возвращает к-во элементов в зависимости от $filter
     *
     * @param $filter
     * @return mixed
     */
    public function getCount($filter);


    /**
     * Добавление элемента
     *
     * @param $filter
     * @param $fields
     * @return mixed
     */
    public function add($filter, $fields);

    /**
     * Редактирование элемента
     *
     * @param $id
     * @param $fields
     * @return mixed
     */
    public function update($id, $fields);

    /**
     * Удаление элемента
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);


    /**
     * Очистка кэша
     *
     * @return mixed
     */
    public function clearCache();
}