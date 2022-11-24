<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Consultant\ConsultantTaskRepository;

class ViewConsultantTaskDetail implements ParticipantQueryTask
{

    /**
     * 
     * @var ConsultantTaskRepository
     */
    protected $consultantTaskRepository;

    public function __construct(ConsultantTaskRepository $consultantTaskRepository)
    {
        $this->consultantTaskRepository = $consultantTaskRepository;
    }

    /**
     * 
     * @param Participant $participant
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->consultantTaskRepository
                ->aConsultantTaskDetailForParticipant($participant->getId(), $payload->getId());
    }

}
