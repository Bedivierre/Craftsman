<?php

use Bedivierre\Craftsman\Masonry\BaseDataObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestResponseObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;
use Bedivierre\Craftsman\Utility;

require_once "../vendor/autoload.php";


$b = new BaseRequestObject('http://172.20.4.7/shopadmin/kassa_check.php4', 'get');
$b->kassa_nomer=8;
$b->check_nomer=1021365;
$ret = $b->get();

print_r($ret->toArray());


