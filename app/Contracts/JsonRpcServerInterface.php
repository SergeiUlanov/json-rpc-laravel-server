<?php

namespace App\Contracts;



/**
 * Существует много библиотек для работы с JSON-RPC,
 * использую интерфейс для возможности смены библиотеки.
 *
 * Interface JsonRpcServerInterface
 * @package App\Contracts
 */
interface JsonRpcServerInterface
{
    /**
     * Метод принимает запрос от клиента, производит его разбор,
     * выполняет запрошенную команду и возвращает результат для отправки клиенту.
     *
     * @param $content                - запрос на выполнение принятый от клиента
     * @param $calledProceduresObject - экземпляр объекта класса с набором процедур для исполнения
     * @return array
     */
    public function rpcRun($content, $calledProceduresObject) : array;
}
