<?php

namespace Query\Application\Auth;

use Resources\Exception\RegularException;

class AuthorizeTeamIsActiveMeetingInitiator
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

    public function execute(string $firmId, string $teamId, string $meetingId): void
    {
        if (!$this->meetingAttendeeRepository
                        ->containRecordOfActiveMeetingAttendeeCorrespondWithTeamAsProgramParticipantHavingInitiatorRole(
                                $firmId, $teamId, $meetingId)) {
            $errorDetail = "forbidden: only meeting initiator can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
