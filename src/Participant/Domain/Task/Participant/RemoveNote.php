<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantNoteRepository;

class RemoveNote implements ParticipantTask
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

    public function execute(Participant $participant, $payload): void
    {
        $participantNote = $this->participantNoteRepository->ofId($payload);
        $participantNote->assertManageableByParticipant($participant);
        $participantNote->remove();
    }

}
