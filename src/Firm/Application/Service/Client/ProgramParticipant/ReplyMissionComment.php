<?php

namespace Firm\Application\Service\Client\ProgramParticipant;

use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;

class ReplyMissionComment
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

    public function __construct(ClientParticipantRepository $clientParticipantRepository,
            MissionCommentRepository $missionCommentRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $programId, string $missionCommentId,
            MissionCommentData $missionCommentData): string
    {
        $missionComment = $this->missionCommentRepository->ofId($missionCommentId);
        $id = $this->missionCommentRepository->nextIdentity();
        $reply = $this->clientParticipantRepository
                ->aClientParticipantCorrespondWithProgram($firmId, $clientId, $programId)
                ->replyMissionComment($missionComment, $id, $missionCommentData);
        $this->missionCommentRepository->add($reply);
        return $id;
    }

}
