<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultantTaskRepository;

class CancelTask implements MentorTask
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
     * @param ProgramConsultant $mentor
     * @param string $payload consultantTaskId
     * @return void
     */
    public function execute(ProgramConsultant $mentor, $payload): void
    {
        $consultantTask = $this->consultantTaskRepository->ofId($payload);
        $consultantTask->assertManageableByConsultant($mentor);
        $consultantTask->cancel();
    }

}
