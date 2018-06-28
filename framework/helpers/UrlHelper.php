<?php
/**
 * Created by PhpStorm.
 * User: dimatall
 * Date: 26.06.18
 * Time: 15:11
 */

namespace framework\helpers;


/**
 * Class UrlHelper
 * @package framework\core
 */
class UrlHelper
{
    /**
     * Get route from url
     * @return array
     */
    public static function parseUrl()
    {
        $route = trim($_SERVER['QUERY_STRING'], '/');
        $params = explode('/', $route);
        $params = array_slice($params, 2);

        return [$route, $params];
    }

    /**
     * Get controller and method by route
     * @param $route
     * @return array
     */
    public static function parseRoute($route)
    {
        $method = '';
        $controller = '';
        $route = explode('/', $route);

        if (isset($route[0])) {
            $controller = $route[0];
        }
        if (isset($route[1])) {
            $method = $route[1];
        }

        return [$controller, $method];
    }

    /**
     * @param $string
     * @param bool $firstToUpper
     * @return mixed
     */
    public static function dashesToCamelCase($string, $firstToUpper = false)
    {
        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

        if (!$firstToUpper) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }

    /**
     * @param $route
     */
    public static function redirect($route)
    {
        $route = trim($route, '/');
        $url = $_SERVER['HTTP_ORIGIN'] .'/' . $route;

        return header("Location:$url");
    }

}