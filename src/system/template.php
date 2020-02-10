<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Template
{
    /**
     * Security is tight here, as any listed public, static, function can be called by the requester.
     */

    public static function dashboard(): bool
    {
        return false;
    }

    public static function personList(): bool
    {
        return false;
    }

    public static function personAdd(): bool
    {
        return false;
    }

    public static function personView(): bool
    {
        return false;
    }

    public static function aidList(): bool
    {
        return false;
    }
    public static function aidAdd(): bool
    {
        return false;
    }

    public static function aidView(): bool
    {
        return false;
    }

    public static function reportList(): bool
    {
        return false;
    }
    public static function reportGenerate(): bool
    {
        return false;
    }
}