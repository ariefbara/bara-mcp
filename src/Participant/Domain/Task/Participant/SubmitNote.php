<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantNoteRepository;

class SubmitNote implements ParticipantTask
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
     * @param Participant $participant
     * @param SubmitNotePayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->submittedNoteId = $this->participantNoteRepository->nextIdentity();
        $participantNote = $participant->submitNote($payload->submittedNoteId, $payload->getLabelData());
        $this->participantNoteRepository->add($participantNote);
    }

}
