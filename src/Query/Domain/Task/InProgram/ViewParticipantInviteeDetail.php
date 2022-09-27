<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantInviteeRepository;

class ViewParticipantInviteeDetail implements ProgramTaskExecutableByCoordinator
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
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->participantInviteeRepository
                ->aParticipantInvitationInProgram($programId, $payload->getId());
    }

}
