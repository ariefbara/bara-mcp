<?php

namespace Client\Domain\Model\Client;

use Client\Domain\Model\Client;
use Resources\Exception\RegularException;

class TeamMembership
{
    /**
     *
     * @var Client
     */
    protected $client;

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
    
    public function quit(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: already inactive member";
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = false;
    }

}
