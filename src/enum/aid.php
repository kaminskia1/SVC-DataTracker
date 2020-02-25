<?php

namespace SVC\Enum;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Aid extends AbstractEnum
{
    /**
     * Aid constructor.
     *
     * @param array $p
     */
    public function __construct( array $p = [] )
    {
        if ( count( $p ) < 1 ) throw new \InvalidArgumentException( "No data provided!" );

        $rows = \SVC\System\PDO::i()->select()->params( "*" )->table( 'Aid' )->where( $p )->run();

        if ( $rows->count() < 1 ) throw new \PDOException( 'No rows found!' );

        foreach ( $this->first() as $k => $v )
        {
            $this->$k = $v;
        }
        $this->_data = (array)$this->first();
    }


    /**
     * Save the current record
     *
     * @return bool
     */
    public function save(): bool
    {
        // Compile params
        $p = [];
        foreach ( (array)$this as $k => $v)
        {
            if ( (array)$this->_data[$k] !== $v)
            {
                array_push( $p, [ $k => $v ] );
            }
        }

        // Run update query and return response
        return (bool)@\SVC\System\PDO::i()->update()->table( 'Aid' )->params( (array)$p )->where([ 'id' => $this->id ]) ?? false;
    }
}