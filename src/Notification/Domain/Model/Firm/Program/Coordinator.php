<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program;

class Coordinator
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
     * @var Personnel
     */
    protected $personnel;

    protected function __construct()
    {
        
    }

}
