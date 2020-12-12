<?php

namespace Notification\Application\Service;

class GenerateMeetingCreatedNotification
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
        $this->meetingRepository->ofId($meetingId)->addMeetingCreatedNotification();
        $this->meetingRepository->update();
    }

}
