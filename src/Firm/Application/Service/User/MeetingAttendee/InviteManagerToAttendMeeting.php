<?php

namespace Firm\Application\Service\User\MeetingAttendee;

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

    function __construct(AttendeeRepository $attendeeRepository, ManagerRepository $managerRepository,
            Dispatcher $dispatcher)
    {
        $this->attendeeRepository = $attendeeRepository;
        $this->managerRepository = $managerRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $userId, string $meetingId, string $toInviteManagerId): void
    {
        $manager = $this->managerRepository->aManagerOfId($toInviteManagerId);
        $attendee = $this->attendeeRepository
                ->anAttendeeBelongsToUserParticipantCorrespondWithMeeting($userId, $meetingId);
        $attendee->inviteUserToAttendMeeting($manager);
        $this->attendeeRepository->update();
        
        $this->dispatcher->dispatch($attendee);
    }

}
