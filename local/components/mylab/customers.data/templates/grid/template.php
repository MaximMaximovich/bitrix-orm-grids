<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>

<?php

use Bitrix\Main\Localization\Loc;

?>

<?php if (!$arResult['IS_MODULE_LOAD']) : ?>

    <?= Loc::getMessage('MYLAB_CUSTOMERS_DATA_TABLE_DEFAULT_TEMPLATE_ERROR1') ?>
    <?= '<br>' ?>

<?php else: ?>

    <?php if (empty($arParams['ORM_NAME'])) : ?>
        <?= Loc::getMessage('MYLAB_CUSTOMERS_DATA_TABLE_DEFAULT_TEMPLATE_ERROR2') ?>
        <?= '<br>' ?>
    <?php endif; ?>
    <?php if (!is_array($arParams['COLUMN_FIELDS'])) : ?>
        <?= Loc::getMessage('MYLAB_CUSTOMERS_DATA_TABLE_DEFAULT_TEMPLATE_ERROR3') ?>
        <?= '<br>' ?>
    <?php endif; ?>
    <?php if (empty($arParams['COLUMN_FIELDS'])) : ?>
        <?= Loc::getMessage('MYLAB_CUSTOMERS_DATA_TABLE_DEFAULT_TEMPLATE_ERROR4') ?>
        <?= '<br>' ?>
    <?php endif; ?>
    <?php if (empty($arParams['LIST_ID'])) : ?>
        <?= Loc::getMessage('MYLAB_CUSTOMERS_DATA_TABLE_GRID_TEMPLATE_ERROR5') ?>
        <?= '<br>' ?>
    <?php endif; ?>

    <?php if (!empty($arParams['ORM_NAME']) && is_array($arParams['COLUMN_FIELDS'])
      && !empty($arParams['LIST_ID']) && !empty($arParams['COLUMN_FIELDS'])) : ?>

        <div style="clear: both;"></div>
        <?= $arResult['ADD_SUCCESS_NAME'] ? Loc::getMessage('MYLAB_CUSTOMERS_DATA_ADD_SUCCESS_PT_1') .
          $arResult['ADD_SUCCESS_NAME'] . Loc::getMessage('MYLAB_CUSTOMERS_DATA_ADD_SUCCESS_PT_2')
          : null ?>

        <div class="transport-action-bar">
            <form action="<?= $arResult['AJAX_PATH'] ?>" method="POST">
                <input type="hidden" id="addButton" name="action" value="add_item">
                <?= bitrix_sessid_post() ?>
                <?= $arResult['BUTTONS']['ADD'] ?>
            </form>
        </div>

        <div class="">

            <?php if (!empty($arParams['FILTER_FIELDS'])) : ?>

                <?php $APPLICATION->includeComponent(
                  'bitrix:main.ui.filter',
                  '',
                  [
                    'FILTER_ID' => $arResult['GRID_ID'],
                    'GRID_ID' => $arResult['GRID_ID'],
                    'FILTER' => $arResult['GRID_FILTER'],
                    'VALUE_REQUIRED_MODE' => true,
                    'ENABLE_LIVE_SEARCH' => true,
                    'ENABLE_LABEL' => true
                  ],
                  $component
                );

                ?>
            <?php endif; ?>

            <?php
            $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
              'GRID_ID' => $arResult['GRID_ID'],
              'COLUMNS' => $arResult['GRID_HEAD'],
              'ROWS' => $arResult['GRID_BODY'],
              'SHOW_ROW_CHECKBOXES' => false,
              'NAV_OBJECT' => $arResult['GRID_NAV'],
              'AJAX_MODE' => 'Y',
              'AJAX_ID' => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
              'PAGE_SIZES' => [
                ['NAME' => "5", 'VALUE' => '5'],
                ['NAME' => '10', 'VALUE' => '10'],
                ['NAME' => '20', 'VALUE' => '20'],
                ['NAME' => '50', 'VALUE' => '50'],
                ['NAME' => '100', 'VALUE' => '100']
              ],
              'AJAX_OPTION_JUMP' => 'N',
              "AJAX_OPTION_STYLE" => "Y",
              'SHOW_CHECK_ALL_CHECKBOXES' => false,
              'SHOW_ROW_ACTIONS_MENU' => true,
              'SHOW_GRID_SETTINGS_MENU' => true,
              'SHOW_NAVIGATION_PANEL' => true,
              'SHOW_PAGINATION' => true,
              'SHOW_SELECTED_COUNTER' => true,
              'SHOW_TOTAL_COUNTER' => true,
              'SHOW_PAGESIZE' => true,
              'SHOW_ACTION_PANEL' => true,
              'ALLOW_COLUMNS_SORT' => true,
              'ALLOW_COLUMNS_RESIZE' => true,
              'ALLOW_HORIZONTAL_SCROLL' => true,
              'ALLOW_SORT' => true,
              'ALLOW_PIN_HEADER' => true,
              'AJAX_OPTION_HISTORY' => 'N',
              "AJAX_OPTION_ADDITIONAL" => $arResult['GRID_ID'],
              'TOTAL_ROWS_COUNT' => $arResult['RECORD_COUNT'],
            ],
              $component
            );
            ?>

        </div>

    <?php endif; ?>

<?php endif; ?>






