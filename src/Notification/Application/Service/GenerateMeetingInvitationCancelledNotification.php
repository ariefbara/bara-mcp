<?php

namespace Notification\Application\Service;

class GenerateMeetingInvitationCancelledNotification
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
    
    public function execute(string $meetingAttendeeId): void
    {
        $this->meetingAttendeeRepository->ofId($meetingAttendeeId)->addInvitationCancelledNotification();
        $this->meetingAttendeeRepository->update();
    }

}
