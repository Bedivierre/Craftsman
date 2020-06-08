<?php

use Bedivierre\Craftsman\Appraise\Assert;
use Bedivierre\Craftsman\Masonry\BaseDataObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestResponseObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;
use Bedivierre\Craftsman\Poetry\Str;
use Bedivierre\Craftsman\Utility;

require_once "../vendor/autoload.php";


$b = new BaseRequestObject('http://172.20.4.7/shopadmin/kassa_check.php4', 'get');
$b->kassa_nomer=8;
$b->check_nomer=1023634;
$ret = $b->get();

echo Str::byte( $a = new Str("qw531qaaedqeda\n")) . "\n";
echo $a->chunk_split(4);
foreach ($a as $c){
    echo $c . " ";
}

print_r($ret->toArray());


