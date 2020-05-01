<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Form
{

    /**
     * @var string Form ID
     */
    private $id;

    /**
     * @var string Form access key (form_ + $id)
     */
    private $key;

    /**
     * @var array Form options
     *
     * Allowed input types:
     * [
     *  'string' => ['minlength', 'maxlength'],
     *  'number' => ['min', 'max'],
     *  'object => ['addition', 'depth'],
     *  'bool'
     * ]
     */
    private $defaultOptions = [
        'id' => "form",
        'title' => "Form",
        'cancel' => "",
        'lang' => []
    ];

    /**
     * Default element options
     */
    private $defaultElementOptions = [
        'text' => [
            '_valueType' => 'string',
            'min' => 0,
            'max' => 255,
            'required' => false,
        ],
        'number' => [
            '_valueType' => "integer",
            'min' => 0,
            'max' => 255,
            'required' => false,
        ],
        'object' => [
            '_valueType' => 'object',
            'values' => [],
            'required' => false,
        ],
        'select' => [
            '_valueType' => 'array',
            'pool' => [],
            'required' => false,
        ],
        'boolean' => [
            '_valueType' => 'boolean',
            'controls' => [],
            'required' => false,
        ],
        'array' => [
            '_valueType' => 'array',
            'values' => [],
            'required' => false,
        ]
    ];

    /**
     * @var array Default form values
     */
    private $values = [];

    /**
     * @var array Form element stack
     */
    private $callStack = [];

    /**
     * Form constructor.
     *
     * @param string $id Form ID
     * @param array $options
     */
    public function __construct( string $id, array $options = [] )
    {
        $this->key = "form_" . $id;
        $this->options = array_merge($this->defaultOptions, $options);
        $this->options['id'] = $id;
    }

    /**
     * Add an item
     *
     * @param $id
     * @param array $options
     */
    public function add( $id, $options = [] ): void
    {
        // Verify that the provided type is set and that it exists in the existing set
        if ( \is_null( $options['type'] ) || \is_null( $this->defaultElementOptions[ $options['type'] ] ) ) throw new \InvalidArgumentException("Invalid element type provided");

        // Remove read-only values from the provided options (keys prefixed with _)
        foreach ( $options as $k => $v )
        {
            if ( \mb_substr( $k, 0, 1 ) == "_" )
            {
                unset( $options[ $k ] );
            }
        }

        // Add the language key
        $this->options['lang'][$id] = $options['name'] ?? $id;

        // Decode values if object / array
        if ( $options['type'] == "array" || $options['type'] == "object" ) $options['values'] = (object)json_decode($options['value']);

        // Merge provided options onto default element options
        $options = \array_merge( $this->defaultElementOptions[ $options['type'] ], $options );

        /**
         * @TODO Validate provided value against the base object before adding to callstack, throw nonfatal error and set to blank if invalid
         * @TODO Validate that provided value datatype is equal to the defaultElementOptions one
         */

        $this->callStack[$id] = $options;
    }

    /**
     * Compile the form into a template
     *
     * @return string
     */
    public function __toString(): string
    {
        return \SVC\Init::$twig->load( "form.twig" )->render([
            'callstack' => $this->callStack,
            'options'   => $this->options
        ]);
    }

    /**
     * Grab the submitted values
     *
     * @return boolean|array
     */
    public function values()
    {
        if ( is_null( \SVC\System\Request::i()->confirm ) || \SVC\System\Request::i()->confirm != true ) return false;

        /**
         * @TODO Validate JSON structure and provided values to prevent unintended injection
         */
        $key = $this->key;
        if ( is_null( \SVC\System\Request::i()->$key ) || !json_decode( \SVC\System\Request::i()->raw( $key ) ) ) return false;

        // Cycle through all
        foreach ( json_decode( \SVC\System\Request::i()->raw( $key ) ) as $key => $val )
        {
            // Verify that the callstack entry exists
            if ( isset( $this->callStack[$key] ) )
            {
                // Verify that information exists if required entry
                if ( $this->callStack[$key]['required'] && ( $val == null || $val == "" ) && $this->callStack[$key]['type'] != "boolean")
                {
                    // Initialize _error if not already declared
                    if ( !isset($this->values['_error'] ) ) $this->values['_error'] = [];

                    // Add element key onto error stack
                    array_push( $this->values['_error'], $key );
                }
                else
                {
                    /**
                     * @todo Sanitize the new values
                     */
                    $this->values[$key] = $val;
                }

                // Set the value to the new entry
                $this->values[$key] = $val;
            }
        }

        return $this->values;
    }

}