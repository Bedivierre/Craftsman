<?php


namespace Bedivierre\Craftsman\Masonry;
use Bedivierre\Craftsman\Appraise\CheckData;
use Bedivierre\Craftsman\Appraise\CheckResult;
use Bedivierre\Craftsman\Utility;

/**
 * Базовый элемент библиотеки для хранения и передачи данных.
 * Представляет собой объект с динамическими свойствами и интерфейсом счётчика, то есть к нему могут быть применены
 * foreach и обращение по индексу.
 * Есть тонкость в механике работы: при создании свойства через стрелочный синтаксис ($obj->prop) передаваемое значение
 * при необходимости автоматически конвертируется в BaseDataObject, а все свойства, начинающиеся с '_' записываются как
 * приватные свойства, неучитываемые через foreach и многие функции вроде count(). Доступ через синтаксис массива
 * ($obj[2]) позволяет записать и получить свойство напрямую и без преобразований, как есть. Более того, это единственный
 * способ получить доступ по индексу типа int. Синтаксис массива не имеет доступа к приватным свойствам.
 * @package Bedivierre\Craftsman\Masonry
 */
class BaseDataObject implements \Iterator, \ArrayAccess, \JsonSerializable
{

    /**
     * @var array $_data
     */
    private $_data = [];
    private $_private_data = [];

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
        foreach($this->_private_data as $key => $value)
        {
            if(is_object($value))
                $this->_private_data[$key] = clone $value;
            else
                $this->_private_data[$key] = $value;
        }
    }

    /**
     * Безопасно добавляет свойство в объект. Если имя свойства начинается со знака подчеркивания, оно
     * добавляется в приватные свойства. Аналогично доступу к объекту через стрелочный  синтаксис.
     * @param string $name Имя нового свойства.
     * @param null|mixed $value Значение свойства.
     * @param bool $convertArrays Указывает, нужно ли преобразовать массивы в объекты BaseDataObject
     * @param bool $overwrite Указывает, что свойство должно быть перезаписано, если существует.
     */
    public function addMember(string $name, $value = null, bool $convertArrays = true, bool $overwrite = true)
    {
        if(substr($name, 0, 1) == '_'){
            $this->addPrivate($name, $value, $convertArrays, $overwrite);
            return;
        }
        if($this->exists($name) && !$overwrite)
            return;
        $v = is_array($value) && $convertArrays ? new BaseDataObject($value) : $value;

        $this->_data[$name] = $v;
    }
    /**
     * Безопасно добавляет приватное свойство в объект.
     * @param string $name Имя нового свойства.
     * @param null|mixed $value Значение свойства.
     * @param bool $convertArrays Указывает, нужно ли преобразовать массивы в объекты BaseDataObject
     * @param bool $overwrite Указывает, что свойство должно быть перезаписано, если существует.
     */
    public function addPrivate(string $name, $value = null, bool $convertArrays = true, bool $overwrite = true)
    {
        if($this->existsPrivate($name) && !$overwrite)
            return;
        $v = is_array($value) && $convertArrays ? new BaseDataObject($value) : $value;

        $this->_private_data[$name] = $v;
    }
    /**
     * Получает свойство из объекта. Прие его отсутствии возвращает null.
     * @param string $name Имя получаемого свойства.
     * @return mixed|null
     */
    public function getMember(string $name)
    {
        if(substr($name, 0, 1) == '_'){
            return $this->getPrivate($name);
        }
        if(isset($this->_data[$name]))
            return $this->_data[$name];
        return null;
    }
    /**
     * Получает приватное свойство из объекта. Прие его отсутствии возвращает null.
     * @param string $name Имя получаемого свойства.
     * @return mixed|null
     */
    public function getPrivate(string $name)
    {
        if(isset($this->_private_data[$name]))
            return $this->_private_data[$name];
        return null;
    }
    /**
     * Удаляет свойство из объекта.
     * @param string $name Имя удаляемого свойства.
     */
    public function removeMember(string $name)
    {
        if(substr($name, 0, 1) == '_'){
            $this->removePrivate($name);
            return;
        }
        unset($this->_data[$name]);
    }
    /**
     * Удаляет приватное свойство из объекта.
     * @param string $name Имя удаляемого свойства.
     */
    public function removePrivate(string $name)
    {
        unset($this->_private_data[$name]);
    }
    /**
     * Указывает, есть ли свойство с таким именем в объекте.
     * @param string $name Имя искомого объекта.
     * @return bool
     */
    public function exists(string $name){
        if(substr($name, 0, 1) == '_'){
            return $this->existsPrivate($name);
        }
        return isset($this->_data[$name]);
    }
    /**
     * Указывает, есть ли приватное свойство с таким именем в объекте.
     * @param string $name Имя искомого объекта.
     * @return bool
     */
    public function existsPrivate(string $name){
        return isset($this->_private_data[$name]);
    }
    /**
     * Возвращает количество свойств объекта.
     * @return int
     */
    public function count(){
        return count($this->_data);
    }
    /**
     * Возвращает количество приватных свойств объекта.
     * @return int
     */
    public function countPrivate(){
        return count($this->_private_data);
    }

    /**
     * Проверяет требования объекта
     * @return CheckResult
     */
    public function checkRequirements(){
        $r = $this->_requirements;

        if(!($r instanceof BaseDataObject)) {
            //проверка не проводится, следовательно, объект по умолчанию валидный
            return new CheckResult(true);
        }
        $result = new CheckResult(true);
        foreach ($r as $k => $v){
            //преобразования в checkData
            $ch = CheckData::transformToCheckData($v);
            $check_result = $ch->checkData($this->{$k});
            if($check_result !== true){
                $result->result = false;
                $result->data->{$k} = $check_result;
            }
        }
        return $result;
    }

    /**
     * Добавляет требования для проверки объекту.
     * Добавляет поле _requirements - массив значений. Ключ должен соответствовать имени свойства объекта. Значение
     * $params может принимать
     * следующие варианты:
     *      bool - простая проверка на существующее и непустое поле.
     *      string - тип проверяемого свойства
     *      Callable - функция проверки свойства
     *      CheckData|array
     *          'required' => bool - проверка на необходимость существования свойства,
     *          'type'=>string, - тип свойства
     *          'pattern'=>string, шаблон для проверки строки
     *          'min'=>float|int, 'max'=>float|int, - максимальные и минимальные значения для int и float
     *          'check' => Callable - функция, вызываемая при проверке свойства. Вызывается после всех остальных
     *              проверок.
     * @param string $key Проверяемое свойство объекта
     * @param bool|string|array|CheckData|Callable $params параметры проверки объекта
     * @param bool|null $required - указывает, необхоимо ли наличие свойства. Если не null переопределяет значение из
     *      $params или значение по умолчанию (true)
     */
    public function addRequirement(string $key, $params = true, bool $required = null){
        if(!$this->_requirements)
            $this->_requirements = new BaseDataObject();
        $this->_requirements->{$key} = CheckData::transformToCheckData($params);
        if(!is_null($required))
            $this->_requirements->{$key}->required = $required;
    }


    /**
     * @param string|array $data Данные, которые передаются в объект данных. По умолчанию должно
     * быть массивом или строкой JSON, но формат входных данных может быть переопределен
     * в наследующих классах при помощи функции getArrayFromInputData.
     */
    public function __construct($data = [])
    {
        $this->apply_data($data);
        $this->onInitialize();
    }

    public function onInitialize(){

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
        foreach ($arr as $key => $value){
            $this->addMember($key, $value, true, $overwrite);
        }
    }

    /**
     * Добавляет "сырое" значение в массив, аналог синтаксиса обращения к массиву.
     * @param mixed $value Добавляемое значение
     */
    public function push($value) : void{
        $this[] = $value;
    }
    /**
     * Функция для обработки входных данных объекта. Должна возвращать массив.
     * @param mixed $data Данные, передаваемые при создании объекта.
     * @return array|null
     */
    protected function getArrayFromInputData($data)
    {
        if(!is_string($data) && !is_array($data) && !($data instanceof BaseDataObject))
            return null;
        if($data instanceof BaseDataObject)
            return $data->toArray();
        return is_array($data) ? $data : json_decode($data, true);
    }

    /**
     * Получает данные по пути.
     * @param string $path Путь, по которому ищется результат.
     * @param bool $convertToObject Указывает, нужно ли конвертировать результат в BaseDataObject если это массив.
     * @param string $delimeter Разделитель уровней в пути.
     * @return array|mixed|null
     */
    public function getDataByPath(string $path, bool $convertToObject = false, string $delimeter = '.'){
        $res = Utility::arr_path($this->_data, $path, $delimeter);
        if($convertToObject && is_array($res))
            return new BaseDataObject($res);
        return $res;
    }



    /**
     * Реализация интерфейса JsonSerializable
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
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
     * Проверка существования объекта по индексу.
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * Ключ, по которому осуществляется проверка существования объекта по индексу.
     * </p>
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }
    /**
     * Получение объекта о ключу. Идентично $obj[$offset].
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * Ключ, по которому возвращается объект.
     * </p>
     * @return mixed Возвращает null в случае отсутствия результата.
     */
    public function offsetGet($offset)
    {
        if(isset($this->_data[$offset]))
            return $this->_data[$offset];
        return null;
    }
    /**
     * Устанавливает значение по ключу, аналогичен $obj[$offset] = $value. В отличие от стрелочного
     * синтаксиса ($obj->{$offset}) не преобразует получаемые массивы в объекты BaseDataObject.
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * Ключ, по которому осуществляется присваивание.
     * </p>
     * @param mixed $value <p>
     * Устанавливаемое значение.
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
     * Удаляет значение по ключу. Идентичен unset($obj[$offset]).
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * Ключ удаляемой пары ключ-значение.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }
    // Конец реализации интерфейса ArrayAccess


    function _processProperties(\Closure $func, $obj, $includePrivate)
    {
        $func($obj, $this->_data);
        if($includePrivate)
            $func($obj, $this->_private_data);
    }

    /**
     * Возвращает список ключей объекта.
     * @param string $pattern Шаблон, по которому можно отсортировать ключи. Валидное регулярное выражение без
     * ограничивающих символов.
     * @param bool $includePrivate Указывает, добавлять ли в выбор приватные свойства объекта.
     * @return BaseDataObject
     */
    public function keys($pattern = '', $includePrivate = false){
        $bo = new BaseDataObject();
        $this->_processProperties(function ($bo, $source) use ($pattern){
            foreach ($source as $k=>$v){
                if(preg_match("~$pattern~", $k))
                    $bo[] = $k;
            }
        }, $bo, $includePrivate);
        return $bo;
    }

    /**
     * Возвращает список значений объекта.
     * @param array $filter
     * @param bool $includePrivate Указывает, добавлять ли в выбор приватные свойства объекта.
     * @return BaseDataObject
     */
    public function values($filter = [], $includePrivate = false){
        $bo = new BaseDataObject();
        $this->_processProperties(function ($bo, $source) use ($filter){
            foreach ($source as $v){
                $bo[] = $v;
            }
        }, $bo, $includePrivate);
        return $bo;
    }

    /**
     * Возвращает этот объект в виде массива. Если вложенные объекты имеют функцию toArray(), в массив подставляется
     * результат этой функции, иначе же используется преобразование в массив (array)$x. Если вложенный объект
     * является экземпляром BaseDataObject, то в его функцию toArray() передаётся $includePrivate.
     * @param bool $includePrivate Определяет, оставлять в результирующем массиве приватные свойства или нет.
     * Приватными свойствами считаются свойства, имя которых начинается со знака подчёркивания ("_")
     * @return array
     */
    public function toArray($includePrivate = false){
        $ret = [];
        $this->_processProperties(function ($includePrivate, $source)use(&$ret){
            foreach ($source as $k=>$v){
                if($v instanceof BaseDataObject) {
                    $ret[$k] = $v->toArray($includePrivate);
                } else if (is_object($v)){
                    if(method_exists($v, 'toArray'))
                        $ret[$k] = $v->toArray();
                    else
                        $ret[$k] = (array) $v;
                } else {
                    $ret[$k] = $v;
                }
            }
        }, $includePrivate, $includePrivate);
        return $ret;
    }

    /**
     * Возвращает этот объект в виде JSON-строки.
     * @param bool $includePrivate Определяет, оставлять в результате приватные свойства или нет.
     * Приватными свойствами считаются свойства, имя которых начинается со знака подчёркивания ("_")
     * @return string
     */
    public function toJson($includePrivate = false){
        return json_encode($this->toArray($includePrivate));
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
     * Возвращает копию объекта, свойства которого объединены со свойствами объекта $obj
     * @param BaseDataObject $obj Добавляемый объект.
     * @param bool $overwrite Указывает, надо ли перезапиывать свойства, если текущий объект уже имеет свойство с таким именем.
     * @return self
     */
    public function merge(BaseDataObject $obj, bool $overwrite = false, bool $includePrivate = false)
    {
        $res = $this->copy();
        foreach ($obj as $key => $value)
        {
            if(!isset($res->_data[$key]) || $overwrite)
                $res->_data[$key] = $value;
        }
        if($includePrivate){
            foreach ($obj->_private_data as $key => $value)
            {
                if(!isset($res->_private_data[$key]) || $overwrite)
                    $res->_private_data[$key] = $value;
            }
        }
        return $res;
    }

    /**
     * Поглощает данные передаваемого объекта. Свойства объекта копируются в текущий.
     * @param BaseDataObject $obj Поглощаемый объект
     * @param bool $overwrite Указывает, нужно ли перезаписывать поля.
     * @param bool $includePrivate Указывает, нужно ли перезаписывать приватные свойства (начинающиеся с символа
     * подчеркивания "_")
     * @return self
     */
    public function absorb(BaseDataObject $obj, bool $overwrite = false, bool $includePrivate = false)
    {
        foreach ($obj as $key => $value){
            if($overwrite || !isset($this[$key])) {
                $this[$key] = is_object($value) ? clone $value : $value;
            } else {
                continue;
            }
        }
        if($includePrivate){
            foreach ($obj->_private_data as $key => $value){
                if($overwrite || !isset($this->_private_data[$key])) {
                    $this->_private_data[$key] = is_object($value) ? clone $value : $value;
                } else {
                    continue;
                }
            }
        }
        return $this;
    }



}