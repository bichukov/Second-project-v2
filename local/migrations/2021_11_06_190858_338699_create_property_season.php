<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Bitrix\Highloadblock as HL;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
class CreatePropertySeason20211106190858338699 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws MigrationException
     */
    public function up()
    {
        $highBlockTypeProduct = array(
            'b_hlbd_season',
            'Seasons',

            array(
                'FIELDS' => array(
                    'UF_DEF' => array('N','boolean',

                        array(
                            'EDIT_FORM_LABEL' => array(
                                'ru' => 'По умолчанию',
                            ),
                            'LIST_COLUMN_LABEL' => array(
                                'ru' => 'По умолчанию',
                            ),
                        )),
                    'UF_ACTIVE' => array('N', 'boolean', array(
                        'EDIT_FORM_LABEL' => array(
                            'ru' => 'Активность',
                        ),
                        'LIST_COLUMN_LABEL' => array(
                            'ru' => 'Активность',
                        ),
                    )),
                    'UF_NAME' => array('Y', 'string', array(
                        'EDIT_FORM_LABEL' => array(
                            'ru' => 'Название',
                        ),
                        'LIST_COLUMN_LABEL' => array(
                            'ru' => 'Название',
                        ),
                    )),
                    'UF_IMAGE' => array('N', 'file', array(
                        'SETTINGS' => array(
                            'LIST_WIDTH' => 200,
                            'LIST_HEIGHT' => 150,
                            'EXTENSIONS' => 'jpg, gif, bmp, png, jpeg',
                        ),
                        'EDIT_FORM_LABEL' => array(
                            'ru' => 'Изображение',
                        ),
                        'LIST_COLUMN_LABEL' => array(
                            'ru' => 'Изображение',
                        ),
                    )),
                    'UF_CODE' => array('Y', 'string', array(
                        'EDIT_FORM_LABEL' => array(
                            'ru' => 'Символьный код',
                        ),
                        'LIST_COLUMN_LABEL' => array(
                            'ru' => 'Символьный код',
                        ),
                    )),
                    'UF_XML_ID' => array('N', 'string', array(
                        'EDIT_FORM_LABEL' => array(
                            'ru' => 'Внешний код',
                        ),
                        'LIST_COLUMN_LABEL' => array(
                            'ru' => 'Внешний код',
                        ),
                    )),
                    'UF_SORT' => array('Y', 'integer', array(
                        'SETTINGS' => array(
                            'DEFAULT_VALUE' => 500,
                        ),
                        'EDIT_FORM_LABEL' => array(
                            'ru' => 'Сортировка',
                        ),
                        'LIST_COLUMN_LABEL' => array(
                            'ru' => 'Сортировка',
                        ),
                    )),
                    'UF_DESCRIPTION' => array('Y', 'string', array(
                        'EDIT_FORM_LABEL' => array(
                            'ru' => 'Описание',
                        ),
                        'LIST_COLUMN_LABEL' => array(
                            'ru' => 'Описание',
                        ),
                    )),
                    'UF_FULL_DESCRIPTION' => array('Y', 'string', array(
                        'EDIT_FORM_LABEL' => array(
                            'ru' => 'Описание полное',
                        ),
                        'LIST_COLUMN_LABEL' => array(
                            'ru' => 'Описание полное',
                        ),
                    )),

                ),
//                'ALTER' => array(
//                    'ALTER TABLE #TABLE_NAME# MODIFY UF_KEY VARCHAR(100);',
//                ),
//                'INDEXES' => array(
//                    array('#TABLE_NAME#', 'ix_#TABLE_NAME#_key', array('UF_KEY'))
//                )
            )
        );

        function oGetMessage($key, $fields)
        {


            return isset($messages[$key])
                ? str_replace(array_keys($fields), array_values($fields), $messages[$key])
                : '';
        }

        $info = array();

        function createHighLoadBlock($tableName, $highBlockName, array $hlData)
        {
            global $info, $APPLICATION;

            foreach (array('highloadblock') as $moduleId) {
                if (!\Bitrix\Main\Loader::includeModule($moduleId)) {
                    throw new \Bitrix\Main\SystemException(oGetMessage('ERROR_INCLUDE_HIGHLOADBLOCK_MODULE', array(
                        '#MODULE#' => $moduleId
                    )));
                }
            }

            $connection = \Bitrix\Main\Application::getConnection();

            $sqlHelper = $connection->getSqlHelper();

            $hlblock = Bitrix\Highloadblock\HighloadBlockTable::getList(array(
                    'filter' => array(
                        'TABLE_NAME' => $tableName,
                    ))
            )->fetch();

            if (!$hlblock) {

                $highBlockName = preg_replace('/([^A-Za-z0-9]+)/', '', trim($highBlockName));

                if ($highBlockName == '') {
                    throw new \Bitrix\Main\SystemException(oGetMessage('HIGHLOADBLOCK_NAME_IS_INVALID'));
                }

                $highBlockName = strtoupper(substr($highBlockName, 0, 1)) . substr($highBlockName, 1);

                $data = array(
                    'NAME' => $highBlockName,
                    'TABLE_NAME' => $tableName,
                );

                $result = Bitrix\Highloadblock\HighloadBlockTable::add($data);

                if ($result->isSuccess()) {
                    $highBlockID = $result->getId();

                    $info[] = oGetMessage('HIGHLOADBLOCK_ADDED_INFO', array(
                        '#NAME#' => $highBlockName,
                        '#ID#' => $highBlockID,
                    ));

                } else {
                    throw new \Bitrix\Main\SystemException(oGetMessage('HIGHLOADBLOCK_ADDED_INFO_ERROR', array(
                        '#NAME#' => $highBlockName,
                        '#ERROR#' => $result->getErrorMessages(),
                    )));
                }

            } else {
                $highBlockID = $hlblock['ID'];
            }

            $oUserTypeEntity = new CUserTypeEntity();

            $sort = 500;

            foreach ($hlData['FIELDS'] as $fieldName => $fieldValue) {
                $aUserField = array(
                    'ENTITY_ID' => 'HLBLOCK_' . $highBlockID,
                    'FIELD_NAME' => $fieldName,
                    'USER_TYPE_ID' => $fieldValue[1],
                    'SORT' => $sort,
                    'MULTIPLE' => 'N',
                    'MANDATORY' => $fieldValue[0],
                    'SHOW_FILTER' => 'N',
                    'SHOW_IN_LIST' => 'Y',
                    'EDIT_IN_LIST' => 'Y',
                    'IS_SEARCHABLE' => 'N',
                    'SETTINGS' => array(),
                );

                if (isset($fieldValue[2]) && is_array($fieldValue[2])) {
                    $aUserField = array_merge($aUserField, $fieldValue[2]);
                }

                $resProperty = CUserTypeEntity::GetList(
                    array(),
                    array('ENTITY_ID' => $aUserField['ENTITY_ID'], 'FIELD_NAME' => $aUserField['FIELD_NAME'])
                );

                if ($aUserHasField = $resProperty->Fetch()) {
                    $idUserTypeProp = $aUserHasField['ID'];
                    if ($oUserTypeEntity->Update($idUserTypeProp, $aUserField)) {
                        $info[] = oGetMessage('USER_TYPE_UPDATE', array(
                            '#FIELD_NAME#' => $aUserHasField['FIELD_NAME'],
                            '#ENTITY_ID#' => $aUserHasField['ENTITY_ID'],
                        ));
                    } else {
                        if (($ex = $APPLICATION->GetException())) {
                            throw new \Bitrix\Main\SystemException(oGetMessage('USER_TYPE_UPDATE_ERROR', array(
                                '#FIELD_NAME#' => $aUserHasField['FIELD_NAME'],
                                '#ENTITY_ID#' => $aUserHasField['ENTITY_ID'],
                                '#ERROR#' => $ex->GetString(),
                            )));
                        }
                    }
                } else {
                    if ($idUserTypeProp = $oUserTypeEntity->Add($aUserField)) {
                        $info[] = oGetMessage('USER_TYPE_ADDED', array(
                            '#FIELD_NAME#' => $aUserField['FIELD_NAME'],
                            '#ENTITY_ID#' => $aUserField['ENTITY_ID'],
                        ));
                    } else {
                        if (($ex = $APPLICATION->GetException())) {
                            throw new \Bitrix\Main\SystemException(oGetMessage('USER_TYPE_ADDED_ERROR', array(
                                '#FIELD_NAME#' => $aUserField['FIELD_NAME'],
                                '#ENTITY_ID#' => $aUserField['ENTITY_ID'],
                                '#ERROR#' => $ex->GetString(),
                            )));
                        }
                    }
                }

                $sort += 100;
            }

            $hlEntity = HLBT::compileEntity(
                HLBT::getRowById($highBlockID)
            );

            if (isset($hlData['ALTER']) && is_array($hlData['ALTER'])) {

                foreach ($hlData['ALTER'] as $alterData) {

                    if ($connection->query(
                        str_replace(
                            '#TABLE_NAME#',
                            $sqlHelper->quote($hlEntity->getDBTableName()),
                            $alterData
                        )
                    )
                    ) {
                        $info[] = oGetMessage('HIGHLOADBLOCK_ALTER_SUCCESS_INFO', array(
                            '#ROW#' => str_replace(
                                '#TABLE_NAME#',
                                $sqlHelper->quote($hlEntity->getDBTableName()),
                                $alterData
                            )
                        ));
                    }

                }

            }

            if (isset($hlData['INDEXES']) && is_array($hlData['INDEXES'])) {

                foreach ($hlData['INDEXES'] as $indexData) {

                    $iResult = $connection->createIndex(
                        str_replace('#TABLE_NAME#', $hlEntity->getDBTableName(), $indexData[0]),
                        str_replace('#TABLE_NAME#', $hlEntity->getDBTableName(), $indexData[1]),
                        $indexData[2]
                    );

                    if ($iResult) {
                        $info[] = oGetMessage('HIGHLOADBLOCK_ADDED_INDEX_INFO', array(
                            '#INDEX_NAME#' => str_replace('#TABLE_NAME#', $hlEntity->getDBTableName(), $indexData[1]),
                            '#TABLE_NAME#' => $hlEntity->getDBTableName(),
                        ));
                    } else {
                        throw new \Bitrix\Main\SystemException(oGetMessage('HIGHLOADBLOCK_ADDED_INDEX_ERROR', array(
                            '#INDEX_NAME#' => str_replace('#TABLE_NAME#', $hlEntity->getDBTableName(), $indexData[1]),
                            '#TABLE_NAME#' => $hlEntity->getDBTableName(),
                            '#ERROR#' => '',
                        )));
                    }

                }

            }

            return $highBlockID;

        }


        $idNewHighLoadBlock = createHighLoadBlock($highBlockTypeProduct[0], $highBlockTypeProduct[1], $highBlockTypeProduct[2]);

        //получения экземпляра класса
        $Exhlblock = HLBT::getById($idNewHighLoadBlock)->fetch();
        $entity = HLBT::compileEntity($Exhlblock);
        $entity_data_class = $entity->getDataClass();

        $result = $entity_data_class::add(array(
            'UF_DEF' => '1',
            "UF_ACTIVE" => "Y",
            'UF_ID' => '1',
            'UF_XML_ID' => 'summer',
            'UF_SORT' => '100',
            'UF_NAME' => 'Летний',
            'UF_DESCRIPTION' =>'Летний',
            'UF_FULL_DESCRIPTION' =>'Летний',
            'UF_CODE' => 'asd'
        ));

//        if ($result->isSuccess()) {
//            echo 'успешно добавлен';
//        } else {
//            echo 'Ошибка: ' . implode(', ', $result->getErrors()) . '</br>';
//        }

        $result = $entity_data_class::add(array(
            'UF_DEF' => '0',
            "UF_ACTIVE" => "Y",
            'UF_ID' => '2',
            'UF_XML_ID' => 'winter',
            'UF_SORT' => '200',
            'UF_NAME' => 'Зимний',
            'UF_DESCRIPTION' =>'Зимний',
            'UF_FULL_DESCRIPTION' =>'Зимний',
            'UF_CODE' => 'qwe'
        ));
//        if ($result->isSuccess()) {
//            echo 'успешно добавлен';
//        } else {
//            echo 'Ошибка: ' . implode(', ', $result->getErrors()) . '</br>';
//        }

        $result = $entity_data_class::add(array(
            'UF_DEF' => '0',
            "UF_ACTIVE" => "Y",
            'UF_ID' => '3',
            'UF_XML_ID' => 'demi-season',
            'UF_SORT' => '300',
            'UF_NAME' => 'Демисезон',
            'UF_DESCRIPTION' =>'Демисезон',
            'UF_FULL_DESCRIPTION' =>'Демисезон',
            'UF_CODE' => 'zxc'
        ));

//        if ($result->isSuccess()) {
//            echo 'успешно добавлен';
//        } else {
//            echo 'Ошибка: ' . implode(', ', $result->getErrors()) . '</br>';
//        }










        $iblockId = $this->getIblockIdByCode('clothes');


        $arFields = Array(
            "NAME"  =>  "Сезоны" ,
            "ACTIVE"  =>  "Y" ,
            "SORT"  =>  "50" ,
            "CODE"  =>  "Seasons" ,
            "PROPERTY_TYPE"  =>  "S" ,
            "USER_TYPE"  =>  "directory" ,
            "IBLOCK_ID"  =>  2 , //номер вашего инфоблока
            "LIST_TYPE"  =>  "L" ,
            "MULTIPLE"  =>  "N" ,
            'IS_REQUIRED' => 'Y',
            'SMART_FILTER' =>"Y",
            'SEARCHABLE'=>"Y",


            "USER_TYPE_SETTINGS" => array("size"=>"1", "width"=>"0", "group"=>"N", "multiple"=>"N", "TABLE_NAME"=>"b_hlbd_season")
////

        );


        $ibp  =  new  CIBlockProperty;
        $PropID  =  $ibp ->Add( $arFields );
        $ibp = new CIBlockProperty();
        if(!$ibp->Update($PropID, $arFields))
            echo $ibp->LAST_ERROR;






        if(CModule::IncludeModule("iblock"))
        {
            $arSelect = Array("ID", "NAME", "PROPERTY_CODE");
            $arFilter = Array("IBLOCK_ID"=>2, "ACTIVE"=>"Y");

            $el_tree= array();

            $res = CIBlockElement::GetList(Array("SORT"=>"DESC"), $arFilter, false, false, $arSelect);
            while($ob_arr = $res->Fetch())
            {
                $el_tree[ $ob_arr[ 'ID' ] ]= $ob_arr;
            }

            foreach ( $el_tree as $el_NAME => $el ) {
                //
                $elementId = $el_NAME;
                $iblockId = 2;
                CIBlockElement::SetPropertyValuesEx($elementId, $iblockId, ['Seasons' => 'summer']);
                // echo 'NAME: ', $el_NAME, ' is <pre>', print_r( $el ), '</pre>';
            }
        }

        $elementId =30;
        $iblockId = 2;
        \CIBlockElement::SetPropertyValuesEx($elementId, $iblockId, ['Seasons' => 'winter']);


        $elementId =29;
        $iblockId = 2;
        \CIBlockElement::SetPropertyValuesEx($elementId, $iblockId, ['Seasons' => 'demi-season']);




















    }





    /**
     * Reverse the migration.
     *
     * @return mixed
     */
    public function down()
    {
        $iblockId = $this->getIblockIdByCode('clothes');

        $this->deleteIblockElementPropertyByCode($iblockId, 'Seasons');

        $filter = array(
            'select' => array('ID'),
            'filter' => array('=NAME' => "Seasons")
        );
        $hlblock = HLBT::getList($filter)->fetch();
        if(is_array($hlblock) && !empty($hlblock))
        {
            HLBT::delete($hlblock['ID']);
        }

    }}