<?php


namespace Bedivierre\Craftsman\Cartography;


use Bedivierre\Craftsman\Masonry\BaseDataObject;

class UriRequest
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * @var Router $router
     */
    private $router;
    private $basePath = '';
    private $uri = '';
    private $fullUri = '';
    private $query = '';
    private $fullPath = '';
    private $path = '';
    private $method = '';
    /**
     * @var BaseDataObject
     */
    private $getParams;
    /**
     * @var BaseDataObject
     */
    private $postParams;
    /**
     * @var BaseDataObject
     */
    private $pathArray;
    /**
     * @var BaseDataObject
     */
    private $fullPathArray;

    /** @var BaseDataObject $mutators */
    private static $mutators;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->basePath = $router->getBasePath();
        $this->method = $router->getRequestMethod();

        self::preset_mutators();

        $uri = substr(rawurldecode($_SERVER['REQUEST_URI']), strlen($router->getBasePath()));
        $fullUri = rawurldecode($_SERVER['REQUEST_URI']);

        $this->uri = '/' . trim($uri, '/');
        $this->fullUri = '/' . trim($fullUri, '/');

        //query params
        if (strstr($fullUri, '?')) {
            $fullUri = substr($fullUri, 0, strpos($fullUri, '?'));
            $query = substr($uri, strpos($uri, '?') + 1);
            $this->query = $query;
            $uri = substr($uri, 0, strpos($uri, '?'));
        }

        $this->path = '/' . trim($uri, '/');
        $this->fullPath = '/' . trim($fullUri, '/');

        $this->postParams = new BaseDataObject($_POST);
        $this->getParams = new BaseDataObject($_GET);

        $this->pathArray = new BaseDataObject(explode('/', trim($uri, '/')));
        $this->fullPathArray = new BaseDataObject(explode('/', trim($fullUri, '/')));

    }
    static function preset_mutators(){
        self::addMutator('i', function ($v){return (int) $v;});
        self::addMutator('f', function ($v){return (float) $v;});
        self::addMutator('b', function ($v){
            if(!$v || strtolower($v) == 'false')
                return false;
            return true;
        });
        self::addMutator('s', function ($v) {
            if(is_string($v))
                return $v;
            if(is_object($v) && method_exists($v, 'toString'))
                return $v->toString();
            try{
                $ret = strval($v);
            } catch (\Exception $ex){
                $ret = '';
            }
            return $ret;
        });
        self::addMutator('j', function ($v){return ($ret = json_decode($v)) ? $ret : $v;});
        self::addMutator('jo', function ($v){return ($ret = json_decode($v)) ? $ret : $v;});
        self::addMutator('ja', function ($v){return ($ret = json_decode($v, true)) ? $ret : $v;});
        self::addMutator('bdo', function ($v){return new BaseDataObject($v);});
    }

    /**
     * Добавляет мутатор для функций get и post.
     * @param string $type текстовый идентификатор для мутатора. Используется в $this->get($key, $type)
     * @param $func Функция-мутатор. Должна принимать значение get или post параметра и возвращать измененное значение
     */
    public static function addMutator(string $type, $func){
        if(!is_callable($func) && !is_string($func))
            return;
        if(!self::$mutators)
            self::$mutators = new BaseDataObject();
        self::$mutators->{$type} = $func;
    }
    static function callMutator($type, $value){
        if(!is_callable(self::$mutators->{$type}))
            return $value;
        return call_user_func(self::$mutators->{$type}, $value);
    }

    /**
     * Получает текущий относительный URI.
     * @return string
     */
    public function getUri() : string
    {
        return $this->uri;
    }
    public function getFullUri() : string
    {
        return $this->fullUri;
    }
    public function getQuery() : string
    {
        return $this->query;
    }
    public function getPath() : string
    {
        return $this->path;
    }
    public function getFullPath() : string
    {
        return $this->fullPath;
    }


    static function mutate_value(string $type, $value){
        if(!$type)
            return $value;
        return self::callMutator($type, $value);
    }
    /**
     * @param string $key
     * @param string $type если пустая строка - возвращается как есть, также имеются встроенные мутаторы -
     *          s - string, i - int, f - float, b - bool (строка 'false' воспринимается как false,
     *          j или jo - json_decode($val), ja - json_decode($val, true), bdo - BaseDataObject($val).
     *          Если указанный мутатор не существует, возвращается само значение.
     * @param null|mixed $default Значение по умолчанию, возвращаемое при отсутствии параметра с таким ключом.
     *          Проходит через мутаторы.
     * @return string|int|float|bool
     */
    public function get(string $key, string $type = '', $default = null){
        if(is_null($res = $this->getParams->getDataByPath($key)))
            $res = $default;
        return self::mutate_value($type, $res);
    }

    /**
     * Возвращает BaseDataObject со всеми get-параметрами.
     * @return BaseDataObject
     */
    public function getAll() : BaseDataObject{
        return $this->getParams;
    }
    /**
     * @param string $key
     * @param string $type если пустая строка - возвращается как есть, также имеются встроенные мутаторы -
     *          s - string, i - int, f - float, b - bool (строка 'false' воспринимается как false,
     *          j или jo - json_decode($val), ja - json_decode($val, true), bdo - BaseDataObject($val).
     *          Если указанный мутатор не существует, возвращается само значение.
     * @param null|mixed $default Значение по умолчанию, возвращаемое при отсутствии параметра с таким ключом.
     *          Проходит через мутаторы.
     * @return string|int|float|bool
     */
    public function post(string $key, string $type = '', $default = null){
        if(is_null($res = $this->postParams->getDataByPath($key)))
            $res = $default;
        return self::mutate_value($type, $res);
    }
    /**
     * Возвращает BaseDataObject со всеми post-параметрами.
     * @return BaseDataObject
     */
    public function postAll() : BaseDataObject{
        return $this->postParams;
    }

    public function getPathArray() : BaseDataObject{
        return $this->pathArray;
    }
    public function getFullPathArray() : BaseDataObject{
        return $this->fullPathArray;
    }

    public function getRouter() : Router{
        return $this->router;
    }
}