<?php

use Bedivierre\Craftsman\Masonry\BaseDataObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestResponseObject;
use Bedivierre\Craftsman\Aqueduct\REST\RestRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;
use Bedivierre\Craftsman\Utility;

require_once "../vendor/autoload.php";

class d{

    public $w = 'qqq';
    private $qq = 'qq';
    public function toArray(){
        return ['w' => $this->w, 'ddd'=>22];
    }
}
class d1{
    public $w2 = 'qqq2';
}

$_d = new d();
$_d1 = new d1();



$r = new BaseDataObject();
$r->dd = 23;
$r->dd2 = 23;
$r->cd3 = 23;
$r->fd4 = $_d;
$r->fd5 = $_d1;
$r->_dd = 44;

$f = $r->copy();
$f->dd = 344453;
$f->qwerty = "qwerty";
$f->_dd = 55;
$f->_ee = "hret";
$d = $r->values();
$r->f = $f;

$b = $r->keys('_?[d]+\d', true);

echo print_r($r->toArray(true));


