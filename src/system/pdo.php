<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class PDO
{
    private static $PDO;

    private $query;

    /**
     * Bind the SQLite database to the class
     *
     * @param $dsn
     * @param string $user
     * @param string $pass
     */
    public static function assign( $dsn, string $user = "", string $pass = "" )
    {
        static::$PDO = new \PDO( $dsn, $user, $pass );
        static::$PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);

    }

    public static function i(): self
    {
        return new self();
    }

    public function run( $query = null )
    {
        return static::$PDO->query( $query ?: $this->query );
    }


}