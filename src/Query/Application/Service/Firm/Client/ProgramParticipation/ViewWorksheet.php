<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\ {
    Domain\Model\Firm\Program\Participant\Worksheet,
    Infrastructure\QueryFilter\WorksheetFilter
};

class ViewWorksheet
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
     * @param string $clientId
     * @param string $clientProgramParticipationId
     * @param int $page
     * @param int $pageSize
     * @param WorksheetFilter|null $worksheetFilter
     * @return Worksheet[]
     */
    public function showAll(
            string $clientId, string $clientProgramParticipationId, int $page, int $pageSize,
            ?WorksheetFilter $worksheetFilter)
    {
        return $this->worksheetRepository->allWorksheetsInProgramParticipationBelongsToClient(
                        $clientId, $clientProgramParticipationId, $page, $pageSize, $worksheetFilter);
    }
    
    public function showById(string $clientId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->aWorksheetBelongsToClient($clientId, $worksheetId);
    }

}
