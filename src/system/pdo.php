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
     * @var string Query type
     */
    private $type;

    /**
     * @var string Query table
     */
    private $table;

    /**
     * @var string Query params
     */
    private $params;

    /**
     * @var string Query row limit
     */
    private $limit;

    /**
     * @var string Query order direction
     */
    private $order;

    /**
     * Bind the SQLite database to the class
     *
     * @param $dsn
     * @param string $user
     * @param string $pass
     */
    public static function assign( $dsn, string $user = "", string $pass = "" ): void
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
     * Set the query type to select
     *
     * @return $this
     */
    public function select(): self
    {
        $this->type = "SELECT";
        return $this;
    }

    /**
     * Set the query type to insert
     *
     * @return $this
     */
    public function insert(): self
    {
        $this->type = "INSERT";
        return $this;
    }

    /**
     * Set the query type to delete
     *
     * @return $this
     */
    public function delete(): self
    {
        $this->type = "DELETE FROM";
        return $this;
    }

    /**
     * Set the query type to drop
     *
     * @return $this
     */
    public function drop(): self
    {
        $this->type = "DROP TABLE";
        return $this;
    }

    /**
     * Set the query type to update
     *
     * @return $this
     */
    public function update(): self
    {
        $this->type = "UPDATE";
        return $this;
    }

    /**
     * Compile a query based off provided information
     *
     * @return void
     */
    private function _compileQuery(): void
    {
        $data = [
            'type' => $this->type,
            'table' => $this->table,
            'params' => $this->params,
            'limit' => $this->limit,
            'order' => $this->order,
        ];
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