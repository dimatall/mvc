<?php
namespace framework\core;

use framework\database\Connector;
use framework\helpers\UrlHelper;

/**
 * Class Application
 * @package framework\core
 */
class Application
{
    /**
     * Config file located at /app/config/config.php
     * @var array $config
     */
    private static $config;

    private static $db;

    public function __construct($config)
    {
        self::$config = $config;
    }

    /**
     * Bootstrap an application
     */
    public function run()
    {
        try {
            session_start();

            list($route, $params) = UrlHelper::parseUrl();
            echo $this->makeAction($route, $params);

        } catch (\Exception $e) {
            echo "<pre>{$e->getMessage()}\n{$e->getTraceAsString()}</pre>";
        }
    }

    /**
     * Get app configuration
     * @param string $key
     * @return array|mixed|null
     */
    public static function getConfig($key = '')
    {
        if (!$key) {
            return self::$config;
        } elseif (isset(self::$config[$key])) {
            return self::$config[$key];
        }
        return null;
    }

    /**
     * Get PDO instance
     * @return \PDO
     */
    public static function getDb()
    {
        if (self::$db instanceof \PDO) {
            return self::$db;
        }

        self::$db = Connector::createConnection(self::$config['db']['driver'])->connection();
        return self::$db;
    }

    /**
     * @return bool
     */
    public static function isGuest()
    {
        return !isset($_SESSION['userId']) || !$_SESSION['userId'];
    }

    /**
     * Do an action by passed route
     * @param $route
     * @param $params
     * @return mixed
     * @throws \Exception
     */
    private function makeAction($route, $params)
    {
        list($controllerId, $method) = UrlHelper::parseRoute($route);

        $controllerName = $controllerId ?: $controllerId = self::$config['defaultController'];
        $method = $method ?: self::$config['defaultMethod'];

        $controllerName = UrlHelper::dashesToCamelCase($controllerName, true);
        $method = UrlHelper::dashesToCamelCase($method, false);

        $controllerPath = self::$config['controllersNamespace'] . $controllerName . 'Controller';
        if (!class_exists($controllerPath) || !method_exists($controllerPath, $method)) {
            throw new \Exception('Invalid route');
        }

        $controller = new $controllerPath($controllerId);

        return call_user_func([$controller, $method], $params);
    }
}