<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class IncludeMod20211112162144491577 extends BitrixMigration

{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function up()
    {
        if ($module = CModule::CreateModuleObject('asd.iblock')) {
            if (!$module->IsInstalled()) {
                $module->DoInstall();
            }
        }
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function down()
    {
        if ($module = CModule::CreateModuleObject('asd.iblock')) {
            if ($module->IsInstalled()) {
                $module->DoUninstall();
            }
        }
    }

}
