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

$arFilter = Array('IBLOCK_ID'=>$arResult['IBLOCK_ID'], 'GLOBAL_ACTIVE'=>'Y','SECTION_ID'=>0,);
$db_list = CIBlockSection::GetList(Array(), $arFilter, true);
while($ar_result = $db_list->GetNext())
{
    $arrayID[] = $ar_result['ID'];
}
//print_r($arrayID);
/*Формируем массив */
$arSelect = Array("ID", "IBLOCK_ID", "NAME", "PROPERTY_*");
$arFilter = Array("IBLOCK_ID"=>$arResult['IBLOCK_ID'], "SECTION_ID"=>$arrayID,"INCLUDE_SUBSECTIONS" => "Y");
$res = CIBlockElement::GetList(Array(), $arFilter, false, false, $arSelect);

while($ob = $res->GetNext()){


    $iblockElementId=$ob['ID'];

    $iblocid=$arResult['IBLOCK_ID'];

    $ipropElementTemplates = new \Bitrix\Iblock\InheritedProperty\ElementTemplates($iblocid, $iblockElementId);
    $templates = $ipropElementTemplates->findTemplates();
    $newTemplates = array(
        'ELEMENT_META_TITLE' => "Купить {=this.property.Seasons} {=this.Name} по приятной цене в {=catalog.store.1.name}"
    );
    $ipropElementTemplates->set($newTemplates);}


