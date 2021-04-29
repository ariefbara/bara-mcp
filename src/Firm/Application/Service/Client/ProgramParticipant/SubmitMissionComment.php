<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Application\Service\Firm\Program\MissionRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;

class SubmitMissionComment
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

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
            ClientParticipantRepository $clientParticipantRepository, MissionRepository $missionRepository,
            MissionCommentRepository $missionCommentRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->missionRepository = $missionRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programId, string $missionId,
            MissionCommentData $missionCommentData): string
    {
        $mission = $this->missionRepository->aMissionOfId($missionId);
        $id = $this->missionCommentRepository->nextIdentity();
        $missionComment = $this->clientParticipantRepository
                ->aClientParticipantCorrespondWithProgram($firmId, $clientId, $programId)
                ->submitCommentInMission($mission, $id, $missionCommentData);
        $this->missionCommentRepository->add($missionComment);
        return $id;
    }

}
