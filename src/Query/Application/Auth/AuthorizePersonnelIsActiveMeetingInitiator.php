<?php

namespace Query\Application\Auth;

use Resources\Exception\RegularException;

class AuthorizePersonnelIsActiveMeetingInitiator
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

    public function execute(string $firmId, string $personnelId, string $meetingId): void
    {
        if (!$this->meetingAttendeeRepository->containRecordOfActiveMeetingAttendeeCorrespondWithPersonnelWithInitiatorRole(
                        $firmId, $personnelId, $meetingId)) {
            $errorDetail = "forbidden: only meeting initiator can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
