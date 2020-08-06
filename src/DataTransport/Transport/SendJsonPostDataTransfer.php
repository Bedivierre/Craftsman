<?php


namespace Bedivierre\Craftsman\Aqueduct\Flow;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

class SendJsonPostDataTransfer extends \Bedivierre\Craftsman\Aqueduct\Flow\DataTransfer
{
    public function __construct()
    {
        parent::__construct("post", function (BaseRequestObject $req, BaseDataObject $data){
            return $this->send($req, $data);
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
    function send(BaseRequestObject $request, BaseDataObject $data){
        try {
            $request->setHeader("Content-Type", "application/json;charset=UTF-8");
            $json = self::post($request, $request->getRequestData()->toJson(), $data);
            return new BaseResponseObject($json, $request->getHost(), 'post',(bool) $data->save_raw);
        } catch (\Exception $ex){
            return self::createErrorResponse('Ошибка при запросе: ' . $ex->getMessage(), $request->getHost(), 'get');
        }
    }
}