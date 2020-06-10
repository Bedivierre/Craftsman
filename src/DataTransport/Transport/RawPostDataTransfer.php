<?php

namespace Bedivierre\Craftsman\Aqueduct\Flow;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

class RawPostDataTransfer extends \Bedivierre\Craftsman\Aqueduct\DataTransfer
{
    public function __construct()
    {
        parent::__construct("post", function (BaseRequestObject $req, BaseDataObject $data){
            return $this->postRaw($req, $data);
        });
    }

    /**
     * Производит GET-запрос, ожидая получить JSON-строку, по указанному адресу с указанными данными.
     * @param BaseRequestObject $request Объект запроса
     * @param BaseDataObject $data Массив данных-параметров запроса.
     * @return BaseResponseObject|null Возвращает объект типа BaseResponseObject, представляющий результат запроса.
     */
    function postRaw(BaseRequestObject $request, BaseDataObject $data){
        try {
            $return = self::post($request, $data);
            return new BaseResponseObject($return, $request->getHost(), 'post', true);
        } catch (\Exception $ex){
            return self::createErrorResponse('Ошибка при запросе: ' . $ex->getMessage(), $request->getHost(), 'post');
        }
    }
}