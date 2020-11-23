<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;

class Client
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
     * @var bool
     */
    protected $activated = false;
    
    protected function __construct()
    {
        
    }
}
