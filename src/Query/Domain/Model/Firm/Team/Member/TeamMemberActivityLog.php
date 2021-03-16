<?php

namespace Query\Domain\Model\Firm\Team\Member;

use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog;
use Query\Domain\Model\Firm\Program\Participant\ViewLearningMaterialActivityLog;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentActivityLog;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\WorksheetActivityLog;
use Query\Domain\Model\Firm\Team\Member;
use Query\Domain\SharedModel\ActivityLog;

class TeamMemberActivityLog
{

    /**
     *
     * @var Member
     */
    protected $member;

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

    public function getMember(): Member
    {
        return $this->member;
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

    public function getWorksheetActivityLog(): ?WorksheetActivityLog
    {
        return $this->activityLog->getWorksheetActivityLog();
    }

    public function getCommentActivityLog(): ?CommentActivityLog
    {
        return $this->activityLog->getCommentActivityLog();
    }

    public function getViewLearningMaterialActivityLog(): ?ViewLearningMaterialActivityLog
    {
        return $this->activityLog->getViewLearningMaterialActivityLog();
    }

}
