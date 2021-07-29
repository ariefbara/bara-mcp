<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Resources\Application\Event\Dispatcher;

class CancelInvitationTask implements ITaskExecutableByMeetingInitiator
{

    /**
     * 
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     * 
     * @var string
     */
    protected $attendeeId;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(AttendeeRepository $attendeeRepository, string $attendeeId, Dispatcher $dispatcher)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->attendeeId = $attendeeId;
        $this->dispatcher = $dispatcher;
    }

    public function executeByMeetingInitiatorOf(Meeting $meeting): void
    {
        $attendee = $this->attendeeRepository->ofId($this->attendeeId);
        $attendee->assertManageableInMeeting($meeting);
        $attendee->cancel();
        
        $this->dispatcher->dispatch($attendee);
    }
    

}
