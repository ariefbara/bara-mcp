<?php

namespace Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant;

use Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant\TeamParticipantRepository;
use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\ActivityTypeRepository;
use Firm\Application\Service\Firm\Program\MeetingType\MeetingRepository;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
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
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     *
     * @var TeamParticipantRepository
     */
    protected $teamParticipantRepository;

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
            MeetingRepository $meetingRepository, TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository, ActivityTypeRepository $activityTypeRepository,
            Dispatcher $dispatcher)
    {
        $this->meetingRepository = $meetingRepository;
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->activityTypeRepository = $activityTypeRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(string $firmId, string $clientId, string $teamId, string $programId, string $activityTypeId,
            MeetingData $meetingData): string
    {
        $id = $this->meetingRepository->nextIdentity();
        $teamParticipant = $this->teamParticipantRepository->aTeamParticipantCorrespondWitnProgram($teamId, $programId);
        $meetingType = $this->activityTypeRepository->ofId($activityTypeId);
        $meeting = $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->initiateMeeting($id, $teamParticipant, $meetingType, $meetingData);
        $this->meetingRepository->add($meeting);
        
        $this->dispatcher->dispatch($meeting);
        return $id;
    }

}
