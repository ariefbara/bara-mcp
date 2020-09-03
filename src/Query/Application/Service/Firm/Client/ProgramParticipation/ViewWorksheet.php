<?php

namespace Query\Application\Service\Firm\Client\ProgramParticipation;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;

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
     * @param string $firmId
     * @param string $clientId
     * @param string $programParticipationId
     * @param int $page
     * @param int $pageSize
     * @return Worksheet[]
     */
    public function showAll(
            string $firmId, string $clientId, string $programParticipationId, int $page, int $pageSize,
            ?string $missionId, ?string $parentWorksheetId)
    {
        return $this->worksheetRepository
                        ->allWorksheetsBelongsToClient(
                                $firmId, $clientId, $programParticipationId, $page, $pageSize, $missionId,
                                $parentWorksheetId);
    }

    public function showById(string $firmId, string $clientId, string $programParticipationId, string $worksheetId): Worksheet
    {
        return $this->worksheetRepository->aWorksheetBelongsToClient($firmId, $clientId, $programParticipationId,
                        $worksheetId);
    }

}
