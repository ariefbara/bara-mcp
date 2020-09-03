<?php

namespace Participant\Domain\Event\Participant\Worksheet;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\Participant\Worksheet\ConsultantCommentRepliedByClientParticipantEventInterface;

class ConsultantCommentRepliedByClientParticipant implements ConsultantCommentRepliedByClientParticipantEventInterface
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
    protected $clientId;

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

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
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

    public function __construct(string $firmId, string $clientId, string $programParticipationId, string $worksheetId,
            string $commentId)
    {
        $this->firmId = $firmId;
        $this->clientId = $clientId;
        $this->programParticipationId = $programParticipationId;
        $this->worksheetId = $worksheetId;
        $this->commentId = $commentId;
    }

    public function getName(): string
    {
        return EventList::CLIENT_PARTICIPANT_REPLIED_CONSULTANT_COMMENT;
    }

}
