<?php


namespace Bedivierre\Craftsman\Carpenter;

class BaseDataObject implements \Iterator
{

    /**
     * @var array $_data
     */
    private $_data = [];

    public function __get(string $name){
        return $this->getMember($name);
    }
    public function __set(string $name, $value)
    {
        $this->addMember($name, $value, true);
    }
    public function __isset ( string $name ){
        return $this->exists($name);
    }
    public function __unset(string $name)
    {
        $this->removeMember($name);
    }

    /**
     * Безопасно добавляет свойство в объект.
     * @param string $name Имя нового свойства.
     * @param null|mixed $value Значение свойства.
     * @param bool $overwrite Указывает, что свойство должно быть перезаписано, если существует.
     */
    public function addMember(string $name, $value = null, bool $overwrite = true)
    {
        if($this->exists($name) && !$overwrite)
            return;
        $this->_data[$name] = $value;
    }
    /**
     * Получает свойство из объекта. Прие его отсутствии возвращает null.
     * @param string $name Имя получаемого свойства.
     * @return mixed|null
     */
    public function getMember(string $name)
    {
        if($this->exists($name))
            return $this->_data[$name];
        return null;
    }
    /**
     * Удаляет свойство из объекта.
     * @param string $name Имя удаляемого свойства.
     */
    public function removeMember(string $name)
    {
        unset($this->_data[$name]);
    }
    /**
     * Указывает, есть ли свойство с таким именем в объекте.
     * @param string $name Имя искомого объекта.
     * @return bool
     */
    public function exists(string $name){
        return isset($this->_data[$name]);
    }

    /**
     * Возвращает количество свойств объекта.
     * @return int
     */
    public function count(){
        return count($this->_data);
    }

    /**
     * Возвращает этот объект в виде массива.
     * @param bool $usePrivate Определяет, оставлять в результирующем массиве приватные свойства или нет.
     * Приватными свойствами считаются свойства, имя которых начинается со знака подчёркивания ("_")
     * @return array
     */
    public function toArray($usePrivate = false){
        $ret = [];
        foreach ($this->_data as $key => $value)
        {
            if(substr($key, 0, 1) != '_' || $usePrivate){
                if($value instanceof BaseDataObject){
                    $ret[$key] = $value->toArray();
                } else {
                    $ret[$key] = $value;
                }
            }
        }
        return  $ret;
    }

    // Реализация интерфейса Iterator
    function rewind() {
        return reset($this->_data);
    }
    function current() {
        return current($this->_data);
    }
    function key() {
        return key($this->_data);
    }
    function next() {
        return next($this->_data);
    }
    function valid() {
        return key($this->_data) !== null;
    }
    //Конец реализации интерфейса Iterator

    /**
     * @param string|array $data Данные, которые передаются в объект данных. По умолчанию должно
     * быть массивом или строкой JSON, но формат входных данных может быть переопределен
     * в наследующих классах при помощи функции getArrayFromInputData.
     */
    public function __construct($data = [])
    {
        $this->apply_data($data);
    }

    /**
     * Функция добавляет данные к объекту, рекурсивоно преобразуя вложенные массивы в объекты BaseDataOject.
     * @param mixed $data Данные, принимаемые объектом. По умолчанию - массивы и JSON, но формат входных данных может быть
     * переопределен с помощью protected-функции getArrayFromInputData.
     * @param bool $overwrite Определяет, нужно ли перезаписывать свойства при их наличии.
     * @param bool $newdata Определяет, нужно ли обнулять данные объекта.
     */
    public function apply_data($data, $overwrite = true, $newdata = false)
    {
        $arr =  $this->getArrayFromInputData($data);
        if(!$arr || !is_array($arr))
            return;

        if($newdata)
            $this->_data = [];

        foreach ($arr as $key => $value)
        {
            if(!$overwrite && $this->exists($key))
                continue;
            if(!is_array($value)) {
                $this->{$key} = $value;
            }
            else {
                $this->{$key} = new BaseDataObject($value);
            }
        }
    }

    /**
     * Функция для обработки входных данных объекта. Должна возвращать массив.
     * @param mixed $data Данные, передаваемые при создании объекта.
     * @return array|null
     */
    protected function getArrayFromInputData($data)
    {
        if(!is_string($data) && !is_array($data))
            return null;
        return is_array($data) ? $data : json_decode($data, true);
    }
}