<?php

namespace Notification\Application\Service;

class GenerateMeetingScheduleChangedNotification
{
    /**
     * 
     * @var MeetingRepository
     */
    protected $meetingRepository;
    
    function __construct(MeetingRepository $meetingRepository)
    {
        $this->meetingRepository = $meetingRepository;
    }
    
    public function execute(string $meetingId): void
    {
        $this->meetingRepository->ofId($meetingId)->addMeetingScheduleChangedNotification();
        $this->meetingRepository->update();
    }

}
