<?php


namespace Bedivierre\Craftsman\Aqueduct\REST;


use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;

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
}