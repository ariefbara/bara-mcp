<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\TaskInProgramExecutableByCoordinator;
use Firm\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class RejectRegisteredParticipant implements TaskInProgramExecutableByCoordinator
{

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param Program $program
     * @param string $payload participantId
     * @return void
     */
    public function execute(Program $program, $payload): void
    {
        $participant = $this->participantRepository->ofId($payload);
        $participant->assertManageableInProgram($program);
        
        $participant->rejectRegistrant();
    }

}
