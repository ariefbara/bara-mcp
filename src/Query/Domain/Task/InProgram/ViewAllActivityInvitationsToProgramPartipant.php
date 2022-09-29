<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantInviteeRepository;

class ViewAllActivityInvitationsToProgramPartipant implements ProgramTaskExecutableByCoordinator, ProgramTaskExecutableByConsultant
{

    /**
     * 
     * @var ParticipantInviteeRepository
     */
    protected $participantInviteeRepository;

    public function __construct(ParticipantInviteeRepository $participantInviteeRepository)
    {
        $this->participantInviteeRepository = $participantInviteeRepository;
    }

    /**
     * 
     * @param string $programId
     * @param ViewAllActivityInvitationsToProgramPartipantPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->participantInviteeRepository->allInvitationsToParticipantInProgram(
                $programId, $payload->getParticipantId(), $payload->getActivityInvitationFilter());
    }

}
