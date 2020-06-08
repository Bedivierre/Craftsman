<?php
namespace Bedivierre\Craftsman;
use Bedivierre\Craftsman\Aqueduct\BaseResponseObject;
use Bedivierre\Craftsman\Aqueduct\BaseRequestObject;

class Utility
{
    /**
     * Функция берет элемент массива по его имени, включая вложенные элементы. Разделителем пути по умолчанию
     * являтся '.'
     * @param array $array Массив, из которого нужно получить данные.
     * @param string $path Имя необходимого элемента.
     * @param string $limiter Разделитель для пути.
     * @return array|mixed|null
     */
    public static function arr_path(array &$array, string $path, string $limiter = '.')
    {
        if (!is_array($array))
            return null;
        $p = explode($limiter, $path);
        if (!$p)
            return $array;
        $count = count($p);
        $counter = 1;
        $var = $array;
        foreach ($p as $_p) {
            if ($count == $counter) {
                if (isset($var[$_p]))
                    return $var[$_p];
                else
                    return null;
            }
            if (!isset($var[$_p]) || !is_array($var[$_p]))
                return null;
            $var = $var[$_p];
            $counter++;
        }
        return null;
    }

    /**
     * Производит POST-запрос по указанному адресу с указанными данными.
     * @param string $url Адрес обращения запроса
     * @param array $data Массив данных, отправляемых в POST-запросе.
     * @return string|null Возвращает строку, представляющую результат запроса.
     * @throws \Exception
     */
    public static function post(string $url, array &$data){
        $query = http_build_query($data);
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
    /**
     * Производит POST-запрос, ожидая получить JSON-строку, по указанному адресу с указанными данными.
     * @param string $url Адрес обращения запроса
     * @param array $data Массив данных, отправляемых в POST-запросе.
     * @return BaseResponseObject|null Возвращает объект типа BaseResponseObject, представляющий результат запроса.
     */
    public static function postJson(string $url, array &$data){
        try {
            $json = self::post($url, $data);
            return new BaseResponseObject($json, $url, 'post');
        } catch (\Exception $ex){
            return self::createErrorResponse('Ошибка при запросе: ' . $ex->getMessage(), $url, 'post');
        }
    }

    /**
     * Производит GET-запрос по указанному адресу с указанными параметрами.
     * @param string $url Адрес обращения запроса
     * @param array $data Массив данных, отправляемых в GET-запросе.
     * @return string|null Возвращает строку, представляющую результат запроса.
     * @throws \Exception
     */
    public static function get(string $url, array $data){
        $query = http_build_query($data);
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
     * Производит GET-запрос, ожидая получить JSON-строку, по указанному адресу с указанными данными.
     * @param string $url Адрес обращения запроса
     * @param array $data Массив данных, отправляемых в GET-запросе.
     * @return BaseResponseObject|null Возвращает объект типа BaseResponseObject, представляющий результат запроса.
     */
    public static function getJson(string $url, array &$data){
        try {
            $json = self::get($url, $data);
            return new BaseResponseObject($json, $url, 'get');
        } catch (\Exception $ex){
            return self::createErrorResponse('Ошибка при запросе: ' . $ex->getMessage(), $url, 'get');
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
}
