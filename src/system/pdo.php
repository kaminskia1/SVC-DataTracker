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
    public $callStack = [];

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
        // Try to establish a connection; bind to static variable if successful
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

        // Cycle through each element in the callstack
        foreach ( $this->callStack as $k => $v )
        {
            switch ( $k )
            {
                case 'params':
                    switch ( $this->callStack['type'] )
                    {
                        case 'INSERT INTO': /* ->insert() */
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

                                            // Push row into column
                                            array_push($iv, $jv);
                                        }

                                        // Push column into array
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

                                        // Push row into column
                                        array_push($iv, $jv);
                                    }

                                    // Convert array into string and add parenthesis padding ( ['a','b','c'] => "(a,b,c)" )
                                    $iv = "(" . implode(",", $iv) . ") ";
                                }

                                // Bind data
                                $ik = "(" . implode(",", $ik) . ") ";

                                // Combine values and data into a single query
                                $stmt .= $ik . " VALUES " . (is_array($iv) ? explode(",", $iv) : $iv) . " ";

                            }
                            else
                            {
                                // Assume string if provided data is unrecognizable
                                $stmt .= "$v ";
                            }
                            break;

                        case 'SELECT': /* ->select() */

                            // Explode array into column names, assume to string if not array
                            $stmt .= ( is_array( $v ) ? implode( ",", $v) : $v ) . " FROM ";
                            break;

                        default:
                            $stmt .= "$v ";
                    }
                    break;

                // Send to where compiler
                case 'where':
                    $stmt .= $this->_compileWhereClause($v) . " ";
                    break;

                // Append without padding on end
                case 'custom':
                    $stmt .= $v;
                    break;

                // Append with padding on end
                default:
                    $stmt .= "$v ";
            }
        }
        return $stmt;
    }

    /**
     * Compile a where clause based off provided information
     *
     * @TODO Finish this
     *
     * @param $data
     * @return string
     */
    protected function _compileWhereClause( $data ): string
    {
        return $data;
    }


    /**
     * Run the query
     *
     * @param null|string $query
     * @return false|\PDOStatement|\SVC\System\PDOSelect
     */
    public function run( $query = null )
    {
        if ( $this->callStack['type'] === "SELECT" )
        {
            // Return an instance of \SVC\System\PDOSelect if query type is select
            return new \SVC\System\PDOSelect( static::$PDO->prepare( $query ?: $this->_compileQuery() ) );
        }
        else
        {
            // Prepare the statement
            $a = static::$PDO->prepare( $query ?: $this->_compileQuery() );

            // Execute the statement
            $a->execute();

            // Commit the change
            static::$PDO->commit();

            // Return the query response
            return $a;
        }
    }

    /**
     * Commit the change
     *
     * @return bool
     */
    public function commit(): bool
    {
        return static::$PDO->commit();
    }

}