<?php

namespace Participant\Domain\Event\Participant\Worksheet;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\Participant\Worksheet\ConsultantCommentRepliedByUserParticipantEventInterface;

class ConsultantCommentRepliedByUserParticipant implements ConsultantCommentRepliedByUserParticipantEventInterface
{

    /**
     *
     * @var string
     */
    protected $userId;

    /**
     *
     * @var string
     */
    protected $programParticipationId;

    /**
     *
     * @var string
     */
    protected $worksheetId;

    /**
     *
     * @var string
     */
    protected $commentId;

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getProgramParticipationId(): string
    {
        return $this->programParticipationId;
    }

    public function getWorksheetId(): string
    {
        return $this->worksheetId;
    }

    public function getCommentId(): string
    {
        return $this->commentId;
    }

    public function __construct(string $userId, string $programParticipationId, string $worksheetId, string $commentId)
    {
        $this->userId = $userId;
        $this->programParticipationId = $programParticipationId;
        $this->worksheetId = $worksheetId;
        $this->commentId = $commentId;
    }

    public function getName(): string
    {
        return EventList::USER_PARTICIPANT_REPLIED_CONSULTANT_COMMENT;
    }

}
