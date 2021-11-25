<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class Dostavka20211118094110158238 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function up()
    {CModule::IncludeModule("sale");
        $cntBasketItems = CSaleBasket::GetList(
    array(),
    array(
        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
        "LID" => SITE_ID,
        "ORDER_ID" => "NULL"
    ),
    array()
);
if ($cntBasketItems === 0) {
    // Если в корзине нет товаров
}

$arFields = array(
    "PERSON_TYPE_ID" => 1,
    "NAME" => "Врем",
    "TYPE" => "STRING",
    "MAXLENGTH"=>10,
    //"SHOW_TIME" => "Y",
    "REQUIED" => "Y",
    "DEFAULT_VALUE" => "",
    "SORT" => 100,
    "CODE" => "Time",
    "USER_PROPS" => "N",
    "IS_LOCATION" => "N",
    "IS_LOCATION4TAX" => "N",
    "PROPS_GROUP_ID" => 2,
    "SIZE1" => 0,
    "SIZE2" => 0,
    "DESCRIPTION" => "",
    "IS_EMAIL" => "N",
    "IS_PROFILE_NAME" => "N",
    "RELATIONS[D][]"=>"2",
    "IS_PAYER" => "N"
);

// Если установлен код свойства, то изменяем свойство с этим кодом,
// иначе добавляем новой свойство
if ($ID>0)
{
    if (!CSaleOrderProps::Update($ID, $arFields))
    {
        echo "Ошибка изменения параметров свойства";
    }
    else
    {
        // Обновим символьный код у значений свойства
        // (хранение избыточных данных для оптимизации работы)
        $db_order_props_tmp =
            Bitrix\Sale\Internals\OrderPropsValueTable::GetList(($b="NAME"),
                ($o="ASC"),
                Array("ORDER_PROPS_ID"=>$ID));
        while ($ar_order_props_tmp = $db_order_props_tmp->Fetch())
        {
            Bitrix\Sale\Internals\OrderPropsValueTable::Update($ar_order_props_tmp["ID"],
                array("CODE" => "Time"));
        }
    }
}
else
{
    $ID = CSaleOrderProps::Add($arFields);
    if ($ID<=0)
        echo "Ошибка добавления свойства";
}


    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function down()
    {CModule::IncludeModule("sale");
        $db_props = CSaleOrderProps::GetList(
            array("SORT" => "ASC"),
            array(
                "CODE" => "Time"
            )
        );
        if ($db_props = $db_props->GetNext())
            CSaleOrderProps::Delete($db_props[ID]);
    }
}
