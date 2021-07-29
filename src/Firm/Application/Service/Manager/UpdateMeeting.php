<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\ActivityType\MeetingType\MeetingData;
use Resources\Application\Event\Dispatcher;

class UpdateMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(AttendeeRepository $attendeeRepository, Dispatcher $dispatcher)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $managerId, string $meetingId, MeetingData $meetingData): void
    {
        $attendee = $this->attendeeRepository
                ->anAttendeeBelongsToManagerCorrespondWithMeeting($firmId, $managerId, $meetingId);
        $attendee->updateMeeting($meetingData);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
