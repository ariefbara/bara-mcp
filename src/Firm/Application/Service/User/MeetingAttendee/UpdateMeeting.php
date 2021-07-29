<?php

namespace Firm\Application\Service\User\MeetingAttendee;

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

    public function execute(string $userId, string $meetingId, MeetingData $meetingData): void
    {
        $attendee = $this->attendeeRepository
                ->anAttendeeBelongsToUserParticipantCorrespondWithMeeting($userId, $meetingId);
        $attendee->updateMeeting($meetingData);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
