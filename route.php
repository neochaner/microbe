<?php


class Route
{

    static $filters = [];
    static $hooks = [];

    public static function get($path, $function)
    {
        $paths = is_array($path) ? $path : [$path];

        foreach($paths as $p) {

            $params = self::getUriParams($p);

            self::$hooks[] = [
                'params' => $params,
                'func' => $function
            ];
        }
    }


    public static function filter($start, $function)
    {
        self::$filters[] = [
            'start' => $start,
            'func' => $function
        ];
    }


    public static function run()
    {
        $paths = explode('/', $_SERVER['REQUEST_URI']);

        foreach(self::$filters as $filter) {
            if (strrpos($_SERVER['REQUEST_URI'], $filter['start']) === 0) {
                call_user_func_array($filter['func'], []);
            }
        }

        foreach(self::$hooks as $hook) {

            $args = [];

            if (self::compareParams($paths, $hook['params'], $args)) {
                call_user_func_array($hook['func'], $args);
                return true;
            }
        }

        return false;
    }

    private static function getUriParams($query)
    {
        $paths = explode("/", $query);
        return array_map(function($args) { return new UriParam($args); }, $paths);
    }

    private static function compareParams($paths, $params, &$args)
    {
        $args = [];
        $length = count($paths);
        $i = 0;

        foreach ($params as $param) {

            if ($length <= $i) {
                return false;
            }

            $path = $paths[$i];

            if ($path === '' && $param->path !== '') {
                return false;
            }

            if ($path === '' && $param->path === '') {
                $i++;
                continue;
            }

            if ($param->isParam) {
                $args[] = ($param->paramName === 'num') ? (int)$path : $path;

            } else {

                if ($param->path != $path) {
                    return false;
                }
            }

            $i++;
            continue;
        }
        
        return $length === $i;
    }

 




}




class UriParam
{

    public $isParam;
    public $paramName;
    public $path;

    function __construct($path)
    {
        $this->path         = $path;
        $this->isParam      = false;

        $length = strlen($path);

        if ($length <= 2) {
            return false;
        }

        if ($path[0] === '{' && $path[$length-1] === '}') {
            
            $this->isParam = true;

            if ($path[$length-2] === '?') {
                // $this->isVarious = true;
                $this->paramName = substr($path, 1, $length-3);
            } else {
                // $this->isVarious = false;
                $this->paramName = substr($path, 1, $length-2);
            }
        }
    }
}
