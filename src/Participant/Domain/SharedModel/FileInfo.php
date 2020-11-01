<?php

namespace Participant\Domain\SharedModel;

use Participant\Domain\DependencyModel\{
    Firm\Client,
    Firm\Client\ClientFileInfo,
    Firm\Team,
    Firm\Team\TeamFileInfo,
    User\UserFileInfo
};

class FileInfo
{

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     *
     * @var ClientFileInfo|null
     */
    protected $clientFileInfo;

    /**
     *
     * @var UserFileInfo|null
     */
    protected $userFileInfo;

    /**
     *
     * @var TeamFileInfo|null
     */
    protected $teamFileInfo;

    protected function __construct()
    {
        
    }

    public function belongsToClient(Client $client): bool
    {
        return isset($this->clientFileInfo) ? $this->clientFileInfo->belongsToClient($client) : false;
    }

    public function belongsToUser(string $userId): bool
    {
        return isset($this->userFileInfo) ? $this->userFileInfo->belongsToUser($userId) : false;
    }

    public function belongsToTeam(Team $team): bool
    {
        return isset($this->teamFileInfo) ? $this->teamFileInfo->belongsToTeam($team) : false;
    }

}
