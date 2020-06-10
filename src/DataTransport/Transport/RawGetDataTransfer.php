<?php


namespace Bedivierre\Craftsman\Aqueduct\Flow;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

class RawGetDataTransfer extends \Bedivierre\Craftsman\Aqueduct\Flow\DataTransfer
{
    public function __construct()
    {
        parent::__construct("get", function (BaseRequestObject $req, BaseDataObject $data){
            return $this->getRaw($req, $data);
        });
    }

    /**
     * Производит GET-запрос, ожидая получить JSON-строку, по указанному адресу с указанными данными.
     * @param BaseRequestObject $request Объект запроса
     * @param array|BaseDataObject $data Дополнительные параметры к запросу. Могут влиять на поведение протокола.
     * @return BaseResponseObject|null Возвращает объект типа BaseResponseObject, представляющий результат запроса.
     */
    function getRaw(BaseRequestObject $request, BaseDataObject $data){
        try {
            $return = self::get($request, $data);
            return new BaseResponseObject($return, $request->getHost(), 'get',true);
        } catch (\Exception $ex){
            return self::createErrorResponse('Ошибка при запросе: ' . $ex->getMessage(), $request->getHost(), 'get');
        }
    }
}