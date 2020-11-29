<?php

namespace ActivityCreator\Domain\DependencyModel\Firm;

use ActivityCreator\Domain\Model\Activity;

class Personnel
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
    protected $active;

    protected function __construct()
    {
        
    }

}
