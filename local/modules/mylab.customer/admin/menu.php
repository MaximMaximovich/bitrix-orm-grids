<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

AddEventHandler('main', 'OnBuildGlobalMenu', 'MylabCustomersModuleMenu');

if (!function_exists('MylabCustomersModuleMenu')) {
    /**
     * Отображение меню
     * @param $adminMenu
     * @param $moduleMenu
     */
    function MylabCustomersModuleMenu(&$adminMenu, &$moduleMenu)
    {
        $adminMenu['global_menu_services']['items'][] = [
            'section' => 'mylab-customer-pages',
            'sort' => 110,
            'text' => Loc::getMessage('MYLAB_CUSTOMER_TITLE_PAGE'),
            'items_id' => 'nlmk-hidden-pages',
            'items' => [
                [
                    'parent_menu' => 'mylab-customer-pages',
                    'section' => 'mylab-customer-pages-customers',
                    'sort' => 500,
                    'url' => 'mylab.customer_customers.php?lang=' . LANG,
                    'text' => Loc::getMessage('MYLAB_CUSTOMERS_PAGE'),
                    'title' => Loc::getMessage('MYLAB_CUSTOMERS_PAGE'),
                    'icon' => 'form_menu_icon',
                    'page_icon' => 'form_page_icon',
                    'items_id' => 'mylab-customer-pages-customers'
                ]
            ]
        ];
    }

}
