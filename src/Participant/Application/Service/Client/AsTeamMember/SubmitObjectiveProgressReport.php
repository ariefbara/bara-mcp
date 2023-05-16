<?php

namespace Participant\Application\Service\Client\AsTeamMember;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Application\Service\Participant\ObjectiveRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Service\FileInfoRepository;

class SubmitObjectiveProgressReport
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
     * @var ObjectiveRepository
     */
    protected $objectiveRepository;

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
            TeamParticipantRepository $teamParticipantRepository, ObjectiveRepository $objectiveRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository, FileInfoRepository $fileInfoRepository)
    {
        $this->teamMemberRepository = $teamMemberRepository;
        $this->teamParticipantRepository = $teamParticipantRepository;
        $this->objectiveRepository = $objectiveRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
        $this->fileInfoRepository = $fileInfoRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $teamParticipantId, string $objectiveId,
            ObjectiveProgressReportData $objectiveProgressReportData): string
    {
        foreach ($objectiveProgressReportData->getKeyResultProgressReportDataIterator() as $keyResultProgressReportData) {
            foreach ($keyResultProgressReportData->getFileInfoIdListOfAttachment() as $fileInfoId) {
                $fileInfo = $this->fileInfoRepository->fileInfoOfTeam($teamId, $fileInfoId);
                $keyResultProgressReportData->addAttachment($fileInfo);
            }
        }
        
        $teamParticipant = $this->teamParticipantRepository->ofId($teamParticipantId);
        $objective = $this->objectiveRepository->ofId($objectiveId);
        $id = $this->objectiveProgressReportRepository->nextIdentity();
        $objectiveProgressReport = $this->teamMemberRepository
                ->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->submitObjectiveProgressReport($teamParticipant, $objective, $id, $objectiveProgressReportData);
        $this->objectiveProgressReportRepository->add($objectiveProgressReport);
        return $id;
    }

}
