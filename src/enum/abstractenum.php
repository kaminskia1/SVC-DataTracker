<?php

namespace SVC\Enum;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

abstract class AbstractEnum
{
    // Implement read-only and abstract getters and setters
    use \SVC\Traits\ReadOnly;
    use \SVC\Traits\AbstractGetSet;

    protected $_data;

    public function __construct( $p, $i = 0 )
    {
        switch ( \gettype ( $p ) )
        {
            case '\SVC\System\PDOSelect':
                if (count($p) < 1) throw new \InvalidArgumentException("No data provided!");

                if ( $p->count() < 1 ) throw new \PDOException('No rows found!');

                foreach ( $p->first( $i ) as $k => $v )
                {
                    $this->$k = $v;
                }
                $this->_data = (array)$p->first();
                break;

            case 'object':
                $p = (array)$p;
            case 'array':
                if (count($p) < 1) throw new \InvalidArgumentException("No data provided!");

                $rows = \SVC\System\PDO::i()->select()->params("*")->table( get_class($this) )->where($p)->run();

                if ( $rows->count() < 1 ) throw new \PDOException('No rows found!');

                foreach ( $rows->fetch( $i ) as $k => $v )
                {
                    $this->$k = $v;
                }
                $this->_data = (array)$rows->first();
                break;

            default:
                throw new \InvalidArgumentException("Invalid data provided");

        }
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
        return (bool)@\SVC\System\PDO::i()->update()->table( get_class( $this ) )->params( (array)$p )->where([ 'id' => $this->id ]) ?? false;
    }

    /**
     * Encode the current record
     *
     * @return array
     */
    public function encode(): array
    {
        return (array)$this;
    }

}