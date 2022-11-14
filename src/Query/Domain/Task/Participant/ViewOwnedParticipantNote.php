<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantNoteRepository;

class ViewOwnedParticipantNote implements ParticipantQueryTask
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
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->participantNoteRepository
                ->aParticipantNoteBelongsToParticipant($participant->getId(), $payload->getId());
    }

}
