<?php

namespace Payment\Domain\Model\Firm;

class Program
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    public function getName(): string
    {
        return $this->name;
    }

    protected function __construct()
    {
        
    }

}
