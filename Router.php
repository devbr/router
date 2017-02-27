<?php
/**
 * Lib\Router
 * PHP version 7
 *
 * @category  Access
 * @package   Librarys
 * @author    Bill Rocha <prbr@ymail.com>
 * @copyright 2016 Bill Rocha <http://google.com/+BillRocha>
 * @license   <https://opensource.org/licenses/MIT> MIT
 * @version   GIT: 0.0.1
 * @link      http://paulorocha.tk/devbr
 */
namespace Lib;

/**
 * Router Class
 *
 * @category Access
 * @package  Librarys
 * @author   Bill Rocha <prbr@ymail.com>
 * @license  <https://opensource.org/licenses/MIT> MIT
 * @link     http://paulorocha.tk/devbr
 */
class Router
{
    private $url = '';
    private $http = '';
    private $base = '';
    private $request = '';
    private $routers = [];
    private $params = [];
    private $all = [];
    private $method = 'GET';
    private $separator = '::';
    private $controller = '';
    private $action = '';
    private $defaultController = 'Resource\Main';
    private $defaultAction = 'pageNotFound';
    
    //namespace prefix for MVC systems - ex.: '\Controller'
    private $namespacePrefix = '';
    private static $node = null;
    private static $ctrl = null;
    
    //GETs -----------------------------------------------------------------
    function getUrl()
    {
        return $this->url;
    }
    function getHttp()
    {
        return $this->http;
    }
    function getBase()
    {
        return $this->base;
    }
    function getRequest()
    {
        return $this->request;
    }
    function getRouters()
    {
        return $this->routers;
    }
    function getAll()
    {
        return $this->all;
    }
    function getMethod()
    {
        return $this->method;
    }
    
    function getController()
    {
        return $this->controller;
    }
    function getAction()
    {
        return $this->action;
    }
    function getSeparator()
    {
        return $this->separator;
    }
    function getParams()
    {
        return count($this->params) > 0 ? $this->params : null;
    }
    //SETs -----------------------------------------------------------------
    function setSeparator($v)
    {
        $this->separator = $v;
        return $this;
    }
    
    function setDefaultController($v)
    {
        $this->defaultController = trim( str_replace('/', '\\', $v), '\\/ ');
        return $this;
    }
    
    function setDefaultAction($v)
    {
        $this->defaultAction = trim($v, '\\/ ');
        return $this;
    }
    
    function setNamespacePrefix($v)
    {
        $this->namespacePrefix = $v === '' ? '' : '\\'.trim( str_replace('/', '\\', $v), '\\/ ');
        return $this;
    }
    
    /**
     * Constructor
     */
    function __construct(
        $request = null,
        $url = null
    ) {
        if ($request !== null) {
            define('_RQST', $request);
        }
        if ($url !== null) {
            define('_URL', $url);
        }
        
        $this->method = $this->requestMethod();
        $this->mount();
    }
    /**
     * Singleton instance
     *
     */
    static function this()
    {
        if (is_object(static::$node)) {
            return static::$node;
        }
        //else...
        list($routers, $request, $url) = array_merge(func_get_args(), [null, null, null]);
        return static::$node = new static($routers, $request, $url);
    }
    
    /**
     *  Get Controller Object
     */
    static function getCtrl()
    {
        return static::$ctrl;
    }
    
    /**
     * Make happen...
     *
     */
    function run()
    {
        //Load configurations
        if (class_exists('\Config\Router\Router')) {
            new \Config\Router\Router;
        }

        //Resolve request
        $this->resolve();
        
        //If is a CALLBACK...
        if (is_object($this->controller)) {
            exit(call_user_func_array($this->controller, [$this->request, $this->params]));
        }
        if ($this->controller === null) {
            $this->controller = $this->defaultController;
        }
        if ($this->action === null) {
            $this->action = $this->defaultAction;
        }

        //Name format to Controller namespace
        $tmp = explode('\\', str_replace('/', '\\', $this->controller));
        $ctrl = $this->namespacePrefix;
        foreach ($tmp as $tmp1) {
            $ctrl .= '\\'.ucfirst($tmp1);
        }
        //save controller param
        $this->controller = $ctrl;
        
        //instantiate the controller
        if (class_exists($ctrl)) {
            static::$ctrl = new $ctrl(['params' => $this->params, 'request' => $this->request]);
        } else {
            header("HTTP/1.0 404 Not Found");
            exit('Page not Found!');
        }

        if (!method_exists(static::$ctrl, $this->action)) {
            $this->action = $this->defaultAction;
        }

        return call_user_func_array([static::$ctrl, $this->action],
                                    [$this->request, $this->params]);
    }

    /**
     * Resolve routers
     *
     */
    function resolve()
    {
        //first: serach in ALL
        $route = $this->searchRouter($this->all);
        //now: search for access method
        if ($route === false && isset($this->routers[$this->method])) {
            $route = $this->searchRouter($this->routers[$this->method]);
        }
        //not match...
        if ($route === false) {
            $route['controller'] = $route['action'] = $route['params'] = $route['request'] = null;
        }
        //set params
        $this->controller = $route['controller'];
        $this->action = $route['action'];
        $this->params = $route['params'];
        //out with decoded router OR all null
        return $route;
    }
    /**
     * Insert/config routers
     *
     */
    function respond(
        $method = 'all',
        $request = '',
        $controller = null,
        $action = null
    ) {
    
        $method = strtoupper(trim($method));
        //Para sintaxe: CONTROLLER::ACTION
        if (!is_object($controller) && strpos($controller, $this->separator) !== false) {
            $a = explode($this->separator, $controller);
            $controller = isset($a[0]) ? $a[0] : null;
            $action = isset($a[1]) ? $a[1] : null;
        }
        if ($method == 'ALL') {
            $this->all[] = ['request' => trim($request, '/'), 'controller' => $controller, 'action' => $action];
        } else {
            foreach (explode('|', $method) as $mtd) {
                $this->routers[$mtd][] = ['request' => trim($request, '/'), 'controller' => $controller, 'action' => $action];
            }
        }
            return $this;
    }
    /**
     * Mount
     */
    private function mount()
    {
        //Detect SSL access
        if (!isset($_SERVER['SERVER_PORT'])) {
            $_SERVER['SERVER_PORT'] = 80;
        }
        $http = (isset($_SERVER['HTTPS']) && ($_SERVER["HTTPS"] == "on" || $_SERVER["HTTPS"] == 1 || $_SERVER['SERVER_PORT'] == 443)) ? 'https://' : 'http://';
        //What's base??!
        $base = isset($_SERVER['PHAR_SCRIPT_NAME']) ? dirname($_SERVER['PHAR_SCRIPT_NAME']) : rtrim(str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']), ' /');
        if ($_SERVER['SERVER_PORT'] != 80  && $_SERVER['SERVER_PORT'] != 443) {
            $base .= ':' . $_SERVER['SERVER_PORT'];
        }
        //URL & REQST Constants:
        defined('_RQST') || define('_RQST', urldecode(isset($_SERVER['REQUEST_URI']) ? urldecode(trim(str_replace($base, '', trim($_SERVER['REQUEST_URI'])), ' /')) : ''));
        defined('_URL') || define('_URL', isset($_SERVER['SERVER_NAME']) ? $http . $_SERVER['SERVER_NAME'] . $base . '/' : '');
        $this->request = _RQST;
        $this->url = _URL;
        $this->base = $base;
        $this->http = $http;
    }
    /**
     * Search for valide router
     *
     * @params
     */
    private function searchRouter($routes)
    {
        foreach ($routes as $route) {
            if ($route['controller'] === null
                  || !preg_match_all('#^' . $route['request'] . '$#',
                        $this->request,
                        $matches,
                        PREG_SET_ORDER)
                  ) {
                continue;
            }
            $route['params'] = array_slice($matches[0], 1);
            return $route;
        }
        //não existe rotas
        return false;
    }
    /**
     * Get all request headers
     * @return array The request headers
     */
    private function requestHeaders()
    {
        // getallheaders available, use that
        if (function_exists('getallheaders')) {
            return getallheaders();
        }
        // getallheaders not available: manually extract 'm
        $headers = array();
        foreach ($_SERVER as $name => $value) {
            if ((substr($name, 0, 5) == 'HTTP_') || ($name == 'CONTENT_TYPE') || ($name == 'CONTENT_LENGTH')) {
                $headers[str_replace(array(' ', 'Http'), array('-', 'HTTP'), ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
    /**
     * Get the request method used, taking overrides into account
     * @return string The Request method to handle
     */
    private function requestMethod()
    {
        // Take the method as found in $_SERVER
        $method = $_SERVER['REQUEST_METHOD'];
        if ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            ob_start();
            $method = 'GET';
        } // If it's a POST request, check for a method override header
        elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $headers = $this->requestHeaders();
            if (isset($headers['X-HTTP-Method-Override']) && in_array($headers['X-HTTP-Method-Override'], array('PUT', 'DELETE', 'PATCH'))) {
                $method = $headers['X-HTTP-Method-Override'];
            }
        }
        return $method;
    }
}
