<?php


namespace Bedivierre\Craftsman\Aqueduct;
use Bedivierre\Craftsman\Masonry\BaseDataObject;

/**
 * Class BaseResponseObject
 * @package Bedivierre\Sberbank\Base
 */
class BaseResponseObject extends BaseDataObject
{
    const ERR_OK = 0;
    const ERR_UNKNOWN = -1;

    /**
     * @param BaseDataObject $data Тело ответа, как правило, строка JSON
     * @param string $url URL, по которому был совершен запрос.
     * @param string $method Метод запроса (post, get или имя своего транспортного канала)
     * @param bool $storeRawData Указывает, надо ли сохранять в объекте ответа входные данные в чистом виде.
     * @param int $httpCode Http-код ответа.
     */
    public function __construct(BaseDataObject $data, string $url = '', string $method = 'post', bool $storeRawData = false)
    {
        $this->_url = $url;
        $this->_method = $method;
        $this->_errorMessage = '';
        $this->_errorCode = 0;
        if(!$data->headers || !($data->headers instanceof BaseDataObject))
            $data->headers = new BaseDataObject();
        $this->_headers = $data->headers;
        $this->_httpCode = $data->code;
        if($storeRawData)
            $this->_raw = is_object($data->body) ? clone $data->body : $data->body;
        parent::__construct($data->body);
    }

    /**
     * Получить специфичный заголовок по имени
     * @param string $name Имя заголовка
     * @return mixed|null
     */
    public function getHeader(string $name){
        return $this->_headers->{strtolower($name)};
    }
    /**
     * Получить все заголовки ответа
     * @return BaseDataObject
     */
    public function getAllHeaders(string $name){
        return $this->_headers;
    }
    /**
     * Метод отправки запроса (POST или GET)
     * @return string
     */
    public function getMethod(): string
    {
        return $this->_method;
    }
    /**
     * URL запроса, от которого получен данный ответ.
     * @return string
     */
    public function getUrl(): string
    {
        return $this->_url;
    }
    /**
     * Возвращает входные данные, переданные объекту при создании, если они сохранялись ("$storeRawData = true" в
     * конструкторе)
     * @return mixed|null
     */
    public function getRaw()
    {
        return $this->_raw;
    }

    /**
     * Сообщение ошибки
     * @return string
     */
    public function errorMessage()
    {
        return $this->_errorMessage;
    }
    /**
     * Код ошиибки
     * @return int
     */
    public function errorCode()
    {
        return $this->_errorCode;
    }
    /**
     * Определяет, есть ли ошибка в ответе.
     * @return bool
     */
    public function hasError()
    {
        return $this->_errorCode != self::ERR_OK;
    }
    /**
     * Установить код ошиибки.
     * @param string $message Сообщение ошибки.
     * @param int $err_code Код ошибки. По умолчанию равен BaseResponseObject::ERR_UNKNOWN
     */
    public function setError(string $message, int $err_code = self::ERR_UNKNOWN)
    {
        $this->_errorMessage = $message;
        $this->_errorCode = $err_code;
    }

    /**
     * Получить http-код ответа
     */
    function getHttpCode() : int{
        return $this->_httpCode;
    }
}