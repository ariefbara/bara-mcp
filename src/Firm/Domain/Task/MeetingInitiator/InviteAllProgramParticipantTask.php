<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Resources\Application\Event\Dispatcher;

class InviteAllProgramParticipantTask implements ITaskExecutableByMeetingInitiator
{

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function executeByMeetingInitiatorOf(Meeting $meeting): void
    {
        $meeting->inviteAllActiveProgramParticipants();
        $this->dispatcher->dispatch($meeting);
    }

}
