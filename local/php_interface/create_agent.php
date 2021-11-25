<?use Bitrix\Main\IO;
use Bitrix\Main\Application;

use Bitrix\Main\Mail\Event;
Class COrderSale
{
function Agent()
{
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/csv_data.php");
    function putDataToCSV($file,$array){
        $fields_type = 'R'; //дописываем строки в файл
        $delimiter = ";";   //разделитель для csv-файла
        $csvFile = new \CCSVData($fields_type, false);
        $csvFile->SetFieldsType($fields_type);
        $csvFile->SetDelimiter($delimiter);
        $csvFile->SetFirstHeader(true);
        $arrayFields = array();
        foreach (array_keys($array[current(array_keys($array))]) as $value)
        {
            if(substr($value,0,1)=='~') continue;
            $arrayFields[] = $value;
        }
        // запишем заголовки:
        $csvFile->SaveFile($file,$arrayFields);

        foreach ($array as $arValue){
            $row = array();
            foreach ($arrayFields as $arrayField)
            {
                $row[] = $arValue[$arrayField];
            }
            $csvFile->SaveFile($file,$row);
        }
    }
    \Bitrix\Main\Loader::includeModule('sale');


    $now = new DateTime();

    $curdate = date('d.m.Y 18:00:00',time() - 86400);
    $to = date('d.m.Y 18:00:00');
    $arrFilterCurDate = Array(">=DATE_INSERT" => $curdate, "<=DATE_INSERT" => $to);

    $arSelectFields = Array('ID','DATE_INSERT_FORMAT','USER_NAME','USER_EMAIL','PRICE','CURRENCY');
    $db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arrFilterCurDate ,'','',$arSelectFields);
    while ($ar_sales = $db_sales->Fetch())
    {$collections[$ar_sales['ID']] = $ar_sales;
    }
    $collections = mb_convert_encoding($collections, "Windows-1251");
    $fileWithPropsValue = '/upload/pai/collections.csv';
    if(!IO\Directory::isDirectoryExists(Application::getDocumentRoot() . '/upload/pai/')){
        IO\Directory::createDirectory(Application::getDocumentRoot() . '/upload/pai/');
    }
    $file = new IO\File(Application::getDocumentRoot() . $fileWithPropsValue);
    $file->putContents(''); // очищаем файл

    putDataToCSV(Application::getDocumentRoot().$fileWithPropsValue,$collections);

    $rsMess = CEventMessage::GetList('ORDER_LIST');
    $asa=0;
    if ($arMess = $rsMess->GetNext())
    {
        $asa= $arMess['ID'];
    }


    $filePath = $_SERVER['DOCUMENT_ROOT'] . "/upload/pai/collections.csv";

    $arFile = CFile::MakeFileArray($filePath);
    $arFile["MODULE_ID"] = "sale";
    $fileId = CFile::SaveFile($arFile, "pai/collections");

    Event::send(array(

        "EVENT_NAME" => "ORDER_LIST",

        "LID" => "s1",

        "C_FIELDS" => array(),
        'MESSAGE_ID' => $asa,
        "FILE" => array($fileId)
    ));


    return 'COrderSale::Agent();';
}}

