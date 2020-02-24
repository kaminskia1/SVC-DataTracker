<?php

namespace SVC\Enum;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Aid extends AbstractEnum
{

    private $_id;

    private $_person_id;

    private $given;

    private $account;

    private $rent;

    private $landlord_address;

    private $extra;

    private $_last_edited;

    public function __construct( array $a = [] )
    {
        switch ( array_keys($a)[0] )
        {
            case 'id':
                break;
            case 'person_id':
                break;

        }
    }

    /**
     * Save the current enumeration
     *
     * @return bool
     */
    public function save(): bool
    {
        return (bool)\SVC\System\PDO::i()->update()->table( 'aid' )->params( (array)$this )->where("`id` = " . $this->_id );
    }
}