<?php

use Bedivierre\Craftsman\Masonry\BaseDataObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestResponseObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;
use Bedivierre\Craftsman\Utility;

require_once "../vendor/autoload.php";
$r = new \Bedivierre\Craftsman\Masonry\BaseDataObject();
$r[] = '44';
$r[1] = '33';
$r[2] = '22';
echo $r[0];


