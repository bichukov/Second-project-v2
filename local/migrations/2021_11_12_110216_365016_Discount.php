<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class Discount20211112110216365016 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function up()
    {

        \Bitrix\Main\Loader::includeModule('sale');
        $IBLOCK_SECTION=13;
        $DISCOUNT_VALUE = 15;//процент скидки
        // $arItemIds = [36,37,38,39,40,41];//Массив с ID элементов товаров

        $arDiscountFields = [
            "LID" => SITE_ID,
            "SITE_ID" => SITE_ID,
            "NAME"=> "Скидка ".$DISCOUNT_VALUE."%",
            "DISCOUNT_VALUE" => $DISCOUNT_VALUE,
            "DISCOUNT_TYPE" => "P",
            "LAST_LEVEL_DISCOUNT" => "Y",
            "LAST_DISCOUNT" => "Y",
            "ACTIVE" => "Y",
            "CURRENCY" => "RUB",
            "USER_GROUPS" => [2],
            'ACTIONS' => [
                "CLASS_ID" => "CondGroup",
                "DATA" => [
                    "All" => "AND"
                ],
                "CHILDREN" => [
                    [
                        "CLASS_ID" => "ActSaleBsktGrp",
                        "DATA" => [
                            "Type" => "Discount",
                            "Value" => $DISCOUNT_VALUE,
                            "Unit" => "Perc",
                            "Max" => 0,
                            "All" => "OR",
                            "True" => "True",
                        ],
                        "CHILDREN" => [
                            [
                                "CLASS_ID" => "ActSaleSubGrp",
                                "DATA" => [
                                    "All" => "AND",
                                    "True" => "True",
                                ],
                                "CHILDREN" => [
                                    [
                                        "CLASS_ID" => "CondIBSection",
                                        "DATA" => [
                                            "logic" => "Equal",
                                            "value" => $IBLOCK_SECTION,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "CONDITIONS" =>  [
                'CLASS_ID' => 'CondGroup',
                'DATA' => [
                    'All' => 'AND',
                    'True' => 'True',
                ],
                'CHILDREN' => [
                    [
                        "CLASS_ID" => "CondBsktProductGroup",
                        "DATA" => [
                            "Found" => "Found",
                            "All" => "OR",
                        ],
                        "CHILDREN" => [
                            [
                                "CLASS_ID" => "CondIBSection",
                                "DATA" => [
                                    "logic" => "Equal",
                                    "value" => $IBLOCK_SECTION,
                                ]
                            ]
                        ]
                    ],
                ],
            ]
        ];
        $iDiscountNumber = \CSaleDiscount::Add($arDiscountFields);
        if(IntVal($iDiscountNumber) > 0)
            \Bitrix\Sale\Internals\DiscountGroupTable::updateByDiscount($DISCOUNT_ID, [2], "Y", true);
    }

    /**
     * Reverse the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function down()
    { \Bitrix\Main\Loader::includeModule('sale');
        $IBLOCK_SECTION=13;
        $DISCOUNT_VALUE = 15;//процент скидки
        //  $arItemIds = [36,37,38,39,40,41];//Массив с ID элементов товаров

        $arDiscountFields = [
            "LID" => SITE_ID,
            "SITE_ID" => SITE_ID,
            "NAME"=> "Скидка ".$DISCOUNT_VALUE."%",
            "DISCOUNT_VALUE" => $DISCOUNT_VALUE,
            "DISCOUNT_TYPE" => "P",
            "LAST_LEVEL_DISCOUNT" => "Y",
            "LAST_DISCOUNT" => "Y",
            "ACTIVE" => "Y",
            "CURRENCY" => "RUB",
            "USER_GROUPS" => [2],
            'ACTIONS' => [
                "CLASS_ID" => "CondGroup",
                "DATA" => [
                    "All" => "AND"
                ],
                "CHILDREN" => [
                    [
                        "CLASS_ID" => "ActSaleBsktGrp",
                        "DATA" => [
                            "Type" => "Discount",
                            "Value" => $DISCOUNT_VALUE,
                            "Unit" => "Perc",
                            "Max" => 0,
                            "All" => "OR",
                            "True" => "True",
                        ],
                        "CHILDREN" => [
                            [
                                "CLASS_ID" => "ActSaleSubGrp",
                                "DATA" => [
                                    "All" => "AND",
                                    "True" => "True",
                                ],
                                "CHILDREN" => [
                                    [
                                        "CLASS_ID" => "CondIBSection",
                                        "DATA" => [
                                            "logic" => "Equal",
                                            "value" => $IBLOCK_SECTION,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            "CONDITIONS" =>  [
                'CLASS_ID' => 'CondGroup',
                'DATA' => [
                    'All' => 'AND',
                    'True' => 'True',
                ],
                'CHILDREN' => [
                    [
                        "CLASS_ID" => "CondBsktProductGroup",
                        "DATA" => [
                            "Found" => "Found",
                            "All" => "OR",
                        ],
                        "CHILDREN" => [
                            [
                                "CLASS_ID" => "CondIBSection",
                                "DATA" => [
                                    "logic" => "Equal",
                                    "value" => $IBLOCK_SECTION,
                                ]
                            ]
                        ]
                    ],
                ],
            ]
        ];
        $iDiscountNumber = \CSaleDiscount::delete( 1);
        if(IntVal($iDiscountNumber) > 0)
            \Bitrix\Sale\Internals\DiscountGroupTable::updateByDiscount($DISCOUNT_ID, [2], "Y", true);

    }
}
