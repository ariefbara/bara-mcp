<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\DeclaredMentoringRepository;

class UpdateDeclaredMentoringTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var DeclaredMentoringRepository
     */
    protected $declaredMentoringRepository;

    /**
     * 
     * @var UpdateDeclaredMentoringPayload
     */
    protected $payload;

    public function __construct(
            DeclaredMentoringRepository $declaredMentoringRepository, UpdateDeclaredMentoringPayload $payload)
    {
        $this->declaredMentoringRepository = $declaredMentoringRepository;
        $this->payload = $payload;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $declaredMentoring = $this->declaredMentoringRepository->ofId($this->payload->getId());
        $declaredMentoring->assertManageableByMentor($mentor);
        $declaredMentoring->update($this->payload->getScheduleData());
    }

}
