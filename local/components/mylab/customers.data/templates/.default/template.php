<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<?php
use Bitrix\Main\Localization\Loc;
?>

<? if (!$arResult['IS_MODULE_LOAD']) : ?>

    <?= Loc::getMessage('YLAB_MEETING_LIST_TABLE_DEFAULT_TEMPLATE_ERROR1') ?>
    <?= '<br>' ?>

<? else: ?>

    <? if (empty($arParams['ORM_NAME'])) : ?>
        <?= Loc::getMessage('YLAB_MEETING_LIST_TABLE_DEFAULT_TEMPLATE_ERROR2') ?>
        <?= '<br>' ?>
    <? endif; ?>
    <? if (!is_array($arParams['COLUMN_FIELDS'])) : ?>
        <?= Loc::getMessage('YLAB_MEETING_LIST_TABLE_DEFAULT_TEMPLATE_ERROR3') ?>
        <?= '<br>' ?>
    <? endif; ?>
    <? if (empty($arParams['COLUMN_FIELDS'])) : ?>
        <?= Loc::getMessage('YLAB_MEETING_LIST_TABLE_DEFAULT_TEMPLATE_ERROR4') ?>
        <?= '<br>' ?>
    <? endif; ?>

    <? if (!empty($arParams['ORM_NAME']) && is_array($arParams['COLUMN_FIELDS'])
      && !empty($arParams['COLUMN_FIELDS'])) : ?>
        <div class="">

            <h3><?= Loc::getMessage('YLAB_MEETING_LIST_TABLE_DEFAULT_TEMPLATE_PREFIX') ?> <?= $arResult['GRID']['TABLE_NAME'] ?></h3>
            <p></p>

            <table border="1" width="100%" cellpadding="5">

                <tr>
                    <? foreach ($arResult['GRID']['GRID_HEAD'] as $arItem) : ?>
                        <th><?= $arItem['name'] ?></th>
                    <? endforeach; ?>
                </tr>

                <? foreach ($arResult['GRID']['ITEMS'] as $arItem) : ?>
                    <tr>
                        <? foreach ($arItem as $key => $value) : ?>
                            <?= '<td>' ?><?= $value ?><?= '</td>' ?>
                        <? endforeach ?>
                    </tr>
                <? endforeach; ?>
            </table>

        </div>
    <? endif; ?>

<? endif; ?>

