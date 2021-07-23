<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant\DedicatedMentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ITaskExecutableByDedicatedMentor;

class ExecuteTask
{

    /**
     * 
     * @var DedicatedMentorRepository
     */
    protected $dedicatedMentorRepository;

    public function __construct(DedicatedMentorRepository $dedicatedMentorRepository)
    {
        $this->dedicatedMentorRepository = $dedicatedMentorRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $dedicatedMentorId, ITaskExecutableByDedicatedMentor $task): void
    {
        $this->dedicatedMentorRepository->aDedicatedMentorBelongsToPersonnel($firmId, $personnelId, $dedicatedMentorId)
                ->executeTask($task);
        $this->dedicatedMentorRepository->update();
    }

}
