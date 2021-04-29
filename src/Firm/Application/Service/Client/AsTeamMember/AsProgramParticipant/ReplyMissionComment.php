<?php

namespace Firm\Application\Service\Client\AsTeamMember\AsProgramParticipant;

use Firm\Application\Service\Client\AsTeamMember\TeamMemberRepository;
use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;

class ReplyMissionComment
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
     * @var MissionCommentRepository
     */
    protected $missionCommentRepository;

    public function __construct(
            TeamMemberRepository $teamMemberRepository, TeamParticipantRepository $teamParticipantRepository,
            MissionCommentRepository $missionCommentRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $programId, string $missionCommentId,
            MissionCommentData $missionCommentData): string
    {
        $teamParticipant = $this->teamParticipantRepository->aTeamParticipantCorrespondWitnProgram($teamId, $programId);
        $missionComment = $this->missionCommentRepository->ofId($missionCommentId);
        $id = $this->missionCommentRepository->nextIdentity();
        
        $reply = $this->teamMemberRepository->aTeamMemberCorrespondWithTeam($firmId, $clientId, $teamId)
                ->replyMissionComment($teamParticipant, $missionComment, $id, $missionCommentData);
        $this->missionCommentRepository->add($reply);
        return $id;
    }

}
