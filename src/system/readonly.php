<?php

namespace SVC\System;

trait ReadOnly
{
    /**
     * Allow for calling private elements as "Read Only"
     *
     * @param $var
     * @return mixed
     */
    public function __get( $var )
    {
        // Default to false if it doesn't exist
        return $this->$var ?? false;
    }
}