<?php


namespace Bedivierre\Craftsman\Masonry;

class BaseDataObject implements \Iterator, \ArrayAccess
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
    public function __clone()
    {
        foreach($this->_data as $key => $value)
        {
            if(is_object($value))
                $this->_data[$key] = clone $value;
            else
                $this->_data[$key] = $value;
        }
    }

    /**
     * Безопасно добавляет свойство в объект.
     * @param string $name Имя нового свойства.
     * @param null|mixed $value Значение свойства.
     * @param bool $convertArrays Указывает, нужно ли преобразовать массивы в объекты BaseDataObject
     * @param bool $overwrite Указывает, что свойство должно быть перезаписано, если существует.
     */
    public function addMember(string $name, $value = null, bool $convertArrays = true, bool $overwrite = true)
    {
        if($this->exists($name) && !$overwrite)
            return;
        $v = is_array($value) && $convertArrays ? new BaseDataObject($value) : $value;

        $this->_data[$name] = $v;
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

    /**
     * Возвращает копию объекта.
     * @return self
     */
    public function copy()
    {
        return clone $this;
    }

    /**
     * Возвращает копию объекта, свойства которого со свойствами объекта $obj
     * @param BaseDataObject $obj Добавляемый объект.
     * @param bool $overwrite Указывает, надо ли перезапиывать свойства, если текущий объект уже имеет свойство с таким именем.
     * @return self
     */
    public function merge(BaseDataObject $obj, bool $overwrite = false)
    {
        $res = $this->copy();
        foreach ($obj as $key => $value)
        {
            if(!isset($res->_data[$key]) || $overwrite)
                $res->_data[$key] = $value;
        }
        return $res;
    }

    /**
     * Поглощает данные передаваемого объекта. Свойства объекта копируются в текущий.
     * @param BaseDataObject $obj Поглощаемый объект
     * @param bool $overwrite Указывает, нужно ли перезаписывать поля.
     * @param bool $overwritePrivate Указывает, нужно ли перезаписывать приватные свойства (начинающиеся с символа
     * подчеркивания "_")
     */
    public function absorb(BaseDataObject $obj, bool $overwrite = false, bool $overwritePrivate = false)
    {
        foreach ($obj as $key => $value)
        {
            $v = is_object($value) ? clone $value : $value;
            if(!$this->offsetExists($key)){
                $this[$key] = $v;
                continue;
            }
            if(!$overwrite && !$overwritePrivate)
                continue;
            if(!is_string($key)) {
                if ($overwrite){
                    $this[$key] = $v;
                }
                continue;
            }
            $check = substr($key, 0, 1) == '_' ? $overwritePrivate : $overwrite;
            if($check){
                $this[$key] = $v;
            }
        }
    }

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
        if(!$arr || !is_array($arr) || !($data instanceof BaseDataObject))
            return;

        if($newdata)
            $this->_data = [];
        if($data instanceof BaseDataObject){
            $this->absorb($data, true);
            return;
        }


        foreach ($arr as $key => $value){
            $this->addMember($key, $value, true, $overwrite);
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
    // Конец реализации интерфейса Iterator

    // Реализация интерфейса ArrayAccess
    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }
    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        if(isset($this->_data[$offset]))
            return $this->_data[$offset];
        return null;
    }
    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if(is_null($offset))
            $this->_data[] = $value;
        else
            $this->_data[$offset] = $value;
    }
    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }
    // Конец реализации интерфейса ArrayAccess
}