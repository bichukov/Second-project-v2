<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AgentSendPart220211123094119215596 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function up()
    {

        CAgent::AddAgent(
            "COrderSale::Agent();",
            "sale",                          // идентификатор модуля
            "N",                                  // агент не критичен к кол-ву запусков
            86400,                               // интервал запуска - 1 сутки
            "",                // дата первой проверки на запуск
            "Y",                                  // агент активен
            date('d.m.Y')." 18:00:00",                // дата первого запуска
            30,
            '',
            '',

        );

//
//
//
//
//
//
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function down()
    {
        $result = CAgent::GetList(
            array(
                "SORT"=>"ASC"
            ),
            array(
                "NAME" => 'COrderSale::Agent();',
            ),
        );

        if($value = $result->GetNext())
            $aUserFields = $value['ID'];
        if (CAgent::Delete($aUserFields));
    }
}
