<?php

namespace SVC\System;

if ( !defined("ENABLE") || @ENABLE != true )
{
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

class PDOSelect
{

    /**
     * @internal This implementation is built to manage small(er) chunks of data, a proper interface would implement proper cursoring in \PDOStatement
     */

    /**
     * @var \PDOStatement PDO Select object
     */
    public $PDOStatement;

    /**
     * @var array Select data
     */
    private $data = [];

    /**
     * @var int Current row
     */
    public $row = 0;

    /**
     * @var int Total number of rows
     */
    public $count;


    /**
     * PDOSelect constructor.
     * @param \PDOStatement $p
     */
    public function __construct( \PDOStatement $p )
    {
        $p->execute();
        $this->PDOStatement = $p;
        $this->data = $p->fetchAll(\PDO::FETCH_ASSOC);
        $this->count = count($this->data);
    }

    /**
     * Cycle current row back one space
     *
     * @return $this
     */
    public function previous(): self
    {
        // Set to last row number if subtracting will cause an \OutOfBoundsException()
        $this->row -1 < 0 ? $this->row = $this->count - 1 : $this->row -= 1;
        return $this;
    }

    /**
     * Cycle current row forward one space
     *
     * @return $this
     */
    public function next(): self
    {
        // Set to 0 if adding will cause an \OutOfBoundsException()
        $this->row + 1 < $this->count ? $this->row += 1 : $this->row = 0;
        return $this;
    }

    /**
     * Fetch current row
     *
     * @param $r Row number to fetch
     * @return array
     */
    public function fetch( $r = null ): array
    {
        return $this->data[$r ?? $this->row];
    }

    /**
     * Fetch all rows
     *
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->data;
    }

    /**
     * Retrieve total number of rows
     *
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }

    /**
     * Retrieve current row number
     *
     * @return int
     */
    public function row(): int
    {
        return $this->row;
    }

}