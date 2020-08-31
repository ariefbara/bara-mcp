<?php

namespace Participant\Domain\Event\Participant\Worksheet;

use Config\EventList;
use Notification\Application\Listener\Firm\Program\ClientParticipant\Worksheet\ConsultantCommentRepliedByParticipantEventInterface;

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
    protected $programId;

    /**
     *
     * @var string
     */
    protected $worksheetId;

    /**
     *
     * @var string
     */
    protected $participantCommentId;

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getProgramId(): string
    {
        return $this->programId;
    }

    public function getWorksheetId(): string
    {
        return $this->worksheetId;
    }

    public function getParticipantCommentId(): string
    {
        return $this->participantCommentId;
    }

    public function __construct(
            string $firmId, string $clientId, string $programId, string $worksheetId, string $participantCommentId)
    {
        $this->firmId = $firmId;
        $this->clientId = $clientId;
        $this->programId = $programId;
        $this->worksheetId = $worksheetId;
        $this->participantCommentId = $participantCommentId;
    }

    public function getName(): string
    {
        return EventList::CONSULTANT_COMMENT_REPLIED_BY_CLIENT_PARTICIPANT;
    }

}
