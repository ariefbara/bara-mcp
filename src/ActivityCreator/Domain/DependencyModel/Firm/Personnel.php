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
    protected $removed;

    protected function __construct()
    {
        
    }

}
