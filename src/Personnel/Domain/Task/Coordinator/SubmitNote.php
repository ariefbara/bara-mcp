<?php

namespace Personnel\Domain\Task\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator\CoordinatorNoteRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class SubmitNote implements CoordinatorTask
{

    /**
     * 
     * @var CoordinatorNoteRepository
     */
    protected $coordinatorNoteRepository;

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(CoordinatorNoteRepository $coordinatorNoteRepository,
            ParticipantRepository $participantRepository)
    {
        $this->coordinatorNoteRepository = $coordinatorNoteRepository;
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param Coordinator $coordinator
     * @param SubmitNotePayload $payload
     * @return void
     */
    public function execute(Coordinator $coordinator, $payload): void
    {
        $payload->submittedNoteId = $this->coordinatorNoteRepository->nextIdentity();
        $participant = $this->participantRepository->ofId($payload->getParticipantId());
        $content = $payload->getContent();
        $viewableByParticipant = $payload->getViewableByParticipant();
        $coordinatorNote = $coordinator->submitNote($payload->submittedNoteId, $participant, $content, $viewableByParticipant);
        
        $this->coordinatorNoteRepository->add($coordinatorNote);
    }

}
