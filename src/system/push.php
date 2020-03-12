<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class Push
{

    /**
     * Push constructor
     *
     * @return static
     */
    public static function i(): self
    {
        return new self();
    }

    /**
     * Update Person
     *
     * @return bool
     */
    public function updatePerson(): string
    {
        // Validate inputs
        if ( !(
            isset( \SVC\System\Request::i()->id ) && \is_integer( \SVC\System\Request::i()->id )
            && isset( \SVC\System\Request::i()->data ) && $data = \json_decode( \SVC\System\Request::i()->data ) != false
        ) ) return \json_encode([false, "Invalid parameters"]);
    }

    /**
     * Delete person
     *
     * @return bool
     */
    public function deletePerson(): string
    {
        // Validate inputs
        if ( \is_null( \SVC\System\Request::i()->id ) ) return json_encode([false, "Invalid parameters"]);

        if ( \SVC\System\PDO::i()->select()->params("*")->table("person")->where(["id" => \filter_var( \SVC\System\Request::i()->id, FILTER_SANITIZE_NUMBER_INT )])->run()->count > 0 )
        {
            \SVC\System\PDO::i()->delete()->table("person")->where(["id" => \filter_var( \SVC\System\Request::i()->id, FILTER_SANITIZE_NUMBER_INT )])->run();
            return \json_encode([true, "Success"]);
        }
        return json_encode([false, "ID Does not exist"]);
    }

    public function updateAid(): string
    {

    }

    public function deleteAid(): string
    {

    }


}