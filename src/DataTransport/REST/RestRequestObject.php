<?php


namespace Bedivierre\Craftsman\DataTransport\REST;


use Bedivierre\Craftsman\Joiner\BaseRequestObject;

class RestRequestObject extends BaseRequestObject
{

    /**
     * RestRequestObject constructor.
     * @param string $method Метод отправки запроса (POST или GET)
     * @param string $host Хост, на который будет направлен запрос.
     */
    public function __construct(string $host, string $method = 'post')
    {
        parent::__construct($host, $method);
    }


    /**
     * Составляет, проверяет и возвращает массив для формирования запроса.
     * @return array
     */
    protected function getRequestData()
    {
        $arr = $this->toArray();
        if(!is_array($arr))
            return [];
        return $arr;
    }
}