<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\{
    Application\Service\Firm\Program\ActivityTypeRepository,
    Application\Service\Firm\Program\MeetingType\MeetingRepository,
    Domain\Model\Firm\Program\MeetingType\MeetingData
};

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

    function __construct(
            MeetingRepository $meetingRepository, ClientParticipantRepository $clientParticipantRepository,
            ActivityTypeRepository $activityTypeRepository)
    {
        $this->meetingRepository = $meetingRepository;
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programId, string $activityTypeId,
            MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $meetingType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->clientParticipantRepository
                ->aClientParticipantCorrespondWithProgram($firmId, $clientId, $programId)
                ->initiateMeeting($id, $meetingType, $meetingData);
        $this->meetingRepository->add($meeting);
        return $id;
    }

}
