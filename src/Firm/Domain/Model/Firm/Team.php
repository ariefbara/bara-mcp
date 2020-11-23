<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;

class Team
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

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function idEquals(string $id): bool
    {
        return $this->id === $id;
    }

}
