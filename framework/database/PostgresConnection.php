<?php

namespace framework\database;

use framework\core\Application;

class PostgresConnection implements ConnectionInterface
{
    public function connection()
    {
        $config = Application::getConfig('db');

        $conn = new \PDO("pgsql:host={$config['host']};dbname={$config['name']}", $config['username'], $config['password']);
        $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        return $conn;
    }
}