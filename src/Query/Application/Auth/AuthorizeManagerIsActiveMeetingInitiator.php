<?php

namespace Query\Application\Auth;

use Resources\Exception\RegularException;

class AuthorizeManagerIsActiveMeetingInitiator
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

    public function execute(string $firmId, string $managerId, string $meetingId): void
    {
        if (!$this->meetingAttendeeRepository->containRecordOfActiveMeetingAttendeeCorrespondWithManagerWithInitiatorRole(
                        $firmId, $managerId, $meetingId)) {
            $errorDetail = "forbidden: only meeting initiator can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
