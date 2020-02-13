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

    public static function dashboard(): array
    {
        return [true, \SVC\Init::$twig->load("dashboard.twig")->render([
            'cards' => [
                [
                    'icon' => 'fa fa-users',
                    'name' => "View People"
                ],
                [
                    'icon' => 'fa fa-plus',
                    'name' => "Add Person"
                ],
                [
                    'icon' => 'fa fa-money',
                    'name' => "View Aid"
                ],
                [
                    'icon' => 'fa fa-plus',
                    'name' => "Add Aid"
                ],
                [
                    'icon' => 'fa fa-file',
                    'name' => "View Reports"
                ],
                [
                    'icon' => 'fa fa-plus',
                    'name' => "Add Reports"
                ],
            ]
        ])];
    }

    public static function personList(): array
    {
        $query = \SVC\System\PDO::i()
            ->select()
            ->params("*")
            ->table("person")
            ->run();
        var_dump($query);
        return [true, ""];
    }

    public static function personAdd(): array
    {
        return [true, ""];
    }

    public static function personView(): array
    {
        return [true, ""];
    }

    public static function aidList(): array
    {
        return [true, ""];
    }

    public static function aidAdd(): array
    {
        return [true, ""];
    }

    public static function aidView(): array
    {
        return [true, ""];
    }

    public static function reportList(): array
    {
        return [true, ""];
    }

    public static function reportGenerate(): array
    {
        return [true, ""];
    }
}