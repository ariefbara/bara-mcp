<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultantRepository;
use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;

class ExecuteTask
{

    /**
     * 
     * @var ProgramConsultantRepository
     */
    protected $mentorRepository;

    public function __construct(ProgramConsultantRepository $mentorRepository)
    {
        $this->mentorRepository = $mentorRepository;
    }

    public function execute(string $firmId, string $personnelId, string $mentorId, ITaskExecutableByMentor $task): void
    {
        $this->mentorRepository->ofId($firmId, $personnelId, $mentorId)
                ->executeTask($task);
        $this->mentorRepository->update();
    }

}
