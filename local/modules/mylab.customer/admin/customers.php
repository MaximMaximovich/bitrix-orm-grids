<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin.php';

global $APPLICATION;
$APPLICATION->IncludeComponent(
  "mylab:customers.data",
  "grid",
  array(
    "COLUMN_FIELDS" => array(
      "ID",
      "LAST_NAME",
      "FIRST_NAME",
      "PHONE",
      "EMAIL",
      "ADDRESSES.CITY",
      "ADDRESSES.COUNTRY",
    ),
    "FILTER_FIELDS" => array(
      "ID",
      "LAST_NAME",
      "FIRST_NAME",
      "PHONE",
      "EMAIL",
      "ADDRESSES.CITY",
      "ADDRESSES.COUNTRY",
    ),
    "LIST_ID" => "customers_list",
    "ORM_NAME" => "Mylab\Customer\Orm\CustomerTable",
    "REPOSITORY_NAME" => "Mylab\Customer\Repository\CustomerRepository"
  ),
  false
);