<?php

namespace Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Application\Service\Firm\Program\MissionRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;

class SubmitMissionComment
{

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
     * @var MissionRepository
     */
    protected $missionRepository;

    /**
     * 
     * @var MissionCommentRepository
     */
    protected $missionCommentRepository;

    public function __construct(
            TeamMemberRepository $teamMemberRepository, TeamParticipantRepository $teamParticipantRepository,
            MissionRepository $missionRepository, MissionCommentRepository $missionCommentRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->missionRepository = $missionRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $programId, string $missionId,
            MissionCommentData $missionCommentData): string
    {
        $teamParticipant = $this->teamParticipantRepository->aTeamParticipantCorrespondWitnProgram($teamId, $programId);
        $mission = $this->missionRepository->aMissionOfId($missionId);
        $id = $this->missionCommentRepository->nextIdentity();
        
        $missionComment = $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->submitCommentInMission($teamParticipant, $mission, $id, $missionCommentData);
        $this->missionCommentRepository->add($missionComment);
        return $id;
    }

}
