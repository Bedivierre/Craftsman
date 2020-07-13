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

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->basePath = $router->getBasePath();
        $this->method = $router->getRequestMethod();

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


    /**
     * Define the current relative URI.
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

    /**
     * @param string $key
     * @param string $type s - string, i - int, f - float, b - bool
     * @return string|int|float|bool
     */
    public function get(string $key, string $type = 's'){
        $res = $this->getParams->{$key};
        switch ($type){
            case 'i':
                return (int)$res;
            case 'f':
                return (float)$res;
            case 'b':
                if($res && (float)$res != 0 && strtolower($res) != 'false')
                    return true;
                return false;
            case 's':
            default:
                return $res ? $res : '';
        }
    }
    public function getAll() : BaseDataObject{
        return $this->getParams;
    }
    public function post(string $key, string $type = 's'){
        $res = $this->postParams->{$key};
        switch ($type){
            case 'i':
                return (int)$res;
            case 'f':
                return (float)$res;
            case 'b':
                if($res && (float)$res != 0 && strtolower($res) != 'false')
                    return true;
                return false;
            case 's':
            default:
                return $res ? $res : '';
        }
    }
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