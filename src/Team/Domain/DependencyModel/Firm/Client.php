<?php

namespace Team\Domain\DependencyModel\Firm;

use Resources\Exception\RegularException;
use Team\Domain\Model\Team;

class Client
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
    protected $activated;

    protected function __construct()
    {
        
    }
    
    public function createTeam(string $teamId, string $teamName, $memberPosition): Team
    {
        if (!$this->activated) {
            $errorDetail = "forbidden: only active client can make this request";
            throw RegularException::forbidden($errorDetail);
        }
        return new Team($this->firmId, $teamId, $this, $teamName, $memberPosition);
    }

}
