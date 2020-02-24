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
                'id' => "personList",
                'title' => "View People",
                'table' => "person",
                'include' => [
                    'id',
                    'name_first',
                    'name_last',
                    'phone',
                    'date'
                ],
                'lang' => [
                    'id'         => 'User ID',
                    'name_first' => 'First Name',
                    'name_last'  => 'Last Name',
                    'phone'      => 'Phone Number',
                    'date'       => 'Date Added',
                ],
                'limit'    => 25,
                'cta'      => true,
                'cta_link' => "index.php?view=personView&id=",
                'process'  => [
                    'name_first' => function( $v )
                    {
                        return strtoupper(substr($v, 0, 1) ) . strtolower( substr( $v, 1 ) );
                    },
                    'name_last'  => function( $v )
                    {
                        return strtoupper(substr($v, 0, 1) ) . strtolower( substr( $v, 1 ) );
                    },
                    'phone'      => function( $v )
                    {
                        $v = \preg_replace( "/[^0-9]/", "", $v );
                        switch ( \strlen( (string)$v ) )
                        {
                            case 7:
                                return \substr( $v, 0, 3 ) . "-" . \substr( $v, 2 );
                                break;

                            case 10:
                                return "(" . \substr( $v,0,3) . ") " . \substr( $v, 3, 3 ) . "-" . \substr( $v, 6 );
                                break;

                            default:
                                return \strlen( (string)$v ) > 10 ? "+" . \substr ($v, 0, \strlen( $v ) - 10 ) . " (" . \substr( $v,\strlen( $v ) - 10,3 ) . ") " . \substr( $v, \strlen( $v ) - 7, 3 ) . "-" . \substr( $v, \strlen( $v ) - 4 ) : $v;
                                break;
                        }
                    },
                    'date'       => function( $v )
                    {
                        return \date( ' g:i:s A - M j, Y', \strtotime( $v ) );
                    }
                ]
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
        $table = \SVC\System\Table::createDB
        (
            [
                'id' => 'aidList',
                'title' => "View Aid",
                'table' => 'aid',
                'include' => [
                    'id',
                    'given',
                    'account',
                ],
                'lang' => [
                    'id'      =>'Entry ID',
                    'given'   =>'Amount Given',
                    'account' =>'Account',
                ],
                'limit'    => 25,
                'cta'      => true,
                'cta_link' => "index.php?view=personView&id=",
                'process'  => [
                    'given' => function( $v )
                    {
                        return "$ " . ( json_decode( $v )->amount ?? 0.0 );
                    },
                    'account'  => function( $v )
                    {
                        return "#" . $v;
                    },
                ]
            ]
        );


        return [ true, (string) $table ];
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