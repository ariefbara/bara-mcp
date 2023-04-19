<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Application\Service\Participant\ObjectiveRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Service\FileInfoRepository;

class SubmitObjectiveProgressReport
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

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

    public function __construct(ClientParticipantRepository $clientParticipantRepository,
            ObjectiveRepository $objectiveRepository,
            ObjectiveProgressReportRepository $objectiveProgressReportRepository, FileInfoRepository $fileInfoRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->objectiveRepository = $objectiveRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
        $this->fileInfoRepository = $fileInfoRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $participantId, string $objectiveId,
            ObjectiveProgressReportData $objectiveProgressReportData): string
    {
        foreach ($objectiveProgressReportData->getKeyResultProgressReportDataIterator() as $keyResultProgressReportData) {
            foreach ($keyResultProgressReportData->getFileInfoIdListOfAttachment() as $fileInfoId) {
                $fileInfo = $this->fileInfoRepository->fileInfoOfClient($firmId, $clientId, $fileInfoId);
                $keyResultProgressReportData->addAttachment($fileInfo);
            }
        }
        $objective = $this->objectiveRepository->ofId($objectiveId);
        $id = $this->objectiveProgressReportRepository->nextIdentity();
        $objectiveProgressReport = $this->clientParticipantRepository
                ->aClientParticipant($firmId, $clientId, $participantId)
                ->submitObjectiveProgressReport($objective, $id, $objectiveProgressReportData);
        $this->objectiveProgressReportRepository->add($objectiveProgressReport);
        return $id;
    }

}
