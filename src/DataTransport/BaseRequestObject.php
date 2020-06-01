<?php
namespace Bedivierre\Craftsman\Joiner;

use Bedivierre\Craftsman\Carpenter\BaseDataObject;
use Bedivierre\Craftsman\Utility;

/**
 * @package Bedivierre\Sberbank\Base
 */
class BaseRequestObject extends BaseDataObject
{

    /**
     * @param string $method Метод отправки запроса (POST или GET)
     * @param string $host Хост, на который будет направлен запрос.
     */
    public function __construct(string $host, string $method = 'post', Callable $func = null)
    {
        parent::__construct();
        $this->setMethod($method, $func);
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
    public function getMethodName(): string
    {
        return $this->_method->name;
    }
    /**
     * Возвращает функцию отправки запроса. Устанавливается она в setMethod.
     * @return Callable|null
     */
    public function getMethodFunc(): string
    {
        return $this->_method->func;
    }
    /**
     * Указывает название и функцию отправки запроса. По умолчанию используется метод POST по адресу getHost()
     * @param string $method Имя для метода передачи.
     * @param callable|null $func Функция, используемая при передаче данных. Если null, используется значение по
     * умолчанию. В функцию будет передаваться результат функции getRequestData и сам объект запроса.
     */
    public function setMethod(string $method, Callable $func = null)
    {
        $m = new BaseDataObject();
        if($func == null || mb_strtolower($method) == 'post') {
            $m->type = 'post';
            $m->func = function() {$this->requestPost();};
        } else if(mb_strtolower($method) == 'get') {
            $m->type = 'get';
            $m->func = function() {$this->requestGet();};
        } else {
            $m->type = $method;
            $m->func = $func;
        }
        $this->_method = $m;
    }

    /**
     * Возвращает структурированные данные для запроса в виде массива. Эти данные будут отправляться в
     * в функцию запроса getMethodFunc.
     * @return array
     */
    protected function getRequestData()
    {
        return $this->toArray();
    }

    /**
     * Делает запрос с помощью указанной в getMethodFunc функции.
     * @return BaseResponseObject
     */
    public function doRequest(){
        if(is_callable($cb = $this->getMethodFunc()))
            return $cb($this->getRequestData(), $this);
        return Utility::createErrorResponse("Не указана функция отправки данных", $this->getHost());
    }

    private function requestPost($data, BaseRequestObject $request){
        return Utility::post($this->getHost(), $data);
    }
    private function requestGet($data, BaseRequestObject $request){
        return Utility::get($this->getHost(), $data);
    }
}