<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultantTaskRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class SubmitTask implements MentorTask
{

    /**
     * 
     * @var ConsultantTaskRepository
     */
    protected $consultantTaskRepository;

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(ConsultantTaskRepository $consultantTaskRepository,
            ParticipantRepository $participantRepository)
    {
        $this->consultantTaskRepository = $consultantTaskRepository;
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param ProgramConsultant $mentor
     * @param SubmitTaskPayload $payload
     * @return void
     */
    public function execute(ProgramConsultant $mentor, $payload): void
    {
        $payload->submittedTaskId = $this->consultantTaskRepository->nextIdentity();
        $participant = $this->participantRepository->ofId($payload->getParticipantId());
        $data = $payload->getTaskData();
        
        $consultantTask = $mentor->submitTask($payload->submittedTaskId, $participant, $data);
        $this->consultantTaskRepository->add($consultantTask);
    }

}
