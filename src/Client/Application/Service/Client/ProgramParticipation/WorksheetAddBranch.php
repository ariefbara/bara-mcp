<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\{
    Application\Service\Firm\Program\MissionRepository,
    Domain\Model\Client\ProgramParticipation\Worksheet
};
use Shared\Domain\Model\FormRecordData;

class WorksheetAddBranch
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    /**
     *
     * @var MissionRepository
     */
    protected $missionRepository;

    function __construct(WorksheetRepository $worksheetRepository, MissionRepository $missionRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
        $this->missionRepository = $missionRepository;
    }

    public function execute(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $parentWorksheetId,
            string $missionId, string $name, FormRecordData $formRecordData): Worksheet
    {
        $id = $this->worksheetRepository->nextIdentity();
        $mission = $this->missionRepository->ofId(
                $programParticipationCompositionId->getClientId(),
                $programParticipationCompositionId->getProgramParticipationId(), $missionId);
        $formRecord = $mission->createWorksheetFormRecord($id, $formRecordData);

        $worksheet = $this->worksheetRepository->ofId($programParticipationCompositionId, $parentWorksheetId)
                ->createBranchWorksheet($id, $name, $mission, $formRecord);
        $this->worksheetRepository->add($worksheet);
        return $worksheet;
    }

}
