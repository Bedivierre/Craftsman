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

$router = new Router();
$router->run();
$request = $router->getRequest();

echo $request->getQuery();


//print_r($request->toArray());


