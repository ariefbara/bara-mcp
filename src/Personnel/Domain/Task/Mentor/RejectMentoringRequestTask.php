<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequestRepository;

class RejectMentoringRequestTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var MentoringRequestRepository
     */
    protected $mentoringRequestRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    public function __construct(MentoringRequestRepository $mentoringRequestRepository, string $id)
    {
        $this->mentoringRequestRepository = $mentoringRequestRepository;
        $this->id = $id;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $mentoringRequest = $this->mentoringRequestRepository->ofId($this->id);
        $mentoringRequest->assertBelongsToMentor($mentor);
        $mentoringRequest->reject();
    }

}
