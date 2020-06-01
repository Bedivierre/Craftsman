<?php
namespace Bedivierre\Craftsman;
use Bedivierre\Craftsman\Joiner\BaseResponseObject;
use Bedivierre\Craftsman\Joiner\BaseRequestObject;

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
     * @param $url
     * @param $data
     * @return BaseResponseObject|null
     */
    public static function post($url, $data){
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

// загрузка страницы и выдача её браузеру
        $json = curl_exec($ch);

        if(curl_error($ch))
        {
            curl_close($ch);
            return self::createErrorResponse('Ошибка при запросе: ' . curl_error($ch), $url);
        }
        curl_close($ch);
        return new BaseResponseObject($json, $url, 'post');
    }

    /**
     * @param $url
     * @param $data
     * @return BaseResponseObject|null
     */
    public static function get($url, $data){
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
            return self::createErrorResponse('Ошибка при запросе: ' . curl_error($ch), $uri);
        }
        curl_close($ch);
        return new BaseResponseObject($json, $uri, 'get');
    }

    /**
     * Возвращает новый объект ответа с ошибкой.
     * @param string $text Текст ошибки
     * @param string $url URL запроса, вернувшего ошибку.
     * @return BaseResponseObject
     */
    public static function createErrorResponse(string $text, string $url)
    {
        $r = new BaseResponseObject('', $url);
        $r->setError($text);
        return $r;
    }


    public static function runRequest(BaseRequestObject $request)
    {
        if($request->getMethod() == 'post') {
            return self::post($request->getHost(), $request->buildQuery());
        }elseif ($request->getMethod() == 'get'){
            return self::get($request->getHost(), $request->buildQuery());
        } else {
            return self::createErrorResponse(
                "Неверный метод отправки данных :".$request->getMethod(),
                $request->getHost());
        }
    }
}
