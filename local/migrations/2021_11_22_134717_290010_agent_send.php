<?php

use Arrilot\BitrixMigrations\BaseMigrations\BitrixMigration;
use Arrilot\BitrixMigrations\Exceptions\MigrationException;

class AgentSend20211122134717290010 extends BitrixMigration
{
    /**
     * Run the migration.
     *
     * @return mixed
     * @throws \Exception
     */
    public function up()
    {$arrCeventRes = [];
        $arrCeventTypes = Array(
            Array(
                'LID' => 'ru',
                'EVENT_NAME' => 'ORDER_LIST',
                'EVENT_TYPE'=> 'email',
                'NAME' => 'Отправка данных о закаказах за день.',
                'DESCRIPTION' => '
                    #EMAIL_TO# E-mail получателя
                    #ORDER_DATE# - дата заказа
                    #ORDER_USER# - заказчик
                    #PRICE# - сумма заказа
                    #EMAIL# - E-Mail заказчика
                    #ORDER_LIST# - состав заказа
                    #SITE_NAME#- имя сайта
                    #EMAIL# - E-Mail заказчика'

            ),
        );

        $arrCeventTemplates = Array(
            'ORDER_LIST' => Array(
                'ACTIVE'=> 'Y',
                'EVENT_NAME' => 'ORDER_LIST',
                'LID' => Array('s1'),
                'EMAIL_FROM' => '#SITE_NAME#',
                'EMAIL_TO' => 'seregadix@mail.ru',
                'SUBJECT' => 'Список заказов с сайта за день',
                'BODY_TYPE' => 'html',
                'MESSAGE' => '
            <!doctype html>
            <html lang="ru">
                <head>
                    <meta charset="utf-8">
                    <title>Список заказов с сайта за день</title>
                </head>
                <body>
                <h2>Добрый день!</h2>
                
                <p>Список заказов за день.</p>
                

                <p>Список заказанных товаров за день в прикрепленном файле:</p>
                
               
                
                <p>Письмо сформировано автоматически.</p>
                
                </body>
            </html>'
            )
        );
        $et = new CEventType;
        foreach($arrCeventTypes as $arrCeventType){
            $res = $et->Add($arrCeventType);
            if(!$res){
                echo $et->LAST_ERROR;
            }
            else{
                $arrCeventRes[$res] = $arrCeventType['EVENT_NAME'];
            }
        }

        if(is_array($arrCeventRes)){
            $em = new CEventMessage;
            foreach($arrCeventRes as $cEventTypeName){
                $res_em = $em->Add($arrCeventTemplates[$cEventTypeName]);

                if(!$res_em){
                    echo $em->LAST_ERROR;
                }
                else{
                    echo 'Шаблон создан '.$res_em.'<br />';
                }
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
        $et = new CEventType;
        $et->Delete("ORDER_LIST");



        global $DB;
        //Удаляем тип почтового события
        $et = new CEventType;
        $et->Delete("ORDER_LIST");
        //Находим все почтовые шаблоные которые были привязаны к нашему типу
        $DB->StartTransaction();
        $emessage = new CEventMessage();
        $rsMess = CEventMessage::GetList($by = 's1', $order = "desc", array("TYPE_ID" => "ORDER_LIST"));
        //рекурсивно по одному удаляем найденные шаблоны
        while ($events = $rsMess->GetNext()) {
            $emessage->Delete(intval($events["ID"]));
            $DB->Commit();
        }

    }

}


