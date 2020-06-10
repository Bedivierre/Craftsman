<?php


namespace Bedivierre\Craftsman\Aqueduct\Flow;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

class JsonPostDataTransfer extends \Bedivierre\Craftsman\Aqueduct\DataTransfer
{
    public function __construct()
    {
        parent::__construct("post", function (BaseRequestObject $req, BaseDataObject $data){
            return $this->postJson($req, $data);
        });
    }

    /**
     * Производит GET-запрос, ожидая получить JSON-строку, по указанному адресу с указанными данными.
     * @param BaseRequestObject $request Объект запроса
     * @param BaseDataObject $data Массив данных-параметров запроса.
     * @return BaseResponseObject|null Возвращает объект типа BaseResponseObject, представляющий результат запроса.
     */
    function postJson(BaseRequestObject $request, BaseDataObject $data){
        try {
            $json = self::post($request, $data);
            return new BaseResponseObject($json, $request->getHost(), 'post',(bool) $data->save_raw);
        } catch (\Exception $ex){
            return self::createErrorResponse('Ошибка при запросе: ' . $ex->getMessage(), $request->getHost(), 'get');
        }
    }
}