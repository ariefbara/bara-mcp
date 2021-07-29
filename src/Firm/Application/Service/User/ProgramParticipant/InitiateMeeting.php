<?php

namespace Firm\Application\Service\User\ProgramParticipant;

use Firm\Application\Service\Firm\Program\ActivityType\MeetingRepository;
use Firm\Application\Service\Firm\Program\ActivityTypeRepository;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Resources\Application\Event\Dispatcher;

class InitiateMeeting
{

    /**
     *
     * @var MeetingRepository
     */
    protected $meetingRepository;

    /**
     *
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    /**
     * 
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(MeetingRepository $meetingRepository, UserParticipantRepository $userParticipantRepository,
            ActivityTypeRepository $activityTypeRepository, Dispatcher $dispatcher)
    {
        $this->meetingRepository = $meetingRepository;
        $this->userParticipantRepository = $userParticipantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $userId, string $programId, string $activityTypeId, MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $meetingType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->userParticipantRepository->aUserParticipantCorrespondWithProgram($userId, $programId)
                ->initiateMeeting($id, $meetingType, $meetingData);
        $this->meetingRepository->add($meeting);
        
        $this->dispatcher->dispatch($meeting);
        return $id;
    }

}
