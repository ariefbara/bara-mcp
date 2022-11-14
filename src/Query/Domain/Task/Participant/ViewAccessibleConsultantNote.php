<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantNoteRepository;

class ViewAccessibleConsultantNote implements ParticipantQueryTask
{

    /**
     * 
     * @var ConsultantNoteRepository
     */
    protected $consultantNoteRepository;

    public function __construct(ConsultantNoteRepository $consultantNoteRepository)
    {
        $this->consultantNoteRepository = $consultantNoteRepository;
    }

    /**
     * 
     * @param Participant $participant
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->consultantNoteRepository
                ->aConsultantNoteForAccessibleByParticipant($participant->getId(), $payload->getId());
    }

}
