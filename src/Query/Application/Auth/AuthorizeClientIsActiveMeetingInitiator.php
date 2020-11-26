<?php

namespace Query\Application\Auth;

use Resources\Exception\RegularException;

class AuthorizeClientIsActiveMeetingInitiator
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

    public function execute(string $firmId, string $clientId, string $meetingId): void
    {
        if (!$this->meetingAttendeeRepository
                        ->containRecordOfActiveMeetingAttendeeCorrespondWithClientAsProgramParticipantHavingInitiatorRole(
                                $firmId, $clientId, $meetingId)) {
            $errorDetail = "forbidden: only meeting initiator can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
