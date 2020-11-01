<?php

namespace Participant\Domain\DependencyModel\Firm\Team;

use Participant\Domain\ {
    DependencyModel\Firm\Team,
    SharedModel\FileInfo
};

class TeamFileInfo
{

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var FileInfo
     */
    protected $fileInfo;
    
    /**
     *
     * @var bool
     */
    protected $removed;
    
    protected function __construct()
    {
    }
    
    public function belongsToTeam(Team $team): bool
    {
        return $this->team === $team;
    }

}
