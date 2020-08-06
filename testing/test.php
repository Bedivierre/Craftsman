<?php

use Bedivierre\Craftsman\Appraise\Assert;
use Bedivierre\Craftsman\Cartography\Router;
use Bedivierre\Craftsman\Masonry\BaseDataObject;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;
use Bedivierre\Craftsman\Poetry\Str;
use Bedivierre\Craftsman\Utility;

require_once "../vendor/autoload.php";

$to = new BaseDataObject();
$to->addRequirement('name', 'string');
$to->addRequirement('code', ['pattern'=>'[0-9a-fA-F]{3,6}']);
$to->addRequirement('code1', function($v){
    if($v == '123' || $v == '456' || $v == 789)
        return true;
    return false;
});
$to->name = 0;
$to->code = 'adefff';
$to->code1 = 1234;
$chres = $to->checkRequirements();
exit();

$r = new BaseRequestObject('https://b2b.alfastrah.ru/wapi/dictionary/address-type/names');
//$r = new BaseRequestObject('https://b2b-test2.alfastrah.ru/wapi/dictionary/address-type/names');

$r->setAuth('TORGSTAIL', 'Es64RwMt', CURLAUTH_DIGEST); //true
//$r->setAuth('TORGSTAIL', 'b4PQs6aS', CURLAUTH_DIGEST); //test

$response = $r->get();

echo $response->getHttpCode();
//print_r($request->toArray());


