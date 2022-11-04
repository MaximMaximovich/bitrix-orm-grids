<?php

use Bitrix\Main\Loader;
use Bitrix\Main\ORM;


/**
 * Класс для отображения списков
 *
 * Class CustomersDataComponent
 *
 */
class CustomersDataComponent extends CBitrixComponent
{
    /** @var string/null $templateName Имя шаблона компонента */
    private $templateName;
    /** @var string $listId Имя отображаемого списка */
    private string $listId;
    /** @var string $ormClassName Имя класса ORM */
    private string $ormClassName;
    /** @var array $columnFields Набор полей колонок грида */
    private array $columnFields;

    /**
     * Метод executeComponent
     *
     * @return mixed|void|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws Exception
     */
    public function executeComponent()
    {

        $this->templateName = $this->GetTemplateName();

        if (Loader::IncludeModule('mylab.customer')) {

            $this->arResult['IS_MODULE_LOAD'] = true;

            if (is_string($this->arParams['LIST_ID'])) {
                $this->listId = $this->arParams['LIST_ID'];
            }
            if (is_string($this->arParams['ORM_NAME'])) {
                $this->ormClassName = $this->arParams['ORM_NAME'];
            }
            if (is_array($this->arParams['COLUMN_FIELDS'])) {
                $this->columnFields = $this->arParams['COLUMN_FIELDS'];
            }


            if ($this->templateName == '' || $this->templateName == '.default') {

                if (!empty($this->columnFields) && !empty($this->ormClassName)) {

                    $this->arResult['GRID'] = $this->getGridData();
                }
            }
        }

        $this->includeComponentTemplate();

    }


    /**
     * Массив для дефолтного шаблона
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getGridData(): array
    {

        $arr['GRID'] = [];

        $arr['GRID']['ITEMS'] = $this->getAllElements();
        $arr['GRID']['GRID_HEAD'] = $this->getGridHead();
        if (!empty($this->listId)) {
            $arr['GRID']['TABLE_NAME'] = $this->listId;
        }

        return $arr['GRID'];
    }

    /**
     * Получение элементов через ORM для шаблона .default
     *
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getAllElements(): array
    {
        $result = [];

        $columnFields = [];

        if (!empty($this->columnFields)) {

            foreach ($this->columnFields as $columnField) {
                if (!empty($columnField)) {
                    $columnFields[] = $columnField;
                }
            }

            $query = new ORM\Query\Query($this->ormClassName);

            $elements = $query
              ->setSelect($columnFields)
              ->exec();

            $result = $elements->fetchAll();

        }

        return $result;
    }

    /**
     * Возращает заголовки таблицы.
     *
     * @return array
     */
    private function getGridHead(): array
    {
        // Массив заголовков для грида
        $gridHead = [];

        if (empty($this->columnFields))
            return [];

        // Получаем имя вызываемого ORM класса
        $ormName = $this->ormClassName;

        // Читаем ORM
        $mapObjects = $ormName::getMap();

        foreach ($this->columnFields as $columnField) {
            if (strpos($columnField, '.') != null) {
                $pieces = explode(".", $columnField);
                foreach ($mapObjects as $mapObject) {
                    $arr = [];
                    $fieldTypes = explode("\\", get_class($mapObject));
                    if (($fieldTypes[4] == 'Relations') && ($mapObject->getName() == $pieces[0])) {
                        $ormRefClassName = $mapObject->getRefEntityName() . 'Table';
                        $mapRefObjects = $ormRefClassName::getMap();
                        foreach ($mapRefObjects as $mapRefObject) {
                            if ($mapRefObject->getName() == $pieces[1]) {
                                $arr['id'] = $ormRefClassName . '_' . $pieces[1] . '_ALIAS';
                                $arr['name'] = $mapRefObject->getTitle();
                                $arr['default'] = true;
                                $arr['sort'] = $ormRefClassName . '_' . $pieces[1] . '_ALIAS';
                                array_push($gridHead, $arr);
                            }
                        }
                    }
                }
            } else {
                foreach ($mapObjects as $mapObject) {
                    $arr = [];
                    $fieldTypes = explode("\\", get_class($mapObject));
                    if (($fieldTypes[4] != 'Relations') && ($mapObject->getName() == $columnField)) {
                        $arr['id'] = $mapObject->getName();
                        $arr['name'] = $mapObject->getTitle();
                        $arr['default'] = true;
                        $arr['sort'] = $mapObject->getName();
                        array_push($gridHead, $arr);
                    }
                }
            }
        }
        return $gridHead;
    }
}