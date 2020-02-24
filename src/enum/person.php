<?php

namespace SVC\Enum;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Person extends AbstractEnum
{

    private $id;

    private $name = (object)[
        'first' => '',
        'last'  => ''
    ];

    private $phone;

    private $address;

    private $family;

    private $employed = (object)[
        'state'   => false,
        'company' => ''
    ];

    private $shutoff = (object)[
        'state'      => false,
        'time'       => '',
        'referredby' => '',
    ];

    private $extra;

    public function __construct( array $p = [] )
    {
        if (count($p) < 1) throw new \InvalidArgumentException("No data provided!");
    }


    /**
     * Save the current record
     *
     * @return bool
     */
    public function save(): bool
    {

    }
}