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
                $this->_data = (array)$p->fetch();
                break;

            // No 'break' intended here (converts to array and overflows into array case)
            case 'object':
                $p = (array)$p;
            case 'array':
                if (count($p) < 1) throw new \InvalidArgumentException("No data provided!");

                $rows = \SVC\System\PDO::i()->select()->params("*")->table( substr( strrchr( get_class( $this ), "\\"), 1) )->where($p)->run();

                if ( $rows->count() < 1 ) throw new \PDOException('No rows found!');

                foreach ( $rows->fetch( $i ) as $k => $v )
                {
                    $this->$k = $v;
                }
                $this->_data = (array)$rows->fetch();
                break;

            case 'integer':
                $rows = \SVC\System\PDO::i()->select()->params("*")->table( substr( strrchr( get_class( $this ), "\\"), 1) )->where(['id'=>$p])->run();

                if ( $rows->count() < 1 ) throw new \PDOException('No rows found!');

                foreach ( $rows->fetch( $i ) as $k => $v )
                {
                    $this->$k = $v;
                }
                $this->_data = (array)$rows->fetch();
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
        var_dump((array)$this);
        foreach ( (array)$this as $k => $v)
        {
            if ( ( substr($k, 0, 1) != "_" ) && array_key_exists( $k, $this->_data ) && (array)$this->_data[ $k ] !== $v )
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

    /**
     * Serialize the current instance
     *
     * @return string
     */
    public function serialize(): string
    {
        return json_encode( $this );
    }

}