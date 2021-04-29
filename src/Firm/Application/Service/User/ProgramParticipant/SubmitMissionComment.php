<?php

namespace Firm\Application\Service\User\ProgramParticipant;

use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Application\Service\Firm\Program\MissionRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;

class SubmitMissionComment
{

    /**
     * 
     * @var UserParticipantRepository
     */
    protected $userParticipantRepository;

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
            UserParticipantRepository $userParticipantRepository, MissionRepository $missionRepository,
            MissionCommentRepository $missionCommentRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->missionRepository = $missionRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function execute(
            string $userId, string $programId, string $missionId, MissionCommentData $missionCommentData): string
    {
        $mission = $this->missionRepository->aMissionOfId($missionId);
        $id = $this->missionCommentRepository->nextIdentity();
        $missionComment = $this->userParticipantRepository
                ->aUserParticipantCorrespondWithProgram($userId, $programId)
                ->submitCommentInMission($mission, $id, $missionCommentData);
        $this->missionCommentRepository->add($missionComment);
        return $id;
    }

}
