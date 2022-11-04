<?php

namespace Mylab\Customer\Orm;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;

/**
 * Class for ORM Entity Address
 * @package    ylab
 * @subpackage meetings
 */
class AddressTable extends Entity\DataManager
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'y_addresses';
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap(): array
    {
        return [
            //ID
          new Entity\IntegerField('ID', [
            'primary' => true,
            'autocomplete' => true,
            'title' => Loc::getMessage('ADDRESS_ENTITY_ID_FIELD'),
            'validation' => function () {
                return [
                    //Регулярное выражение для проверки ID - только цифры
                  new Entity\Validator\RegExp('/[0-9]+/'),
                ];
            },
          ]),
            // Customer ID
          new Entity\IntegerField('CUSTOMER_ID', [
            'required' => true,
            Loc::getMessage('ADDRESS_ENTITY_CUSTOMER_ID_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\RegExp('/[0-9]+/')
                ];
            },
          ]),
            //Адрес
          new Entity\StringField('ADDRESS_LINE', [
            'required' => true,
            'title' => Loc::getMessage('ADDRESS_ENTITY_LINE_FIELD'),
            'validation' => function () {
                return [
                    //Проверка на минимальную и максимальную длину строки
                  new Entity\Validator\Length(3, 100),
                ];
            },
          ]),
            // Тип адреса
          new Entity\StringField('ADDRESS_TYPE', [
            'required' => true,
            'title' => Loc::getMessage('ADDRESS_ENTITY_TYPE_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\RegExp('/^(Shipping|Billing)$'),
                ];
            },
          ]),
            // Город
          new Entity\StringField('CITY', [
            'required' => true,
            'title' => Loc::getMessage('ADDRESS_ENTITY_CITY_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\Length(3, 50),
                ];
            },
          ]),
            // Индекс
          new Entity\StringField('POSTAL_CODE', [
            'required' => true,
            'title' => Loc::getMessage('ADDRESS_ENTITY_POSTAL_CODE_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\Length(6, 6),
                  new Entity\Validator\RegExp('/[0-9]+/'),
                ];
            },
          ]),
            // Область
          new Entity\StringField('REGION', [
            'required' => true,
            'title' => Loc::getMessage('ADDRESS_ENTITY_REGION_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\Length(3, 20),
                ];
            },
          ]),
            // Страна
          new Entity\StringField('COUNTRY', [
            'required' => true,
            'title' => Loc::getMessage('ADDRESS_ENTITY_COUNTRY_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\RegExp('/^(Russia|Belarus)$'),
                ];
            },
          ]),
            //JOIN на customer (отношение "1 customer - N addresses")
          (new Reference(
            'CUSTOMER',
            CustomerTable::class,
            Join::on('this.CUSTOMER_ID', 'ref.ID')
          ))
            ->configureJoinType('inner')
        ];
    }
}
