<?php

namespace Query\Domain\SharedModel;

use DateTimeImmutable;
use Query\Domain\Model\Firm\{
    Program\ConsultationSetup\ConsultationRequest\ConsultationRequestActivityLog,
    Program\ConsultationSetup\ConsultationSession\ConsultationSessionActivityLog,
    Program\Participant\Worksheet\Comment\CommentActivityLog,
    Program\Participant\Worksheet\WorksheetActivityLog,
    Team\Member\TeamMemberActivityLog
};

class ActivityLog
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $occuredTime;

    /**
     *
     * @var ConsultationRequestActivityLog|null
     */
    protected $consultationRequestActivityLog;

    /**
     *
     * @var ConsultationSessionActivityLog|null
     */
    protected $consultationSessionActivityLog;

    /**
     *
     * @var WorksheetActivityLog|null
     */
    protected $worksheetActivityLog;

    /**
     *
     * @var CommentActivityLog|null
     */
    protected $commentActivityLog;

    /**
     *
     * @var ViewLearningMaterialActivityLog|null
     */
    protected $viewLearningMaterialActivityLog;

    /**
     *
     * @var TeamMemberActivityLog|null
     */
    protected $teamMemberActivityLog;

    public function getId(): string
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    protected function __construct()
    {
        
    }

    public function getOccuredTimeString(): string
    {
        return $this->occuredTime->format("Y-m-d H:i:s");
    }

    public function getConsultationRequestActivityLog(): ?ConsultationRequestActivityLog
    {
        return $this->consultationRequestActivityLog;
    }

    public function getConsultationSessionActivityLog(): ?ConsultationSessionActivityLog
    {
        return $this->consultationSessionActivityLog;
    }

    public function getWorksheetActivityLog(): ?WorksheetActivityLog
    {
        return $this->worksheetActivityLog;
    }

    public function getCommentActivityLog(): ?CommentActivityLog
    {
        return $this->commentActivityLog;
    }

    public function getViewLearningMaterialActivityLog(): ?ViewLearningMaterialActivityLog
    {
        return $this->viewLearningMaterialActivityLog;
    }

    public function getTeamMemberActivityLog(): ?TeamMemberActivityLog
    {
        return $this->teamMemberActivityLog;
    }

}
