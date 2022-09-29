<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Team\TeamParticipantRepository;

class ViewTeamParticipantDetail implements ProgramTaskExecutableByCoordinator, ProgramTaskExecutableByConsultant
{

    /**
     * 
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

    public function __construct(TeamParticipantRepository $teamParticipantRepository)
    {
        $this->teamParticipantRepository = $teamParticipantRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->teamParticipantRepository->aTeamParticipantInProgram($programId, $payload->getId());
    }

}
