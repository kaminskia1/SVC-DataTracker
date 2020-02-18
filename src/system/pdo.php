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
        // Try to establish a connection; set if successful
        try
        {
            static::$PDO = new \PDO($dsn, $user, $pass);
        } catch( \PDOException $e )
        {
            // Failure to connect, halt execution
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
        array_key_exists( strtolower( $call ), $this::typeCalls ) ? $arr = [ 'type' => $this::typeCalls[ strtolower( $call ) ] ] : null;

        // Check if table
        strtolower( $call ) === "table" ? $arr = [ 'table' => $args[0] ] : null;

        // Check if params
        strtolower( $call ) == "params" ? $arr = [ 'params' => $args[0] ] : null;

        // Check if limit
        strtolower( $call ) === "limit" ? $arr = [ 'limit' => "LIMIT $args[0]" ] : null;

        // Check if order
        strtolower( $call ) === "order" ? $arr = [ 'order' => "ORDER BY $args[0]" ] : null;

        // Check if where
        strtolower( $call ) === "where" ? $arr = [ 'where' => $args[0] ] : null;

        // Verify that call exists
        if ( !isset( $arr ) ) throw new \InvalidArgumentException();

        // Push to end of callstack
        $this->callStack = array_merge($this->callStack, $arr);
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
    private function _compileQuery(): string
    {
        /*
          Reference
            $data = [
            'type' => str
            'table' => str
            'params' => str
            'where' => str|array
            'limit' => int
            'order' => str
          ]; */

        $stmt = "";

        foreach ( $this->callStack as $k => $v )
        {
            switch ( $k )
            {
                case 'params':
                    switch ( $this->callStack['type'] )
                    {
                        case 'INSERT INTO':
                            $ik = [];
                            $iv = [];

                            // Check if array
                            if ( is_array( $v ) )
                            {
                                // Check array dimensions
                                if ( is_array( $v[0] ) )
                                {
                                    // Two dimensional
                                    foreach ( $v as $arr)
                                    {
                                        foreach ($arr as $jk => $jv)
                                        {
                                            // Separate keys and values into their own arrays
                                            if (!in_array($jk, $ik))
                                            {
                                                array_push($ik, $jk);
                                            }
                                            array_push($iv, $jv);
                                        }
                                        array_push($iv, "(" . implode(",", $iv) . ") ");
                                    }
                                }
                                else
                                {
                                    // One dimensional
                                    foreach ($k as $jk => $jv)
                                    {
                                        // Separate keys and values into their own arrays
                                        if (!in_array($jk, $ik))
                                        {
                                            array_push($ik, $jk);
                                        }
                                        array_push($iv, $jv);
                                    }
                                    $iv = "(" . implode(",", $iv) . ") ";
                                }
                                $ik = "(" . implode(",", $ik) . ") ";
                                $stmt .= $ik . " VALUES " . (is_array($iv) ? explode(",", $iv) : $iv) . " ";

                            }
                            else
                            {
                                $stmt .= "$v ";
                            }
                            break;
                        case 'SELECT':
                            $stmt .= (is_array($v) ? implode(",", $v) : $v) . " FROM ";
                            break;
                        default:
                            $stmt .= "$v ";
                    }
                    break;
                case 'where':
                    $stmt .= $this->_compileWhereClause($v) . " ";
                    break;

                case 'custom':
                    $stmt .= $v;
                default:
                    $stmt .= "$v ";
            }
        }
        return $stmt;
    }

    /**
     * Compile a where clause based off provided information
     *
     * @param $data
     * @return string
     */
    protected function _compileWhereClause($data): string
    {

    }


    /**
     * Run the query
     *
     * @param null|string $query
     * @return false|\PDOStatement|\SVC\System\PDOSelect
     */
    public function run( $query = null )
    {
        return $this->callStack['type'] === "SELECT" ? new \SVC\System\PDOSelect( static::$PDO->prepare( $query ?: $this->_compileQuery() ) ) : static::$PDO->prepare( $query ?: $this->_compileQuery() );
    }


}