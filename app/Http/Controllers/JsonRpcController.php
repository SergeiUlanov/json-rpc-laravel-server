<?php

namespace App\Http\Controllers;


use App\Contracts\JsonRpcServerInterface;
use App\Models\FormWidgetModel;
use Illuminate\Http\Request;



/**
 * Контроллер для принятия и обработки запросов с формы виджета на сайтах.
 *
 * Class JsonRpcController
 * @package App\Http\Controllers
 */
class JsonRpcController extends Controller
{
    private FormWidgetModel        $model;
    private JsonRpcServerInterface $rpcServer;



    /**
     * Инициализация JSON-RPC-сервера и модели.
     *
     * @param JsonRpcServerInterface $rpcServer
     * @param FormWidgetModel $model
     */
    public function __construct(JsonRpcServerInterface $rpcServer, FormWidgetModel $model)
    {
        $this->model     = $model;
        $this->rpcServer = $rpcServer;
    }



    /**
     * Обработка данных принятых от клиента, их передача RPC-серверу для выполнения.
     *
     * @param Request $request
     * @return mixed
     */
    public function storeWidgetFormApi(Request $request)
    {
        $content = $request->getContent();
        return $this->rpcServer->rpcRun($content, $this->model);
    }



    /**
     * Метод для целей отладки
     *
     * @return mixed
     */
    public function serverDebug()
    {
        return $this->model->srvDebug();
    }


}   // end class
