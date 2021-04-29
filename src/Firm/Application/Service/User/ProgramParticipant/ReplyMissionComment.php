<?php

namespace Firm\Application\Service\User\ProgramParticipant;

use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;

class ReplyMissionComment
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

    public function __construct(UserParticipantRepository $userParticipantRepository,
            MissionCommentRepository $missionCommentRepository)
    {
        $this->userParticipantRepository = $userParticipantRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function execute(
            string $userId, string $programId, string $missionCommentId, MissionCommentData $missionCommentData): string
    {
        $missionComment = $this->missionCommentRepository->ofId($missionCommentId);
        $id = $this->missionCommentRepository->nextIdentity();
        $reply = $this->userParticipantRepository
                ->aUserParticipantCorrespondWithProgram($userId, $programId)
                ->replyMissionComment($missionComment, $id, $missionCommentData);
        $this->missionCommentRepository->add($reply);
        return $id;
    }

}
