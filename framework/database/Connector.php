<?php

namespace framework\database;

class Connector
{
    /**
     * Create connection via factory method
     * @param $drive
     * @return ConnectionInterface
     */
    public static function createConnection($drive)
    {
        switch ($drive) {
            case 'mysql':
                return new MysqlConnection();
                break;
            case 'postgres':
                return new  PostgresConnection();
                break;
        }
    }
}