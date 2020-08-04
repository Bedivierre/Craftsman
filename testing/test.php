<?php

use Bedivierre\Craftsman\Appraise\Assert;
use Bedivierre\Craftsman\Cartography\Router;
use Bedivierre\Craftsman\Masonry\BaseDataObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestResponseObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;
use Bedivierre\Craftsman\Poetry\Str;
use Bedivierre\Craftsman\Utility;

require_once "../vendor/autoload.php";

/*
$r = new BaseRequestObject('https://b2b.alfastrah.ru/wapi/dictionary/address-type/names');
//$r = new BaseRequestObject('https://b2b-test2.alfastrah.ru/wapi/dictionary/address-type/names');

$r->setAuth('TORGSTAIL', 'VZ8577gC', CURLAUTH_DIGEST); //true
//$r->setAuth('TORGSTAIL', 'b4PQs6aS', CURLAUTH_DIGEST); //test

$response = $r->get();

echo $response->getHttpCode();
//print_r($request->toArray());

*/
$r = new BaseRequestObject('https://b2b.alfastrah.ru/wapi/dictionary/address-type/names');

