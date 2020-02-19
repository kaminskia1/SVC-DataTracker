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

    /**
     * Dashboard Template
     *
     * @return array
     */
    public static function dashboard(): array
    {
        return [true, \SVC\Init::$twig->load("dashboard.twig")->render([
            'user' => 'User',
            'cards' => [
                [
                    'icon' => 'fa fa-users',
                    'name' => "View People",
                    'content' => 'Default Content',
                    'callback' => 'personList'
                ],
                [
                    'icon' => 'fa fa-plus',
                    'name' => "Add Person",
                    'content' => 'Default Content',
                    'callback' => 'personAdd'
                ],
                [
                    'icon' => 'fa fa-money',
                    'name' => "View Aid",
                    'content' => 'Default Content',
                    'callback' => 'aidList'
                ],
                [
                    'icon' => 'fa fa-plus',
                    'name' => "Add Aid",
                    'content' => 'Default Content',
                    'callback' => 'aidAdd'
                ],
                [
                    'icon' => 'fa fa-file',
                    'name' => "View Reports",
                    'content' => 'Default Content',
                    'callback' => 'reportList'
                ],
                [
                    'icon' => 'fa fa-plus',
                    'name' => "Add Reports",
                    'content' => 'Default Content',
                    'callback' => 'reportAdd'
                ],
            ]
        ])];
    }

    /**
     * Person List Template
     *
     * @return array
     */
    public static function personList(): array
    {
        $table = \SVC\System\Table::createDB
        (
            [
                'id' => 'personList',
                'table' => 'person'
            ]
        );


        return [ true, (string) $table ];
    }

    /**
     * Person Add Template
     *
     * @return array
     */
    public static function personAdd(): array
    {
        return [true, ""];
    }

    /**
     * Person View Template
     *
     * @return array
     */
    public static function personView(): array
    {
        return [true, ""];
    }

    /**
     * Aid List Template
     *
     * @return array
     */
    public static function aidList(): array
    {
        return [true, ""];
    }

    /**
     * Aid Add Template
     *
     * @return array
     */
    public static function aidAdd(): array
    {
        return [true, ""];
    }

    /**
     * Aid View Template
     *
     * @return array
     */
    public static function aidView(): array
    {
        return [true, ""];
    }

    /**
     * Report List Template
     *
     * @return array
     */
    public static function reportList(): array
    {
        return [true, ""];
    }

    /**
     * Report Add Template
     *
     * @return array
     */
    public static function reportAdd(): array
    {
        return [true, ""];
    }
}