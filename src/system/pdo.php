<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class PDO
{
    /**
     * @var \PDO Global database object
     */
    private static $PDO;

    /**
     * @var string Instance query
     */
    private $query;

    /**
     * @var array Query call stack
     */
    private $callStack = [];

    /**
     * @var array Available type calls
     */
    private const typeCalls = [
        "select" => "SELECT",
        "update" => "UPDATE",
        "insert" => "INSERT INTO",
        "delete" => "DELETE FROM",
        "drop" => "DROP TABLE",
    ];



    /**
     * Bind the SQLite database to the class
     *
     * @param string $dsn
     * @param string $user
     * @param string $pass
     */
    public static function assign( string $dsn, string $user = "", string $pass = "" ): void
    {
        try
        {
            static::$PDO = new \PDO($dsn, $user, $pass);
        } catch(\PDOException $e)
        {
            die("Could not bind to the database!");
        }
    }

    /**
     * Create a temporary instance from a static deceleration
     *
     * @return self
     */
    public static function i(): self
    {
        return new self();
    }

    /**
     * Combine all setter functions into one
     *
     * @param string $call
     * @param array $args [optional]
     * @return self
     * @throws \InvalidArgumentException
     */
    public function __call( string $call, array $args = [] ): self
    {
        // Check if type
        in_array( strtolower( $call ), $this::typeCalls ) ? $arr = [ 'type' => $this::typeCalls[ strtolower( $call ) ] ] : null;

        // Check if table
        strtolower( $call ) === "table" ? $arr = [ 'table' => $args[0] ] : null;

        // Check if params
        strtolower( $call ) == "params" ? $arr = [ 'params' => $args ] : null;

        // Check if limit
        strtolower( $call ) === "limit" ? $arr = [ 'limit' => "LIMIT $args[0]" ] : null;

        // Check if order
        strtolower( $call ) === "order" ? $arr = [ 'order' => "ORDER BY $args[0]" ] : null;

        // Verify that call exists
        if ( !isset( $arr ) ) throw new \InvalidArgumentException();

        array_merge($this->callStack, $arr);

        return $this;
    }

    /**
     * Push custom data to the callstack
     *
     * @param string $text
     * @param bool $padding [optional]
     * @param string $name [optional]
     * @return $this
     */
    private function add( string $text, bool $padding = true, string $name = 'custom' ): self
    {
        array_merge($this->callStack, [ $name => $padding ? " {$text} " : $text ] );
        return $this;
    }

    /**
     * Compile a query based off provided information
     *
     * @return void
     */
    private function _compileQuery(): void
    {
        /*
          Reference
            $data = [
            'type' => str
            'table' => str
            'params' => str
            'limit' => int
            'order' => str
          ]; */
    }

    /**
     * Run the query
     *
     * @param null|string $query
     * @return false|\PDOStatement
     */
    public function run( $query = null )
    {
        if ( isset( $this->type ) ) $this->_compileQuery();
        return static::$PDO->query( $query ?: $this->query );
    }


}