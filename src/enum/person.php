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
     * $id -
     * $date -
     * $name_first -
     * $name_last -
     * $phone -
     * $address -
     * $assistance -
     * $shutoff -
     * $shutoff_date -
     * $shutoff_referredby -
     * $family -
     * $employed
     * $employed_location
     * $last_edited
     * $extra
     *
     * // Enum Variables
     * $_data
     * $_formattedPhone
     * $_formattedFamily
     * $_formattedDate
     * $_totalAidGiven
     * $_aidTable
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

        // Format phone
        $v = \preg_replace( "/[^0-9]/", "", $this->phone );
        switch ( \strlen( (string)$v ) )
        {
            case 7:
                $v = \substr( $v, 0, 3 ) . "-" . \substr( $v, 2 );
                break;

            case 10:
                $v = "(" . \substr( $v,0,3) . ") " . \substr( $v, 3, 3 ) . "-" . \substr( $v, 6 );
                break;

            default:
                $v = \strlen( (string)$v ) > 10 ? "+" . \substr ($v, 0, \strlen( $v ) - 10 ) . " (" . \substr( $v,\strlen( $v ) - 10,3 ) . ") " . \substr( $v, \strlen( $v ) - 7, 3 ) . "-" . \substr( $v, \strlen( $v ) - 4 ) : $v;
                break;
        }

        // Define custom variables
        $this->_formattedPhone = $v;
        $this->_aidEntries = $arr;
        $this->_formattedExtra = (array)json_decode($this->extra);
        $this->_formattedFamily = (array)json_decode($this->family);
        $this->_formattedShutoff_date = \date( 'M j, Y', \strtotime( $this->shutoff_date ) );
        $this->_formattedDate = \date( 'M j, Y', \strtotime( $this->date ) );
        $this->_totalAidGiven = (float)0.00;
        $this->_aidTable = \SVC\System\Table::create( $arr, [
            'title' => "",
            'forceAjax' => true,
            'limit' => 8,
            'cta' => true
        ] );

        foreach( $this->_aidEntries as $v )
        {
            $this->_totalAidGiven += $v->given;
        }
    }

}