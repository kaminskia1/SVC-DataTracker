<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class PDOSelect
{

    /**
     * @var \PDOStatement PDO Select result
     */
    private $PDOStatement;

    /**
     * PDOSelect constructor.
     * @param \PDOStatement $p
     */
    public function __construct( \PDOStatement $p )
    {
        $this->PDOStatement = $p;
    }
}