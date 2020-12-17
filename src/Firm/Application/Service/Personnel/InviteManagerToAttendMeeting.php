<?php

namespace Firm\Application\Service\Personnel;

use Firm\Application\Service\Firm\ManagerRepository;
use Resources\Application\Event\Dispatcher;

class InviteManagerToAttendMeeting
{

    /**
     *
     * @var AttendeeRepository
     */
    protected $attendeeRepository;

    /**
     *
     * @var ManagerRepository
     */
    protected $managerRepository;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            AttendeeRepository $attendeeRepository, ManagerRepository $managerRepository, Dispatcher $dispatcher)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->managerRepository = $managerRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $personnelId, string $meetingId, string $managerId): void
    {
        $manager = $this->managerRepository->aManagerOfId($managerId);
        $attendee = $this->attendeeRepository
                ->anAttendeeBelongsToPersonnelCorrespondWithMeeting($firmId, $personnelId, $meetingId);
        $attendee->inviteUserToAttendMeeting($manager);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
