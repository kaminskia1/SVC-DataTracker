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
    private $options = [];

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
    public function __construct( $id, $title, $options = [] )
    {
        $options['id'] = $id;
        $options['title'] = $title;
        $this->key = "form_" . $id;
        $this->options = $options;
    }

    /**
     * Add an item
     *
     * @param $id
     * @param array $options
     */
    public function add( $id, $options = [] ): void
    {
        if ( isset( $this->values[$id] ) ) $options['value'] = $this->values[$id];
        array_merge( $this->callStack, [$id =>$options] );
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
        /**
         * @TODO Check that ' and "" don't get replaced by \Request::i()
         */
        if ( is_null( $new = \SVC\System\Request::i()->$this->key ) || !json_decode( \SVC\System\Request::i()->$this->key ) ) return false;

        // Cycle through all
        foreach ( $new as $key => $val )
        {
            // Verify that the callstack entry exists
            if ( isset( $this->callStack[$key] ) )
            {
                /**
                 * @todo Sanitize the new keys
                 */
                // Set the value to the new entry
                $this->values[$key] = $val;
            }
        }

        return $this->values;
    }

}