<?php

namespace SVC\Enum;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Report extends AbstractEnum
{

    /**
     * @internal Available Variables
     *
     * $id
     * $name
     * $data
     * $date
     * $last_edited
     *
     * $_data
     */

    /**
     * Report constructor.
     *
     * @param $p
     */
    public function __construct( $p )
    {
        parent::__construct( $p );
        $_dataDecode = json_decode($this->data);
    }
}