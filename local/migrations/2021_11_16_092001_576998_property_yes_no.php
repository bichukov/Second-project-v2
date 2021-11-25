<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

global $USER_FIELD_MANAGER;
class PropertyYESNO20211116092001576998 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function up()
    {
        global $USER_FIELD_MANAGER;//$iblockId = $this->getIblockIdByCode('clothes');



        $oUserTypeEntity    = new CUserTypeEntity();

        $aUserFields    = array(

            'ENTITY_ID'         => 'ASD_IBLOCK',
            'FIELD_NAME'        => 'UF_YES_OR_NO_FOR_EXPORT_VK',
            'USER_TYPE_ID'      => 'boolean',
            'XML_ID'            => 'XML_YES_OR_NO_FOR_EXPORT_VK',
            'SORT'              => 500,
            'MULTIPLE'          => 'N',
            'MANDATORY'         => 'N',
            'SHOW_FILTER'       => 'N',
            'SHOW_IN_LIST'      => '',
            'EDIT_IN_LIST'      => '',
            'IS_SEARCHABLE'     => 'N',
            'EDIT_FORM_LABEL'   => array(
                'ru'    => 'Экспорт в VK',
                'en'    => '',
            ),
            'LIST_COLUMN_LABEL' => array(
                'ru'    => 'Экспорт в VK',
                'en'    => '',
            ),
            'LIST_FILTER_LABEL' => array(
                'ru'    => 'Экспорт в VK',
                'en'    => '',
            ),
            'ERROR_MESSAGE'     => array(
                'ru'    => 'Ошибка при заполнении пользовательского свойства',
                'en'    => 'An error in completing the user field',
            ),
            'HELP_MESSAGE'      => array(
                'ru'    => 'Экспорт в VK',
                'en'    => '',
            ),
        );

        $oUserTypeEntity->Add( $aUserFields ); // int
        if (Cmodule::IncludeModule('asd.iblock')) {
            $arFields = CASDiblockTools::GetIBUF(2);
        }

        $aSection   = CIBlock::GetList(  array("SORT"=>"ASC"),
            array(
                'CODE'          => 'clothes'
            ) )->Fetch();

        if( !$aSection ) {
            throw new Exception( 'Секция не найдена' );
        }



        $USER_FIELD_MANAGER->Update( 'ASD_IBLOCK', $aSection['ID'], array(
            'UF_YES_OR_NO_FOR_EXPORT_VK'  => 1
        ) );

    }

    public function down()
    {

        $oUserTypeEntity = new CUserTypeEntity();

        $result = CUserTypeEntity::GetList(
            array(
                "SORT"=>"ASC"
            ),
            array(
                "FIELD_NAME" => 'UF_YES_OR_NO_FOR_EXPORT_VK',
            ),
        );

        if($value = $result->GetNext())
            $aUserFields = $value['ID'];

        $oUserTypeEntity->Delete($aUserFields);




    }
}