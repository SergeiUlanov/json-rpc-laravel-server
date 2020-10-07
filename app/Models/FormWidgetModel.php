<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Boolean;


class FormWidgetModel extends Model
{
    use HasFactory;


    /**
     * Чтение из БД записей для указанной страницы
     * @param $pageUID
     * @return array
     */
    public function srvRead($pageUID) : array
    {
      //$dbRecs = DB::select('select * from form_widget_dates order by id desc limit 10');
      //$dbRecs = DB::select('select * from form_widget_dates where page_uid = :id order by id desc limit 10', ['id' => $pageUID]);

        $dbRecs = DB::table('form_widget_dates')
            ->select('page_uid', 'user_name', 'user_text', 'created_at', 'id')
            ->where('page_uid', '=', $pageUID)
            ->orderBy('created_at', 'desc')
            ->get();

        $recs = array();
        foreach($dbRecs as $objRec) {
            $rec = array(
                'id'   => $objRec->id,
                'name' => $objRec->user_name,
                'text' => $objRec->user_text,
                'time' => $objRec->created_at,
                'puid' => $objRec->page_uid,
            );
            $recs[] = $rec;
        }
        return $recs;
    }



    /**
     * Добавление в БД новой записи для указанной страницы
     *
     * @param $pageUID
     * @param $user
     * @param $text
     * @return array
     */
    public function srvAdd($pageUID, $user, $text) : array
    {
        $clearUID = $this->filterString($pageUID);
        $userName = $this->filterString($user);
        $userText = $this->filterString($text);

        if($clearUID != $pageUID) {
            return array('error' => 'Ошибка в значении page_uid');
        }
        if(empty($userName) || empty($userText)) {
            return array('error' => 'Ошибка, пустое значение в данных');
        }

        //$sqlQuery = 'insert into form_widget_dates (page_uid, user_name, user_text, created_at) values (?, ?, ?, NOW())';
        //$sqlParams = array($pageUID, $userName, $userText, $this->freshTimestamp());      // now() учитывает часовой пояс
        //$result = DB::insert($sqlQuery, $sqlParams);
        //$msg = ": $pageUID, $userName, $userText, $result";

        DB::table('form_widget_dates')->insert([
            'page_uid'  => $pageUID,
            'user_name' => $userName,
            'user_text' => $userText,
            'created_at' => $this->freshTimestamp(),
        ]);
        $msg = 'OK, данные сохранены';

        return array('ok' => $msg);
    }



    /**
     * Очистка строк, поступивших со стороны клиентов
     *
     * @param string $str
     * @return string
     */
    private function filterString(string $str) : string
    {
        return trim( htmlentities($str, ENT_QUOTES, "UTF-8") );
    }



    /**
     * Метод для целей отладки
     * @return array
     */
    public function srvDebug()
    {
        //$arPageData = array(
        //    '0s'=>array(
        //        'id'   => '1234',
        //        'name' => 'Test 1',
        //        'text' => 'Формирование сообщения из БД 1...',
        //        'time' => date('d.m.Y G:i', time()),
        //        'puid' => 0
        //    ),
        //    '1t'=>array(
        //        'id'   => '1234',
        //        'name' => 'Test 2',
        //        'text' => 'Формирование сообщения из БД 2...',
        //        'time' => date('d.m.Y G:i', time()),
        //        'puid' => 0
        //    ),
        //);

        $this->srvAdd('home', 'User', 'text-la-la-la');

        $arPageData = $this->srvRead('about');
        return array(
            'jsonrpc' => '2.0',
            'id'      => 1001,
            'result'  => $arPageData,
        );
    }


}   // end class
