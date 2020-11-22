<?php

namespace Query\Application\Auth;

use Resources\Exception\RegularException;

class AuthorizeUserIsActiveMeetingInitiator
{

    /**
     *
     * @var MeetingAttendeeRepository
     */
    protected $meetingAttendeeRepository;

    function __construct(MeetingAttendeeRepository $meetingAttendeeRepository)
    {
        $this->meetingAttendeeRepository = $meetingAttendeeRepository;
    }

    public function execute(string $userId, string $meetingId): void
    {
        if (!$this->meetingAttendeeRepository
                        ->containRecordOfActiveMeetingAttendeeCorrespondWithUserAsProgramParticipantHavingInitiatorRole(
                                $userId, $meetingId)) {
            $errorDetail = "forbidden: only meeting initiator can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
