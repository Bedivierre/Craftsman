<?php


namespace Bedivierre\Craftsman\Aqueduct\Flow;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

class DataTransfer extends BaseDataObject
{
    /**
     * @param string $headers
     * @return BaseDataObject
     */
    static function parseHeaders(string $headers) : BaseDataObject{
        $h = new BaseDataObject();
        $headers_arr = explode("\r\n", $headers);
        $headers_arr = array_filter($headers_arr);
        foreach ($headers_arr as $header){
            $m = [];
            if(preg_match('/^\s*([\w_-]+)\s*:\s*(.*)\s*$/', $header,$m)){
                $name = $m[1];
                $content = $m[2];
                $h->{strtolower($name)} = $content;
            }
        }
        return $h;
    }

    static function setAuth(BaseRequestObject $request, $ch){
        if(!($auth = $request->getAuth()))
            return;
        curl_setopt($ch, CURLOPT_HTTPAUTH,  $auth->type);
        curl_setopt($ch, CURLOPT_USERPWD,  "{$auth->userName}:{$auth->password}");
    }

    /**
     * @param $ch
     * @return BaseDataObject
     * @throws \Exception
     */
    static function processResult($ch) : BaseDataObject{
        $result = curl_exec($ch);

        if(curl_error($ch))
        {
            curl_close($ch);
            throw new \Exception('Ошибка при запросе: ' . curl_error($ch));
        }

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header_text = substr($result, 0, $header_size);
        $headers = self::parseHeaders($header_text);
        $body = substr($result, $header_size);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $return = new BaseDataObject([
            'headers'=>$headers,
            'body'=>$body,
            'code'=>$code
        ]);

        curl_close($ch);
        return $return;
    }


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
     * @param array|BaseDataObject $data Дополнительные параметры к запросу. Могут влиять на поведение протокола.
     * @return BaseResponseObject
     * @throws \Exception
     */
    public function doTransfer(BaseRequestObject $request, BaseDataObject $data) : BaseResponseObject{
        try {
            $func = $this->getTransferFunc();
            return $func($request, $data);
        } catch (\Exception $ex){
            return self::createErrorResponse($ex->getMessage(), $request->getHost(), $request->getTransferProtocolName());
        }
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
     * @param array|BaseDataObject $data Дополнительные параметры к запросу. Могут влиять на поведение протокола.
     * @return BaseDataObject Возвращает объект, представляющий результат запроса.
     * @throws \Exception
     */
    public static function get(BaseRequestObject $request, BaseDataObject $data){
        $url = $request->getHost();
        $query = http_build_query($request->getRequestData()->toArray());
        $ch = curl_init();
        $uri = $url."?".$query;
        $headers = $request->getHeaders()->toArray();
        $defaults = array(
            CURLOPT_URL => $uri,
            CURLOPT_HEADER => 1,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_HTTPHEADER => $headers,
        );
        curl_setopt_array($ch, $defaults);
        self::setAuth($request, $ch);

        return self::processResult($ch);
    }

    /**
     * Производит POST-запрос по указанному адресу с указанными данными.
     * @param BaseRequestObject $request Объект запроса.
     * @param array|BaseDataObject $data Дополнительные параметры к запросу. Могут влиять на поведение протокола.
     * @return BaseDataObject Возвращает объект, представляющий результат запроса.
     * @throws \Exception
     */
    public static function post(BaseRequestObject $request, BaseDataObject $data){
        $url = $request->getHost();
        $query = http_build_query($request->getRequestData()->toArray());
        $ch = curl_init();
        $headers = $request->getHeaders()->toArray();
        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $query,
            CURLOPT_HEADER => 1,
            CURLOPT_RETURNTRANSFER=>true,
            CURLOPT_HTTPHEADER => $headers,
        );
        curl_setopt_array($ch, $defaults);
        self::setAuth($request, $ch);

        return self::processResult($ch);
    }
}