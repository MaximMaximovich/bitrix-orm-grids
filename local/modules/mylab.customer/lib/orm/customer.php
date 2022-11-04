<?php

namespace Mylab\Customer\Orm;

use Bitrix\Main\Entity;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;
use Bitrix\Main\Localization\Loc;

/**
 * Class for ORM Entity Customer
 * @package    mylab
 * @subpackage customer
 */
class CustomerTable extends Entity\DataManager
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'y_customers';
    }

    /**
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap(): array
    {
        return [
          new Entity\IntegerField('ID', [
            'primary' => true,
            'autocomplete' => true,
            'title' => Loc::getMessage('CUSTOMER_ENTITY_ID_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\RegExp('/[0-9]+/')
                ];
            },
          ]),
            // First Name - optional, max 50 char
          new Entity\StringField('FIRST_NAME', [
            'required' => false,
            'title' => Loc::getMessage('CUSTOMER_ENTITY_FIRST_NAME_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\Length(3, 50),
                  new Entity\Validator\RegExp('/^[a-zA-Z\p{Cyrillic}][a-zA-Z\p{Cyrillic}0-9\s\-]/u'),
                ];
            },
          ]),
            // Last Name - required, max 50 char
          new Entity\StringField('LAST_NAME', [
            'required' => true,
            'title' => Loc::getMessage('CUSTOMER_ENTITY_LAST_NAME_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\Length(3, 50),
                  new Entity\Validator\RegExp('/^[a-zA-Z\p{Cyrillic}][a-zA-Z\p{Cyrillic}0-9\s\-]/u'),
                ];
            },
          ]),
            // Phone - optional, E.164 format
          new Entity\StringField('PHONE', [
            'required' => false,
            'title' => Loc::getMessage('CUSTOMER_ENTITY_PHONE_NUMBER_FIELD'),
            'validation' => function () {
                return [
                    //Регулярное выражение для проверки формата E.164
                  new Entity\Validator\RegExp('/^\+?[1-9]\d{1,14}$'),
                ];
            },
          ]),
            // Email - required, unique
          new Entity\StringField('EMAIL', [
            'required' => true,
            'title' => Loc::getMessage('CUSTOMER_ENTITY_EMAIL_FIELD'),
            'validation' => function () {
                return [
                  new Entity\Validator\Unique(),
                    //Регулярное выражение для проверки email
                  new Entity\Validator\RegExp('/^[^\s@]+@[^\s@]+\.[^\s@]+$'),
                ];
            },
          ]),
            //Обратный референс (для реализации двунаправленности) (отношение "1 customer - N addresses")
          (new OneToMany('ADDRESSES', AddressTable::class, 'CUSTOMER'))
            ->configureJoinType('inner'),
        ];
    }
}
