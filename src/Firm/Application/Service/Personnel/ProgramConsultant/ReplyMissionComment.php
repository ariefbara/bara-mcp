<?php

namespace Firm\Application\Service\Personnel\ProgramConsultant;

use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;

class ReplyMissionComment
{

    /**
     * 
     * @var ProgramConsultantRepository
     */
    protected $consultantRepository;

    /**
     * 
     * @var MissionCommentRepository
     */
    protected $missionCommentRepository;

    public function __construct(ProgramConsultantRepository $consultantRepository,
            MissionCommentRepository $missionCommentRepository)
    {
        $this->consultantRepository = $consultantRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function execute(
            string $firmId, string $personnelId, string $programId, string $missionCommentId,
            MissionCommentData $missionCommentData): string
    {
        $missionComment = $this->missionCommentRepository->ofId($missionCommentId);
        $id = $this->missionCommentRepository->nextIdentity();
        $reply = $this->consultantRepository->aConsultantCorrespondWithProgram($firmId, $personnelId, $programId)
                ->replyMissionComment($missionComment, $id, $missionCommentData);
        $this->missionCommentRepository->add($reply);
        return $id;
    }

}
