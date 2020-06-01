<?php


namespace Bedivierre\Craftsman\Aqueduct\REST;


use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;

class RestResponseObject extends BaseResponseObject
{

    public function __construct($data, string $url = '', string $method = 'post')
    {
        parent::__construct($data, $url, $method);
    }

}