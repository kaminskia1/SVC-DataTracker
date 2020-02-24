<?php

namespace SVC\Traits;

trait AbstractGetSet
{
    /**
     * Abstract getting / setting
     *
     * @internal prefix the variable with _ to exclude
     *
     * @param $k
     * @param $v
     * @return bool|mixed
     */
    public function __call( $k, $v )
    {
        // Check if get
        if ( strlen($k) > 3 )
        {
            // Set call name to a temporary variable, as PHP doesn't allow for inline interpretation of function calls as pointer variables
            $call = substr($k, 2);

            // Check if getter
            if ( substr($k, 0, 3) === 'get')
            {
                // Return value if element exists, failure if not
                return isset($this->$call) ? $this->$call : false;
            }

            // Check if setter
            if ( substr($k, 0, 3) === 'set' )
            {
                $call = substr($k, 2);
                if (isset($this->$call) && gettype($v[0]) == gettype($this->$call) && substr($v, 0, 1) != "_" )
                {
                    // Check if value is same type
                    $this->$call = $v[0];

                    // Return success
                    return true;
                }
            }
        }

        // Not called, return failure
        return false;
    }
}