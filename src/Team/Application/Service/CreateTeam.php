<?php

namespace Team\Application\Service;

use Resources\Exception\RegularException;

class CreateTeam
{

    /**
     *
     * @var TeamRepository
     */
    protected $teamRepository;

    /**
     *
     * @var ClientRepository
     */
    protected $clientRepository;

    public function __construct(TeamRepository $teamRepository, ClientRepository $clientRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->clientRepository = $clientRepository;
    }
    
    public function execute(string $firmId, string $clientId, string $teamName, ?string $memberPosition): string
    {
        $this->assertNameAvailable($firmId, $teamName);
        
        $id = $this->teamRepository->nextIdentity();
        $team = $this->clientRepository->ofId($firmId, $clientId)->createTeam($id, $teamName, $memberPosition);
        $this->teamRepository->add($team);
        return $id;
    }
    
    protected function assertNameAvailable(string $firmId, string $teamName): void
    {
        if (!$this->teamRepository->isNameAvailable($firmId, $teamName)) {
            $errorDetail = "conflict: team name already registered";
            throw RegularException::conflict($errorDetail);
        }
    }

}
