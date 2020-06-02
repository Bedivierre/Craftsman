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
$r->sss = 'w';
$r->ddd = 's';
$r[1]->s = 'qwerty';

$d = new BaseDataObject();
$d->ddd = 'werrt';
$d->qqq = 'qwerty';
$d[1] = new BaseDataObject();
$d[1]->s = 'GTRE';

$r->absorb($d, true);
$r[1]->s = 'ertyu';

echo $r[0];


