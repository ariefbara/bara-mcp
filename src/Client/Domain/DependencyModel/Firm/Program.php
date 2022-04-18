<?php

namespace Client\Domain\DependencyModel\Firm;

use Client\Domain\DependencyModel\Firm;

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

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

}
