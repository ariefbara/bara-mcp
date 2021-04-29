<?php

namespace Query\Application\Service\Firm\Client\AsTeamMember\AsProgramParticipant;

use Query\Application\Service\TeamMember\TeamMemberRepository;
use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;

class ViewMissionComment
{

    /**
     * 
     * @var TeamMemberRepository
     */
    protected $teamMemberRepository;

    /**
     * 
     * @var TeamProgramParticipationRepository
     */
    protected $teamProgramParticipationRepository;

    /**
     * 
     * @var MissionCommentRepository
     */
    protected $missionCommentRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository,
            TeamProgramParticipationRepository $teamProgramParticipationRepository,
            MissionCommentRepository $missionCommentRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamProgramParticipationRepository = $teamProgramParticipationRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function showById(
            string $firmId, string $clientId, string $teamId, string $programId, string $missionCommentId): MissionComment
    {
        $teamParticipant = $this->teamProgramParticipationRepository
                ->aTeamProgramParticipationCorrespondWithProgram($teamId, $programId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                        ->viewMissionComment($teamParticipant, $this->missionCommentRepository, $missionCommentId);
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $teamId
     * @param string $programParticipationId
     * @param string $missionId
     * @param int $page
     * @param int $pageSize
     * @return MissionComment[]
     */
    public function showAll(
            string $firmId, string $clientId, string $teamId, string $programId, string $missionId,
            int $page, int $pageSize)
    {
        $teamParticipant = $this->teamProgramParticipationRepository
                ->aTeamProgramParticipationCorrespondWithProgram($teamId, $programId);
        return $this->teamMemberRepository->aTeamMemberOfClientCorrespondWithTeam($firmId, $clientId, $teamId)
                        ->viewAllMissionComments(
                                $teamParticipant, $this->missionCommentRepository, $missionId, $page, $pageSize);
    }

}
