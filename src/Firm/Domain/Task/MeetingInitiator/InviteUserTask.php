<?php

namespace Firm\Domain\Task\MeetingInitiator;

use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\ITaskExecutableByMeetingInitiator;
use Resources\Application\Event\Dispatcher;

class InviteUserTask implements ITaskExecutableByMeetingInitiator
{

    /**
     * 
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * 
     * @var string
     */
    protected $userId;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(UserRepository $userRepository, string $userId, Dispatcher $dispatcher)
    {
        $this->userRepository = $userRepository;
        $this->userId = $userId;
        $this->dispatcher = $dispatcher;
    }

    public function executeByMeetingInitiatorOf(Meeting $meeting): void
    {
        $user = $this->userRepository->aUserOfId($this->userId)
                ->inviteToMeeting($meeting);
        
        $this->dispatcher->dispatch($meeting);
    }

}
