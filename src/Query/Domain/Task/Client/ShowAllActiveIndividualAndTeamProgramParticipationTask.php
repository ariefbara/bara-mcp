<?php

namespace Query\Domain\Task\Client;

use Query\Domain\Model\Firm\ITaskExecutableByClient;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class ShowAllActiveIndividualAndTeamProgramParticipationTask implements ITaskExecutableByClient
{
    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;
    /**
     * 
     * @var Participant[]
     */
    public $result;
    
    public function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
        $this->result = [];
    }

    public function execute(string $clientId): void
    {
        $this->result = $this->participantRepository
                ->allActiveIndividualAndTeamProgramParticipationBelongsToClient($clientId);
    }

}
