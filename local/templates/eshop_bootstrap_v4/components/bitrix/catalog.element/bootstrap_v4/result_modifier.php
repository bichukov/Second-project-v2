<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var CatalogElementComponent $component
 */

$component = $this->getComponent();
$arParams = $component->applyTemplateModifications();

$dbBasketItems = CSaleBasket::GetList(
    array(
        "NAME" => "ASC",
        "ID" => "ASC"
    ),
    array(
        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
        "LID" => SITE_ID,
        //"PRODUCT_ID" => $arResult['ID'], //ID текущего товара
        "ORDER_ID" => "NULL"
    ),
    false,
    false,
    array("PRODUCT_ID")
);





while ($arItemsBasket = $dbBasketItems->Fetch()) {
    $itInBasket = $arItemsBasket['PRODUCT_ID'];


    $intElementID = $itInBasket; // ID предложения
    $mxResult = CCatalogSku::GetProductInfo(
        $intElementID
    );




    if ($mxResult['ID'] == $arResult['ID']){
        $arResult['TYU'] = true;
        break;
    }
}

