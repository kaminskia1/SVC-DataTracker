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

    /**
     * Person List Template
     *
     * @return array
     */
    public static function personList(): array
    {
        $query = \SVC\System\PDO::i()
            ->select()
            ->params("*")
            ->table("person")
            ->run();
        var_dump($query->fetch());
        $query->next();
        var_dump($query->fetch());


        return [true, ""];
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