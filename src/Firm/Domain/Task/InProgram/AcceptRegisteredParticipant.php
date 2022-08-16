<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\TaskInProgramExecutableByCoordinator;
use Firm\Domain\Task\Dependency\Firm\Program\ParticipantRepository;
use Resources\Application\Event\AdvanceDispatcher;

class AcceptRegisteredParticipant implements TaskInProgramExecutableByCoordinator
{

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    /**
     * 
     * @var AdvanceDispatcher
     */
    protected $dispatcher;

    public function __construct(ParticipantRepository $participantRepository, AdvanceDispatcher $dispatcher)
    {
        $this->participantRepository = $participantRepository;
        $this->dispatcher = $dispatcher;
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
        
        $participant->acceptRegistrant();
        $this->dispatcher->dispatch($participant);
    }

}
