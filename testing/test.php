<?php

use Bedivierre\Craftsman\Masonry\BaseDataObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestResponseObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;
use Bedivierre\Craftsman\Utility;

require_once "../vendor/autoload.php";
$r = new BaseDataObject();
$r[] = '44';
$r[1] = new BaseDataObject();
$r[1]->s = 'qwerty';
$r[2] = '22';
$r[] = '11';

$d = $r->copy();
$r[0] = 345678;
echo $r[0];

$d[1]->s = 'GTRE';

echo $r[0];


