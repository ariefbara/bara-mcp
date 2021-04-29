<?php

namespace Query\Application\Service\User\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;

class ViewMissionComment
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

    /**
     * 
     * @var MissionCommentRepository
     */
    protected $missionCommentRepository;

    public function __construct(
            UserParticipantRepository $userParticipantRepository, MissionCommentRepository $missionCommentRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function showById(string $userId, string $programId, string $missionCommentId): MissionComment
    {
        return $this->userParticipantRepository->aProgramParticipationOfUserCorrespondWithProgram($userId, $programId)
                        ->viewMissionComment($this->missionCommentRepository, $missionCommentId);
    }

    /**
     * 
     * @param string $userId
     * @param string $programId
     * @param string $missionId
     * @param int $page
     * @param int $pageSize
     * @return MissionComment[]
     */
    public function showAll(string $userId, string $programId, string $missionId, int $page, int $pageSize)
    {
        return $this->userParticipantRepository->aProgramParticipationOfUserCorrespondWithProgram($userId, $programId)
                        ->viewAllMissionComments($this->missionCommentRepository, $missionId, $page, $pageSize);
    }

}
