<?php


namespace Bedivierre\Craftsman\DataTransport\REST;


use Bedivierre\Craftsman\Joiner\BaseResponseObject;

class RestPesponseObject extends BaseResponseObject
{

    public function __construct($data, string $url = '', string $method = 'post')
    {
        parent::__construct($data, $url, $method);
    }

}