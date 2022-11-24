<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultantNoteRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class SubmitNote implements MentorTask
{

    /**
     * 
     * @var ConsultantNoteRepository
     */
    protected $consultantNoteRepository;

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(ConsultantNoteRepository $consultantNoteRepository,
            ParticipantRepository $participantRepository)
    {
        $this->consultantNoteRepository = $consultantNoteRepository;
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param ProgramConsultant $mentor
     * @param SubmitNotePayload $payload
     * @return void
     */
    public function execute(ProgramConsultant $mentor, $payload): void
    {
        $payload->submittedNoteId = $this->consultantNoteRepository->nextIdentity();
        $participant = $this->participantRepository->ofId($payload->getParticipantId());
        $consultantNote = $mentor->submitNote(
                $payload->submittedNoteId, $participant, $payload->getLabelData(), $payload->getViewableByParticipant());
        $this->consultantNoteRepository->add($consultantNote);
    }

}
