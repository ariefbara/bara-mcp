<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Service\FileInfoRepository;

class UpdateObjectiveProgressReport
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
     * @var ObjectiveProgressReportRepository
     */
    protected $objectiveProgressReportRepository;

    /**
     * 
     * @var FileInfoRepository
     */
    protected $fileInfoRepository;

    public function __construct(TeamMemberRepository $teamMemberRepository,
            TeamParticipantRepository $teamParticipantRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository, FileInfoRepository $fileInfoRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
        $this->fileInfoRepository = $fileInfoRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId,
            string $objectiveProgressReportId, ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        foreach ($objectiveProgressReportData->getKeyResultProgressReportDataIterator() as $keyResultProgressReportData) {
            foreach ($keyResultProgressReportData->getFileInfoIdListOfAttachment() as $fileInfoId) {
                $fileInfo = $this->fileInfoRepository->fileInfoOfTeam($teamId, $fileInfoId);
                $keyResultProgressReportData->addAttachment($fileInfo);
            }
        }
        
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        $objectiveProgressReport = $this->objectiveProgressReportRepository->ofId($objectiveProgressReportId);
        $this->teamMemberRepository->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->updateObjectiveProgressReport($teamParticipant, $objectiveProgressReport, $objectiveProgressReportData);
        $this->teamMemberRepository->update();
    }

}
