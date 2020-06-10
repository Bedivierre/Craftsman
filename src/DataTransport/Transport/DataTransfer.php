<?php


namespace Bedivierre\Craftsman\Aqueduct\Flow;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

class DataTransfer extends BaseDataObject
{
    public function __construct(string $name, Callable $func)
    {
        if($func === null)
            throw new \Exception("Не указана функция трансфера данных");
        $this->_func = $func;
        parent::__construct();
    }

    public function getTransferFunc() : Callable{
        if(!is_callable($this->_func))
            throw new \Exception("Не указана функция трансфера данных");
        return $this->_func;
    }

    /**
     * Функция трансфера, делающая запрос BaseRequestObject и получающая в ответ BaseResponseObject.
     * @param BaseRequestObject $request Объект запроса, обрабатываемый функцией трансфера
     * @param BaseDataObject $data Дополнительный объект с настройками
     * @return BaseResponseObject
     * @throws \Exception
     */
    public function doTransfer(BaseRequestObject $request, BaseDataObject $data) : BaseResponseObject{
        $func = $this->getTransferFunc();
        return $func($request, $data);
    }

    /**
     * Возвращает новый объект ответа с ошибкой.
     * @param string $text Текст ошибки
     * @param string $url URL запроса, вернувшего ошибку.
     * @return BaseResponseObject
     */
    public static function createErrorResponse(string $text, string $url, string $method = 'post')
    {
        $r = new BaseResponseObject('', $url, $method);
        $r->setError($text);
        return $r;
    }

    /**
     * Производит GET-запрос по указанному адресу с указанными параметрами.
     * @param BaseRequestObject $request Объект запроса.
     * @param BaseDataObject $data Дополнительные данные.
     * @return string|null Возвращает строку, представляющую результат запроса.
     * @throws \Exception
     */
    public static function get(BaseRequestObject $request, BaseDataObject $data){
        $url = $request->getHost();
        $query = http_build_query($request->getRequestData()->toArray());
        $ch = curl_init();
        $uri = $url."?".$query;
        $defaults = array(
            CURLOPT_URL => $uri,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER=>true,
        );
        curl_setopt_array($ch, $defaults);

        $result = curl_exec($ch);

        if(curl_error($ch))
        {
            curl_close($ch);
            throw new \Exception('Ошибка при запросе: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    /**
     * Производит POST-запрос по указанному адресу с указанными данными.
     * @param BaseRequestObject $request Объект запроса.
     * @param BaseDataObject $data Дополнительные данные.
     * @return string|null Возвращает строку, представляющую результат запроса.
     * @throws \Exception
     */
    public static function post(BaseRequestObject $request, BaseDataObject $data){
        $url = $request->getHost();
        $query = http_build_query($request->getRequestData()->toArray());
        $ch = curl_init();
        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER=>true,
        );
        curl_setopt_array($ch, $defaults);

        $result = curl_exec($ch);

        if(curl_error($ch))
        {
            curl_close($ch);
            throw new \Exception('Ошибка при запросе: ' . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
}