<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class View
{

    private $enum;

    private $template;

    public static function create( \SVC\Enum\AbstractEnum $enum, $template ): self
    {
        $x = new self();
        $x->enum = $enum;
        $x->template = $template;
        return $x;
    }

    public function __toString(): string
    {
        return \SVC\Init::$twig->load( $this->template )->render( $this->enum->encode() );
    }

}
