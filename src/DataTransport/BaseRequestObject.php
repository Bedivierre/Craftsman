<?php
namespace Bedivierre\Craftsman\Joiner;

use Bedivierre\Craftsman\Carpenter\BaseDataObject;
/**
 * @package Bedivierre\Sberbank\Base
 */
class BaseRequestObject extends BaseDataObject
{

    /**
     * @param string $method Метод отправки запроса (POST или GET)
     * @param string $host Хост, на который будет направлен запрос.
     */
    public function __construct(string $host, string $method = 'post')
    {
        parent::__construct();
        $this->setMethod($method);
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
     * Возвращает метод отправки запроса (POST или GET)
     * @return string
     */
    public function getMethod(): string
    {
        return $this->_method;
    }
    /**
     * @param string $method
     */
    public function setMethod(string $method)
    {
        $methods = ['post', 'get'];
        if(in_array(strtolower($method), $methods))
            $this->_method = $method;
        else if (!in_array($this->_method, $methods)) // если метод не установлен, установить по умолчанию post
            $this->_method = $methods[0];
        else // иначе не менять
            return;
    }

    /**
     * Составляет, проверяет и возвращает массив для формирования запроса.
     * @return array
     */
    public function buildQuery()
    {
        $arr = $this->getQueryArray();
        if(!is_array($arr))
            return [];
        return $arr;
    }
    /**
     * Возвращает массив данных, которые будут записаны в запрос.
     * @return array
     */
    protected function getQueryArray()
    {
        return $this->toArray();
    }

}