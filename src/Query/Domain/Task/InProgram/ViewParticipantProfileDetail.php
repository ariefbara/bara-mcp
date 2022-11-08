<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantProfileRepository;

class ViewParticipantProfileDetail implements ProgramTaskExecutableByConsultant, ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var ParticipantProfileRepository
     */
    protected $participantProfileRepository;

    public function __construct(ParticipantProfileRepository $participantProfileRepository)
    {
        $this->participantProfileRepository = $participantProfileRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->participantProfileRepository
                ->aParticipantProfileBelongsInProgram($programId, $payload->getId());
    }

}
