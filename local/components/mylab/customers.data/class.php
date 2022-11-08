<?php

use Bitrix\Main\Loader;
use Bitrix\Main\ORM;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;


/**
 * Класс для отображения списков customer
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
    /** @var string $repositoryName Имя класса репозитория */
    private string $repositoryName;
    /** @var array $columnFields Набор полей колонок грида */
    private array $columnFields;
    /** @var ?PageNavigation $gridNav Параметры навигации грида */
    private ?PageNavigation $gridNav = null;
    /** @var array $filterFields Набор полей доступных для фильтрации */
    private array $filterFields;

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
            if (is_string($this->arParams['REPOSITORY_NAME'])) {
                $this->repositoryName = $this->arParams['REPOSITORY_NAME'];
            }
            if (is_array($this->arParams['COLUMN_FIELDS'])) {
                $this->columnFields = $this->arParams['COLUMN_FIELDS'];
            }
            if (is_array($this->arParams['FILTER_FIELDS'])) {
                $this->filterFields = $this->arParams['FILTER_FIELDS'];
            }

            if (!empty($this->columnFields) && !empty($this->ormClassName)) {

                if ($this->templateName == 'grid') {

                    $this->showByGrid();

                } else if ($this->templateName == '' || $this->templateName == '.default') {

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
                                $arr['id'] = $pieces[0] . '_' . $pieces[1] . '_ALIAS';
                                $arr['name'] = $mapRefObject->getTitle();
                                $arr['type'] = $this->gridFilterDataType($mapObject->getDataType());
                                $arr['default'] = true;
                                $arr['sort'] = $pieces[0] . '_' . $pieces[1] . '_ALIAS';
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
                        $arr['type'] = $this->gridFilterDataType($mapObject->getDataType());
                        $arr['default'] = true;
                        $arr['sort'] = $mapObject->getName();
                        array_push($gridHead, $arr);
                    }
                }
            }
        }
        return $gridHead;
    }

    /**
     * Возвращает тип поля для фильтрации.
     *
     * @param string $dataType
     * @return string
     */
    private
    function gridFilterDataType(string $dataType): string
    {

        if ($dataType == 'integer' || $dataType == 'float') {
            $gridFilterDataType = 'number';
        } else if ($dataType == 'data' || $dataType == 'datetime') {
            $gridFilterDataType = 'data';
        } else {
            $gridFilterDataType = 'string';
        }

        return $gridFilterDataType;
    }

    /**
     * Отображение через грид
     */
    public function showByGrid(): void
    {
        $this->arResult['GRID_ID'] = $this->getGridId();
        $this->arResult['GRID_BODY'] = $this->getGridBody();
        $this->arResult['GRID_HEAD'] = $this->getGridHead();
        $this->arResult['GRID_NAV'] = $this->getGridNav();
        $this->arResult['RECORD_COUNT'] = $this->getGridNav()->getRecordCount();
        $this->arResult['GRID_FILTER'] = $this->getGridFilterParams();
    }

    /**
     * Возвращает идентификатор грида.
     *
     * @return string
     */
    private function getGridId(): string
    {
        $listId = "template_id";

        if (!empty($this->listId)) {
            $listId = $this->listId;
        }

        return 'mylab_customers_' . $listId;
    }

    /**
     * Возвращает содержимое (тело) таблицы.
     *
     * @return array
     */
    private function getGridBody(): array
    {
        $arBody = [];

        $arItems = $this->getElements();

        foreach ($arItems as $arItem) {
            $arGridElement = [];

            foreach ($this->columnFields as $columnField) {
                if (strpos($columnField, '.') != null) {
                    $pieces = explode(".", $columnField);
                    $aliasName = $pieces[0] . '_' . $pieces[1] . '_ALIAS';
                    $arGridElement['data'][$aliasName] = $arItem[$aliasName];

                } else {
                    $arGridElement['data'][$columnField] = $arItem[$columnField];
                }
            }

            $arBody[] = $arGridElement;
        }

        return $arBody;
    }


    /**
     * Получение элементов через ORM для шаблона grid
     *
     * @return array
     */
    public function getElements(): array
    {
        $result = [];

        if (empty($this->columnFields))
            return [];

        $columnFields = [];

        foreach ($this->columnFields as $columnField) {
            if (strpos($columnField, '.') != null) {
                $pieces = explode(".", $columnField);
                $aliasName = $pieces[0] . '_' . $pieces[1] . '_ALIAS';
                $columnFields[$aliasName] = $columnField;
            } else {
                $columnFields[] = $columnField;
            }
        }

        $repository = new $this->repositoryName();

        $arCurSort = $this->getObGridParams()->getSorting(['sort' => ['ID' => 'DESC']])['sort'];
        $arFilter = $this->getGridFilterValues();

        $result = $repository->fetchAll(
          $arFilter,
          $columnFields,
          $arCurSort,
          $this->getGridNav()->getOffset(),
          $this->getGridNav()->getLimit()
        );

        $this->getGridNav()->setRecordCount($repository->getCount($arFilter));

        return $result;
    }

    /**
     * Параметры навигации грида
     *
     * @return PageNavigation
     */
    private
    function getGridNav(): PageNavigation
    {
        if ($this->gridNav === null) {
            $this->gridNav = new PageNavigation($this->getGridId());

            $gridOptions = $this->getObGridParams();
            $navParams = $gridOptions->GetNavParams();

            $this->gridNav
              ->allowAllRecords(true)
              ->setPageSize($navParams['nPageSize'])
              ->initFromUri();
        }

        return $this->gridNav;
    }

    /**
     * Возвращает единственный экземпляр настроек грида.
     *
     * @return GridOptions
     */
    private
    function getObGridParams(): GridOptions
    {
        return $this->gridOption ?? $this->gridOption = new GridOptions($this->getGridId());

    }

    /**
     * Возвращает настройки отображения грид фильтра.
     *
     * @return array
     */
    private
    function getGridFilterParams(): array
    {

        // Массив параметров фильтра
        $getGridFilterParams = [];

        if (!empty($this->filterFields)) {

            $filterFields = [];

            foreach ($this->filterFields as $filterField) {
                if (!empty($filterField)) {
                    if (strpos($filterField, '.') != null) {
                        $pieces = explode(".", $filterField);
                        $aliasName = $pieces[0] . '_' . $pieces[1] . '_ALIAS';
                        $filterFields[] = $aliasName;
                    } else {
                        $filterFields[] = $filterField;
                    }
                }
            }

            foreach ($this->getGridHead() as $GridHeadElement) {

                $arr = [];

                if (in_array($GridHeadElement['id'], $filterFields)) {
                    $arr['id'] = $GridHeadElement['id'];
                    $arr['name'] = $GridHeadElement['name'];
                    $arr['type'] = $GridHeadElement['type'];
                } else {
                    continue;
                }

                array_push($getGridFilterParams, $arr);
            }

        }

        return $getGridFilterParams;
    }

    /**
     * Возвращает значения грид фильтра.
     *
     * @return array
     */
    public
    function getGridFilterValues(): array
    {

        $obFilterOption = new FilterOptions($this->getGridId());
        $arFilterData = $obFilterOption->getFilter();
        $baseFilter = array_intersect_key($arFilterData, array_flip($obFilterOption->getUsedFields()));
        $formatedFilter = $this->prepareFilter($arFilterData, $baseFilter);

        return array_merge(
          $baseFilter,
          $formatedFilter
        );
    }

    /**
     * Подготавливает параметры фильтра
     *
     * @param array $arFilterData
     * @param array $baseFilter
     * @return array
     */
    public
    function prepareFilter(array $arFilterData, &$baseFilter = []): array
    {
        $arFilter = [];

        foreach ($this->getGridFilterParams() as $gridFilterParam) {

            if ($gridFilterParam['type'] == 'number') {

                if (!empty($arFilterData[$gridFilterParam['id'] . '_from'])) {
                    $arFilter['>=' . $gridFilterParam['id']] = (int)$arFilterData[$gridFilterParam['id'] . '_from'];
                }
                if (!empty($arFilterData[$gridFilterParam['id'] . '_to'])) {
                    $arFilter['<=' . $gridFilterParam['id']] = (int)$arFilterData[$gridFilterParam['id'] . '_to'];
                }
            }

            if ($gridFilterParam['type'] == 'data') {

                if (!empty($arFilterData[$gridFilterParam['id'] . '_from'])) {
                    $arFilter['>=' . $gridFilterParam['id']] = date(
                      "Y-m-d H:i:s",
                      strtotime($arFilterData[$gridFilterParam['id'] . '_from']));
                }
                if (!empty($arFilterData[$gridFilterParam['id'] . '_to'])) {
                    $arFilter['<=' . $gridFilterParam['id']] = date(
                      "Y-m-d H:i:s",
                      strtotime($arFilterData[$gridFilterParam['id'] . '_to']));
                }

            }

        }

        return $arFilter;
    }
}
