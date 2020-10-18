<?php

namespace Query\Application\Service\Firm\Team\ProgramParticipation;

use Query\{
    Domain\Model\Firm\Program\Participant\Worksheet,
    Infrastructure\QueryFilter\WorksheetFilter
};

class ViewWorksheetRepository
{

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    public function __construct(WorksheetRepository $worksheetRepository)
    {
        $this->worksheetRepository = $worksheetRepository;
    }

    /**
     * 
     * @param string $teamId
     * @param string $teamProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @param WorksheetFilter|null $worksheetFilter
     * @return Worksheet[]
     */
    public function showAll(string $teamId, string $teamProgramParticipationId, int $page, int $pageSize,
            ?WorksheetFilter $worksheetFilter)
    {
        return $this->worksheetRepository->allWorksheetsInProgramParticipationBelongsToTeam($teamId,
                        $teamProgramParticipationId, $page, $pageSize, $worksheetFilter);
    }

    public function showById(string $teamId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->aWorksheetBelongsToTeam($teamId, $worksheetId);
    }

}
