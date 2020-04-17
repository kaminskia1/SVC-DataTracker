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
                'id'      => "personList",
                'title'   => "View People",
                'table'   => "person",
                'include' => [
                    'id',
                    'name_first',
                    'name_last',
                    'phone',
                    'last_edited'
                ],
                'lang' => [
                    'id'          => 'User ID',
                    'name_first'  => 'First Name',
                    'name_last'   => 'Last Name',
                    'phone'       => 'Phone Number',
                    'last_edited' => 'Last Modified',
                ],
                'limit'    => 25,
                'cta'      => true,
                'cta_link' => "view=personView&id=",
                'process'  => [
                    'name_first' => function( $v )
                    {
                        return strtoupper(substr($v, 0, 1) ) . strtolower( substr( $v, 1 ) );
                    },
                    'name_last' => function( $v )
                    {
                        return strtoupper(substr($v, 0, 1) ) . strtolower( substr( $v, 1 ) );
                    },
                    'phone' => function( $v )
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
                    'date' => function( $v )
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
        try {

            $view = \SVC\System\View::create
            (
                new \SVC\Enum\Person([ 'id' => \SVC\System\Request::i()->id ]),
                "personDisplay.twig"
            );

            return [ true, (string)$view ];
        }
        catch( \TypeError $e )
        {
            return [false, ""];
        }
        catch( \InvalidArgumentException $e )
        {
            return [false, ""];
        }
    }

    /**
     * Edit the person
     *
     * @return array
     */
    public static function personEdit(): array
    {
        // Check that person exists
        if ( is_null( \SVC\System\Request::i()->id ) ) return [ false, "" ];

        // Create the person
        $person = new \SVC\Enum\Person([ 'id' => \SVC\System\Request::i()->id ]);

        // Create the form
        $form = new \SVC\System\Form("personEdit", ['title' => "Edit Person"] );

        // Add form elements
        $form->add( "name_first", [
            'type'=>'text',
            'value'=> $person->name_first,
            'name' => 'First Name',
            'required' => true,
        ]);

        $form->add( "name_last", [
            'type'=>'text',
            'value'=> $person->name_first,
            'name' => 'Last Name',
            'required' => true,
        ]);

        $form->add( "phone", [
            'type'=>'number',
            'value'=> $person->phone,
            'min' => 10000000,
            'max' => 10000000000,
            'name' => "Phone",
            'required' => false,
        ]);

        $form->add( "address", [
            'type'=>'text',
            'value'=> $person->address,
            'name' => 'Address',
            'required' => false,
        ]);

        $form->add( "assistance", [
            'type'=>'text',
            'value'=> $person->assistance,
            'name' => 'Assistance',
            'required' => false,
        ]);

        $form->add( "shutoff", [
            'type'=>'boolean',
            'value'=> $person->shutoff,
            'controls' => ['shutoff_date', 'shutoff_referredby'],
            'name' => 'Service Shutoff',
            'required' => true,
        ]);

        $form->add( "shutoff_date", [
            'type'=>'text',
            'value'=> $person->shutoff_date,
            'name' => 'Service Shutoff - Date',
            'required' => false,
        ]);

        $form->add( "shutoff_referredby", [
            'type'=>'text',
            'value'=> $person->shutoff_referredby,
            'name' => 'Service Shutoff - Referred by',
            'required' => false,
        ]);

        $form->add( "employed", [
            'type'=>'boolean',
            'value'=> $person->employed,
            'controls' => ['employed_location'],
            'name' => 'Employed',
            'required' => true,
        ]);

        $form->add( "employed_location", [
            'type'=>'text',
            'value'=> $person->employed_location,
            'name' => 'Employed - Location',
            'required' => false,
        ]);

        $form->add( "family", [
            'type'=>'object',
            'value'=> $person->family,
            'base' => [
                'gender' => [ "Male", "Female" ],
                'age' => -1,
                'type' => [ "Child", "Adult", "Descendant" ]
            ],
            'name' => 'Family',
            'required' => false,
        ]);

        $form->add( "extra", [
            'type'=>'array',
            'value'=> $person->extra,
            'name' => 'Extra Data',
            'required' => false,
        ]);


        // Check if form has been submitted
        if ( $values = $form->values() )
        {

            return [true, json_encode( $values ) ];
        }

        // Return the form
        return [true, (string)$form ];
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
                'id'      => "aidList",
                'title'   => "View Aid",
                'table'   => "aid",
                'include' => [
                    'id',
                    'person_id',
                    'given',
                    'account',
                    'last_edited'
                ],
                'lang' => [
                    'id'          => "Entry ID",
                    'person_id'   => "Issued to",
                    'given'       => "Amount Issued",
                    'account'     => "Account",
                    'last_edited' => "Last Modified"
                ],
                'limit'    => 25,
                'cta'      => true,
                'cta_link' => "index.php?view=aidView&id=",
                'process'  => [
                    'given' => function( $v )
                    {
                        return "$ " . ( json_decode( $v )->amount ?? 0.0 );
                    },
                    'account' => function( $v )
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
        try
        {
            $view = \SVC\System\View::create
            (
                new \SVC\Enum\Person([ 'id' => \SVC\System\Request::i()->id ]),
                "aidDisplay.twig"
            );
            return [ true, (string)$view ];
        }
        catch( \TypeError $e )
        {
            return [false, ""];
        }
        catch( \InvalidArgumentException $e )
        {
            return [false, ""];
        }
    }

    /**
     * Report List Template
     *
     * @return array
     */
    public static function reportList(): array
    {
        $table = \SVC\System\Table::createDB
        (
            [
                'id'      => "reportList",
                'title'   => "View Reports",
                'table'   => "report",
                'include' => [
                    'id',
                    'name',
                    'date',
                    'last_edited'
                ],
                'lang' => [
                    'id'          =>'Entry ID',
                    'name'        => "Name",
                    'date'        =>'Generation Date',
                    'last_edited' => 'Last Modified'
                ],
                'limit'    => 25,
                'cta'      => true,
                'cta_link' => "index.php?view=reportView&id=",
                'process'  => [
                    'name' => function( $v )
                    {
                        return strtoupper(substr($v, 0, 1) ) . strtolower( substr( $v, 1 ) );
                    },
                    'date' => function( $v )
                    {
                        return \date( ' g:i:s A - M j, Y', \strtotime( $v ) );
                    },
                    'last_edited' => function( $v )
                    {
                        return \date( ' g:i:s A - M j, Y', \strtotime( $v ) );
                    }
                ]
            ]
        );

        return [ true, (string)$table ];
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