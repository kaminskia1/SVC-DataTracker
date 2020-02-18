<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Push
{

    /**
     * Push constructor
     *
     * @return static
     */
    public static function i(): self
    {

    }


}