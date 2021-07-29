<?php

namespace Firm\Application\Service\Personnel\CoordinatorAttendee;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;

class ExecuteTaskAsMeetingInitiator
{

    /**
     * 
     * @var CoordinatorAttendeeRepository
     */
    protected $coordinatorAttendeeRepository;

    public function __construct(CoordinatorAttendeeRepository $coordinatorAttendeeRepository)
    {
        $this->coordinatorAttendeeRepository = $coordinatorAttendeeRepository;
    }
    
    public function execute(
            string $firmid, string $personnelId, string $attendeeId, ITaskExecutableByMeetingInitiator $task): void
    {
        $this->coordinatorAttendeeRepository
                ->aCoordinatorAttendeeBelongsToPersonnel($firmid, $personnelId, $attendeeId)
                ->executeTaskAsMeetingInitiator($task);
        $this->coordinatorAttendeeRepository->update();
    }

}
