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
$b->check_nomer=1026286;
$ret = $b->get();

echo Str::byte( $a = new Str("qw531qaaedqeda\n")) . "\n";
echo $a->chunk_split(4);
foreach ($a as $c){
    echo $c . " ";
}




//==================




$items = [
    [
        'code' => "111",
        'name' => "Предмет 1",
        'price' => 222222,
        'mark' => 'fR55"233cs22-00sk',
    ],
    [
        'code' => "111",
        'name' => "Предмет 2",
        'price' => 444,
        'mark' => "fR55\"233'$5222-00sk",
    ],
]; // Массив с позициями чека

$check_data = [
    'error_code' => 0,
    'order' => 12345,
    'summa' => 50000,
    'discont' => 7500,
    // любые другие данные, общие для всего для чека
    'content'=>[],
];
foreach ($items as $i){
    $position = [
        'line'=>[ // элемент line существует в нынешней структуре, так что его лучше так и оставить
            'code' => $i['code'],
            'name' => $i['name'],
            'price' => $i['price'],
            'mark' => $i['mark'],
            // и так далее
        ]
    ];
    $check_data['content'][] = $position; //добавляем элемент в конец массива content
}
$json = json_encode($check_data); // результат, который надо скормить сайту. Эта функция сама
                                  // заэкранирует всё, что требуется.
print_r($json);
//=======================










print_r($ret->toArray());


