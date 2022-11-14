<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\ParticipantNoteRepository;

class UpdateNote implements ParticipantTask
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
     * @param UpdateNotePayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $participantNote = $this->participantNoteRepository->ofId($payload->getId());
        $participantNote->assertManageableByParticipant($participant);
        $participantNote->update($payload->getContent());
    }

}
