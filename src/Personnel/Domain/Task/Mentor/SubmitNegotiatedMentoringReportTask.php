<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequest\NegotiatedMentoringRepository;

class SubmitNegotiatedMentoringReportTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var NegotiatedMentoringRepository
     */
    protected $negotiatedMentoringRepository;

    /**
     * 
     * @var SubmitMentoringReportPayload
     */
    protected $payload;

    public function __construct(NegotiatedMentoringRepository $negotiatedMentoringRepository,
            SubmitMentoringReportPayload $payload)
    {
        $this->negotiatedMentoringRepository = $negotiatedMentoringRepository;
        $this->payload = $payload;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $negotiatedMentoring = $this->negotiatedMentoringRepository->ofId($this->payload->getId());
        $negotiatedMentoring->assertBelongsToMentor($mentor);
        $negotiatedMentoring->submitReport($this->payload->getFormRecordData(), $this->payload->getParticipantRating());
    }

}
