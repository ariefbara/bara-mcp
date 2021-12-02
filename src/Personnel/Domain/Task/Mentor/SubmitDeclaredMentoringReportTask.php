<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\DeclaredMentoringRepository;

class SubmitDeclaredMentoringReportTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var DeclaredMentoringRepository
     */
    protected $declaredMentoringRepository;

    /**
     * 
     * @var SubmitMentoringReportPayload
     */
    protected $payload;

    public function __construct(DeclaredMentoringRepository $declaredMentoringRepository,
            SubmitMentoringReportPayload $payload)
    {
        $this->declaredMentoringRepository = $declaredMentoringRepository;
        $this->payload = $payload;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $declaredMentoring = $this->declaredMentoringRepository->ofId($this->payload->getId());
        $declaredMentoring->assertManageableByMentor($mentor);
        $declaredMentoring->submitReport($this->payload->getFormRecordData(), $this->payload->getParticipantRating());
    }

}
