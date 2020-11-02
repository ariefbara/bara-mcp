<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Personnel;

use ActivityCreator\Domain\DependencyModel\Firm\{
    Personnel,
    Program
};

class ProgramConsultation
{

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var bool
     */
    protected $removed;

    protected function __construct()
    {
        
    }

}
