<?php

namespace Personnel\Domain\Model\Firm\Personnel;

use Personnel\Domain\Model\Firm\ {
    Personnel,
    Program
};

class ProgramConsultant
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
