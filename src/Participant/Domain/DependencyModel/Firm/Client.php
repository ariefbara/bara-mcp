<?php

namespace Participant\Domain\DependencyModel\Firm;

class Client
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $activated = false;

    protected function __construct()
    {
        
    }

}
