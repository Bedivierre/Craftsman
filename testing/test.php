<?php

use Bedivierre\Craftsman\Appraise\Assert;
use Bedivierre\Craftsman\Cartography\Router;
use Bedivierre\Craftsman\Masonry\BaseDataObject;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;
use Bedivierre\Craftsman\Poetry\Str;
use Bedivierre\Craftsman\Utility;

require_once "../vendor/autoload.php";

$v = new BaseDataObject([
    'q'=>['ss', 1234, 'qwerty'],
    'f'=>['ss'=>2, 'asde'=>3],
    'e'=>'wert'
]);
$req = new \Bedivierre\Craftsman\Cartography\UriRequest(new Router());
echo $v->getDataByPath('q.0').":".$v->getDataByPath('f.ss').":".$v->getDataByPath('e');
exit();



$r = new BaseRequestObject('https://b2b.alfastrah.ru/wapi/dictionary/address-type/names');
//$r = new BaseRequestObject('https://b2b-test2.alfastrah.ru/wapi/dictionary/address-type/names');

$r->setAuth('TORGSTAIL', 'Es64RwMt', CURLAUTH_DIGEST); //true
//$r->setAuth('TORGSTAIL', 'b4PQs6aS', CURLAUTH_DIGEST); //test

$response = $r->post();

echo $response->getHttpCode();
//print_r($request->toArray());


