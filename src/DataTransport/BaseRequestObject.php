<?php
namespace Bedivierre\Craftsman\Aqueduct;

use Bedivierre\Craftsman\Aqueduct\Flow\DataTransfer;
use Bedivierre\Craftsman\Aqueduct\Flow\JsonGetDataTransfer;
use Bedivierre\Craftsman\Aqueduct\Flow\JsonPostDataTransfer;
use Bedivierre\Craftsman\Masonry\BaseDataObject;
use Bedivierre\Craftsman\Utility;

/**
 * @package Bedivierre\Sberbank\Base
 */
class BaseRequestObject extends BaseDataObject
{

    /**
     * @param string $host Хост, на который будет направлен запрос.
     * @param string $method Метод отправки запроса (POST или GET)
     * @param callable|null $func Функция, которая будет вызываться для метода. Если метод указан как get
     * или post, используется соответствующий транспортный канал.
     */
    public function __construct(string $host, string $method = 'post', DataTransfer $protocol = null)
    {
        parent::__construct();
        $this->setTransferProtocol($method, $protocol);
        $this->_host = $host;
    }

    /**
     * Возвращает хост, на который будет направлен запрос
     * @return string
     */
    public function getHost(): string
    {
        return $this->_host;
    }
    /**
     * Возвращает название метода отправки запроса
     * @return string
     */
    public function getTransferProtocolName(): string
    {
        return $this->_transfer->name;
    }
    /**
     * Возвращает функцию отправки запроса. Устанавливается она в setMethod.
     * @return Callable|null
     */
    public function getTransferProtocol(): DataTransfer
    {
        return $this->_transfer->protocol;
    }
    /**
     * Указывает название и функцию отправки запроса. По умолчанию используется метод POST по адресу getHost()
     * @param string $method Имя для метода передачи.
     * @param DataTransfer $protocol Протокол передачи данных. Если имя метода равно get или post, а этот аргумент равен
     * null, то берутся стандартные протоколы передачи через JSON.
     */
    public function setTransferProtocol(string $method, DataTransfer $protocol = null)
    {
        $m = new BaseDataObject();
        if(mb_strtolower($method) == 'get') {
            $m->type = 'get';
            $m->protocol = $protocol !== null ? $protocol : new JsonGetDataTransfer();
        } else if($protocol == null || mb_strtolower($method) == 'post') {
            $m->type = 'post';
            $m->protocol = $protocol !== null ? $protocol : new JsonPostDataTransfer();
        } else {
            $m->type = $method;
            $m->protocol = $protocol;
        }

        $this->_transfer = $m;
    }

    /**
     * Возвращает структурированные данные для запроса в виде массива. Эти данные будут отправляться в
     * в функцию запроса getMethodFunc.
     * @return BaseDataObject
     */
    public function getRequestData()
    {
        return $this;
    }
    /**
     * Делает запрос с помощью указанной в getMethodFunc функции.
     * @return BaseResponseObject
     */
    public function doRequest($data = []){
        return $this->runCustomTransferProtocol($this->getTransferProtocol(), $data);
    }

    /**
     * @param DataTransfer $protocol Протокол передачи данных
     * @param array|BaseDataObject $data Дополнительные параметры к запросу. Могут влиять на поведение протокола.
     * @return BaseResponseObject
     */
    public function runCustomTransferProtocol(DataTransfer $protocol, $data = []):BaseResponseObject{
        try{
            if (is_array($data))
                $data = new BaseDataObject($data);
            if(!($data instanceof BaseDataObject))
                $data = new BaseDataObject();

            $ret = $protocol->doTransfer($this, $data);
            return $ret;
        }catch(\Exception $ex){
            return Utility::createErrorResponse(
                "Ошибка при транспортировке данных: ". $ex->getMessage(),
                $this->getHost(),
                '');
        }
    }
    /**
     * Делает POST-запрос (ожидая JSON-строку в ответ) по хосту getHost() и возвращает объект BaseResponseObject.
     * @param array|BaseDataObject $data Дополнительные параметры к запросу. Могут влиять на поведение протокола.
     * @return BaseResponseObject
     */
    public function post($data = []):BaseResponseObject{
        return $this->runCustomTransferProtocol(new JsonPostDataTransfer(), $data);
    }
    /**
     * Делает GET-запрос (ожидая JSON-строку в ответ) по хосту getHost() и возвращает объект BaseResponseObject.
     * @param array|BaseDataObject $data Дополнительные параметры к запросу. Могут влиять на поведение протокола.
     * @return BaseResponseObject
     */
    public function get($data = []):BaseResponseObject{
        return $this->runCustomTransferProtocol(new JsonGetDataTransfer(), $data);
    }
}