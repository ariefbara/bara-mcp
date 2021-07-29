<?php

namespace Firm\Application\Service\Manager\ManagerAttendee;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;

class ExecuteTaskAsMeetingInitiator
{

    /**
     * 
     * @var ManagerAttendeeRepository
     */
    protected $managerAttendeeRepository;

    public function __construct(ManagerAttendeeRepository $managerAttendeeRepository)
    {
        $this->managerAttendeeRepository = $managerAttendeeRepository;
    }
    
    public function execute(
            string $firmid, string $managerId, string $meetingId, ITaskExecutableByMeetingInitiator $task): void
    {
        $this->managerAttendeeRepository
                ->aManagerAttendeeBelongsToManager($firmid, $managerId, $meetingId)
                ->executeTaskAsMeetingInitiator($task);
        $this->managerAttendeeRepository->update();
    }

}
