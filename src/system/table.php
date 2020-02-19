<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Table
{

    /**
     * @var array|PDO Table data
     */
    private $data;

    /**
     * @var array Table options
     */
    private $options = [];

    /**
     * @var array Default Table options
     */
    private $defaultArrayOptions = [
        'id'      => "\SVC\System\Table",
        'pageTag' => "page",
        'page'    => 0,
        'include' => "*",
        'limit'   => 25,
    ];

    private $defaultDBOptions = [
        'id'      => "\SVC\System\Table",
        'pageTag' => "page",
        'page'    => 0,
        'include' => "*",
        'table'   => "",
        'order'   => "ASC",
        'limit'   => 25,
    ]

    /**
     * Create a table from a 2D array
     *
     * @param array $data
     * @param array $options
     * @return static
     */
    public static function create( array $options ): self
    {
        // Create a fresh instance to emulate $this
        $table = new self();

        // Bind data
        $table->data = \SVC\System\PDO::i()->select();

        // Merge user provided options onto default ones
        $table->options = array_merge($table->defaultOptions, $options);

        // Return instance
        return $table;
    }

    /**
     * Create a table to model a database table
     *
     * @param PDO $data
     * @param array $options
     * @return Table
     */
    public static function createDB( \SVC\System\PDO $data, $options = [] ): self
    {
        // Create a fresh instance to emulate $this
        $table = new self();

        // Bind data
        $table->data = $data;

        // Merge user provided options onto default ones
        $table->options = array_merge($table->defaultOptions, $options);

        // Return instance
        return $table;
    }

    /**
     * Allow for dynamic setting of options
     *
     * @param $name
     * @param $arguments
     * @return Table
     */
    public function __call($name, $arguments): self
    {
        if ( in_array( $this->options, $name ) )
        {
            $this->options[$name] = $arguments;
            return $this;
        }
        throw new \InvalidArgumentException( "Invalid call!" );
    }

    /**
     * Convert the table into something viewable
     *
     * @return string
     */
    public function __toString(): string
    {
        foreach ( $this->options as $option => $val )
        {
            switch ( $option )
            {
                case 'id':
                    break;
                case 'order':
                    break;
                case 'include':
                    if ( $this->data instanceof \SVC\System\PDO )
                    {
                        // Set select param to value in \SVC\System\PDO instance callstack
                        $this->data->params( is_array( $val ) ? implode( ",", $val ) : $val );
                    }
                    else
                    {
                        // Explode value into indexible array if string provided
                        if ( is_string($val) ) $val = explode( ",", $val );

                        // Cycle through each row
                        foreach ($this->data as $i => $row)
                        {
                            // Cycle through each collumn value
                            foreach ($row as $k => $v)
                            {
                                // Check if key is present in include array
                                if ( !in_array( $k, $val ) )
                                {
                                    // Pop!
                                    unset( $this->data[$i][$k] );
                                }
                            }
                        }
                    }
                    break;
                case 'page':

                    break;
            }
        }



        return \SVC\Init::$twig->prepare("table.twig")->render([$this->data]);
    }

}