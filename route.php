<?php


class Route
{
    private static $hooks = [];
    private static $uri;

    public static function get($text, callable $func)
    {
        self::request(null, $text, $func, true, true);
    }    
    
    public static function regex($pattern, callable $func)
    {
        self::request($pattern, null, $func, false, true);
    }

    public static function run($skip_query = false)
    {
        $found = false;
        self::$uri = $_SERVER['REQUEST_URI'];

        if ($skip_query) {
            self::$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        foreach(self::$hooks as $hook)
        {
            if (!empty($hook['text']) && $hook['text'] === self::$uri) {
                $found = true;
                call_user_func_array($hook['func'], []);
            } elseif(!empty($hook['pattern']) && preg_match($hook['pattern'], self::$uri, $matches)) {
                $found = true;
                array_shift($matches);
                call_user_func_array($hook['func'], $matches);
            } else {
                continue;
            }

            if ($hook['break']) {
                return true;
            }
        }

        return $found;
    }

    private static function request($pattern, $text, callable $func, $priority = false, $break = true)
    {
        $hook = [
            'pattern'   => $pattern,
            'text'      => $text,
            'func'      => $func,
            'priority'  => $priority,
            'break'     => $break,
        ];

        $priority ? array_unshift(self::$hooks, $hook) : self::$hooks[] = $hook;
    }
}
