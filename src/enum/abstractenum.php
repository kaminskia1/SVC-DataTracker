<?php

namespace SVC\Enum;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

abstract class AbstractEnum
{
    // Implement read-only and abstract getters and setters
    use \SVC\Traits\ReadOnly;
    use \SVC\Traits\AbstractGetSet;

    protected $_data;

    abstract public function __construct();

    abstract public function save(): bool;

    public function format(\Closure $func, $key): self
    {
        $this->$key = $func( $this->$key );
    }

}