<?php

namespace Query\Domain\SharedModel;

use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest\ConsultationRequestNotification;
use Query\Domain\Model\Firm\Program\ConsultationSetup\ConsultationSession\ConsultationSessionNotification;
use Query\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee\MeetingAttendeeNotification;
use Query\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingNotification;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment\CommentNotification;

class Notification
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
     * @var ConsultationRequestNotification|null
     */
    protected $consultationRequestNotification;

    /**
     *
     * @var ConsultationSessionNotification|null
     */
    protected $consultationSessionNotification;

    /**
     *
     * @var CommentNotification|null
     */
    protected $commentNotification;

    /**
     *
     * @var MeetingNotification|null
     */
    protected $meetingNotification;

    /**
     *
     * @var MeetingAttendeeNotification|null
     */
    protected $meetingAttendeeNotification;

    public function getId(): string
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getConsultationRequestNotification(): ?ConsultationRequestNotification
    {
        return $this->consultationRequestNotification;
    }

    public function getConsultationSessionNotification(): ?ConsultationSessionNotification
    {
        return $this->consultationSessionNotification;
    }

    public function getCommentNotification(): ?CommentNotification
    {
        return $this->commentNotification;
    }

    function getMeetingNotification(): ?MeetingNotification
    {
        return $this->meetingNotification;
    }

    function getMeetingAttendeeNotification(): ?MeetingAttendeeNotification
    {
        return $this->meetingAttendeeNotification;
    }

    protected function __construct()
    {
        ;
    }

}
