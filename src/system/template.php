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
//                [
//                    'icon' => 'fa fa-file',
//                    'name' => "View Reports",
//                    'content' => 'Default Content',
//                    'callback' => 'reportList'
//                ],
//                [
//                    'icon' => 'fa fa-plus',
//                    'name' => "Add Reports",
//                    'content' => 'Default Content',
//                    'callback' => 'reportAdd'
//                ],
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

        // Create the form
        $form = new \SVC\System\Form("personAdd", ['title' => "Add Person", 'cancel' => "dashboard"] );

        $form->add( "name_first", [
            'type'=>'text',
            'value'=> "",
            'name' => 'First Name',
            'required' => true,
        ]);

        $form->add( "name_last", [
            'type'=>'text',
            'value'=> "",
            'name' => 'Last Name',
            'required' => true,
        ]);

        $form->add( "phone", [
            'type'=>'number',
            'value'=> "",
            'min' => 10000000,
            'max' => 10000000000,
            'name' => "Phone",
            'required' => false,
        ]);

        $form->add( "address", [
            'type'=>'text',
            'value'=> "",
            'name' => 'Address',
            'required' => false,
        ]);

        $form->add( "assistance", [
            'type'=>'text',
            'value'=> "",
            'name' => 'Assistance',
            'required' => false,
        ]);

        $form->add( "shutoff", [
            'type'=>'boolean',
            'value'=> false,
            'controls' => ['shutoff_date', 'shutoff_referredby'],
            'name' => 'Service Shutoff',
            'required' => true,
        ]);

        $form->add( "shutoff_date", [
            'type'=>'text',
            'value'=> "",
            'name' => 'Service Shutoff - Date',
            'required' => false,
        ]);

        $form->add( "shutoff_referredby", [
            'type'=>'text',
            'value'=> "",
            'name' => 'Service Shutoff - Referred by',
            'required' => false,
        ]);

        $form->add( "employed", [
            'type'=>'boolean',
            'value'=> false,
            'controls' => ['employed_location'],
            'name' => 'Employed',
            'required' => true,
        ]);

        $form->add( "employed_location", [
            'type'=>'text',
            'value'=> "",
            'name' => 'Employed - Location',
            'required' => false,
        ]);

        $form->add( "family", [
            'type'=>'object',
            'value'=> "{}",
            'base' => [
                'Gender' => [ "Male", "Female" ],
                'Age' => -1,
                'Type' => [ "Child", "Adult", "Descendant" ]
            ],
            'name' => 'Family',
            'required' => false,
        ]);

        $form->add( "extra", [
            'type'=>'array',
            'value'=> "{}",
            'name' => 'Extra Data',
            'required' => false,
        ]);

        if ( $values = $form->values() )
        {
            // Return new person if success, error if incomplete
            return isset($values['_error']) ? [true, json_encode($values['_error'])] : [\SVC\System\PDO::i()->insert()->table("Person")->params($values)->run(), 'personList'];
        }

        return [true, (string)$form];
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
            return [true, "<b>TypeError:</b> " + print_r($e)];
        }
        catch( \InvalidArgumentException $e )
        {
            return [true, "<b>Invalid Argument Exception:</b> " + print_r($e)];
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
        $form = new \SVC\System\Form("personEdit", ['title' => "Edit Person", 'cancel' => "personView"] );

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
                'Gender' => [ "Male", "Female" ],
                'Age' => -1,
                'Type' => [ "Child", "Adult", "Descendant" ]
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
            // Return new person if success, error if incomplete
            return isset($values['_error']) ? [true, json_encode($values['_error'])] : [(bool)\SVC\System\PDO::i()->update()->table("Person")->params((array)$values)->where([ 'id' => \SVC\System\Request::i()->id ])->run(), 'personView'];
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
                'cta_link' => "view=aidView&id=",
                'process'  => [
                    'person_id' => function( $v )
                    {
                        return implode(", ", \SVC\System\PDO::i()->select()->params("name_first,name_last")->table("Person")->where(['id'=>$v])->run()->fetch() );
                    },
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
        $form = new \SVC\System\Form( "aidAdd", ['title' => "Add Aid", 'cancel' => "dashboard"] );

        // Build pool
        $pool = [];
        $pdo = \SVC\System\PDO::i()->select()->params("id,name_first,name_last")->table("Person")->run();
        for ($i=0;$i<$pdo->count();$i++)
        {
            $pool[$pdo->fetch()['id']] = $pdo->fetch()['id'] . ": " . $pdo->fetch()['name_last'] . ", " . $pdo->fetch()['name_first'];
            $pdo->next();
        }

        $form->add( "person_id", [
            'type' =>'select',
            'value'=> \SVC\System\Request::i()->id,
            'name' => 'Person ID',
            'pool' => $pool,
            'required' => true,
        ]);

        $form->add( "date", [
            'type' =>'text',
            'value'=> "",
            'name' => 'Date',
            'required' => true,
        ]);

        $form->add( "account", [
            'type'=>'number',
            'value'=> "",
            'name' => 'Account',
            'required' => true,
        ]);

        $form->add( "given", [
            'type'=>'text',
            'value'=> "",
            'name' => 'Amount Given',
            'required' => true,
        ]);

        $form->add( "rent", [
            'type'=>'text',
            'value'=> "",
            'name' => 'Rent',
            'required' => true,
        ]);

        $form->add( "landlord_address", [
            'type'=>'text',
            'value'=> "",
            'name' => 'Landlord Address',
            'required' => true,
        ]);

        $form->add( "extra", [
            'type'=>'array',
            'value'=> "{}",
            'name' => 'Extra',
            'required' => true,
        ]);

        if ( $values = $form->values() )
        {
            // Return new person if success, error if incomplete
            return isset($values['_error']) ? [true, json_encode($values['_error'])] : [\SVC\System\PDO::i()->insert()->table("Aid")->params($values)->run(), 'aidList'];
        }

        return [true, (string)$form];
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
                new \SVC\Enum\Aid([ 'id' => \SVC\System\Request::i()->id ]),
                "aidDisplay.twig"
            );
            return [ true, (string)$view ];
        }
        catch( \TypeError $e )
        {
            return [true, "TypeError"];
        }
        catch( \InvalidArgumentException $e )
        {
            return [true, $e];
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