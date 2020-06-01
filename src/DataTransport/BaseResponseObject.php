<?php


namespace Bedivierre\Craftsman\Joiner;
use Bedivierre\Craftsman\Carpenter\BaseDataObject;

/**
 * Class BaseResponseObject
 * @package Bedivierre\Sberbank\Base
 */
class BaseResponseObject extends BaseDataObject
{
    const ERR_OK = 0;
    const ERR_UNKNOWN = -1;

    /**
     * @param string|array $data Тело ответа, как правило, строка JSON
     * @param string $url URL, по которому был совершен запрос.
     * @param string $method Метод запроса (POST или GET)
     */
    public function __construct($data, string $url = '', string $method = 'post')
    {
        $this->_url = $url;
        $this->_method = $method;
        $this->_errorMessage = '';
        $this->_errorCode = 0;
        parent::__construct($data);
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
     * URL запроса, от кторого получен данный ответ.
     * @return string
     */
    public function getUrl(): string
    {
        return $this->_url;
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

}