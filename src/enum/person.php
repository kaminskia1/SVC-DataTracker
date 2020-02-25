<?php

namespace SVC\Enum;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Person extends AbstractEnum
{

    /**
     * @internal Available Variables
     *
     * // Table Columns
     * $id
     * $date
     * $name_first
     * $name_last
     * $phone
     * $address
     * $assistance
     * $shutoff
     * $shutoff_date
     * $shutoff_referredby
     * $family
     * $employed
     * $employed_location
     * $last_edited
     *
     * // Enum Variables
     * $_aidEntries
     * $_data
     */

    /**
     * Person constructor.
     *
     * @param $p
     */
    public function __construct( $p )
    {
        parent::__construct( $p );
        $q = \SVC\System\PDO::i()->select()->params('*')->table('Aid')->where([ 'id' => $this->id ])->run();
        $arr = [];
        for ($i=0;$i<$q->count();$i++)
        {
            array_push($arr, new \SVC\Enum\Report($q, $i) );
        }
        $this->_aidEntries = $arr;
    }

}