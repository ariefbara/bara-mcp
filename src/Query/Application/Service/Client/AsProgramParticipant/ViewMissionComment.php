<?php

namespace Query\Application\Service\Client\AsProgramParticipant;

use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;

class ViewMissionComment
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

    /**
     * 
     * @var MissionCommentRepository
     */
    protected $missionCommentRepository;

    public function __construct(
            ClientParticipantRepository $clientParticipantRepository, MissionCommentRepository $missionCommentRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function showById(string $firmId, string $clientId, string $programId, string $missionCommentId): MissionComment
    {
        return $this->clientParticipantRepository->aClientParticipantCorrespondWithProgram($firmId, $clientId,
                                $programId)
                        ->viewMissionComment($this->missionCommentRepository, $missionCommentId);
    }

    /**
     * 
     * @param string $firmId
     * @param string $clientId
     * @param string $programId
     * @param string $missionId
     * @param int $page
     * @param int MissionComment[]
     */
    public function showAll(
            string $firmId, string $clientId, string $programId, string $missionId, int $page, int $pageSize)
    {
        return $this->clientParticipantRepository->aClientParticipantCorrespondWithProgram($firmId, $clientId, $programId)
        ->viewAllMissionComments($this->missionCommentRepository, $missionId, $page, $pageSize);
    }

}
