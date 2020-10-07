<?php

namespace App\Services;


use App\Contracts\JsonRpcServerInterface;
use Exception;



/**
 * Серверный класс для обработки запросов от JSON-RPC-клиента.
 * Используется на время поиска под Laravel 8 полноценной серверной библиотеки.
 *
 * Class DemoJsonRpcServer
 * @package App\Services
 */
class DemoJsonRpcServer implements JsonRpcServerInterface
{
    const JSON_RPC_VERSION = '2.0';

    const PARSE_ERROR      = -32700;
    const INVALID_REQUEST  = -32600;
    const METHOD_NOT_FOUND = -32601;
    const INVALID_PARAMS   = -32602;
    const INTERNAL_ERROR   = -32603;



    /**
     * Метод принимает запрос от клиента, производит его разбор,
     * выполняет запрошенную команду и возвращает результат для отправки клиенту.
     *
     * @param $content                - запрос на выполнение принятый от клиента
     * @param $calledProceduresObject - экземпляр объекта класса с набором процедур для исполнения
     * @return array
     */
    public function rpcRun($content, $calledProceduresObject) : array
    {
        try {
            $data = json_decode($content, true);
            $error = json_last_error();
            if($error != JSON_ERROR_NONE) {
                throw new Exception('Parse error: '.json_last_error_msg(), self::PARSE_ERROR);
            }elseif(empty($data)) {
                throw new Exception('Parse error: empty data', self::PARSE_ERROR);
            }

            //throw new Exception('Invalid Request', self::INVALID_REQUEST);

            $id        = $data['id'];
            $rpcMethod = $data['method'];
            $rpcParams = $data['params'];

            if(! method_exists($calledProceduresObject, $rpcMethod)) {
                throw new Exception('Method "'.$rpcMethod.'" not found', self::METHOD_NOT_FOUND);
            }
            $resRun = call_user_func_array(array($calledProceduresObject, $rpcMethod), $rpcParams);
            $result = $this->rpcSuccess($resRun, $id);
        }
        catch(Exception $e) {
            $id = (string) time();
            $result = $this->rpcError($e->getCode(), $e->getMessage(), $id);
        }
        return $result;
    }



    /**
     * Формирование успешного ответа на запрос клиента.
     *
     * @param array $result
     * @param string|null $id
     * @return array
     */
    private function rpcSuccess(array $result, string $id) : array
    {
        return array(
            'jsonrpc' => self::JSON_RPC_VERSION,
            'id'      => $id,
            'result'  => $result,
        );
    }



    /**
     * Формирование сообщения об ошибке для отправки клиенту.
     *
     * @param int $errorCode
     * @param string $errorMessage
     * @param string|null $id
     * @return array
     */
    private function rpcError(int $errorCode, string $errorMessage, string $id) : array
    {
        return array(
            'jsonrpc' => self::JSON_RPC_VERSION,
            'id'      => $id,
            'error'   => array('code' => $errorCode, 'message' => $errorMessage),
        );
    }


}   // end class
