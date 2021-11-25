<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequest\NegotiatedMentoringRepository;

class SubmitNegotiatedMentoringReportTask implements ITaskExecutableByParticipant
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

    public function __construct(
            NegotiatedMentoringRepository $negotiatedMentoringRepository, SubmitMentoringReportPayload $payload)
    {
        $this->negotiatedMentoringRepository = $negotiatedMentoringRepository;
        $this->payload = $payload;
    }

    public function execute(\Participant\Domain\Model\Participant $participant): void
    {
       $negotiatedMentoring = $this->negotiatedMentoringRepository->ofId($this->payload->getId());
       $negotiatedMentoring->assertManageableByParticipant($participant);
       $negotiatedMentoring->submitReport($this->payload->getMentorRating(), $this->payload->getFormRecordData());
    }

}
