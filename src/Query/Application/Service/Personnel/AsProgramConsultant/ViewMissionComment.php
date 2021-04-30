<?php

namespace Query\Application\Service\Personnel\AsProgramConsultant;

use Query\Domain\Model\Firm\Program\Mission\MissionComment;
use Query\Domain\Service\Firm\Program\Mission\MissionCommentRepository;

class ViewMissionComment
{

    /**
     * 
     * @var ConsultantRepository
     */
    protected $consultantRepository;

    /**
     * 
     * @var MissionCommentRepository
     */
    protected $missionCommentRepository;

    public function __construct(
            ConsultantRepository $consultantRepository, MissionCommentRepository $missionCommentRepository)
    {
        $this->consultantRepository = $consultantRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    /**
     * 
     * @param string $firmId
     * @param string $personnelId
     * @param string $programId
     * @param string $missionId
     * @param int $page
     * @param int $pageSize
     * @return MissionComment[]
     */
    public function showAll(
            string $firmId, string $personnelId, string $programId, string $missionId, int $page, int $pageSize)
    {
        return $this->consultantRepository->aConsultantCorrepondWithProgram($firmId, $personnelId, $programId)
                        ->viewAllMissionComments($this->missionCommentRepository, $missionId, $page, $pageSize);
    }

    public function showById(string $firmId, string $personnelId, string $programId, string $missionCommentId): MissionComment
    {
        return $this->consultantRepository->aConsultantCorrepondWithProgram($firmId, $personnelId, $programId)
                        ->viewMissionComment($this->missionCommentRepository, $missionCommentId);
    }

}
