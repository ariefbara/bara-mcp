<?php

namespace Firm\Application\Service\User\ProgramParticipant;

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
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     *
     * @var ActivityTypeRepository
     */
    protected $activityTypeRepository;

    function __construct(
            MeetingRepository $meetingRepository, UserParticipantRepository $userParticipantRepository,
            ActivityTypeRepository $activityTypeRepository)
    {
        $this->meetingRepository = $meetingRepository;
        $this->userParticipantRepository = $userParticipantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
    }

    public function execute(string $userId, string $programId, string $activityTypeId, MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $meetingType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->userParticipantRepository->aUserParticipantCorrespondWithProgram($userId, $programId)
                ->initiateMeeting($id, $meetingType, $meetingData);
        $this->meetingRepository->add($meeting);
        return $id;
    }

}
