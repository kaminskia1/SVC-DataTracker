<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class PDOSelect
{

    private $PDOStatement;

    public function __construct( \PDOStatement $p )
    {
        $this->PDOStatement = $p;
    }
}