<?php

use Bedivierre\Craftsman\Carpenter\BaseDataObject;
use Bedivierre\Craftsman\DataTransport\REST\RestPesponseObject;
use Bedivierre\Craftsman\DataTransport\REST\RestRequestObject;
use Bedivierre\Craftsman\Joiner\BaseRequestObject;
use Bedivierre\Craftsman\Joiner\BaseResponseObject;
use Bedivierre\Craftsman\Utility;

require_once "../vendor/autoload.php";

$l = new RestRequestObject("http://172.20.4.7/shopadmin/kassa_check.php4", 'get');
$l->kassa_nomer=8;
$l->check_nomer=1017969;

function rest_get($url, $data, $method = 'get'){
    $query = http_build_query($data);
    $ch = curl_init();
    $uri = $url."?".$query;
    $defaults = array(
        CURLOPT_URL => $uri,
        CURLOPT_HEADER => 0,
        CURLOPT_RETURNTRANSFER=>true,
    );
    curl_setopt_array($ch, $defaults);

// загрузка страницы и выдача её браузеру
    $json = curl_exec($ch);
    if(curl_error($ch))
    {
        curl_close($ch);
        return Utility::createErrorResponse('Ошибка при запросе: ' . curl_error($ch), $uri, 'get');
    }
    curl_close($ch);
    return new RestPesponseObject($json, $uri, $method);
}

$l->setMethod('custom', function ($d, BaseRequestObject $r){
    return rest_get($r->getHost(), $r->toArray(), 'custom');
});
$res = $l->doRequest();


echo $res->getMethod() . "<br>";
echo $res->discont . "<br>";
echo $res->summa . "<br>";
echo $res->order . "<br>";

foreach ($res->content as $p)
{
    $pos = $p->line;
    $price = (float)$pos->price / 100;
    $amount = (float)$pos->klv;
    $chd = (int) $pos->discont_restrict ? "no" : 'yes';
    echo "ID: $pos->idtov, name: '$pos->name', price: '$price', amount: $amount, can have discount: $chd<br>";
}


