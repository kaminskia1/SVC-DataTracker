<?php

namespace SVC\System;

use Twig\Environment;

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
     * @var array Default table options
     */
    private $defaultArrayOptions = [
        'title'    => "Title",
        'id'      => "\SVC\System\Table",
        'page'    => 0,
            'pageTag' => "page",
        'include' => "*",
        'limit'   => 25,
        'lang' => [],
        'cta' => false,
        'cta_icon' => 'fa fa-arrow-right',
        'cta_link' => '',
        'process' => [],
        'forceAjax' => false,
    ];

    /**
     * @var array Default database table options
     */
    private $defaultDBOptions = [
        'title'    => "Title",
        'id'      => "\SVC\System\Table\DB",
        'include' => "*",
        'table'   => "",
        'where'   => "",
        'page'    => 0,
        'pageTag' => "page",
        'order'   => "id",
            'sort' => "ASC",
        'limit'   => 25,
        'lang' => [],
        'cta' => false,
            'cta_icon' => 'fa fa-arrow-right',
            'cta_link' => '',
        'process' => [],
        'forceAjax' => false,
    ];

    /**
     * Create a table from a 2D array
     *
     * @param array $data
     * @param array $options
     * @return static
     */
    public static function create( array $data, array $options ): self
    {
        // Create a fresh instance to emulate $this
        $table = new self();

        // Bind data
        $table->data = $data;

        // Merge user provided options onto default ones
        $table->options = array_merge($table->defaultArrayOptions, $options);

        // Return instance
        return $table;
    }

    /**
     * Create a table to model a database table
     *
     * @param array $options
     * @return Table
     */
    public static function createDB($options = [] ): self
    {
        // Create a fresh instance to emulate $this
        $table = new self();

        // Bind data
        $table->data = \SVC\System\PDO::i()->select();
        // Merge user provided options onto default ones
        $table->options = array_merge($table->defaultDBOptions, $options);

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
        // Check if in array
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
                    $key = $this->options['pageTag'];
                    $page = (int)\SVC\System\Request::i()->$key ?: 0;
                    if ( $this->data instanceof \SVC\System\PDO )
                    {
                        // Set order param, use LIMIT to emulate pagination
                        $this->data->order( $this->options['order'] . " " .  $this->options['sort'] . " LIMIT " . $this->options['limit'] . " OFFSET " . $page * $this->options['limit'] );
                       $pageMax = \ceil( \SVC\System\PDO::i()
                           ->select()
                           ->params("COUNT(*)")
                           ->table( $this->options['table'] )
                           ->run()
                           ->fetch()['COUNT(*)'] / $this->options['limit'] );
                    }
                    else
                    {
                        foreach( $this->data as $k => $v )
                        {
                            // Unset rows that do not fall within the page's range
                            if ( $key < $page * $this->options['limit'] || $key >= $page+1 * $this->options['limit'] ) unset( $this->data[$k] );
                        }
                    }
                    break;

                case 'where':
                    // Only applicable to TableDB, no if-else needed
                    $this->data->order( $this->options['where'] );
                    break;

                case 'table':
                    // Only applicable to TableDB, no if-else needed
                    $this->data->table( $this->options['table'] );
            }
        }

        // Attempt to query $data
        if ($this->data instanceof \SVC\System\PDO) {
            try
            {
                $this->data = $this->data->run()->fetchAll();
            } catch (\TypeError $e)
            {
                // Output error table if failure to query
                return \SVC\Init::$twig->load( "tableError.twig" )->render([
                    'options' => $this->options,
                    'error' => 'Internal Error: ' . $e->getMessage()
                ]);
            }
        }

        if ( count( $this->data ) > 0 )
        {
            // Data postprocessing
            if ( count( $this->options['process'] ) > 0 )
            {
                for ( $i=0; $i< count( $this->data ); $i++ )
                {
                    foreach ( $this->data[$i] as $k => $v)
                    {
                        if ( array_key_exists( $k, $this->options[ 'process' ] ) )
                        {
                            $this->data[ $i ][ $k ] = $this->options[ 'process' ] [ $k ] ( $v );
                        }
                    }
                }
            }

            // Table title lang
            if ( count( $this->options[ 'lang' ] ) > 0 )
            {
                $enc = json_encode( @$this->data[ 0 ] );
                foreach ( $this->options[ 'lang' ] as $old => $new )
                {
                    $enc = str_replace('"'.htmlspecialchars( $old ).'":', '"'.htmlspecialchars( $new ).'":', $enc );
                }
                $this->data[0] = (array)json_decode( $enc );
            }

            // Call to action
            if ( $this->options[ 'cta' ] === true )
            {
                for ( $i=0; $i < count( $this->data ); $i++ )
                {
                    $this->data[ $i ][ '_cta' ] = [
                        'icon' => $this->options[ 'cta_icon' ],
                        'link' => $this->options[ 'cta_link' ] . $this->data[ $i ][ array_keys( $this->data[ $i ] ) [ 0 ] ] ];
                }
            }

            // Render
            return \SVC\Init::$twig->load( ( \SVC\System\Request::i()->isAjax() && \SVC\System\Request::i()->pageAjax ) || $this->options['forceAjax'] ? "tableAjax.twig" : "table.twig" )->render([
                'data' => $this->data,
                'options' => $this->options,
                'page' => [
                    'current' => $page ?: 0,
                    'max' => @$pageMax - 1 ?: 0,
                ]
            ]);
        }

        return \SVC\Init::$twig->load( ( \SVC\System\Request::i()->isAjax() && \SVC\System\Request::i()->pageAjax ) || $this->options['forceAjax'] ? "tableAjaxError.twig" : "tableError.twig" )->render([
            'options' => $this->options,
            'error' => 'No data to show!'
        ]);
    }

}