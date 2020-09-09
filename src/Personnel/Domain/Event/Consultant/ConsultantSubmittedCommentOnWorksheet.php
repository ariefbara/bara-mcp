<?php

namespace Personnel\Domain\Event\Consultant;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\Consultant\ConsultantSubmittedCommentOnWorksheetEventInterface;

class ConsultantSubmittedCommentOnWorksheet implements ConsultantSubmittedCommentOnWorksheetEventInterface
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $personnelId;

    /**
     *
     * @var string
     */
    protected $programConsultationId;

    /**
     *
     * @var string
     */
    protected $consultantCommentId;

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getPersonnelId(): string
    {
        return $this->personnelId;
    }

    public function getProgramConsultationId(): string
    {
        return $this->programConsultationId;
    }

    public function getConsultantCommentId(): string
    {
        return $this->consultantCommentId;
    }

    public function __construct(string $firmId, string $personnelId, string $programConsultationId,
            string $consultantCommentId)
    {
        $this->firmId = $firmId;
        $this->personnelId = $personnelId;
        $this->programConsultationId = $programConsultationId;
        $this->consultantCommentId = $consultantCommentId;
    }

    public function getName(): string
    {
        return EventList::CONSULTANT_SUBMITTED_COMMENT_ON_WORKSHEET;
    }

}
