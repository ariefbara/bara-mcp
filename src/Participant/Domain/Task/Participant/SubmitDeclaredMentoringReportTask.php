<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;

class SubmitDeclaredMentoringReportTask implements ITaskExecutableByParticipant
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

    public function execute(\Participant\Domain\Model\Participant $participant): void
    {
        $declaration = $this->declaredMentoringRepository->ofId($this->payload->getId());
        $declaration->assertManageableByParticipant($participant);
        $declaration->submitReport($this->payload->getFormRecordData(), $this->payload->getMentorRating());
    }

}
