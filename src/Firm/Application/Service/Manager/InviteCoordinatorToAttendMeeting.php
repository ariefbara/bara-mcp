<?php

namespace Firm\Application\Service\Manager;

use Firm\Application\Service\Firm\Program\CoordinatorRepository;
use Resources\Application\Event\Dispatcher;

class InviteCoordinatorToAttendMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     *
     * @var CoordinatorRepository
     */
    protected $coordinatorRepository;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(AttendeeRepository $attendeeRepository, CoordinatorRepository $coordinatorRepository,
            Dispatcher $dispatcher)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->coordinatorRepository = $coordinatorRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $managerId, string $meetingId, string $coordinatorId): void
    {
        $coordinator = $this->coordinatorRepository->aCoordinatorOfId($coordinatorId);
        $attendee = $this->attendeeRepository
                ->anAttendeeBelongsToManagerCorrespondWithMeeting($firmId, $managerId, $meetingId);
        $attendee->inviteUserToAttendMeeting($coordinator);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
