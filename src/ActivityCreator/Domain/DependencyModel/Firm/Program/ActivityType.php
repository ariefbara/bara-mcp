<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program;

use ActivityCreator\Domain\DependencyModel\Firm\Program;
use Doctrine\Common\Collections\ArrayCollection;

class ActivityType
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
     * @var ArrayCollection
     */
    protected $participants;

    protected function __construct()
    {
        
    }

}
