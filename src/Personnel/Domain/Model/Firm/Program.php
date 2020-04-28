<?php

namespace Personnel\Domain\Model\Firm;

use Personnel\Domain\Model\Firm;

class Program
{

    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $description = null;

    /**
     *
     * @var bool
     */
    protected $published = false;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    protected function __construct()
    {
        ;
    }
}
