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
     * @internal Available Variables
     *
     * // Table Columns
     * $id
     * $person_id
     * $date
     * $given
     * $account
     * $rent
     * $landlord_address
     * $extra
     * $last_edited
     *
     * // Enum Variables
     * $_aidRecipient
     * $_data
     */

    /**
     * Aid constructor.
     *
     * @param $p
     */
    public function __construct( $p )
    {
        parent::__construct( $p );
        $this->_aidRecipient = new \SVC\Enum\Person( \SVC\System\PDO::i()->select()->param('*')->table('Person')->where(['id'=>$this->person_id])->run() );
    }

}