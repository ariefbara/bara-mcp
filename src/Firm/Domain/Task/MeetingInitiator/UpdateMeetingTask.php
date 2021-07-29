<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Resources\Application\Event\Dispatcher;

class UpdateMeetingTask implements ITaskExecutableByMeetingInitiator
{

    /**
     * 
     * @var MeetingData
     */
    protected $meetingData;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(MeetingData $meetingData, Dispatcher $dispatcher)
    {
        $this->meetingData = $meetingData;
        $this->dispatcher = $dispatcher;
    }

    public function executeByMeetingInitiatorOf(Meeting $meeting): void
    {
        $meeting->update($this->meetingData);
        $this->dispatcher->dispatch($meeting);
    }

}
