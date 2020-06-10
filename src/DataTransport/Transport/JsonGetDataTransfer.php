<?php


namespace Bedivierre\Craftsman\Aqueduct\Flow;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

class JsonGetDataTransfer extends \Bedivierre\Craftsman\Aqueduct\Flow\DataTransfer
{
    public function __construct()
    {
        parent::__construct("get", function (BaseRequestObject $req, BaseDataObject $data){
            return $this->getJson($req, $data);
        });
    }

    /**
     * Производит GET-запрос, ожидая получить JSON-строку, по указанному адресу с указанными данными.
     * @param BaseRequestObject $request Объект запроса
     * @param array|BaseDataObject $data Дополнительные параметры к запросу. Могут влиять на поведение протокола. Установка
     * поля save_raw в true заставит протокол сохранять чистый ответ в объекте BaseResponseObject, который потом можно
     * получить с функцией getRaw()
     * @return BaseResponseObject|null Возвращает объект типа BaseResponseObject, представляющий результат запроса.
     */
    function getJson(BaseRequestObject $request, BaseDataObject $data){
        try {
            $json = self::get($request, $data);
            return new BaseResponseObject($json, $request->getHost(), 'get',(bool) $data->save_raw);
        } catch (\Exception $ex){
            return self::createErrorResponse('Ошибка при запросе: ' . $ex->getMessage(), $request->getHost(), 'get');
        }
    }
}