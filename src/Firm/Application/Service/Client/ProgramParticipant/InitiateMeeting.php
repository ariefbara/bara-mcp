<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

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
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

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

    function __construct(
            MeetingRepository $meetingRepository, ClientParticipantRepository $clientParticipantRepository,
            ActivityTypeRepository $activityTypeRepository, Dispatcher $dispatcher)
    {
        $this->meetingRepository = $meetingRepository;
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $programId, string $activityTypeId, MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $meetingType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->clientParticipantRepository
                ->aClientParticipantCorrespondWithProgram($firmId, $clientId, $programId)
                ->initiateMeeting($id, $meetingType, $meetingData);
        $this->meetingRepository->add($meeting);
        
        $this->dispatcher->dispatch($meeting);
        
        return $id;
    }

}
