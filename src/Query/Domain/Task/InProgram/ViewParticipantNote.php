<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByConsultant;
use Query\Domain\Model\Firm\Program\ProgramTaskExecutableByCoordinator;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantNoteRepository;

class ViewParticipantNote implements ProgramTaskExecutableByConsultant, ProgramTaskExecutableByCoordinator
{

    /**
     * 
     * @var ParticipantNoteRepository
     */
    protected $participantNoteRepository;

    public function __construct(ParticipantNoteRepository $participantNoteRepository)
    {
        $this->participantNoteRepository = $participantNoteRepository;
    }

    /**
     * 
     * @param string $programId
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(string $programId, $payload): void
    {
        $payload->result = $this->participantNoteRepository->aParticipantNoteInProgram($programId, $payload->getId());
    }

}
