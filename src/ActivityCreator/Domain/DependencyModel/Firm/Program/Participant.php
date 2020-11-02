<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program;

use ActivityCreator\Domain\DependencyModel\Firm\Program;

class Participant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }

}
