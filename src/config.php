<?php

namespace SVC;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Config
{
    /**
     * Debugging state
     *
     * @var bool
     */
    public static $debug = true;

    /**
     * Location of the asset directory
     *
     * @var string
     */
    public static $assetDirectory = "assets\\";

}

