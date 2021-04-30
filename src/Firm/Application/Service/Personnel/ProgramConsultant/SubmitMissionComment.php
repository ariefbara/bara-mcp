<?php

namespace Firm\Application\Service\Personnel\ProgramConsultant;

use Firm\Application\Service\Firm\Program\Mission\MissionCommentRepository;
use Firm\Application\Service\Firm\Program\MissionRepository;
use Firm\Domain\Model\Firm\Program\Mission\MissionCommentData;

class SubmitMissionComment
{

    /**
     * 
     * @var ProgramConsultantRepository
     */
    protected $consultantRepository;

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

    public function __construct(ProgramConsultantRepository $consultantRepository, MissionRepository $missionRepository,
            MissionCommentRepository $missionCommentRepository)
    {
        $this->consultantRepository = $consultantRepository;
        $this->missionRepository = $missionRepository;
        $this->missionCommentRepository = $missionCommentRepository;
    }

    public function execute(string $firmId, string $personnelId, string $programId, string $missionId,
            MissionCommentData $missionCommentData): string
    {
        $mission = $this->missionRepository->aMissionOfId($missionId);
        $id = $this->missionCommentRepository->nextIdentity();
        $missionComment = $this->consultantRepository
                ->aConsultantCorrespondWithProgram($firmId, $personnelId, $programId)
                ->submitCommentInMission($mission, $id, $missionCommentData);
        $this->missionCommentRepository->add($missionComment);
        return $id;
    }

}
