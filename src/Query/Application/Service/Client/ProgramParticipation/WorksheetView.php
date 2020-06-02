<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;

class WorksheetView
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    function __construct(WorksheetRepository $worksheetRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
    }

    /**
     * 
     * @param ProgramParticipationCompositionId $programParticipationCompositionId
     * @param int $page
     * @param int $pageSize
     * @return Worksheet[]
     */
    public function showAll(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize,
            ?string $missionId, ?string $parentWorksheetId)
    {
        return $this->worksheetRepository->allWorksheetsOfParticipant(
                        $programParticipationCompositionId, $page, $pageSize, $missionId, $parentWorksheetId);
    }

    public function showById(ProgramParticipationCompositionId $programParticipationCompositionId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->aWorksheetOfParticipant($programParticipationCompositionId, $worksheetId);
    }

    /**
     * 
     * @param ProgramParticipationCompositionId $programParticipationCompositionId
     * @param string $missionId
     * @return Worksheet[]
     */
    public function showAllWorksheetsCorrespondWithMission(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $missionId)
    {
        return $this->worksheetRepository->allWorksheetOfParticipantCorrespondWithMission(
                        $programParticipationCompositionId, $missionId);
    }

}
