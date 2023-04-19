<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\Participant\ObjectiveProgressReportRepository;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Service\FileInfoRepository;

class UpdateObjectiveProgressReport
{

    /**
     * 
     * @var ClientParticipantRepository
     */
    protected $clientParticipantRepository;

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
            ObjectiveProgressReportRepository $objectiveProgressReportRepository, FileInfoRepository $fileInfoRepository)
    {
        $this->clientParticipantRepository = $clientParticipantRepository;
        $this->objectiveProgressReportRepository = $objectiveProgressReportRepository;
        $this->fileInfoRepository = $fileInfoRepository;
    }

    public function execute(
            string $firmId, string $clientId, string $participantId, string $objectiveProgressReportId,
            ObjectiveProgressReportData $objectiveProgressReportData): void
    {
        foreach ($objectiveProgressReportData->getKeyResultProgressReportDataIterator() as $keyResultProgressReportData) {
            foreach ($keyResultProgressReportData->getFileInfoIdListOfAttachment() as $fileInfoId) {
                $fileInfo = $this->fileInfoRepository->fileInfoOfClient($firmId, $clientId, $fileInfoId);
                $keyResultProgressReportData->addAttachment($fileInfo);
            }
        }
        
        $objectiveProgressReport = $this->objectiveProgressReportRepository->ofId($objectiveProgressReportId);
        $this->clientParticipantRepository->aClientParticipant($firmId, $clientId, $participantId)
                ->updateObjectiveProgressReport($objectiveProgressReport, $objectiveProgressReportData);
        $this->clientParticipantRepository->update();
    }

}
