<?php

namespace Query\Domain\Model\Firm\Program\Consultant;

use Query\Domain\ {
    Model\Firm\Program\Consultant,
    Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog,
    Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog,
    Model\Firm\Program\Participant\Worksheet\Comment\CommentActivityLog,
    SharedModel\ActivityLog
};

class ConsultantActivityLog
{

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var ActivityLog
     */
    protected $activityLog;

    public function getConsultant(): Consultant
    {
        return $this->consultant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    public function getMessage(): string
    {
        return $this->activityLog->getMessage();
    }

    public function getOccuredTimeString(): string
    {
        return $this->activityLog->getOccuredTimeString();
    }

    public function getConsultationRequestActivityLog(): ?ConsultationRequestActivityLog
    {
        return $this->activityLog->getConsultationRequestActivityLog();
    }

    public function getConsultationSessionActivityLog(): ?ConsultationSessionActivityLog
    {
        return $this->activityLog->getConsultationSessionActivityLog();
    }

    public function getCommentActivityLog(): ?CommentActivityLog
    {
        return $this->activityLog->getCommentActivityLog();
    }

}
